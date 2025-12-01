@extends('layouts.app')

@section('content')
@push('styles')
<style>
    /* Checkbox dark mode monokrom */
    .checkbox-style {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        width: 18px;
        height: 18px;
        border: 2px solid #4B5563; /* border-gray-600 */
        border-radius: 0.25rem;
        outline: none;
        cursor: pointer;
        position: relative;
        transition: all 0.2s;
        background-color: #1F2937; /* bg-gray-800 */
    }

    .checkbox-style:checked {
        background-color: #9CA3AF; /* bg-gray-400 */
        border-color: #9CA3AF; /* border-gray-400 */
    }

    .checkbox-style:checked::after {
        content: '✓';
        position: absolute;
        color: #111827; /* text-gray-900 */
        font-size: 12px;
        font-weight: 900;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    .animate-fadeIn {
        animation: fadeIn 0.3s ease-out forwards;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Scrollbar dark mode */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    ::-webkit-scrollbar-track {
        background: #1f2937; /* bg-gray-800 */
    }
    ::-webkit-scrollbar-thumb {
        background: #4B5563; /* bg-gray-600 */
        border-radius: 4px;
    }
    ::-webkit-scrollbar-thumb:hover {
        background: #6B7280; /* bg-gray-500 */
    }
</style>
@endpush

{{-- Wrapper layout yang salah (min-h-screen, dll) DIHAPUS --}}

    @if (session('success'))
        {{-- Script ini akan memicu notifikasi di layout app.blade.php --}}
    @endif

    <form id="bulkDeleteForm" method="POST" action="{{ route('pengeluarans.massDelete') }}">
        @csrf
        @method('DELETE')
        <input type="hidden" name="selected_ids" id="selectedIds">
    </form>

    <div class="mb-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">

            <div>
                <h1 class="text-2xl font-bold text-white">Manajemen Struk</h1>
                <p class="text-gray-400">Kelola dan atur semua struk pengeluaran</p>
            </div>
            <div class="flex flex-wrap gap-2 w-full sm:w-auto items-center">
                @php
                $isPemasukan = request()->routeIs('struks.index');
                @endphp

                <div class="flex items-center gap-2 mt-2 sm:mt-0">
                    <div class="relative inline-block w-12 align-middle select-none">
                        <input type="checkbox" id="toggle-pengeluaran" class="hidden"
                            {{ $isPemasukan ? '' : 'checked' }}>
                        <label for="toggle-pengeluaran" title="Lihat Data Pemasukan"
                            class="block h-6 rounded-full cursor-pointer transition-colors duration-300 ease-in-out
                            {{ !$isPemasukan ? 'bg-blue-600' : 'bg-gray-700' }}">
                            <span
                                class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow-md transform transition-transform duration-300 ease-in-out
                                {{ !$isPemasukan ? 'translate-x-6' : '' }}">
                            </span>
                        </label>
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const toggle = document.getElementById('toggle-pengeluaran');
                        if (toggle) {
                            toggle.addEventListener('change', function() {
                                if (!this.checked) {
                                    window.location.href = "{{ route('struks.index') }}";
                                }
                            });
                        }
                    });
                </script>

                <div class="relative group">
                    <button
                        class="flex items-center gap-2 px-4 py-2 bg-gray-800 text-gray-300 rounded-lg border border-gray-700 hover:bg-gray-700 transition-colors shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Ekspor
                    </button>
                    <div
                        class="absolute right-0 mt-2 w-40 bg-gray-800 rounded-md shadow-lg z-10 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 origin-top-right border border-gray-700">
                        <a href="{{ route('struks.export.excel') }}" {{-- Asumsi route-nya sama, ganti jika beda --}}
                            class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white">Format
                            Excel</a>
                        <a href="{{ route('struks.export.csv') }}" {{-- Asumsi route-nya sama, ganti jika beda --}}
                            class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white">Format
                            CSV</a>
                    </div>
                </div>
                <div id="bulkActionsContainer"
                    class="hidden flex items-center gap-2 bg-red-900 bg-opacity-50 rounded-lg p-1 border border-red-700">
                    <span id="selectedCount"
                        class="px-2 py-1 bg-red-800 text-red-200 text-xs font-medium rounded-md">0 dipilih</span>
                    <button onclick="confirmBulkDelete()"
                        class="flex items-center gap-2 px-3 py-1.5 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                            </path>
                        </svg>
                        Hapus
                    </button>
                    <button onclick="clearSelection()"
                        class="p-1 text-red-400 hover:text-red-300 rounded-full hover:bg-red-800 transition-colors"
                        title="Batal">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
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
                        <svg class="w-6 h-6 text-gray-100" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002 2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-white">Data Struk Pengeluaran</h3>
                        <p class="text-sm text-gray-400">{{ $pengeluarans->total() }} struk ditemukan</p>
                    </div>
                </div>
                <div class="relative w-64">
                    
                    {{-- PERBAIKAN: Placeholder statis diletakkan di input, div overlay dihapus --}}
                    <input type="text" name="search" id="searchInput"
                        class="pl-10 pr-4 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-gray-500 w-full transition-all bg-gray-800 text-sm text-white"
                        value="{{ request('search') }}" autocomplete="off"
                        placeholder="Cari Management SPK, No. Struk...">
                    
                    {{-- DIV PLACEHOLDER DIHAPUS --}}
                    {{-- <div id="staticPlaceholder" ...> </div> --}}

                    <button type="button" class="absolute left-3 top-2.5 text-gray-500 pointer-events-none">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>

                    @if (request('search'))
                    <button id="clearSearch" class="absolute right-3 top-2.5 text-gray-500 hover:text-gray-300"
                        title="Bersihkan pencarian">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    @endif

                    <div id="searchLoading" class="hidden absolute right-10 top-2.5">
                        <svg class="animate-spin h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </div>
                </div>

                {{-- PERBAIKAN: Script untuk placeholder DIV dihapus --}}
                <script>
                    // const input = document.getElementById('searchInput');
                    // const placeholder = document.getElementById('staticPlaceholder');
                    // ... (script placeholder dihapus) ...
                </script>

            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-800 border-b border-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-center font-medium text-gray-400 uppercase tracking-wider w-10">
                            <input type="checkbox" id="selectAll" class="checkbox-style">
                        </th>
                        <th class="px-6 py-3 text-left font-medium text-gray-400 uppercase tracking-wider">No.</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-400 uppercase tracking-wider">Management SPK</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-400 uppercase tracking-wider">No. Struk</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-400 uppercase tracking-wider">Keluar</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-400 uppercase tracking-wider">Pegawai</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-400 uppercase tracking-wider">Barang</th>
                        <th class="px-6 py-3 text-center font-medium text-gray-400 uppercase tracking-wider">Jumlah</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-center font-medium text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse ($pengeluarans as $index => $pengeluaran)
                    <tr class="even:bg-gray-800 hover:bg-gray-700 transition-colors animate-fadeIn">
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <input type="checkbox" name="selected_ids[]" value="{{ $pengeluaran->id }}"
                                class="rowCheckbox checkbox-style">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-gray-300">
                            {{ $pengeluarans->firstItem() + $index }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-medium text-white">{{ $pengeluaran->nama_toko ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-400">
                            {{ $pengeluaran->nomor_struk ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-white">{{ $pengeluaran->tanggal->format('d M Y') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-gray-300">{{ $pengeluaran->pegawai->nama ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap align-top">
                            @foreach ($pengeluaran->daftar_barang as $item)
                            <div class="flex items-center mb-1">
                                <span class="inline-block w-2 h-2 rounded-full bg-gray-500 mr-2 align-middle"></span>
                                <span class="text-gray-300">
                                    {{ $barangs[$item['nama']] ?? $item['nama'] }}
                                </span>
                            </div>
                            @endforeach
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap align-top text-gray-400 text-center">
                            @foreach ($pengeluaran->daftar_barang as $item)
                            <div class="mb-1">x{{ $item['jumlah'] }}</div>
                            @endforeach
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $pengeluaran->status == 'completed' ? 'bg-green-900 text-green-300' : 'bg-yellow-900 text-yellow-300' }}">
                                {{ ucfirst($pengeluaran->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="flex justify-center space-x-2">
                                <a href="{{ route('pengeluarans.show', $pengeluaran->id) }}"
                                    class="text-gray-500 hover:text-gray-300 p-1 rounded-full hover:bg-gray-700 transition-colors"
                                    title="Lihat Detail">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                        </path>
                                    </svg>
                                </a>
                                <a href="{{ route('pengeluarans.edit', $pengeluaran->id) }}"
                                    class="text-gray-500 hover:text-gray-300 p-1 rounded-full hover:bg-gray-700 transition-colors"
                                    title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                        </path>
                                    </svg>
                                </a>
                                <button
                                    onclick="confirmSingleDelete('{{ route('pengeluarans.destroy', $pengeluaran->id) }}')"
                                    class="text-gray-500 hover:text-red-500 p-1 rounded-full hover:bg-red-900 hover:bg-opacity-30 transition-colors"
                                    title="Hapus">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr class="animate-fadeIn">
                        <td colspan="11" class="px-6 py-4 text-center text-gray-500">Tidak ada pengeluaran
                            ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($pengeluarans->hasPages())
        <div class="px-6 py-4 border-t border-gray-700 flex items-center justify-between">
            <div class="text-sm text-gray-400">
                Menampilkan {{ $pengeluarans->firstItem() }} sampai {{ $pengeluarans->lastItem() }} dari
                {{ $pengeluarans->total() }}
                hasil
            </div>
            <div class="flex space-x-1">
                @if ($pengeluarans->onFirstPage())
                <span class="px-3 py-1 rounded-lg border border-gray-700 text-gray-600 cursor-not-allowed">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 19l-7-7 7-7" />
                    </svg>
                </span>
                @else
                <a href="{{ $pengeluarans->previousPageUrl() }}"
                    class="px-3 py-1 rounded-lg border border-gray-700 text-gray-300 hover:bg-gray-800 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                @endif

                @foreach ($pengeluarans->getUrlRange(1, $pengeluarans->lastPage()) as $page => $url)
                @if ($page == $pengeluarans->currentPage())
                <span class="px-3 py-1 rounded-lg bg-gray-700 text-white">{{ $page }}</span>
                @else
                <a href="{{ $url }}"
                    class="px-3 py-1 rounded-lg border border-gray-700 text-gray-300 hover:bg-gray-800 transition-colors">{{ $page }}</a>
                @endif
                @endforeach

                @if ($pengeluarans->hasMorePages())
                <a href="{{ $pengeluarans->nextPageUrl() }}"
                    class="px-3 py-1 rounded-lg border border-gray-700 text-gray-300 hover:bg-gray-800 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5l7 7-7 7" />
                    </svg>
                </a>
                @else
                <span class="px-3 py-1 rounded-lg border border-gray-700 text-gray-600 cursor-not-allowed">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5l7 7-7 7" />
                    </svg>
                </span>
                @endif
            </div>
        </div>
        @endif
    </div>

{{-- Modal --}}
<div id="imageModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-80 flex items-center justify-center">
    <div class="relative">
        <img id="modalImage" src="" alt="Preview Struk" class="max-h-[80vh] rounded shadow-lg select-none">
        <button onclick="closeModal()"
            class="absolute top-2 right-2 text-white hover:text-gray-300 transition duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</div>

<div id="deleteModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-black opacity-75"></div>
        </div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div
            class="inline-block align-bottom bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-700">
            <div class="bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div
                        class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-900 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-white" id="modalTitle">Konfirmasi Penghapusan
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-400">Apakah Anda yakin ingin menghapus data yang dipilih?</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-800 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-700">
                <button type="button" onclick="submitBulkDelete()"
                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Hapus
                </button>
                <button type="button" onclick="closeDeleteModal()"
                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-600 shadow-sm px-4 py-2 bg-gray-700 text-base font-medium text-gray-200 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<div id="singleDeleteModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-black opacity-75"></div>
        </div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div
            class="inline-block align-bottom bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-700">
            <div class="bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div
                        class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-900 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-white">Konfirmasi Penghapusan</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-400">Apakah Anda yakin ingin menghapus struk ini?</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-800 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-700">
                <form id="singleDeleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Hapus
                    </button>
                </form>
                <button type="button" onclick="closeSingleDeleteModal()"
                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-600 shadow-sm px-4 py-2 bg-gray-700 text-base font-medium text-gray-200 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('selectAll');
        const rowCheckboxes = document.querySelectorAll('.rowCheckbox');
        const bulkActions = document.getElementById('bulkActionsContainer');
        const selectedCount = document.getElementById('selectedCount');
        const selectedIdsInput = document.getElementById('selectedIds');

        function updateBulkActions() {
            const selectedCheckboxes = document.querySelectorAll('.rowCheckbox:checked');
            const selectedIds = Array.from(selectedCheckboxes).map(checkbox => checkbox.value);

            if (selectedCheckboxes.length > 0) {
                bulkActions.classList.remove('hidden');
                selectedCount.textContent = `${selectedCheckboxes.length} dipilih`;
                selectedIdsInput.value = JSON.stringify(selectedIds);
            } else {
                bulkActions.classList.add('hidden');
                selectedIdsInput.value = '';
            }

            if (selectAll) {
                const totalCheckboxes = rowCheckboxes.length;
                const checkedCount = selectedCheckboxes.length;
                selectAll.checked = checkedCount === totalCheckboxes && totalCheckboxes > 0;
                selectAll.indeterminate = checkedCount > 0 && checkedCount < totalCheckboxes;
            }
        }

        if (selectAll) {
            selectAll.addEventListener('change', function() {
                const isChecked = this.checked;
                rowCheckboxes.forEach(checkbox => {
                    checkbox.checked = isChecked;
                });
                updateBulkActions();
            });
        }

        rowCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateBulkActions);
        });

        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('rowCheckbox')) {
                updateBulkActions();
            }
        });

        window.clearSelection = function() {
            rowCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            if (selectAll) {
                selectAll.checked = false;
            }
            updateBulkActions();
        };

        window.confirmBulkDelete = function() {
            const selectedIds = JSON.parse(selectedIdsInput.value || '[]');
            if (selectedIds.length === 0) {
                Swal.fire({ title: 'Peringatan', text: 'Pilih setidaknya satu data untuk dihapus.', icon: 'warning', background: '#1F2937', color: '#F9FAFB' });
                return;
            }
            const modal = document.getElementById('deleteModal');
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        };

        window.submitBulkDelete = function() {
            document.getElementById('bulkDeleteForm').submit();
        };

        window.closeDeleteModal = function() {
            const modal = document.getElementById('deleteModal');
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        };

        const searchInput = document.getElementById('searchInput');
        const clearSearchButton = document.getElementById('clearSearch');
        const searchLoading = document.getElementById('searchLoading');
        let searchTimeout;

        function loadData(searchTerm = '') {
            const url = new URL(window.location.href);
            if (searchTerm) {
                url.searchParams.set('search', searchTerm);
            } else {
                url.searchParams.delete('search');
            }

            searchLoading.classList.remove('hidden');

            fetch(url.toString(), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');

                    const newTableBody = doc.querySelector('tbody');
                    if (newTableBody) {
                        document.querySelector('tbody').innerHTML = newTableBody.innerHTML;
                    }
                    
                    const newPagination = doc.querySelector('.flex.space-x-1');
                    const currentPagination = document.querySelector('.flex.space-x-1');
                    if (newPagination && currentPagination) {
                        currentPagination.parentElement.innerHTML = newPagination.parentElement.innerHTML;
                    } else if (currentPagination) {
                         currentPagination.parentElement.innerHTML = '';
                    }

                    const newResultsInfo = doc.querySelector('.text-sm.text-gray-400');
                    const currentResultsInfo = document.querySelector('.text-sm.text-gray-400');
                    if (newResultsInfo && currentResultsInfo) {
                        currentResultsInfo.textContent = newResultsInfo.textContent;
                    } else if (currentResultsInfo) {
                        currentResultsInfo.textContent = '0 hasil ditemukan';
                    }

                    searchLoading.classList.add('hidden');
                    animateRows();
                })
                .catch(error => {
                    console.error('Error:', error);
                    searchLoading.classList.add('hidden');
                });
        }

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const cursorPosition = this.selectionStart;
                localStorage.setItem('searchCursorPosition', cursorPosition);

                searchTimeout = setTimeout(() => {
                    const searchTerm = this.value.trim();
                    loadData(searchTerm);
                    const url = new URL(window.location.href);
                    if (searchTerm) {
                        url.searchParams.set('search', searchTerm);
                    } else {
                        url.searchParams.delete('search');
                    }
                    window.history.pushState({}, '', url.toString());
                }, 500);
            });

            if (clearSearchButton) {
                clearSearchButton.addEventListener('click', function() {
                    searchInput.value = '';
                    localStorage.removeItem('searchCursorPosition');
                    loadData('');
                    const url = new URL(window.location.href);
                    url.searchParams.delete('search');
                    window.history.pushState({}, '', url.toString());
                });
            }

            window.addEventListener('popstate', function() {
                const url = new URL(window.location.href);
                const searchTerm = url.searchParams.get('search') || '';
                searchInput.value = searchTerm;
                loadData(searchTerm);
            });

            const savedCursorPosition = localStorage.getItem('searchCursorPosition');
            if (searchInput.value && savedCursorPosition) {
                setTimeout(() => {
                    searchInput.focus();
                    searchInput.selectionStart = searchInput.selectionEnd = parseInt(savedCursorPosition);
                }, 50);
            }
        }

        function animateRows() {
            document.querySelectorAll('tbody tr').forEach((row, index) => {
                row.style.opacity = '0';
                row.style.transform = 'translateY(10px)';
                row.style.transition = 'all 0.3s ease-out';
                row.style.transitionDelay = `${index * 0.05}s`;
                setTimeout(() => {
                    row.style.opacity = '1';
                    row.style.transform = 'translateY(0)';
                }, 50);
            });
        }
    });

    function openModal(imageSrc) {
        document.getElementById('modalImage').src = imageSrc;
        document.getElementById('imageModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeModal() {
        document.getElementById('imageModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    if(document.getElementById('imageModal')) {
        document.getElementById('imageModal').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !document.getElementById('imageModal').classList.contains('hidden')) {
            closeModal();
        }
    });

    function confirmSingleDelete(deleteUrl) {
        const form = document.getElementById('singleDeleteForm');
        form.action = deleteUrl;
        document.getElementById('singleDeleteModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeSingleDeleteModal() {
        document.getElementById('singleDeleteModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    if(document.getElementById('singleDeleteModal')) {
        document.getElementById('singleDeleteModal').addEventListener('click', function(e) {
            if (e.target === this) closeSingleDeleteModal();
        });
    }
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !document.getElementById('singleDeleteModal').classList.contains('hidden')) {
            closeSingleDeleteModal();
        }
    });
