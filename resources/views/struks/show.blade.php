@extends('layouts.app')

@section('content')
{{-- PERBAIKAN: Menghapus wrapper 'min-h-screen' dan 'bg-gradient'. --}}
{{-- Menambahkan 'mx-auto' untuk me-tengahkan kartu di dalam layout --}}
<div class="w-full max-w-4xl bg-gray-900 rounded-3xl shadow-2xl overflow-hidden border border-gray-700 mx-auto">

    <div class="bg-gray-800 p-4 border-b border-gray-700">
        <h1 class="text-xl sm:text-2xl font-bold text-white">Detail Struk</h1>
        <p class="text-sm text-gray-400">Informasi lengkap struk barang</p>
    </div>

    <div class="p-4 sm:p-6 space-y-6">
        {{-- PERBAIKAN: Diubah ke dark mode --}}
        <div class="bg-gray-800 rounded-xl p-4 border border-gray-700 shadow-sm">
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-gray-400">Nama Toko</p>
                    <p class="text-lg font-bold text-white">{{ $struk->nama_toko }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Nomor Struk</p>
                    <p class="text-lg font-bold text-white">{{ $struk->nomor_struk }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Tanggal Masuk</p>
                    <p class="text-md font-semibold text-white">{{ date('d M Y', strtotime($struk->tanggal_struk)) }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Total Pembayaran</p>
                    {{-- FIX: Kalikan total_harga dengan 1000 --}}
                    <p class="text-xl font-extrabold text-white">
                        Rp{{ number_format($struk->total_harga * 1000, 0, ',', '.') }}
                    </p>
                </div>
            </div>

            @if ($struk->foto_struk)
            <div class="mt-4 pt-4 border-t border-gray-700">
                <p class="text-xs text-gray-400 mb-2">Foto Struk</p>
                <img src="{{ asset('storage/struk_foto/' . $struk->foto_struk) }}"
                     alt="Foto Struk"
                     class="rounded-lg border-2 border-gray-700 shadow-md w-full max-w-[300px] cursor-pointer transition-all duration-300 hover:scale-105"
                     onclick="openModal('{{ asset('storage/struk_foto/' . $struk->foto_struk) }}')">
            </div>
            @endif
        </div>

        {{-- PERBAIKAN: Diubah ke dark mode --}}
        <div class="bg-gray-800 rounded-xl border border-gray-700 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-700 bg-gray-800">
                <h3 class="text-md font-semibold text-white flex items-center space-x-2">
                    {{-- PERBAIKAN: text-indigo-500 diubah ke text-gray-400 --}}
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4M4 6h16M4 18h16" />
                    </svg>
                    <span>Daftar Barang</span>
                </h3>
            </div>

            <div class="p-2 overflow-x-auto">
                <table class="w-full border-collapse text-sm text-gray-300 table-fixed responsive-table">
                    <thead>
                        <tr class="bg-gray-700 text-gray-300 uppercase text-xs">
                            <th class="border border-gray-700 px-2 py-1 text-center font-semibold w-[40px]">No</th>
                            <th class="border border-gray-700 px-2 py-1 text-left font-semibold">Kode</th>
                            <th class="border border-gray-700 px-2 py-1 text-left font-semibold">Nama</th>
                            <th class="border border-gray-700 px-2 py-1 text-center font-semibold">Jumlah</th>
                            <th class="border border-gray-700 px-2 py-1 text-center font-semibold">Harga</th>
                            <th class="border border-gray-700 px-2 py-1 text-center font-semibold">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="bg-gray-800 divide-y divide-gray-700">
                        @php
                            $items = [];
                            $rawItems = $struk->items;
                            if (is_string($rawItems)) {
                                $decoded = json_decode($rawItems, true);
                                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                    $items = $decoded;
                                }
                            } elseif (is_object($rawItems) || is_array($rawItems)) {
                                foreach ($rawItems as $key => $value) {
                                    $items[$key] = is_object($value) ? (array) $value : $value;
                                }
                            }
                        @endphp

                        @foreach ($items as $index => $item)
                            @php
                                $kodeBarang = $item['kode_barang'] ?? ($item['nama'] ?? null);
                                $nama = $masterBarang[$kodeBarang]->nama_barang ?? $item['nama'] ?? $kodeBarang;
                                $jumlah = (int) ($item['jumlah'] ?? 0);
                                // FIX: Kalikan harga dengan 1000 untuk tampilan puluhan ribu
                                $harga = (int) str_replace('.', '', ($item['harga'] ?? 0));
                                $subtotal = $jumlah * $harga;
                            @endphp
                            <tr>
                                <td class="border border-gray-700 px-2 py-1 text-center" data-label="No">{{ $index + 1 }}</td>
                                <td class="border border-gray-700 px-2 py-1 font-mono text-gray-300" data-label="Kode">{{ $kodeBarang }}</td>
                                <td class="border border-gray-700 px-2 py-1 font-semibold text-white" data-label="Nama">{{ $nama }}</td>
                                <td class="border border-gray-700 px-2 py-1 text-center" data-label="Jumlah">{{ $jumlah }}</td>
                                {{-- PERBAIKAN: text-indigo-600 diubah ke text-gray-200 --}}
                                <td class="border border-gray-700 px-2 py-1 text-center text-gray-200 font-medium" data-label="Harga">Rp{{ number_format($harga, 0, ',', '.') }}</td>
                                <td class="border border-gray-700 px-2 py-1 text-center text-gray-200 font-semibold" data-label="Subtotal">Rp{{ number_format($subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex justify-start">
            {{-- PERBAIKAN: Tombol diubah ke monokrom --}}
            <a href="{{ route('struks.index') }}"
               class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-md text-gray-200 bg-gray-700 hover:bg-gray-600 transition select-none">
                Kembali
            </a>
        </div>
    </div>
</div>

{{-- Modal Lihat Gambar - DIPERBAIKI dengan Loading --}}
<div id="imageModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-90 flex items-center justify-center p-4">
    <div class="relative max-w-7xl max-h-screen">
        {{-- Close button --}}
        <button onclick="closeModal()" 
                class="absolute -top-12 right-0 text-white hover:text-gray-300 transition duration-200 bg-gray-800 rounded-full p-2">
            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        
        {{-- Image container with loading --}}
        <div class="relative">
            <img id="modalImage" 
                 src="" 
                 alt="Foto Struk" 
                 class="max-h-[85vh] max-w-full rounded-lg shadow-2xl object-contain hidden">
            
            {{-- Loading spinner --}}
            <div id="imageLoading" class="flex items-center justify-center bg-gray-900 bg-opacity-75 rounded-lg p-20">
                <svg class="animate-spin h-12 w-12 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
        </div>
        
        {{-- Image info --}}
        <div class="text-center mt-4 text-gray-300 text-sm">
            <p>Klik di luar gambar atau tombol ✕ untuk menutup</p>
        </div>
    </div>
</div>

<script>
    function openModal(imageSrc) {
        console.log('Opening modal with image:', imageSrc);
        
        const modal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modalImage');
        const imageLoading = document.getElementById('imageLoading');
        
        // Show modal and loading
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        imageLoading.classList.remove('hidden');
        modalImage.classList.add('hidden');
        
        // Reset image source to force reload
        modalImage.src = '';
        
        // Set new image source
        modalImage.src = imageSrc;
        
        // Handle image load success
        modalImage.onload = function() {
            console.log('Image loaded successfully');
            imageLoading.classList.add('hidden');
            modalImage.classList.remove('hidden');
        };
        
        // Handle image load error
        modalImage.onerror = function() {
            console.error('Failed to load image:', imageSrc);
            imageLoading.classList.add('hidden');
            
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Gagal Memuat Gambar',
                    html: 'Gambar tidak dapat ditampilkan.<br><small class="text-gray-400">Pastikan file ada di: storage/app/public/struk_foto/</small>',
                    icon: 'error',
                    background: '#1F2937',
                    color: '#F9FAFB',
                    confirmButtonColor: '#3B82F6'
                });
            } else {
                alert('Gagal memuat gambar. Pastikan file ada di folder storage/app/public/struk_foto/');
            }
            closeModal();
        };
    }

    function closeModal() {
        const modal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modalImage');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        modalImage.src = ''; // Clear image source
    }

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && !document.getElementById('imageModal').classList.contains('hidden')) {
            closeModal();
        }
    });
</script>

{{-- PERBAIKAN: Style tabel responsif diubah ke dark mode --}}
<style>
    /* Tabel normal */
    table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        word-wrap: break-word;
    }

    /* Responsif berubah jadi model turun */
    @media (max-width: 768px) {
        thead {
            display: none;
        }
        tr {
            display: block;
            margin-bottom: 1rem;
            border: 1px solid #374151;  /* border-gray-700 */
            border-radius: 0.5rem;
            background: #1F2937; /* bg-gray-800 */
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: none;
            border-bottom: 1px solid #374151; /* border-gray-700 */
            padding: 0.5rem 0.75rem;
            font-size: 0.8rem;
        }
        td:last-child {
            border-bottom: none;
        }
        td::before {
            content: attr(data-label);
            font-weight: 600;
            color: #9ca3af; /* text-gray-400 */
            text-transform: capitalize;
        }
        .max-w-4xl {
            max-width: 95%;
        }
    }
</style>
@endsection