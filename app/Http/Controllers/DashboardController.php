<?php

namespace App\Http\Controllers;

use App\Models\Struk;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Models\Barang;
use Illuminate\Pagination\LengthAwarePaginator;

final class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $timePeriod = $request->input('time_period', 'all');
        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $totalStrukPemasukan = $this->getFilteredCount(Struk::query(), $timePeriod, $bulan, $tahun, $startDate, $endDate);
        $totalStrukPengeluaran = $this->getFilteredCount(Pengeluaran::query(), $timePeriod, $bulan, $tahun, $startDate, $endDate);
        $totalStruk = $totalStrukPemasukan + $totalStrukPengeluaran;

        $latestStruk = Struk::latest()->first();
        $latestPengeluaranStruk = Pengeluaran::latest()->first();

        if ($latestStruk) {
            $latestStruk->processed_items = $this->parseItems($latestStruk->items);
        }
        
        if ($latestPengeluaranStruk) {
            $latestPengeluaranStruk->processed_items = $this->parseItems($latestPengeluaranStruk->daftar_barang);
        }

        $totalBarangMasuk = $this->calculateTotalBarangFiltered(Struk::class, 'items', $timePeriod, $bulan, $tahun, $startDate, $endDate);
        $totalBarangKeluar = $this->calculateTotalBarangFiltered(Pengeluaran::class, 'daftar_barang', $timePeriod, $bulan, $tahun, $startDate, $endDate);

        // ===== PERBAIKAN: Buat query dengan filter time period =====
        $strukQuery = Struk::query();
        $pengeluaranQuery = Pengeluaran::query();

        // Terapkan filter time period
        switch ($timePeriod) {
            case 'daily':
                $strukQuery->whereDate('tanggal_struk', today());
                $pengeluaranQuery->whereDate('tanggal', today());
                break;
            case 'weekly':
                $strukQuery->whereBetween('tanggal_struk', [now()->startOfWeek(), now()->endOfWeek()]);
                $pengeluaranQuery->whereBetween('tanggal', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'monthly':
                $strukQuery->whereMonth('tanggal_struk', $bulan)->whereYear('tanggal_struk', $tahun);
                $pengeluaranQuery->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun);
                break;
            case 'range':
                if ($startDate && $endDate) {
                    $strukQuery->whereBetween('tanggal_struk', [$startDate, $endDate . ' 23:59:59']);
                    $pengeluaranQuery->whereBetween('tanggal', [$startDate, $endDate . ' 23:59:59']);
                }
                break;
        }

        $barangMaster = Barang::pluck('nama_barang', 'kode_barang');

        $barangList = $this->processAndPaginateBarangList(
            $strukQuery,
            $request->only(['search', 'sort']),
            'page_barang',
            $barangMaster
        );

        $pengeluaranBarangList = $this->processAndPaginatePengeluaranList(
            $pengeluaranQuery,
            $request->only(['search_pengeluaran', 'sort_pengeluaran']),
            'page_pengeluaran',
            $barangMaster
        );

        $historyBarang = $this->generateAndPaginateHistoryBarang('page_history', $barangMaster);

        // Chart data
        $labels = [];
        $dataMasuk = [];
        $dataKeluar = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $labels[] = now()->subDays($i)->format('d-m-Y');

            $masuk = Struk::whereDate('tanggal_struk', $date)->get()->reduce(function ($carry, $struk) {
                $items = $this->parseItems($struk->items);
                return $carry + collect($items)->sum('jumlah');
            }, 0);
            $dataMasuk[] = $masuk;

            $keluar = Pengeluaran::whereDate('tanggal', $date)->get()->reduce(function ($carry, $pengeluaran) {
                $items = $this->parseItems($pengeluaran->daftar_barang);
                return $carry + collect($items)->sum('jumlah');
            }, 0);
            $dataKeluar[] = $keluar;
        }

        // ===== PERBAIKAN: Gunakan query yang sudah difilter =====
        // Clone query untuk menghindari modifikasi query asli
        $strukQueryForList = clone $strukQuery;
        
        // Terapkan filter search untuk pemasukan
        if ($request->filled('search')) {
            $search = $request->input('search');
            $strukQueryForList->where(function($q) use ($search) {
                $q->where('nomor_struk', 'like', "%{$search}%")
                  ->orWhere('items', 'like', "%{$search}%")
                  ->orWhere('nama_toko', 'like', "%{$search}%");
            });
        }

        // Terapkan filter status untuk pemasukan
        if ($request->filled('status_pemasukan')) {
            $strukQueryForList->where('status', $request->input('status_pemasukan'));
        }

        // Terapkan sorting untuk pemasukan
        $sortPemasukan = $request->input('sort', 'tanggal_desc');
        switch ($sortPemasukan) {
            case 'tanggal_asc':
                $strukQueryForList->orderBy('tanggal_struk', 'asc');
                break;
            case 'nama_asc':
                $strukQueryForList->orderBy('nama_toko', 'asc');
                break;
            case 'nama_desc':
                $strukQueryForList->orderBy('nama_toko', 'desc');
                break;
            default: // tanggal_desc
                $strukQueryForList->orderBy('tanggal_struk', 'desc');
                break;
        }

        $pemasukans = $strukQueryForList->paginate(10, ['*'], 'page_pemasukan')
            ->appends($request->query());

        // Process Completed Pemasukans
        $completedPemasukanQuery = clone $strukQuery;
        $completedPemasukanQuery->where('status', 'completed');
        $completedPemasukans = $this->paginateCollection(
            $completedPemasukanQuery->get(), 
            10, 
            'page_pemasukan_completed'
        );

        // ===== PERBAIKAN: Gunakan query yang sudah difilter untuk pengeluaran =====
        $pengeluaranQueryForList = clone $pengeluaranQuery;
        $pengeluaranQueryForList->with('pegawai');

        // Terapkan filter search untuk pengeluaran
        if ($request->filled('search_pengeluaran')) {
            $search = $request->input('search_pengeluaran');
            $pengeluaranQueryForList->where(function($q) use ($search) {
                $q->where('nomor_struk', 'like', "%{$search}%")
                  ->orWhere('daftar_barang', 'like', "%{$search}%")
                  ->orWhere('nama_toko', 'like', "%{$search}%")
                  ->orWhereHas('pegawai', function($query) use ($search) {
                      $query->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        // Terapkan filter status untuk pengeluaran
        if ($request->filled('status_pengeluaran')) {
            $pengeluaranQueryForList->where('status', $request->input('status_pengeluaran'));
        }

        // Terapkan sorting untuk pengeluaran
        $sortPengeluaran = $request->input('sort_pengeluaran', 'tanggal_desc');
        switch ($sortPengeluaran) {
            case 'tanggal_asc':
                $pengeluaranQueryForList->orderBy('tanggal', 'asc');
                break;
            case 'nama_asc':
                $pengeluaranQueryForList->orderBy('nama_toko', 'asc');
                break;
            case 'nama_desc':
                $pengeluaranQueryForList->orderBy('nama_toko', 'desc');
                break;
            default: // tanggal_desc
                $pengeluaranQueryForList->orderBy('tanggal', 'desc');
                break;
        }

        $pengeluarans = $pengeluaranQueryForList->paginate(10, ['*'], 'page_pengeluaran')
            ->appends($request->query());

        return view('dashboard', [
            'barangList' => $barangList,
            'pengeluaranBarangList' => $pengeluaranBarangList,
            'historyBarang' => $historyBarang,
            'totalStruk' => $totalStruk,
            'latestStruk' => $latestStruk,
            'latestPengeluaranStruk' => $latestPengeluaranStruk,
            'totalPemasukan' => $totalStrukPemasukan,
            'totalPengeluaran' => $totalStrukPengeluaran,
            'totalBarangMasuk' => $totalBarangMasuk,
            'totalBarangKeluar' => $totalBarangKeluar,
            'timePeriod' => $timePeriod,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'labels' => $labels,
            'dataMasuk' => $dataMasuk,
            'dataKeluar' => $dataKeluar,
            'barangMaster' => $barangMaster,
            'pemasukans' => $pemasukans,
            'completedPemasukans' => $completedPemasukans,
            'pengeluarans' => $pengeluarans,
        ]);
    }

    private function getFilteredCount($query, $timePeriod, $bulan = null, $tahun = null, $startDate = null, $endDate = null)
    {
        $query = clone $query;
        $model = $query->getModel();
        $dateColumn = $model instanceof \App\Models\Struk ? 'tanggal_struk' : 'tanggal';

        switch ($timePeriod) {
            case 'daily':
                $query->whereDate($dateColumn, today());
                break;
            case 'weekly':
                $query->whereBetween($dateColumn, [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'monthly':
                $query->whereMonth($dateColumn, $bulan)->whereYear($dateColumn, $tahun);
                break;
            case 'range':
                if ($startDate && $endDate) {
                    $query->whereBetween($dateColumn, [$startDate, $endDate . ' 23:59:59']);
                }
                break;
        }

        return $query->count();
    }

    private function calculateTotalBarangFiltered(string $model, string $itemsField, $timePeriod, $bulan = null, $tahun = null, $startDate = null, $endDate = null): int
    {
        $query = $model::query();
        $dateColumn = $model === \App\Models\Struk::class ? 'tanggal_struk' : 'tanggal';

        switch ($timePeriod) {
            case 'daily':
                $query->whereDate($dateColumn, today());
                break;
            case 'weekly':
                $query->whereBetween($dateColumn, [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'monthly':
                $query->whereMonth($dateColumn, $bulan)->whereYear($dateColumn, $tahun);
                break;
            case 'range':
                if ($startDate && $endDate) {
                    $query->whereBetween($dateColumn, [$startDate, $endDate . ' 23:59:59']);
                }
                break;
        }

        return $query->get()->reduce(function ($carry, $record) use ($itemsField) {
            $items = $this->parseItems($record->{$itemsField});
            return $carry + collect($items)->sum('jumlah');
        }, 0);
    }

    private function calculateTotalBarang(string $model, string $itemsField): int
    {
        return $model::query()->get()->reduce(function ($carry, $record) use ($itemsField) {
            $items = $this->parseItems($record->{$itemsField});
            return $carry + collect($items)->sum('jumlah');
        }, 0);
    }

    private function processAndPaginateBarangList($query, array $filters = [], string $pageName = 'page', $barangMaster = [])
    {
        $barangList = $query->get()->flatMap(function ($struk) {
            $items = $this->parseItems($struk->items);
            return collect($items)->map(function ($item) use ($struk) {
                return [
                    'nama' => $item['nama'],
                    'jumlah' => (int) $item['jumlah'],
                    'nomor_struk' => $struk->nomor_struk,
                    'tanggal' => $struk->tanggal_struk,
                    'tanggal_keluar' => $struk->tanggal_keluar,
                    'status_progres' => $this->determineStatusProgres($struk, $item),
                ];
            });
        })->groupBy('nama')->map(function ($items, $nama) {
            $latest = $items->sortByDesc('tanggal')->first();

            $latestPengeluaran = \App\Models\Pengeluaran::query()->get()->flatMap(function ($pengeluaran) {
                return collect($this->parseItems($pengeluaran->daftar_barang))->map(function ($item) use ($pengeluaran) {
                    return [
                        'nama' => $item['nama'],
                        'tanggal' => $pengeluaran->tanggal,
                    ];
                });
            })->where('nama', $nama)->sortByDesc('tanggal')->first();

            return [
                'nama' => $nama,
                'jumlah' => $items->sum('jumlah'),
                'nomor_struk' => $latest['nomor_struk'],
                'tanggal' => $latest['tanggal'],
                'tanggal_keluar' => $latest['tanggal_keluar'] ?? null,
                'status_progres' => $latest['status_progres'] ?? 'pending',
            ];
        })->values();

        $barangList = $barangList->filter(function ($item) {
            return $item['status_progres'] !== 'completed';
        });

        if (!empty($filters['search'])) {
            $search = strtolower($filters['search']);
            $barangList = $barangList->filter(function ($item) use ($search) {
                return stripos($item['nama'], $search) !== false ||
                    stripos($item['nomor_struk'], $search) !== false;
            });
        }

        $sort = $filters['sort'] ?? 'tanggal_desc';
        switch ($sort) {
            case 'nama_asc':
                $barangList = $barangList->sortBy('nama')->values();
                break;
            case 'nama_desc':
                $barangList = $barangList->sortByDesc('nama')->values();
                break;
            case 'tanggal_asc':
                $barangList = $barangList->sortBy('tanggal')->values();
                break;
            case 'status_asc':
                $barangList = $barangList->sortBy('status_progres')->values();
                break;
            case 'status_desc':
                $barangList = $barangList->sortByDesc('status_progres')->values();
                break;
            default:
                $barangList = $barangList->sortByDesc('tanggal')->values();
                break;
        }

        return $this->paginateCollection($barangList, 10, $pageName);
    }

    private function determineStatusProgres($struk, $item): string
    {
        if ($struk->status === 'completed') {
            return 'completed';
        }
        if ($struk->status === 'progress') {
             return 'progress';
        }

        if ($struk->tanggal_keluar) {
            return 'completed';
        }
        
        if ($struk->tanggal_struk && now()->diffInDays($struk->tanggal_struk) > 7) {
            return 'progress';
        }
        
        return 'progress';
    }

    private function processAndPaginatePengeluaranList($query, array $filters = [], string $pageName = 'page', $barangMaster = [])
    {
        $pengeluaranList = $query->get()->flatMap(function ($pengeluaran) {
            $items = $this->parseItems($pengeluaran->daftar_barang);
            return collect($items)->map(function ($item) use ($pengeluaran) {
                return [
                    'nama_barang' => $item['nama'],
                    'jumlah' => $item['jumlah'],
                    'nomor_struk' => $pengeluaran->nomor_struk,
                    'tanggal' => $pengeluaran->tanggal,
                    'tanggal_struk' => $pengeluaran->tanggal_struk,
                ];
            });
        });

        if (!empty($filters['search_pengeluaran'])) {
            $search = strtolower($filters['search_pengeluaran']);
            $pengeluaranList = $pengeluaranList->filter(function ($item) use ($search) {
                return stripos($item['nama_barang'], $search) !== false ||
                    stripos($item['nomor_struk'], $search) !== false;
            });
        }

        $pengeluaranList = $this->sortCollection($pengeluaranList, $filters['sort_pengeluaran'] ?? null, [
            'nama_asc' => fn($c) => $c->sortBy('nama_barang'),
            'nama_desc' => fn($c) => $c->sortByDesc('nama_barang'),
            'tanggal_asc' => fn($c) => $c->sortBy('tanggal'),
            'tanggal_desc' => fn($c) => $c->sortByDesc('tanggal'),
        ], 'tanggal_desc');

        return $this->paginateCollection($pengeluaranList, 10, $pageName);
    }

    private function generateAndPaginateHistoryBarang(string $pageName = 'page', $barangMaster = [])
    {
        $masuk = Struk::query()->get()->flatMap(function ($struk) {
            return collect($this->parseItems($struk->items))->map(function ($item) use ($struk) {
                return [
                    'tipe' => 'Masuk',
                    'nama_barang' => $item['nama'],
                    'jumlah' => (int) $item['jumlah'],
                    'nomor_struk' => $struk->nomor_struk,
                    'tanggal' => $struk->tanggal_struk,
                    'timestamp' => strtotime($struk->tanggal_struk),
                    'status_progres' => $this->determineStatusProgres($struk, $item),
                ];
            });
        });

        $keluar = Pengeluaran::query()->get()->flatMap(function ($pengeluaran) {
            return collect($this->parseItems($pengeluaran->daftar_barang))->map(function ($item) use ($pengeluaran) {
                return [
                    'tipe' => 'Keluar',
                    'nama_barang' => $item['nama'],
                    'jumlah' => (int) $item['jumlah'],
                    'nomor_struk' => $pengeluaran->nomor_struk,
                    'tanggal' => $pengeluaran->tanggal,
                    'timestamp' => strtotime($pengeluaran->tanggal),
                ];
            });
        });

        $history = $masuk->concat($keluar)
            ->sortByDesc('timestamp')
            ->values()
            ->map(function ($item) {
                unset($item['timestamp']);
                return $item;
            });

        return $this->paginateCollection($history, 10, $pageName);
    }

    private function parseItems($items): array
    {
        if (is_string($items)) {
            $decoded = json_decode($items, true);
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
                return [];
            }
            return array_filter($decoded, fn($item) => is_array($item) && isset($item['nama'], $item['jumlah']));
        }

        if (is_array($items)) {
            return array_filter($items, fn($item) => is_array($item) && isset($item['nama'], $item['jumlah']));
        }

        return [];
    }

    private function sortCollection(Collection $collection, ?string $sort, array $sortOptions, string $defaultSort): Collection
    {
        return ($sortOptions[$sort] ?? $sortOptions[$defaultSort])($collection);
    }

    private function paginateCollection(Collection $collection, int $perPage = 10, string $pageName = 'page'): LengthAwarePaginator
    {
        $currentPage = LengthAwarePaginator::resolveCurrentPage($pageName);
        $currentItems = $collection->slice(($currentPage - 1) * $perPage, $perPage)->values();

        return new LengthAwarePaginator(
            $currentItems,
            $collection->count(),
            $perPage,
            $currentPage,
            [
                'path' => request()->url(),
                'pageName' => $pageName,
                'query' => request()->query(),
            ]
        );
    }
}