</script>

<style>
    /* ... (Style animasi tetap sama) ... */
    @keyframes slideIn { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .animate-slideIn { animation: slideIn 0.3s ease-out forwards; }
    .animate-fadeIn { animation: fadeIn 0.3s ease-out forwards; }
    .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .group:hover .group-hover\:block { display: block; }
    .group:hover .group-hover\:opacity-100 { opacity: 1; }
    .group:hover .group-hover\:visible { visibility: visible; }
    .hover-scale { transition: transform 0.2s ease; }
    .hover-scale:hover { transform: scale(1.02); }

    /* PERBAIKAN: Paginasi diubah ke dark mode monokrom */
    .pagination {
        display: flex;
        justify-content: center;
        list-style: none;
        padding: 0;
    }
    .pagination li {
        margin: 0 4px;
    }
    .pagination a,
    .pagination span {
        display: inline-block;
        padding: 8px 12px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 0.875rem;
        border: 1px solid #374151; /* border-gray-700 */
    }
    .pagination a {
        color: #D1D5DB; /* text-gray-300 */
        background-color: #1F2937; /* bg-gray-800 */
    }
    .pagination a:hover {
        background-color: #374151; /* bg-gray-700 */
    }
    .pagination .active span {
        background-color: #4B5563; /* bg-gray-600 */
        color: white;
        border-color: #4B5563; /* border-gray-600 */
    }
    .pagination .disabled span {
        color: #4B5563; /* text-gray-600 */
        border-color: #374151; /* border-gray-700 */
    }
    /* ... (Style animasi loading tetap sama) ... */
    @keyframes pulse { 0% { opacity: 0.6; } 50% { opacity: 1; } 100% { opacity: 0.6; } }
    .loading-pulse { animation: pulse 1.5s infinite; }

    /* PERBAIKAN: Style scrollbar dark mode ditambahkan */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    ::-webkit-scrollbar-track {
        background: #1f2937; /* bg-gray-800 */
    }
    ::-webkit-scrollbar-thumb {
        background: #4B5563; /* bg-gray-600 */
        border-radius: 4px;
    }
    ::-webkit-scrollbar-thumb:hover {
        background: #6B7280; /* bg-gray-500 */
    }

    /* PERBAIKAN: Style tabel responsif diubah ke dark mode */
    @media (max-width: 768px) {
        .responsive-table thead {
            display: none;
        }
        .responsive-table tr {
            display: block;
            margin-bottom: 1rem;
            border: 1px solid #374151;  /* border-gray-700 */
            border-radius: 0.5rem;
            background: #1F2937; /* bg-gray-800 */
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        .responsive-table td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: none;
            border-bottom: 1px solid #374151; /* border-gray-700 */
            padding: 0.75rem 1rem;
            font-size: 0.875rem; /* 14px */
        }
        .responsive-table td:last-child {
            border-bottom: none;
        }
        .responsive-table td::before {
            content: attr(data-label);
            font-weight: 600;
            color: #9ca3af; /* text-gray-400 */
            text-transform: capitalize;
            margin-right: 1rem;
        }
    }
</style>
@endsection