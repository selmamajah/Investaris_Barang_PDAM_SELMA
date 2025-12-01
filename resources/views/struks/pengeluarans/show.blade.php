@extends('layouts.app')

@section('content')
{{-- PERBAIKAN: Layout diubah total ke dark mode, wrapper gradient dihapus --}}
<div class="w-full max-w-4xl bg-gray-900 rounded-3xl shadow-2xl overflow-hidden border border-gray-700 mx-auto">

    <div class="bg-gray-800 p-4 border-b border-gray-700">
        <h1 class="text-xl sm:text-2xl font-bold text-white">Detail Struk Pengeluaran</h1>
        <p class="text-sm text-gray-400">Informasi lengkap struk barang keluar</p>
    </div>

    <div class="p-4 sm:p-6 space-y-6">
        <div class="bg-gray-800 rounded-xl p-4 border border-gray-700 shadow-sm">
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-gray-400">Management SPK</p>
                    <p class="text-lg font-bold text-white">{{ $pengeluaran->nama_toko }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Nomor Struk</p>
                    <p class="text-lg font-bold text-white">{{ $pengeluaran->nomor_struk }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Tanggal Keluar</p>
                    <p class="text-md font-semibold text-white">{{ date('d M Y', strtotime($pengeluaran->tanggal)) }}</p>
                </div>
                 <div>
                    <p class="text-xs text-gray-400">Pegawai</p>
                    <p class="text-md font-semibold text-white">{{ $pengeluaran->pegawai->nama ?? '-' }}</p>
                </div>
            </div>
        </div>

        <div class="bg-gray-800 rounded-xl border border-gray-700 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-700 bg-gray-800">
                <h3 class="text-md font-semibold text-white flex items-center space-x-2">
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
                        </tr>
                    </thead>
                    <tbody class="bg-gray-800 divide-y divide-gray-700">
                        @php
                            $items = [];
                            $rawItems = $pengeluaran->daftar_barang; // Menggunakan 'daftar_barang'
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
                                $nama = $barangs[$kodeBarang] ?? $item['nama'] ?? $kodeBarang; // Menggunakan array $barangs dari controller
                                $jumlah = (int) ($item['jumlah'] ?? 0);
                            @endphp
                            <tr>
                                <td class="border border-gray-700 px-2 py-1 text-center" data-label="No">{{ $index + 1 }}</td>
                                <td class="border border-gray-700 px-2 py-1 font-mono text-gray-300" data-label="Kode">{{ $kodeBarang }}</td>
                                <td class="border border-gray-700 px-2 py-1 font-semibold text-white" data-label="Nama">{{ $nama }}</td>
                                <td class="border border-gray-700 px-2 py-1 text-center" data-label="Jumlah">{{ $jumlah }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex justify-start">
            <a href="{{ route('pengeluarans.index') }}"
               class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-md text-gray-200 bg-gray-700 hover:bg-gray-600 transition select-none">
                Kembali
            </a>
        </div>
    </div>
</div>

<div id="imageModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-80 flex items-center justify-center">
    <div class="relative w-full max-w-[90vw]">
        <img id="modalImage" src="" class="max-h-[80vh] w-full rounded shadow-lg select-none">
        <button onclick="closeModal()" class="absolute top-2 right-2 text-white hover:text-gray-300 transition duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</div>

<script>
    function openModal(src) {
        document.getElementById('modalImage').src = src;
        document.getElementById('imageModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeModal() {
        document.getElementById('imageModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && !document.getElementById('imageModal').classList.contains('hidden')) closeModal();
    });
</script>

{{-- Style tabel responsif dark mode --}}
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