@extends('layouts.app')

@section('content')
{{-- Wrapper layout yang salah (min-h-screen, dll) telah dihapus --}}

    @if (session('success'))
        {{-- Script ini akan memicu notifikasi di layout app.blade.php --}}
    @endif

    <form id="bulkDeleteForm" method="POST" action="{{ route('struks.bulk-delete') }}">
        @csrf
        @method('DELETE')
        <input type="hidden" name="selected_ids" id="selectedIds">
    </form>

    <div class="mb-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-white">Manajemen Struk</h1>
            <p class="text-gray-400">Kelola dan atur semua struk pemasukan</p>
        </div>
            <div class="flex flex-wrap gap-2 w-full sm:w-auto items-center">
                @php
                $isPemasukan = request()->routeIs('struks.index');
                @endphp
                <div class="flex items-center gap-2 mt-2 sm:mt-0">
                    <div class="relative inline-block w-12 align-middle select-none">
                        <input type="checkbox" id="toggle-struk" class="hidden" {{ $isPemasukan ? '' : 'checked' }}>
                        <label for="toggle-struk" title="Lihat Data Pengeluaran" class="block h-6 rounded-full cursor-pointer transition-colors duration-300 ease-in-out {{ $isPemasukan ? 'bg-blue-600' : 'bg-gray-700' }}">
                            <span class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow-md transform transition-transform duration-300 ease-in-out {{ $isPemasukan ? '' : 'translate-x-6' }}"></span>
                        </label>
                    </div>
                </div>

                <div class="relative group">
                    <button class="flex items-center gap-2 px-4 py-2 bg-gray-800 text-gray-300 rounded-lg border border-gray-700 hover:bg-gray-700 transition-colors shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Ekspor
                    </button>
                    <div class="absolute right-0 mt-2 w-40 bg-gray-800 rounded-md shadow-lg z-10 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 origin-top-right border border-gray-700">
                        <a href="{{ route('struks.export.excel') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white">Format Excel</a>
                        <a href="{{ route('struks.export.csv') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white">Format CSV</a>
                    </div>
                </div>
                
                <div id="bulkActionsContainer" class="hidden flex items-center gap-2 bg-red-900 bg-opacity-50 rounded-lg p-1 border border-red-700">
                    <span id="selectedCount" class="px-2 py-1 bg-red-800 text-red-200 text-xs font-medium rounded-md">0 dipilih</span>
                    <button onclick="confirmBulkDelete()" class="flex items-center gap-2 px-3 py-1.5 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Hapus
                    </button>
                    <button onclick="clearSelection()" class="p-1 text-red-400 hover:text-red-300 rounded-full hover:bg-red-800 transition-colors" title="Batal">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-gray-900 rounded-xl shadow-sm border border-gray-700 overflow-hidden transition-all duration-300 hover:shadow-lg">
        <div class="px-6 py-4 border-b border-gray-700">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-gray-700 rounded-lg">
                        <svg class="w-6 h-6 text-gray-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002 2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-white">Data Struk Pemasukan</h3>
                        <p class="text-sm text-gray-400">{{ $struks->total() }} struk ditemukan</p>
                    </div>
                </div>
                <div class="relative w-64">
                    <input type="text" name="search" id="searchInput"
                        class="pl-10 pr-4 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-gray-500 w-full transition-all bg-gray-800 text-sm text-white"
                        value="{{ request('search') }}" autocomplete="off"
                        placeholder="Cari Nama Toko atau No. Struk">
                    <button type="button" class="absolute left-3 top-2.5 text-gray-500 pointer-events-none">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                    @if (request('search'))
                    <button id="clearSearch" class="absolute right-3 top-2.5 text-gray-500 hover:text-gray-300"
                        title="Bersihkan pencarian">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    @endif
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-800 border-b border-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left font-medium text-gray-400 uppercase tracking-wider w-10">
                            <input type="checkbox" id="selectAll" class="rounded border-gray-600 bg-gray-900 text-blue-500 focus:ring-blue-500">
                        </th>
                        <th class="px-6 py-3 text-left font-medium text-gray-400 uppercase tracking-wider">No.</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-400 uppercase tracking-wider">Nama Toko</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-400 uppercase tracking-wider">No. Struk</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-400 uppercase tracking-wider">Masuk</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-400 uppercase tracking-wider">Barang</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-400 uppercase tracking-wider">Jumlah</th>
                        <th class="px-6 py-3 text-right font-medium text-gray-400 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-center font-medium text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-center font-medium text-gray-400 uppercase tracking-wider">Foto Struk</th>
                        <th class="px-6 py-3 text-center font-medium text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-700">
                    @forelse ($struks as $index => $struk)
                    @php
                    $items = is_string($struk->items) ? json_decode($struk->items, true) : $struk->items;
                    if (!is_array($items)) $items = [];
                    $totalHarga = collect($items)->sum(fn($item) => ($item['jumlah'] ?? 0) * ($item['harga'] ?? 0) * 1000);
                    @endphp

                    <tr class="even:bg-gray-800 hover:bg-gray-700 transition-colors animate-fadeIn">
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <input type="checkbox" name="selected_ids[]" value="{{ $struk->id }}" class="row-checkbox rounded border-gray-600 bg-gray-900 text-blue-500 focus:ring-blue-500">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-100">{{ $struks->firstItem() + $index }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-medium text-gray-100">{{ $struk->nama_toko }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-gray-100">{{ $struk->nomor_struk }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-gray-100">{{ date('d M Y', strtotime($struk->tanggal_struk)) }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="space-y-2 overflow-x-auto">
                                @foreach ($items as $item)
                                <div class="flex items-start whitespace-nowrap">
                                    <span class="inline-block w-2 h-2 rounded-full bg-gray-500 mt-2 mr-2 flex-shrink-0"></span>
                                    <span class="text-gray-300">
                                        {{ $item['nama_barang'] ?? $barangList[$item['nama']]?->nama_barang ?? $item['nama'] }}
                                    </span>
                                </div>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="max-w-xs space-y-2">
                                @foreach ($items as $item)
                                <div class="text-gray-400 text-sm">
                                    <span class="whitespace-nowrap">x{{ $item['jumlah'] ?? '-' }}</span>
                                </div>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right font-semibold text-white">
                            {{ 'Rp' . number_format($totalHarga, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $struk->status == 'progress' ? 'bg-yellow-900 text-yellow-300' : 'bg-blue-900 text-blue-300' }}">
                                {{ ucfirst($struk->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if ($struk->foto_struk)
                            <button onclick="openModal('{{ url('storage/struk_foto/' . $struk->foto_struk) }}')" class="text-blue-500 hover:text-blue-400">
                                <svg class="w-6 h-6 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span class="text-xs text-gray-400">Lihat</span>
                            </button>
                            @else
                            <span class="text-gray-500 italic text-xs">Tidak ada gambar</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="flex justify-center space-x-2">
                                <a href="{{ route('struks.show', $struk->id) }}" class="text-gray-500 hover:text-gray-300 p-1 rounded-full hover:bg-gray-700 transition-colors" title="Lihat Detail">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                <button type="button" onclick="openEditModal({{ $struk->id }})" class="text-gray-500 hover:text-blue-858 p-1 rounded-full hover:bg-gray-700 transition-colors" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                <form action="{{ route('struks.destroy', $struk->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus struk ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="openDeleteModal('{{ route('struks.destroy', $struk->id) }}')" class="text-gray-500 hover:text-red-500 p-1 rounded-full hover:bg-red-900 hover:bg-opacity-30 transition-colors" title="Hapus">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr class="animate-fadeIn">
                        <td colspan="11" class="px-6 py-4 text-center text-gray-500">Tidak ada struk ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($struks->hasPages())
        <div class="px-6 py-4 border-t border-gray-700 flex items-center justify-between">
            <div class="text-sm text-gray-400">
                Menampilkan {{ $struks->firstItem() }} sampai {{ $struks->lastItem() }} dari {{ $struks->total() }} hasil
            </div>
            <div class="flex space-x-1">
                @if ($struks->onFirstPage())
                <span class="px-3 py-1 rounded-lg border border-gray-700 text-gray-600 cursor-not-allowed">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </span>
                @else
                <a href="{{ $struks->previousPageUrl() }}" class="px-3 py-1 rounded-lg border border-gray-700 text-gray-300 hover:bg-gray-800 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                @endif

                @foreach ($struks->getUrlRange(1, $struks->lastPage()) as $page => $url)
                @if ($page == $struks->currentPage())
                <span class="px-3 py-1 rounded-lg bg-blue-600 text-white">{{ $page }}</span>
                @else
                <a href="{{ $url }}" class="px-3 py-1 rounded-lg border border-gray-700 text-gray-300 hover:bg-gray-800 transition-colors">{{ $page }}</a>
                @endif
                @endforeach

                @if ($struks->hasMorePages())
                <a href="{{ $struks->nextPageUrl() }}" class="px-3 py-1 rounded-lg border border-gray-700 text-gray-300 hover:bg-gray-800 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
                @else
                <span class="px-3 py-1 rounded-lg border border-gray-700 text-gray-600 cursor-not-allowed">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </span>
                @endif
            </div>
        </div>
        @endif
    </div>

    {{-- Modal Lihat Gambar --}}
    <div id="imageModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-80 flex items-center justify-center">
        <div class="relative">
            <img id="modalImage" src="" class="max-h-[80vh] rounded shadow-lg select-none">
            <button onclick="closeModal()" class="absolute top-2 right-2 text-white hover:text-gray-300 transition duration-200">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    {{-- Modal Edit Struk --}}
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-75 hidden z-50 flex items-center justify-center p-4 overflow-y-auto">
        <div class="relative w-full max-w-4xl mx-auto my-auto">
            <div class="bg-gray-800 rounded-lg shadow-xl border border-gray-700 flex flex-col max-h-[90vh]">
                <div class="bg-gray-800 px-6 py-4 border-b border-gray-700 rounded-t-lg flex-shrink-0">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-white">Edit Struk</h3>
                            <p class="text-sm text-gray-400">Ubah detail struk, termasuk item dan status</p>
                        </div>
                        <button type="button" onclick="closeEditModal()" class="text-gray-500 hover:text-gray-300 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <form method="POST" action="" id="editForm" class="flex-1 flex flex-col min-h-0">
                    @csrf
                    @method('PUT')

                    <div class="px-6 py-4 flex-1 overflow-y-auto">
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-300">Nama Toko</label>
                                <input type="text" name="nama_toko" id="editNamaToko" class="w-full border border-gray-600 bg-gray-700 text-white rounded-lg px-3 py-2 mt-1 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300">Nomor Struk</label>
                                <input type="text" name="nomor_struk" id="editNomorStruk" class="w-full border border-gray-600 bg-gray-700 text-white rounded-lg px-3 py-2 mt-1 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300">Tanggal Masuk</label>
                                <input type="date" name="tanggal_struk" id="editTanggalStruk" class="w-full border border-gray-600 bg-gray-700 text-white rounded-lg px-3 py-2 mt-1 focus:ring-2 focus:ring-gray-500 focus:border-gray-500" style="color-scheme: dark;">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300">Status</label>
                                <select name="status" id="editStatus" class="w-full border border-gray-600 bg-gray-700 text-white rounded-lg px-3 py-2 mt-1 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                                    <option value="progress">Progress</option>
                                    <option value="completed">Completed</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-12 gap-4 pb-3 mb-4 border-b border-gray-700">
                            <div class="col-span-5"><label class="block text-sm font-medium text-gray-300 text-center">Nama Barang</label></div>
                            <div class="col-span-2"><label class="block text-sm font-medium text-gray-300 text-center">Jumlah</label></div>
                            <div class="col-span-3"><label class="block text-sm font-medium text-gray-300 text-right">Harga Satuan</label></div>
                            <div class="col-span-2"><label class="block text-sm font-medium text-gray-300 text-center">Aksi</label></div>
                        </div>

                        <div id="modalItemsContainer" class="space-y-3 mb-6"></div>

                        <div class="flex justify-end text-gray-200 font-medium mb-4">
                            <span id="modalTotalPrice">Total: Rp0</span>
                        </div>

                        <div class="border-2 border-dashed border-gray-600 rounded-lg p-4 bg-gray-900 mb-4">
                            <div class="flex items-center gap-2 mb-3">
                                <svg class="w-5 h-5 text-gray-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                <span class="text-sm font-medium text-gray-100">Tambah Item Baru</span>
                            </div>
                            <div class="grid grid-cols-12 gap-4 items-end">
                                <div class="col-span-5 relative">
                                    <input type="text" class="item-search w-full border border-gray-600 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-gray-500 focus:border-gray-500 bg-gray-700 text-white" id="modalNewItemNama" placeholder="Cari barang..." autocomplete="off" readonly onclick="toggleBarangDropdown()">
                                    <div class="absolute z-10 w-full mt-1 bg-gray-700 border border-gray-600 rounded-lg shadow-lg max-h-60 overflow-y-auto hidden" id="barangDropdown">
                                        <div class="sticky top-0 bg-gray-700 p-2 border-b border-gray-600">
                                            <input type="text" id="filterBarang" class="w-full px-3 py-2 border border-gray-600 rounded-md focus:ring-2 focus:ring-gray-500 focus:border-gray-500 bg-gray-800 text-white" placeholder="Filter barang...">
                                        </div>
                                        <div class="divide-y divide-gray-600" id="barangListContainer">
                                            @foreach($barangList as $barang)
                                            <div class="px-4 py-3 hover:bg-gray-600 cursor-pointer flex justify-between items-center barang-item"
                                                 data-kode="{{ $barang->kode_barang }}"
                                                 data-nama="{{ $barang->nama_barang }}"
                                                 data-harga="{{ $barang->harga }}">
                                                <span>
                                                    <span class="font-mono text-xs text-gray-400">{{ $barang->kode_barang }}</span>
                                                    <span class="ml-2 text-gray-200">{{ $barang->nama_barang }}</span>
                                                </span>
                                                @if($barang->harga > 0)
                                                <span class="text-xs text-gray-400">{{ 'Rp' . number_format($barang->harga * 1000, 0, ',', '.') }}</span>
                                                @endif
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-span-2">
                                    <input type="number" class="w-full border border-gray-600 bg-gray-700 text-white rounded-lg px-3 py-2.5 text-center focus:ring-2 focus:ring-gray-500 focus:border-gray-500" placeholder="Jumlah" id="modalNewItemJumlah" min="1">
                                </div>
                                <div class="col-span-3">
                                    <div class="relative">
                                        <span class="absolute left-3 top-2.5 text-gray-400 text-sm">Rp</span>
                                        <input type="text" class="w-full border border-gray-600 bg-gray-700 text-white rounded-lg pl-8 pr-3 py-2.5 text-right focus:ring-2 focus:ring-gray-500 focus:border-gray-500 harga-input-new"
                                               id="modalNewItemHarga" placeholder="Harga" data-value="0">
                                    </div>
                                </div>
                                <div class="col-span-2">
                                    <button type="button" onclick="addNewItemToModal()" class="w-full flex items-center justify-center gap-1 px-3 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                        Tambah
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-800 px-6 py-4 border-t border-gray-700 rounded-b-lg flex-shrink-0">
                        <div class="flex justify-end space-x-3">
                            <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-sm font-medium text-gray-300 bg-gray-700 rounded-lg hover:bg-gray-600 transition-colors">Batal</button>
                            <button locatie="button" onclick="submitEditForm()" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors">Simpan Perubahan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Konfirmasi Hapus --}}
    <div id="deleteModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-80 flex items-center justify-center">
        <div class="bg-gray-800 rounded-lg shadow-xl border border-gray-700 w-full max-w-md p-6">
            <div class="flex flex-col items-center">
                <svg class="w-16 h-16 text-red-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <h3 class="text-xl font-semibold text-white mb-2">Konfirmasi Hapus</h3>
                <p class="text-gray-400 text-center mb-6">Apakah Anda yakin ingin menghapus struk ini? Tindakan ini tidak dapat dibatalkan.</p>
                <form id="deleteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <div class="flex justify-center space-x-4">
                        <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 text-sm font-medium text-gray-300 bg-gray-700 rounded-lg hover:bg-gray-600 transition-colors">Batal</button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<script>
    // ==================== FORMAT RUPIAH OTOMATIS ====================
    function formatRupiahInput(input) {
        let value = input.value.replace(/[^\d]/g, '');
        if (value === '') {
            input.dataset.value = '0';
            input.value = '';
            return;
        }
        const num = parseInt(value, 10);
        input.dataset.value = num.toString();
        const formatted = num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        input.value = formatted;
    }

    function formatRupiah(angka) {
        if (isNaN(angka) || angka === null) return 'Rp0';
        const formatted = angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        return 'Rp' + formatted;
    }

    // ==================== UPDATE TOTAL HARGA ====================
    function updateModalTotalPrice() {
        const container = document.getElementById('modalItemsContainer');
        let total = 0;
        container.querySelectorAll('.item-row').forEach(row => {
            const jumlah = parseFloat(row.querySelector('input[name="jumlah[]"]').value) || 0;
            const hargaNumeric = parseFloat(row.querySelector('.harga-display').dataset.value) || 0;
            total += jumlah * hargaNumeric;
        });
        document.getElementById('modalTotalPrice').textContent = `Total: ${formatRupiah(total)}`;

        let totalHargaInput = document.getElementById('editTotalHarga');
        if (!totalHargaInput) {
            totalHargaInput = document.createElement('input');
            totalHargaInput.type = 'hidden';
            totalHargaInput.name = 'total_harga';
            totalHargaInput.id = 'editTotalHarga';
            document.getElementById('editForm').appendChild(totalHargaInput);
        }
        totalHargaInput.value = total / 1000;
    }

    // ==================== TAMBAH ROW ITEM ====================
    function addItemRowToModal(item = {}, index = null) {
        const container = document.getElementById('modalItemsContainer');
        const barangList = @json($barangList);
        const namaBarang = barangList[item.nama]?.nama_barang || item.nama_barang || item.nama || '-';

        const hargaNumeric = (parseFloat(item.harga) || 0) * 1000;
        const hargaFormatted = hargaNumeric.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');

        const itemRow = document.createElement('div');
        itemRow.className = 'grid grid-cols-12 gap-4 items-center item-row p-3 bg-gray-700 rounded-lg border border-gray-600';
        itemRow.innerHTML = `
            <div class="col-span-5 flex flex-col">
                <input type="text" value="${namaBarang}" class="w-full border border-gray-600 rounded-lg px-3 py-2.5 bg-gray-600 font-semibold text-base text-white" readonly>
                <input type="hidden" name="nama[]" value="${item.nama || item.kode || ''}">
            </div>
            <div class="col-span-2">
                <input name="jumlah[]" type="number" value="${item.jumlah || ''}" min="1"
                       class="w-full border border-gray-600 rounded-lg px-3 py-2.5 text-center bg-gray-700 text-white focus:ring-2 focus:ring-gray-500">
            </div>
            <div class="col-span-3">
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-gray-400 text-sm">Rp</span>
                    <input type="text" class="harga-display w-full border border-gray-600 rounded-lg pl-8 pr-3 py-2.5 text-right bg-gray-700 text-white focus:ring-2 focus:ring-gray-500"
                           data-value="${hargaNumeric}" value="${hargaFormatted}" placeholder="0">
                    <input type="hidden" name="harga[]" class="harga-hidden">
                </div>
            </div>
            <div class="col-span-2 flex justify-center">
                <button type="button" onclick="removeItemFromModal(this)" class="text-red-500 hover:text-red-400 hover:bg-red-900 hover:bg-opacity-30 p-2 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </div>
        `;
        container.appendChild(itemRow);

        const jumlahInput = itemRow.querySelector('input[name="jumlah[]"]');
        const hargaDisplay = itemRow.querySelector('.harga-display');
        const hargaHidden = itemRow.querySelector('.harga-hidden');

        jumlahInput.addEventListener('input', updateModalTotalPrice);

        hargaDisplay.addEventListener('input', function () {
            formatRupiahInput(this);
            const numValue = parseFloat(this.dataset.value) || 0;
            hargaHidden.value = numValue / 1000;
            updateModalTotalPrice();
        });

        hargaHidden.value = hargaNumeric / 1000;
    }

    // ==================== TAMBAH ITEM BARU ====================
    function addNewItemToModal() {
        const namaInput = document.getElementById('modalNewItemNama');
        const jumlahInput = document.getElementById('modalNewItemJumlah');
        const hargaInput = document.getElementById('modalNewItemHarga');
        const kodeBarang = namaInput.dataset.kode || '';

        if (!kodeBarang || !jumlahInput.value.trim() || !hargaInput.dataset.value || hargaInput.dataset.value == '0') {
            Swal.fire({title: 'Data Tidak Lengkap', text: 'Pilih barang dan isi jumlah & harga.', icon: 'warning', background: '#1F2937', color: '#F9FAFB'});
            return;
        }

        addItemRowToModal({
            nama: KodBarang,
            nama_barang: namaInput.value,
            jumlah: jumlahInput.value,
            harga: parseFloat(hargaInput.dataset.value) / 1000
        });

        clearNewItemFields();
        updateModalTotalPrice();
    }

    function clearNewItemFields() {
        document.getElementById('modalNewItemNama').value = '';
        document.getElementById('modalNewItemNama').dataset.kode = '';
        document.getElementById('modalNewItemJumlah').value = '';
        document.getElementById('modalNewItemHarga').value = '';
        document.getElementById('modalNewItemHarga').dataset.value = '0';
    }

    // ==================== DROPDOWN BARANG ====================
    function initBarangDropdown() {
        const hargaInput = document.getElementById('modalNewItemHarga');

        document.querySelectorAll('.barang-item').forEach(item => {
            item.addEventListener('click', function () {
                document.getElementById('modalNewItemNama').value = this.dataset.nama;
                document.getElementById('modalNewItemNama').dataset.kode = this.dataset.kode;

                const hargaAsli = parseFloat(this.dataset.harga) || 0;
                const hargaDisplay = hargaAsli * 1000;
                hargaInput.value = hargaDisplay.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                hargaInput.dataset.value = hargaDisplay;

                document.getElementById('barangDropdown').classList.add('hidden');
                document.getElementById('modalNewItemJumlah').focus();
            });
        });

        hargaInput.addEventListener('input', function () {
            formatRupiahInput(this);
        });
    }

    // ==================== OPEN EDIT MODAL ====================
    let currentEditStrukId = null;
    window.strukItemsData = {
        @foreach($struks as $struk)
            {{ $struk->id }}: {!! json_encode($struk) !!},
        @endforeach
    };

    function openEditModal(strukId) {
        const modal = document.getElementById('editModal');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        document.getElementById('editForm').action = `/struks/${strukId}`;

        const struk = window.strukItemsData[strukId];
        document.getElementById('editNamaToko').value = struk.nama_toko;
        document.getElementById('editNomorStruk').value = struk.nomor_struk;
        document.getElementById('editTanggalStruk').value = struk.tanggal_struk;
        document.getElementById('editStatus').value = struk.status;

        let items = typeof struk.items === 'string' ? JSON.parse(struk.items) : struk.items;
        if (!Array.isArray(items)) items = [];

        const container = document.getElementById('modalItemsContainer');
        container.innerHTML = '';

        items.forEach((item, i) => addItemRowToModal(item, i));
        updateModalTotalPrice();
        initBarangDropdown();
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
        document.getElementById('modalItemsContainer').innerHTML = '';
        clearNewItemFields();
    }

    function removeItemFromModal(btn) {
        if (document.getElementById('modalItemsContainer').children.length <= 1) {
            Swal.fire({title: 'Tidak bisa hapus', text: 'Minimal harus ada satu item.', icon: 'error', background: '#1F2937', color: '#F9FAFB'});
            return;
        }
        btn.closest('.item-row').remove();
        updateModalTotalPrice();
    }

    function submitEditForm() {
        if (document.getElementById('modalItemsContainer').children.length === 0) {
            Swal.fire({title: 'Item kosong', text: 'Tambahkan minimal satu item.', icon: 'warning', background: '#1F2937', color: '#F9FAFB'});
            return;
        }
        document.getElementById('editForm').submit();
    }

    // ==================== LAINNYA (toggle, search, dll) ====================
    document.addEventListener('DOMContentLoaded', function() {
        const toggle = document.getElementById('toggle-struk');
        if (toggle) {
            toggle.addEventListener('change', function() {
                if (this.checked) window.location.href = "{{ route('pengeluarans.index') }}";
            });
        }
        initBarangDropdown();
    });

    function openModal(src) {
        document.getElementById('modalImage').src = src;
        document.getElementById('imageModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }
    function closeModal() {
        document.getElementById('imageModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
    function openDeleteModal(action) {
        document.getElementById('deleteForm').action = action;
        document.getElementById('deleteModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }
    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    // Klik di luar modal untuk tutup
    document.getElementById('editModal').addEventListener('click', e => { if (e.target === e.currentTarget) closeEditModal(); });
    document.getElementById('deleteModal').addEventListener('click', e => { if (e.target === e.currentTarget) closeDeleteModal(); });
    document.getElementById('imageModal').addEventListener('click', e => { if (e.target === e.currentTarget) closeModal(); });

    // ESC untuk tutup semua modal
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            closeModal();
            closeDeleteModal();
            closeEditModal();
        }
    });
</script>

<style>
    #barangDropdown { display: none; }
    #barangDropdown:not(.hidden) { display: block; }
    ::-webkit-scrollbar { width: 8px; height: 8px; }
    ::-webkit-scrollbar-track { background: #1f2937; }
    ::-webkit-scrollbar-thumb { background: #4b5563; border-radius: 4px; }
    ::-webkit-scrollbar-thumb:hover { background: #6b7280; }
</style>
@endsection