<?php

namespace App\Http\Controllers;

use App\Models\Struk;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Illuminate\Support\Facades\DB;

class StrukController extends Controller
{
    public function index(Request $request)
    {
        $query = Struk::query();
        
        // ✅ PERBAIKAN: Hapus default 'progress', biarkan tampil semua jika tidak ada filter
        $status = $request->input('status');

        // Hanya filter jika user memilih status tertentu
        if ($status === 'progress') {
            $query->where('status', 'progress');
        } elseif ($status === 'completed') {
            $query->where('status', 'completed');
        }
        // Jika status = null atau 'all', tampilkan semua data

        if ($request->filled('search')) {
            $search = strtolower($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(nama_toko) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(nomor_struk) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(CAST(items AS TEXT)) LIKE ?', ["%{$search}%"]);
            });
        }
        
        $struks = $query->latest()->paginate(10)->withQueryString();
        $barangList = Barang::all()->keyBy('kode_barang');

        $struks->getCollection()->transform(function ($struk) use ($barangList) {
            $items = json_decode($struk->items, true) ?? [];
            $items = array_map(function ($item) use ($barangList) {
                $item['nama_barang'] = $barangList[$item['nama']]?->nama_barang ?? $item['nama'];
                return $item;
            }, $items);
            $struk->items = json_encode($items);
            return $struk;
        });

        Log::info('Struk index loaded', ['status_filter' => $status, 'total_struks' => $struks->total()]);

