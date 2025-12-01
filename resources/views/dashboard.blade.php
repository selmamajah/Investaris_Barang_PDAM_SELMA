@extends('layouts.app')

@section('content')
<div class="w-full space-y-6">
    
    <div class="flex flex-col md:flex-row md:items-start md:justify-between mb-4"> 
        <div class="mb-4 md:mb-0">
            <h1 class="text-2xl font-bold text-white">Dashboard Analisis</h1>
            <p class="mt-1 text-sm text-gray-400">Selamat datang kembali, 
                <span class="font-medium text-gray-200">{{ Auth::user()->name ?? 'Pengguna' }}</span>!
            </p>
        </div>

        <div class="flex-shrink-0">
            <form action="{{ route('dashboard') }}" method="GET" class="flex flex-col sm:flex-row sm:items-center gap-2 bg-gray-900 p-2.5 rounded-lg shadow-sm border border-gray-700">
                
                <div class="flex items-center gap-1.5 w-full sm:w-auto">
                    <label class="text-xs text-gray-400 font-medium flex-shrink-0">Periode:</label>
                    <select name="time_period" id="time-period-select"
                        class="border border-gray-600 rounded-md px-2 py-1.5 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-xs font-semibold bg-gray-800 text-white w-full">
                        <option value="daily" {{ request('time_period') == 'daily' ? 'selected' : '' }}>Harian</option>
                        <option value="weekly" {{ request('time_period') == 'weekly' ? 'selected' : '' }}>Mingguan</option>
                        <option value="monthly" {{ request('time_period') == 'monthly' ? 'selected' : '' }}>Bulanan</option>
                        <option value="range" {{ request('time_period') == 'range' ? 'selected' : '' }}>Rentang Tanggal</option>
                        <option value="all" {{ !request('time_period') || request('time_period') == 'all' ? 'selected' : '' }}>Semua</option>
                    </select>
                </div>
                
                <div id="bulan-tahun-filter"
                    class="{{ (request('time_period') == 'monthly' || $timePeriod == 'monthly') ? 'flex' : 'hidden' }} items-center gap-1.5 w-full sm:w-auto">
                    <select name="bulan" class="border border-gray-600 rounded-md px-2 py-1.5 text-xs bg-gray-800 text-white font-semibold w-full focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        @foreach(range(1, 12) as $month)
                        <option value="{{ $month }}" {{ request('bulan', $bulan ?? now()->month) == $month ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $month)->format('M') }}
                        </option>
                        @endforeach
                    </select>
                    <select name="tahun" class="border border-gray-600 rounded-md px-2 py-1.5 text-xs bg-gray-800 text-white font-semibold w-full focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        @foreach(range(now()->year, now()->year - 5) as $year)
                        <option value="{{ $year }}" {{ request('tahun', $tahun ?? now()->year) == $year ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div id="date-range-filter"
                    class="{{ (request('time_period') == 'range') ? 'flex' : 'hidden' }} flex-col sm:flex-row items-center gap-1.5 w-full sm:w-auto">
                    <label class="text-xs text-gray-400 font-medium flex-shrink-0">Dari:</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}"
                           class="border border-gray-600 rounded-md px-2 py-1.5 text-xs bg-gray-800 text-white font-semibold w-full focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" style="color-scheme: dark;">
                    <label class="text-xs text-gray-400 font-medium flex-shrink-0">Sampai:</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}"
                           class="border border-gray-600 rounded-md px-2 py-1.5 text-xs bg-gray-800 text-white font-semibold w-full focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" style="color-scheme: dark;">
                </div>

                <div class="w-full sm:w-auto">
                    <button type="submit"
                        class="bg-blue-600 text-white px-3 py-1.5 rounded-md hover:bg-blue-700 text-xs flex items-center justify-center gap-1.5 font-semibold w-full transition-colors duration-150">
                        <i class="fas fa-filter text-xs"></i>
                        Terapkan
                    </button>
                </div>
            </form>
            <div class="text-xs text-gray-400 mt-2 text-center sm:text-right flex items-center justify-center sm:justify-end space-x-1">
                @if($timePeriod != 'all')
                    <i class="fas fa-info-circle text-gray-400"></i>
                    <span>
                        Data untuk 
                        <span class="font-medium text-gray-200">
                            @if($timePeriod == 'daily')
                            hari ini
                            @elseif($timePeriod == 'weekly')
                            minggu ini
                            @elseif($timePeriod == 'monthly')
                            {{ DateTime::createFromFormat('!m', $bulan ?? now()->month)->format('F') }} {{ $tahun ?? now()->year }}
                            @elseif($timePeriod == 'range' && request('start_date') && request('end_date'))
                                {{ \Carbon\Carbon::parse(request('start_date'))->format('d M Y') }} - {{ \Carbon\Carbon::parse(request('end_date'))->format('d M Y') }}
                            @endif
                        </span>
                    </span>
                @else
                    <i class="fas fa-globe-asia text-gray-400"></i>
                    <span>Menampilkan semua data</span>
                @endif
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="bg-gray-900 p-5 rounded-lg shadow-sm relative overflow-hidden border-l-4 border-blue-500">
            <i class="fa-solid fa-file-invoice w-12 h-12 text-blue-400 opacity-10 absolute top-4 right-4"></i> 
            <p class="text-sm font-medium text-gray-400">Total SPK</p>
            <p class="text-3xl font-bold text-white mt-1">{{ $totalStruk ?? '0' }}</p>
        </div>

        <div class="bg-gray-900 p-5 rounded-lg shadow-sm relative overflow-hidden border-l-4 border-blue-500">
            <i class="fa-solid fa-arrow-circle-down w-12 h-12 text-blue-400 opacity-10 absolute top-4 right-4"></i>
            <p class="text-sm font-medium text-gray-400">Total Barang Masuk</p>
            <p class="text-3xl font-bold text-white mt-1">{{ $totalBarangMasuk ?? '0' }}</p>
        </div>

        <div class="bg-gray-900 p-5 rounded-lg shadow-sm relative overflow-hidden border-l-4 border-blue-500">
            <i class="fa-solid fa-arrow-circle-up w-12 h-12 text-blue-400 opacity-10 absolute top-4 right-4"></i>
            <p class="text-sm font-medium text-gray-400">Total Barang Keluar</p>
            <p class="text-3xl font-bold text-white mt-1">{{ $totalBarangKeluar ?? '0' }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div class="bg-gray-900 p-5 rounded-lg shadow-sm border border-gray-700 flex flex-col h-full">
            <div class="flex items-center justify-between pb-3 mb-3 border-b border-gray-700">
                <h2 class="text-base font-semibold text-gray-100 flex items-center">
                    <i class="fas fa-plus-circle text-gray-100 mr-2"></i> 
                    Struk Pemasukan Terbaru
                </h2>
                <span class="text-xs px-2 py-1 bg-blue-900 text-blue-300 rounded-full font-medium">
                    <i class="fas fa-history text-xs"></i> Terkini
                </span>
            </div>

            @if (isset($latestStruk) && $latestStruk)
                @php
                    $items = is_string($latestStruk->items ?? null) ? json_decode($latestStruk->items, true) : ($latestStruk->items ?? []);
                    $firstItem = collect($items)->first();
                    $namaBarang = $firstItem['nama'] ?? '-';
                @endphp
                <dl class="text-sm text-gray-300 space-y-2">
                    <div class="grid grid-cols-3 gap-2">
                        <dt class="text-gray-400 font-medium col-span-1">Nama Toko</dt>
                        <dd class="text-gray-100 truncate col-span-2">: {{ $latestStruk->nama_toko }}</dd>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <dt class="text-gray-400 font-medium col-span-1">No Struk</dt>
                        <dd class="font-mono text-gray-100 truncate col-span-2">: {{ $latestStruk->nomor_struk }}</dd>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <dt class="text-gray-400 font-medium col-span-1">Nama Barang</dt>
                        <dd class="text-gray-100 truncate col-span-2">: {{ $barangMaster[$namaBarang] ?? $namaBarang }}</dd>
                    </div>
                </dl>
            @else
                <div class="text-center py-4 flex-grow flex flex-col justify-center items-center">
                    <i class="fas fa-file-alt fa-2x text-gray-700"></i>
                    <p class="mt-2 text-gray-400 text-sm">Belum ada SPK terbaru.</p>
                </div>
            @endif
        </div>

        <div class="bg-gray-900 p-5 rounded-lg shadow-sm border border-gray-700 flex flex-col h-full">
            <div class="flex items-center justify-between pb-3 mb-3 border-b border-gray-700">
                <h2 class="text-base font-semibold text-gray-100 flex items-center">
                    <i class="fas fa-minus-circle text-gray-100 mr-2"></i>
                    Struk Pengeluaran Terbaru
                </h2>
                <span class="text-xs px-2 py-1 bg-blue-900 text-blue-300 rounded-full font-medium">
                    <i class="fas fa-history text-xs"></i> Terkini
                </span>
            </div>

            @if (isset($latestPengeluaranStruk) && $latestPengeluaranStruk)
                @php
                    $itemsKeluar = is_string($latestPengeluaranStruk->daftar_barang ?? null) ? json_decode($latestPengeluaranStruk->daftar_barang, true) : ($latestPengeluaranStruk->daftar_barang ?? []);
                    $firstItemKeluar = collect($itemsKeluar)->first();
                    $namaBarangKeluar = $firstItemKeluar['nama'] ?? '-';
                @endphp
                <dl class="text-sm text-gray-300 space-y-2">
                    <div class="grid grid-cols-3 gap-2">
                        <dt class="text-gray-400 font-medium col-span-1">Nama Spk</dt>
                        <dd class="text-gray-100 truncate col-span-2">: {{ $latestPengeluaranStruk->nama_toko ?? '-' }}</dd>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <dt class="text-gray-400 font-medium col-span-1">No Spk</dt>
                        <dd class="font-mono text-gray-100 truncate col-span-2">: {{ $latestPengeluaranStruk->nomor_struk }}</dd>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <dt class="text-gray-400 font-medium col-span-1">Nama Barang</dt>
                        <dd class="text-gray-100 truncate col-span-2">: {{ $barangMaster[$namaBarangKeluar] ?? $namaBarangKeluar }}</dd>
                    </div>
                </dl>
            @else
                <div class="text-center py-4 flex-grow flex flex-col justify-center items-center">
                    <i class="fas fa-file-export fa-2x text-gray-700"></i>
                    <p class="mt-2 text-gray-400 text-sm">Belum ada SPK pengeluaran terbaru.</p>
                </div>
            @endif
        </div>
    </div>

    <div class="bg-gray-900 p-5 rounded-lg shadow-sm border border-gray-700">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 mb-4 border-b border-gray-700">
            <h2 class="text-lg font-semibold text-gray-100 flex items-center mb-3 sm:mb-0">
                <i class="fas fa-boxes text-gray-100 mr-2"></i>
                Daftar Pemasukan Barang
            </h2>
            <form action="{{ route('dashboard') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-2 w-full sm:w-auto">
                <input type="hidden" name="time_period" value="{{ request('time_period') }}">
                <input type="hidden" name="bulan" value="{{ request('bulan') }}">
                <input type="hidden" name="tahun" value="{{ request('tahun') }}">
                <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                <input type="hidden" name="end_date" value="{{ request('end_date') }}">

                <input type="text" name="search" value="{{ request('search') }}"
                           class="border border-gray-600 bg-gray-800 text-white px-3 py-1.5 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm w-full sm:w-auto"
                           placeholder="Cari nama barang...">
                
                <select name="status_pemasukan"
                            class="border border-gray-600 bg-gray-800 text-white px-3 py-1.5 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 w-full sm:w-auto">
                    <option value="">Semua Status</option>
                    <option value="progress" {{ request('status_pemasukan') == 'progress' ? 'selected' : '' }}>Progress</option>
                    <option value="completed" {{ request('status_pemasukan') == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>

                <select name="sort"
                            class="border border-gray-600 bg-gray-800 text-white px-3 py-1.5 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 w-full sm:w-auto">
                    <option value="">Sortir berdasarkan</option>
                    <option value="tanggal_desc" {{ request('sort') == 'tanggal_desc' ? 'selected' : '' }}>Tanggal Terbaru</option>
                    <option value="tanggal_asc" {{ request('sort') == 'tanggal_asc' ? 'selected' : '' }}>Tanggal Terlama</option>
                    <option value="nama_asc" {{ request('sort') == 'nama_asc' ? 'selected' : '' }}>Nama A-Z</option>
                    <option value="nama_desc" {{ request('sort') == 'nama_desc' ? 'selected' : '' }}>Nama Z-A</option>
                </select>
                <button type="submit" class="bg-blue-600 text-white px-3 py-1.5 rounded-md hover:bg-blue-700 text-sm w-full sm:w-auto transition-colors duration-150">
                    Cari
                </button>
            </form>
        </div>

        <div class="block md:hidden space-y-3">
            @forelse ($pemasukans as $pemasukan)
                @php
                    $items = is_string($pemasukan->items) ? json_decode($pemasukan->items, true) : ($pemasukan->items ?? []);
                    $firstItem = collect($items)->first();
                    $namaBarang = $firstItem['nama'] ?? '-';
                    $totalJumlah = collect($items)->sum('jumlah');
                    $status = $pemasukan->status ?? 'progress';
                    $statusColors = ['completed' => 'bg-green-900 text-green-300', 'progress' => 'bg-yellow-900 text-yellow-300'];
                    $statusTexts = ['completed' => 'Completed', 'progress' => 'Progress'];
                    $colorClass = $statusColors[$status] ?? $statusColors['progress'];
                    $statusText = $statusTexts[$status] ?? 'Progress';
                @endphp
                <div class="bg-gray-800 p-4 rounded-lg border border-gray-700">
                    <div class="flex justify-between items-start mb-2">
                        <div class="font-semibold text-gray-100 truncate pr-2">
                            {{ $barangMaster[$namaBarang] ?? $namaBarang }}
                        </div>
                        <span class="px-2 py-0.5 text-xs font-medium rounded-full {{ $colorClass }} flex-shrink-0">
                            {{ $statusText }}
                        </span>
                    </div>
                    <div class="text-sm text-gray-400 space-y-1">
                        <p><strong>Jumlah:</strong> <span class="text-gray-200">{{ $totalJumlah ?? '0' }}</span></p>
                        <p><strong>No Struk:</strong> <span class="text-gray-200">{{ $pemasukan->nomor_struk ?? '-' }}</span></p>
                        <p><strong>Tanggal:</strong> <span class="text-gray-200">{{ $pemasukan->tanggal_struk ? \Carbon\Carbon::parse($pemasukan->tanggal_struk)->format('d M Y') : '-' }}</span></p>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-2x text-gray-700"></i>
                    <p class="mt-2 text-gray-400 text-sm">Tidak ada data pemasukan.</p>
                </div>
            @endforelse
        </div>

        <div class="hidden md:block border border-gray-700 rounded-lg overflow-x-auto">
            <table class="w-full min-w-full text-sm">
                <thead class="bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-400 uppercase tracking-wider">No</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-400 uppercase tracking-wider">Nama Barang</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-400 uppercase tracking-wider">Jumlah</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-400 uppercase tracking-wider">Nomor Struk</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-400 uppercase tracking-wider">Tanggal Masuk</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-400 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse ($pemasukans as $pemasukan)
                        @php
                            $items = is_string($pemasukan->items) ? json_decode($pemasukan->items, true) : ($pemasukan->items ?? []);
                            $firstItem = collect($items)->first();
                            $namaBarang = $firstItem['nama'] ?? '-';
                            $totalJumlah = collect($items)->sum('jumlah');
                            $status = $pemasukan->status ?? 'progress';
                            $statusColors = ['completed' => 'bg-green-900 text-green-300', 'progress' => 'bg-yellow-900 text-yellow-300'];
                            $statusTexts = ['completed' => 'Completed', 'progress' => 'Progress'];
                            $colorClass = $statusColors[$status] ?? $statusColors['progress'];
                            $statusText = $statusTexts[$status] ?? 'Progress';
                        @endphp
                        <tr class="odd:bg-gray-900 even:bg-gray-950 hover:bg-gray-800 transition-colors duration-150">
                            <td class="px-4 py-3 whitespace-nowrap text-gray-400">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3 font-medium text-gray-100">{{ $barangMaster[$namaBarang] ?? $namaBarang }}</td>
                            <td class="px-4 py-3 text-gray-300">{{ $totalJumlah ?? '0' }}</td>
                            <td class="px-4 py-3 text-gray-300">{{ $pemasukan->nomor_struk ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-300">
                                {{ $pemasukan->tanggal_struk ? \Carbon\Carbon::parse($pemasukan->tanggal_struk)->format('d M Y') : '-' }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $colorClass }}">
                                    {{ $statusText }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-5 text-center text-gray-400 bg-gray-900">
                                Tidak ada data pemasukan ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-5">
            {{ $pemasukans->appends(request()->except('page'))->links('vendor.pagination.custom') }}
        </div>
    </div>

    <div class="bg-gray-900 p-5 rounded-lg shadow-sm border border-gray-700">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 mb-4 border-b border-gray-700">
            <h2 class="text-lg font-semibold text-gray-100 flex items-center mb-3 sm:mb-0">
                <i class="fas fa-box-open text-gray-100 mr-2"></i>
                Daftar Pengeluaran Barang
            </h2>
            <form action="{{ route('dashboard') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-2 w-full sm:w-auto">
                <input type="hidden" name="time_period" value="{{ request('time_period') }}">
                <input type="hidden" name="bulan" value="{{ request('bulan') }}">
                <input type="hidden" name="tahun" value="{{ request('tahun') }}">
                <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                <input type="hidden" name="end_date" value="{{ request('end_date') }}">

                <input type="text" name="search_pengeluaran" value="{{ request('search_pengeluaran') }}"
                           class="border border-gray-600 bg-gray-800 text-white px-3 py-1.5 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm w-full sm:w-auto"
                           placeholder="Cari nama barang...">

                <select name="status_pengeluaran"
                            class="border border-gray-600 bg-gray-800 text-white px-3 py-1.5 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 w-full sm:w-auto">
                    <option value="">Semua Status</option>
                    <option value="progress" {{ request('status_pengeluaran') == 'progress' ? 'selected' : '' }}>Progress</option>
                    <option value="completed" {{ request('status_pengeluaran') == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>

                <select name="sort_pengeluaran"
                            class="border border-gray-600 bg-gray-800 text-white px-3 py-1.5 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 w-full sm:w-auto">
                    <option value="">Sortir berdasarkan</option>
                    <option value="tanggal_desc" {{ request('sort_pengeluaran') == 'tanggal_desc' ? 'selected' : '' }}>Tanggal Terbaru</option>
                    <option value="tanggal_asc" {{ request('sort_pengeluaran') == 'tanggal_asc' ? 'selected' : '' }}>Tanggal Terlama</option>
                    <option value="nama_asc" {{ request('sort_pengeluaran') == 'nama_asc' ? 'selected' : '' }}>Nama A-Z</option>
                    <option value="nama_desc" {{ request('sort_pengeluaran') == 'nama_desc' ? 'selected' : '' }}>Nama Z-A</option>
                </select>
                <button type="submit" class="bg-blue-600 text-white px-3 py-1.5 rounded-md hover:bg-blue-700 text-sm w-full sm:w-auto transition-colors duration-150">
                    Cari
                </button>
            </form>
        </div>

        <div class="block md:hidden space-y-3">
            @forelse ($pengeluarans as $pengeluaran)
                @php
                    $items = is_string($pengeluaran->daftar_barang) ? json_decode($pengeluaran->daftar_barang, true) : $pengeluaran->daftar_barang;
                    $firstItem = collect($items)->first();
                    $namaBarang = $firstItem['nama'] ?? '-';
                    $totalJumlah = collect($items)->sum('jumlah');
                    $status = $pengeluaran->status ?? 'progress';
                    $statusColors = ['completed' => 'bg-green-900 text-green-300', 'progress' => 'bg-yellow-900 text-yellow-300'];
                    $statusTexts = ['completed' => 'Completed', 'progress' => 'Progress'];
                    $colorClass = $statusColors[$status] ?? $statusColors['progress'];
                    $statusText = $statusTexts[$status] ?? 'Progress';
                @endphp
                <div class="bg-gray-800 p-4 rounded-lg border border-gray-700">
                    <div class="flex justify-between items-start mb-2">
                        <div class="font-semibold text-gray-100 truncate pr-2">
                            {{ $barangMaster[$namaBarang] ?? $namaBarang }}
                        </div>
                        <span class="px-2 py-0.5 text-xs font-medium rounded-full {{ $colorClass }} flex-shrink-0">
                            {{ $statusText }}
                        </span>
                    </div>
                    <div class="text-sm text-gray-400 space-y-1">
                        <p><strong>Pegawai:</strong> <span class="text-gray-200">{{ $pengeluaran->pegawai->nama ?? '-' }}</span></p>
                        <p><strong>Jumlah:</strong> <span class="text-gray-200">{{ $totalJumlah ?? '0' }}</span></p>
                        <p><strong>No SPK:</strong> <span class="text-gray-200">{{ $pengeluaran->nomor_struk ?? '-' }}</span></p>
                        <p><strong>Tanggal:</strong> <span class="text-gray-200">{{ $pengeluaran->tanggal ? \Carbon\Carbon::parse($pengeluaran->tanggal)->format('d M Y') : '-' }}</span></p>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="fas fa-box-open fa-2x text-gray-700"></i>
                    <p class="mt-2 text-gray-400 text-sm">Tidak ada data pengeluaran.</p>
                </div>
            @endforelse
        </div>

        <div class="hidden md:block border border-gray-700 rounded-lg overflow-x-auto">
            <table class="w-full min-w-full text-sm">
                <thead class="bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-400 uppercase tracking-wider">No</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-400 uppercase tracking-wider">Pegawai</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-400 uppercase tracking-wider">Nomor SPK</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-400 uppercase tracking-wider">Nama Barang</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-400 uppercase tracking-wider">Jumlah</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-400 uppercase tracking-wider">Tanggal Keluar</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-400 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse ($pengeluarans as $pengeluaran)
                        @php
                            $items = is_string($pengeluaran->daftar_barang) ? json_decode($pengeluaran->daftar_barang, true) : $pengeluaran->daftar_barang;
                            $firstItem = collect($items)->first();
                            $namaBarang = $firstItem['nama'] ?? '-';
                            $totalJumlah = collect($items)->sum('jumlah');
                            $status = $pengeluaran->status ?? 'progress';
                            $statusColors = ['completed' => 'bg-green-900 text-green-300', 'progress' => 'bg-yellow-900 text-yellow-300'];
                            $statusTexts = ['completed' => 'Completed', 'progress' => 'Progress'];
                            $colorClass = $statusColors[$status] ?? $statusColors['progress'];
                            $statusText = $statusTexts[$status] ?? 'Progress';
                        @endphp
                        <tr class="odd:bg-gray-900 even:bg-gray-950 hover:bg-gray-800 transition-colors duration-150">
                            <td class="px-4 py-3 whitespace-nowrap text-gray-400">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3 font-medium text-gray-100">{{ $pengeluaran->pegawai->nama ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-300">{{ $pengeluaran->nomor_struk ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-300">{{ $barangMaster[$namaBarang] ?? $namaBarang }}</td>
                            <td class="px-4 py-3 text-gray-300">{{ $totalJumlah ?? '0' }}</td>
                            <td class="px-4 py-3 text-gray-300">
                                {{ $pengeluaran->tanggal ? \Carbon\Carbon::parse($pengeluaran->tanggal)->format('d M Y') : '-' }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $colorClass }}">
                                    {{ $statusText }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-5 text-center text-gray-400 bg-gray-900">
                                Tidak ada data pengeluaran ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-5">
            {{ $pengeluarans->appends(request()->except('page'))->links('vendor.pagination.custom') }}
        </div>
    </div>

</div> 
@endsection

@push('styles')
<style>
    /* Style pagination kustom */
    .pagination { 
        display: flex; 
        justify-content: center; 
        list-style: none; 
        padding: 0; 
        margin: 1rem 0 0.5rem 0; 
    }
    .pagination li { 
        margin: 0 0.125rem; 
    }
    .pagination li a, .pagination li span { 
        display: inline-flex; 
        align-items: center; 
        justify-content: center; 
        width: 2.25rem; 
        height: 2.25rem; 
        border-radius: 0.375rem; 
        font-size: 0.875rem; 
        font-weight: 500; 
        transition: all 0.2s ease; 
        border: 1px solid #374151; 
    }
    .pagination li a { 
        color: #9ca3af; 
        background-color: #1f2937; 
    }
    .pagination li a:hover { 
        background-color: #374151; 
        color: #f9fafb; 
    }
    .pagination li.disabled span { 
        background-color: #1f2937; 
        border-color: #374151; 
        color: #6b7280; 
        cursor: not-allowed; 
    }
    .pagination li.active span { 
        background-color: #2563eb; 
        color: white; 
        border-color: #2563eb; 
    }
    .showing-results { 
        color: #9ca3af; 
        font-size: 0.75rem; 
        margin-top: 0.5rem; 
        text-align: center; 
    }
    
    /* Style tabel kustom */
    table { 
        width: 100%; 
        border-collapse: collapse; 
        table-layout: auto; 
    }
    th, td { 
        overflow: hidden; 
        text-overflow: ellipsis; 
        padding: 12px 16px; 
        font-size: 0.875rem; 
        vertical-align: middle; 
    }
    thead th { 
        background-color: #1f2937; 
        padding-top: 10px; 
        padding-bottom: 10px; 
        color: #9ca3af; 
    }
    
    /* Zebra Striping - Baris ganjil dan genap */
    tbody tr:nth-child(odd) {
        background-color: #111827; /* bg-gray-900 */
    }
    tbody tr:nth-child(even) {
        background-color: #030712; /* bg-gray-950 */
    }
    tbody tr:hover { 
        background-color: #1f2937 !important; /* bg-gray-800 */
    }
    
    @media (max-width: 767px) {
        .pemasukan-card .text-sm, .pengeluaran-card .text-sm { 
            font-size: 0.8rem; 
        }
        .pemasukan-card strong, .pengeluaran-card strong { 
            font-weight: 600; 
            color: #d1d5db; 
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const timePeriodSelect = document.getElementById('time-period-select');
        const bulanTahunFilter = document.getElementById('bulan-tahun-filter');
        const dateRangeFilter = document.getElementById('date-range-filter');

        function togglePeriodFilters() {
            const selectedPeriod = timePeriodSelect.value;

            // Sembunyikan semua filter dulu
            bulanTahunFilter.classList.add('hidden');
            bulanTahunFilter.classList.remove('flex');
            dateRangeFilter.classList.add('hidden');
            dateRangeFilter.classList.remove('flex', 'flex-col', 'sm:flex-row');

            // Tampilkan filter yang sesuai
            if (selectedPeriod === 'monthly') {
                bulanTahunFilter.classList.remove('hidden');
                bulanTahunFilter.classList.add('flex');
            } else if (selectedPeriod === 'range') {
                dateRangeFilter.classList.remove('hidden');
                dateRangeFilter.classList.add('flex', 'flex-col', 'sm:flex-row');
            }
        }

        // Jalankan saat halaman dimuat
        togglePeriodFilters();

        // Jalankan saat nilai select berubah
        timePeriodSelect.addEventListener('change', togglePeriodFilters);
    });
</script>
@endpush