        return view('struks.index', compact('struks', 'barangList'));
    }

    public function create()
    {
        $barangList = Barang::all();
        $barang = null;
        return view('struks.create', compact('barangList', 'barang'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama_toko' => 'required|string|max:255',
            'nomor_struk' => 'required|string|max:255|unique:struks,nomor_struk',
            'tanggal_struk' => 'required|date',
            'tanggal_keluar' => 'nullable|date',
            'items' => 'required|array|min:1',
            'items.*.nama' => 'required|exists:master_barang,kode_barang',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.harga' => 'required|numeric|min:0',
            'total_harga' => 'required|numeric|min:0',
            'status' => 'required|in:progress,completed',
            'foto_struk' => 'nullable|image|max:2048',
        ]);

        Log::info('Storing new struk', ['request_data' => $request->all()]);

        $fotoFilename = null;
        if ($request->hasFile('foto_struk')) {
            $fotoPath = $request->file('foto_struk')->store('struk_foto', 'public');
            $fotoFilename = basename($fotoPath);
        }

        DB::beginTransaction();
        try {
            foreach ($validatedData['items'] as $item) {
                $kodeBarang = $item['nama'];
                $jumlah = $item['jumlah'];

                $barang = Barang::where('kode_barang', $kodeBarang)->firstOrFail();
                $barang->jumlah += $jumlah;
                $barang->save();
                Log::info('Increased stock for barang: ' . $kodeBarang, ['new_jumlah' => $barang->jumlah]);
            }

            $struk = Struk::create([
                'nama_toko' => $validatedData['nama_toko'],
                'nomor_struk' => $validatedData['nomor_struk'],
                'tanggal_struk' => $validatedData['tanggal_struk'],
                'tanggal_keluar' => $validatedData['tanggal_keluar'] ?? null,
                'items' => json_encode($validatedData['items']),
                'total_harga' => $validatedData['total_harga'],
                'foto_struk' => $fotoFilename,
                'status' => $request->status,
            ]);

            DB::commit();
            Log::info('Struk created successfully', ['struk_id' => $struk->id]);
            return redirect()->route('struks.index')->with('success', 'Struk berhasil disimpan dan stok diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($fotoFilename) {
                Storage::disk('public')->delete('struk_foto/' . $fotoFilename);
            }
            Log::error('Failed to store struk', ['error' => $e->getMessage()]);
            return back()->withErrors('Gagal menyimpan struk: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(Struk $struk)
    {
        $struk->items = json_decode($struk->items, true) ?? [];
        $barangList = Barang::all();
        $barang = null;
        return view('struks.edit', compact('struk', 'barangList', 'barang'));
    }

    public function update(Request $request, $id)
    {
        $struk = Struk::findOrFail($id);

        $validated = $request->validate([
            'nama_toko' => 'required|string',
            'nomor_struk' => 'required|string',
            'tanggal_struk' => 'required|date',
            'status' => 'required|string',
            'nama' => 'required|array',
            'jumlah' => 'required|array',
            'harga' => 'required|array',
            'total_harga' => 'required|numeric',
        ]);

        $items = [];
        foreach ($validated['nama'] as $i => $nama) {
            $items[] = [
                'nama' => $nama,
                'jumlah' => $validated['jumlah'][$i],
                'harga' => $validated['harga'][$i],
            ];
        }

        $struk->update([
            'nama_toko' => $validated['nama_toko'],
            'nomor_struk' => $validated['nomor_struk'],
            'tanggal_struk' => $validated['tanggal_struk'],
            'status' => $validated['status'],
            'items' => json_encode($items),
            'total_harga' => $validated['total_harga'],
        ]);

        return redirect()->route('struks.index')->with('success', 'Struk berhasil diupdate!');
    }

    public function destroy(Struk $struk)
    {
        Log::info('Deleting struk ID: ' . $struk->id);

        DB::beginTransaction();
        try {
            $items = json_decode($struk->items, true) ?? [];
            foreach ($items as $item) {
                $barang = Barang::where('kode_barang', $item['nama'])->first();
                if ($barang) {
                    $barang->jumlah -= $item['jumlah'];
                    $barang->save();
                    Log::info('Reduced stock for barang: ' . $item['nama'], ['new_jumlah' => $barang->jumlah]);
                }
            }

            if ($struk->foto_struk) {
                Storage::disk('public')->delete('struk_foto/' . $struk->foto_struk);
            }

            $struk->delete();

            DB::commit();
            Log::info('Struk deleted successfully', ['struk_id' => $struk->id]);
            return redirect()->route('struks.index')->with('success', 'Struk berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete struk ID: ' . $struk->id, ['error' => $e->getMessage()]);
            return back()->withErrors('Gagal menghapus struk: ' . $e->getMessage());
        }
    }

    public function show(Struk $struk)
    {
        $struk->items = json_decode($struk->items, true) ?? [];
        $masterBarang = Barang::all()->keyBy('kode_barang');
        return view('struks.show', compact('struk', 'masterBarang'));
    }

    public function exportExcel()
    {
        $status = request()->input('status');
        $query = Struk::query();
        if ($status === 'progress') {
            $query->where('status', 'progress');
        } elseif ($status === 'completed') {
            $query->where('status', 'completed');
        }
        $struks = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray(['ID', 'Nama Toko', 'Nomor Struk', 'Tanggal', 'Items', 'Total Harga', 'Status'], null, 'A1');

        $row = 2;
        foreach ($struks as $struk) {
            $items = json_decode($struk->items, true) ?? [];
            $itemsString = collect($items)->map(fn($item) => "{$item['nama']} ({$item['jumlah']} x {$item['harga']})")->implode(', ');
            $sheet->fromArray([
                $struk->id,
                $struk->nama_toko,
                $struk->nomor_struk,
                $struk->tanggal_struk,
                $itemsString,
                $struk->total_harga,
                $struk->status,
            ], null, "A$row");
            $row++;
        }

        $sheet->getStyle("F2:F$row")->getNumberFormat()->setFormatCode('#,##0');

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="data_struks_' . date('Ymd_His') . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function exportCsv()
    {
        $status = request()->input('status');
        $query = Struk::query();
        if ($status === 'progress') {
            $query->where('status', 'progress');
        } elseif ($status === 'completed') {
            $query->where('status', 'completed');
        }
        $struks = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray(['ID', 'Nama Toko', 'Nomor Struk', 'Tanggal', 'Items', 'Total Harga', 'Status'], null, 'A1');

        $row = 2;
        foreach ($struks as $struk) {
            $items = json_decode($struk->items, true) ?? [];
            $itemsString = collect($items)->map(fn($item) => "{$item['nama']} ({$item['jumlah']} x {$item['harga']})")->implode(', ');
            $sheet->fromArray([
                $struk->id,
                $struk->nama_toko,
                $struk->nomor_struk,
                $struk->tanggal_struk,
                $itemsString,
                $struk->total_harga,
                $struk->status,
            ], null, "A$row");
            $row++;
        }

        $writer = new Csv($spreadsheet);
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="data_struks_' . date('Ymd_His') . '.csv"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function updateItems(Request $request, $id)
    {
        $struk = Struk::findOrFail($id);

        $request->validate([
            'nama.*' => 'required|exists:master_barang,kode_barang',
            'jumlah.*' => 'required|integer|min:1',
            'harga.*' => 'required|numeric|min:0',
        ]);

        Log::info('Updating items for struk ID: ' . $id, ['request_data' => $request->all()]);

        DB::beginTransaction();
        try {
            $oldItems = json_decode($struk->items, true) ?? [];
            foreach ($oldItems as $item) {
                $barang = Barang::where('kode_barang', $item['nama'])->first();
                if ($barang) {
                    $barang->jumlah -= $item['jumlah'];
                    $barang->save();
                    Log::info('Reduced stock for barang: ' . $item['nama'], ['new_jumlah' => $barang->jumlah]);
                }
            }

            $namaArr = $request->input('nama', []);
            $jumlahArr = $request->input('jumlah', []);
            $hargaArr = $request->input('harga', []);

            $items = [];
            foreach ($namaArr as $i => $nama) {
                $jumlah = (int) ($jumlahArr[$i] ?? 0);
                $harga = (float) ($hargaArr[$i] ?? 0);

                $barang = Barang::where('kode_barang', $nama)->firstOrFail();
                $barang->jumlah += $jumlah;
                $barang->save();
                Log::info('Increased stock for barang: ' . $nama, ['new_jumlah' => $barang->jumlah]);

                $items[] = [
                    'nama' => $nama,
                    'jumlah' => $jumlah,
                    'harga' => $harga,
                ];
            }

            $total = collect($items)->sum(fn($item) => $item['jumlah'] * $item['harga']);

            $struk->update([
                'items' => json_encode($items),
                'total_harga' => $total,
            ]);

            DB::commit();
            Log::info('Items updated successfully for struk ID: ' . $id);
            return redirect()->route('struks.index')->with('success', 'Item berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update items for struk ID: ' . $id, ['error' => $e->getMessage()]);
            return back()->withErrors('Gagal mengupdate item: ' . $e->getMessage())->withInput();
        }
    }

    public function addItem(Request $request, $id)
    {
        $struk = Struk::findOrFail($id);

        $request->validate([
            'nama' => 'required|exists:master_barang,kode_barang',
            'jumlah' => 'required|integer|min:1',
            'harga' => 'required|numeric|min:0',
        ]);

        Log::info('Adding item to struk ID: ' . $id, ['request_data' => $request->all()]);

        DB::beginTransaction();
        try {
            $items = json_decode($struk->items, true) ?? [];
            $barang = Barang::where('kode_barang', $request->nama)->firstOrFail();
            $barang->jumlah += $request->jumlah;
            $barang->save();
            Log::info('Increased stock for barang: ' . $request->nama, ['new_jumlah' => $barang->jumlah]);

            $items[] = $request->only(['nama', 'jumlah', 'harga']);
            $total = collect($items)->sum(fn($item) => $item['jumlah'] * $item['harga']);

            $struk->update([
                'items' => json_encode($items),
                'total_harga' => $total,
            ]);

            DB::commit();
            Log::info('Item added successfully to struk ID: ' . $id);
            return redirect()->route('struks.index')->with('success', 'Item baru berhasil ditambahkan dan stok telah ditambah.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to add item to struk ID: ' . $id, ['error' => $e->getMessage()]);
            return back()->withErrors('Gagal menambahkan item: ' . $e->getMessage())->withInput();
        }
    }

    public function deleteItem($id, $index)
    {
        $struk = Struk::findOrFail($id);
        $items = json_decode($struk->items, true) ?? [];

        if (!isset($items[$index])) {
            Log::warning('Item not found for deletion', ['struk_id' => $id, 'index' => $index]);
            return redirect()->route('struks.index')->with('error', 'Item tidak ditemukan.');
        }

        Log::info('Deleting item from struk ID: ' . $id, ['index' => $index]);

        DB::beginTransaction();
        try {
            $deletedItem = $items[$index];
            $barang = Barang::where('kode_barang', $deletedItem['nama'])->first();
            if ($barang) {
                $barang->jumlah -= $deletedItem['jumlah'];
                $barang->save();
                Log::info('Reduced stock for barang: ' . $deletedItem['nama'], ['new_jumlah' => $barang->jumlah]);
            }

            unset($items[$index]);
            $items = array_values($items);
            $total = collect($items)->sum(fn($item) => $item['jumlah'] * $item['harga']);

            $struk->update([
                'items' => json_encode($items),
                'total_harga' => $total,
            ]);

            DB::commit();
            Log::info('Item deleted successfully from struk ID: ' . $id);
            return redirect()->route('struks.index')->with('success', 'Item berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete item from struk ID: ' . $id, ['error' => $e->getMessage()]);
            return redirect()->route('struks.index')->with('error', 'Gagal menghapus item: ' . $e->getMessage());
        }
    }

    public function bulkDelete(Request $request)
    {
        $selectedIds = $request->input('selected_ids');

        if (is_string($selectedIds)) {
            $selectedIds = explode(',', $selectedIds);
            $request->merge(['selected_ids' => $selectedIds]);
        }

        $request->validate([
            'selected_ids' => 'required|array',
            'selected_ids.*' => 'exists:struks,id',
        ]);

        Log::info('Bulk deleting struks', ['selected_ids' => $selectedIds]);

        DB::beginTransaction();
        try {
            $status = $request->input('status');
            $query = Struk::whereIn('id', $selectedIds);
            if ($status === 'progress') {
                $query->where('status', 'progress');
            } elseif ($status === 'completed') {
                $query->where('status', 'completed');
            }
            $struks = $query->get();

            foreach ($struks as $struk) {
                $items = json_decode($struk->items, true) ?? [];
                foreach ($items as $item) {
                    $barang = Barang::where('kode_barang', $item['nama'])->first();
                    if ($barang) {
                        $barang->jumlah -= $item['jumlah'];
                        $barang->save();
                        Log::info('Reduced stock for barang: ' . $item['nama'], ['new_jumlah' => $barang->jumlah]);
                    }
                }

                if ($struk->foto_struk) {
                    Storage::disk('public')->delete('struk_foto/' . $struk->foto_struk);
                }
            }

            $query->delete();

            DB::commit();
            Log::info('Struks bulk deleted successfully', ['count' => count($selectedIds)]);
            return redirect()->route('struks.index')->with('success', count($selectedIds) . ' struk berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to bulk delete struks', ['error' => $e->getMessage()]);
            return back()->withErrors('Gagal menghapus struk: ' . $e->getMessage());
        }
    }

    public function getItems(Struk $struk)
    {
        $items = json_decode($struk->items, true) ?? [];
        $barangList = Barang::all()->keyBy('kode_barang');

        $items = array_map(function ($item) use ($barangList) {
            $item['nama_barang'] = $barangList[$item['nama']]?->nama_barang ?? $item['nama'];
            return $item;
        }, $items);

        Log::info('Fetching items for struk ID: ' . $struk->id, ['items_count' => count($items)]);

        return response()->json([
            'items' => $items,
            'foto_struk' => $struk->foto_struk,
            'nama_toko' => $struk->nama_toko,
            'nomor_struk' => $struk->nomor_struk,
            'tanggal_struk' => $struk->tanggal_struk,
            'tanggal_keluar' => $struk->tanggal_keluar,
            'status' => $struk->status,
        ]);
    }
}