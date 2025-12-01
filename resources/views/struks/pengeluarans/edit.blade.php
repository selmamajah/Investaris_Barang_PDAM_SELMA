@extends('layouts.app')

@section('content')
{{-- PERBAIKAN: Layout diubah total ke dark mode, wrapper gradient dihapus --}}
<div class="w-full max-w-4xl bg-gray-900 rounded-3xl shadow-2xl overflow-hidden border border-gray-700 mx-auto">

    <div class="bg-gray-800 p-6 text-white border-b border-gray-700">
        <h2 class="text-xl font-bold">Edit Item Struk</h2>
        <p class="text-sm text-gray-400">Ubah, tambah, atau hapus item dalam struk</p>
    </div>

    <form action="{{ route('pengeluarans.update', $pengeluaran->id) }}" method="POST" class="p-6 space-y-6" id="pengeluaran-form">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block mb-1 font-medium text-gray-300">Nama Toko</label>
                <input type="text" class="w-full border rounded-lg px-3 py-2 bg-gray-700 text-gray-400 border-gray-600 cursor-not-allowed" 
                       value="{{ $pengeluaran->nama_toko }}" readonly>
                <input type="hidden" name="nama_toko" value="{{ $pengeluaran->nama_toko }}">
            </div>
            <div>
                <label class="block mb-1 font-medium text-gray-300">Nomor Struk</label>
                <input type="text" class="w-full border rounded-lg px-3 py-2 bg-gray-700 text-gray-400 border-gray-600 cursor-not-allowed" 
                       value="{{ $pengeluaran->nomor_struk }}" readonly>
                <input type="hidden" name="nomor_struk" value="{{ $pengeluaran->nomor_struk }}">
            </div>
            <div>
                <label class="block mb-1 font-medium text-gray-300">Tanggal Pengeluaran</label>
                <input type="date" name="tanggal" class="w-full border rounded-lg px-3 py-2 bg-gray-800 text-white border-gray-600 focus:ring-gray-500 focus:border-gray-500"
                       value="{{ old('tanggal', \Carbon\Carbon::parse($pengeluaran->tanggal)->format('Y-m-d')) }}" required style="color-scheme: dark;">
            </div>
            <div>
                <label class="block mb-1 font-medium text-gray-300">Status</label>
                <select name="status" id="status" class="w-full border rounded-lg px-3 py-2 bg-gray-800 text-white border-gray-600 focus:ring-gray-500 focus:border-gray-500" required>
                    <option value="progress"
                        {{ old('status', $pengeluaran->status) == 'progress' ? 'selected' : '' }}>
                        Progress
                    </option>
                    <option value="completed"
                        {{ old('status', $pengeluaran->status) == 'completed' ? 'selected' : '' }}>
                        Completed
                    </option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block mb-1 font-medium text-gray-300">Pegawai</label>
                <input type="text" class="w-full border rounded-lg px-3 py-2 bg-gray-700 text-gray-400 border-gray-600 cursor-not-allowed" 
                       value="{{ $pengeluaran->pegawai->nama }}" readonly>
                <input type="hidden" name="pegawai_id" value="{{ $pengeluaran->pegawai_id }}">
            </div>
        </div>

        <div class="space-y-3" id="items-container">
            <div class="grid grid-cols-3 gap-4 font-medium text-gray-400 pb-2 border-b border-gray-700">
                <div>Nama Barang</div>
                <div>Jumlah</div>
                <div>Aksi</div>
            </div>
            <div class="input-group">
                @foreach ($pengeluaran->daftar_barang as $index => $item)
                @php
                    $kodeBarang = $item['kode_barang'] ?? $item['nama'];
                    $barang = \App\Models\Barang::where('kode_barang', $kodeBarang)->first();
                    $namaBarang = $barang->nama_barang ?? ($item['nama'] ?? 'Tidak diketahui');
                    $stokTersedia = $barang?->jumlah ?? 0;
                @endphp
                <div class="grid grid-cols-3 gap-4 items-center py-3 border-b border-gray-700 existing-item"
                     data-kode="{{ $kodeBarang }}" data-stok="{{ $stokTersedia }}">
                    <div class="text-sm">
                        <div class="text-white font-semibold">{{ $namaBarang }}</div>
                        <div class="text-gray-400 text-xs">Kode: {{ $kodeBarang }}</div>
                        <div class="text-gray-400 text-xs">
                            Stok: <span class="stok-tersedia" data-awal="{{ $stokTersedia }}">{{ $stokTersedia }}</span>
                        </div>
                    </div>
                    <div>
                        <input type="number" name="existing_items[{{ $index }}][jumlah]" value="{{ $item['jumlah'] }}" min="1"
                               class="w-full border rounded-lg px-3 py-2 bg-gray-800 text-white border-gray-600 focus:ring-gray-500 focus:border-gray-500 existing-jumlah-input">
                        <input type="hidden" name="existing_items[{{ $index }}][kode_barang]" value="{{ $kodeBarang }}">
                    </div>
                    <div>
                        <button type="button" class="hapus-item text-red-500 hover:text-red-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="border-2 border-dashed border-gray-700 p-4 rounded-lg bg-gray-900">
            <div class="flex justify-between items-center mb-3">
                <h3 class="font-medium text-white">+ Tambah Item Baru</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                <div class="flex flex-col">
                    <label class="block text-sm text-gray-400 mb-1">Cari barang atau pilih dari daftar</label>
                    <select class="w-full border rounded-lg px-3 py-2 bg-gray-800 text-white border-gray-600 focus:ring-gray-500 focus:border-gray-500 barang-select">
                        <option value="">-- Pilih Barang --</option>
                        @foreach($barangs as $barang)
                        <option value="{{ $barang->nama_barang }}"
                            data-kode="{{ $barang->kode_barang }}"
                            data-stok="{{ $barang->jumlah }}">
                            {{ $barang->kode_barang }} - {{ $barang->nama_barang }} (Stok: {{ $barang->jumlah }})
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col">
                    <label class="block text-sm text-gray-400 mb-1">Jumlah</label>
                    <input type="number" class="w-full border rounded-lg px-3 py-2 bg-gray-800 text-white border-gray-600 focus:ring-gray-500 focus:border-gray-500 new-jumlah-input" value="1" min="1">
                </div>
            </div>
            <div class="flex justify-start">
                {{-- PERBAIKAN: Tombol diubah ke monokrom --}}
                <button type="button" id="tambah-barang"
                        class="bg-gray-200 text-gray-900 px-4 py-2 rounded-lg hover:bg-gray-300 font-medium transition-colors">
                    + Tambah
                </button>
            </div>
        </div>

        <div class="flex justify-end space-x-4 pt-4">
            <a href="{{ route('pengeluarans.index') }}"
               class="inline-flex items-center px-4 py-2 border border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-200 bg-gray-700 hover:bg-gray-600">
                Batal
            </a>
            {{-- PERBAIKAN: Tombol diubah ke monokrom --}}
            <button type="submit" id="submit-button"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-gray-900 bg-gray-200 hover:bg-gray-300">
                Simpan Perubahan
            </button>
        </div>
    </form>

    <template id="template-barang-baru">
        <div class="grid grid-cols-3 gap-4 items-center py-3 border-b border-gray-700 barang-baru" data-kode="" data-stok="">
            <div class="text-sm">
                <div class="text-white font-semibold new-nama-barang"></div>
                <div class="text-gray-400 text-xs">Kode: <span class="new-kode-barang"></span></div>
                <div class="text-gray-400 text-xs">Stok: <span class="stok-tersedia" data-awal=""></span></div>
            </div>
            <div>
                <input type="number" name="new_items[][jumlah]" class="w-full border rounded-lg px-3 py-2 bg-gray-800 text-white border-gray-600 focus:ring-gray-500 focus:border-gray-500 new-jumlah-field" min="1">
            </div>
            <div>
                <input type="hidden" name="new_items[][kode_barang]" class="new-kode-field" value="">
                <button type="button" class="hapus-barang text-red-500 hover:text-red-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>
    </template>
</div>
@endsection

@push('scripts')
{{-- SCRIPT JAVASCRIPT ANDA SAYA BIARKAN UTUH, HANYA ALERT DIUBAH KE DARK MODE --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const template = document.getElementById('template-barang-baru');
        const container = document.getElementById('items-container');
        const tambahBtn = document.getElementById('tambah-barang');
        const barangSelect = document.querySelector('.barang-select');
        const jumlahInputBaru = document.querySelector('.new-jumlah-input');
        const submitBtn = document.getElementById('submit-button');

        let newItemIndex = 0; // Counter untuk new_items

        const stokSementara = new Map();

        // Inisialisasi stok awal dari select2
        if (barangSelect) {
            barangSelect.querySelectorAll('option').forEach(option => {
                const kode = option.getAttribute('data-kode');
                const stok = parseInt(option.getAttribute('data-stok')) || 0;
                if (kode) stokSementara.set(kode, stok);
            });
        }
        
        // Inisialisasi stok awal dari item yang sudah ada
        document.querySelectorAll('.existing-item').forEach(item => {
            const kode = item.getAttribute('data-kode');
            const stokAwal = parseInt(item.getAttribute('data-stok')) || 0;
            const jumlahAwal = parseInt(item.querySelector('.existing-jumlah-input').value) || 0;
            // Set stok awal di map HANYA JIKA BELUM ADA (dari select)
            if (!stokSementara.has(kode)) {
                 stokSementara.set(kode, stokAwal + jumlahAwal); // Stok asli = stok saat ini + yg sudah dipakai
            }
        });


        if (tambahBtn) {
            tambahBtn.addEventListener('click', () => {
                const option = barangSelect.options[barangSelect.selectedIndex];
                if (!option.value) {
                    Swal.fire({ title: 'Peringatan', text: 'Pilih barang terlebih dahulu', icon: 'warning', background: '#1F2937', color: '#F9FAFB' });
                    return;
                }

                const nama = option.value;
                const kode = option.getAttribute('data-kode');
                const stok = stokSementara.get(kode);
                const jumlah = parseInt(jumlahInputBaru.value) || 1;

                if (jumlah > stok) {
                    Swal.fire({ title: 'Stok Tidak Cukup', text: `Stok tidak mencukupi!\nStok tersedia: ${stok}\nJumlah diminta: ${jumlah}`, icon: 'error', background: '#1F2937', color: '#F9FAFB' });
                    return;
                }

                const sudahAda = [...document.querySelectorAll('.existing-item, .barang-baru')]
                    .some(el => el.getAttribute('data-kode') === kode);
                
                if (sudahAda) {
                    Swal.fire({ title: 'Barang Duplikat', text: 'Barang ini sudah ditambahkan sebelumnya.', icon: 'warning', background: '#1F2937', color: '#F9FAFB' });
                    return;
                }

                stokSementara.set(kode, stok - jumlah);

                const clone = template.content.cloneNode(true);
                const el = clone.querySelector('.barang-baru');
                
                el.setAttribute('data-kode', kode);
                el.setAttribute('data-stok', stok); // Simpan stok ASLI

                el.querySelector('.new-nama-barang').textContent = nama;
                el.querySelector('.new-kode-barang').textContent = kode;
                el.querySelector('.stok-tersedia').textContent = stok - jumlah; // Tampilkan sisa stok
                el.querySelector('.stok-tersedia').setAttribute('data-awal', stok);

                const jumlahInput = el.querySelector('.new-jumlah-field');
                const kodeInput = el.querySelector('.new-kode-field');
                jumlahInput.value = jumlah;
                jumlahInput.max = stok; // Max adalah stok asli
                jumlahInput.setAttribute('name', `new_items[${newItemIndex}][jumlah]`);
                kodeInput.value = kode;
                kodeInput.setAttribute('name', `new_items[${newItemIndex}][kode_barang]`);
                newItemIndex++;

                jumlahInput.addEventListener('input', () => {
                    const jumlahBaru = parseInt(jumlahInput.value) || 0;
                    const stokAwal = stok; // stok asli
                    const sisa = stokAwal - jumlahBaru;

                    if (sisa < 0) {
                        Swal.fire({ title: 'Stok Tidak Cukup', text: `Stok tidak cukup!\nStok tersedia: ${stokAwal}\nDiminta: ${jumlahBaru}`, icon: 'error', background: '#1F2937', color: '#F9FAFB' });
                        jumlahInput.value = 1; // Reset ke 1
                        jumlahInput.classList.add('border-red-500');
                        stokSementara.set(kode, stokAwal - 1); // Update map
                        el.querySelector('.stok-tersedia').textContent = stokAwal - 1;
                        return;
                    }

                    jumlahInput.classList.remove('border-red-500');
                    stokSementara.set(kode, sisa);
                    el.querySelector('.stok-tersedia').textContent = sisa;
                });

                el.querySelector('.hapus-barang').addEventListener('click', function() {
                    stokSementara.set(kode, stok); // Kembalikan stok asli
                    el.remove();
                });

                container.appendChild(clone);

                barangSelect.value = '';
                jumlahInputBaru.value = 1;
            });
        }

        document.querySelectorAll('.existing-jumlah-input').forEach(input => {
            const item = input.closest('.existing-item');
            const kode = item.getAttribute('data-kode');
            const stokDisplay = item.querySelector('.stok-tersedia');
            const jumlahAwal = parseInt(input.value) || 0;
            const stokAwal = (stokSementara.get(kode) || 0) + jumlahAwal; // Hitung stok asli
            
            stokDisplay.setAttribute('data-awal', stokAwal);
            stokDisplay.textContent = stokAwal - jumlahAwal; // Tampilkan sisa stok

            input.addEventListener('input', () => {
                const jumlahBaru = parseInt(input.value) || 0;
                const sisa = stokAwal - jumlahBaru;

                if (sisa < 0) {
                    Swal.fire({ title: 'Stok Tidak Cukup', text: `Stok tidak cukup!\nStok tersedia: ${stokAwal}\nDiminta: ${jumlahBaru}`, icon: 'error', background: '#1F2937', color: '#F9FAFB' });
                    input.value = jumlahAwal; // Kembalikan ke jumlah awal
                    input.classList.add('border-red-500');
                    return;
                }

                input.classList.remove('border-red-500');
                stokDisplay.textContent = sisa;
                stokSementara.set(kode, sisa);
            });
        });

        document.querySelectorAll('.hapus-item').forEach(button => {
            button.addEventListener('click', function() {
                const item = this.closest('.existing-item');
                const kode = item.getAttribute('data-kode');
                const stokAwal = parseInt(item.querySelector('.stok-tersedia').getAttribute('data-awal')) || 0;
                
                stokSementara.set(kode, stokAwal); // Kembalikan stok asli ke map
                item.remove();
            });
        });

        if (submitBtn) {
            submitBtn.addEventListener('click', (e) => {
                const newItems = document.querySelectorAll('.barang-baru');
                newItems.forEach((item, index) => {
                    const kodeField = item.querySelector('.new-kode-field');
                    const jumlahField = item.querySelector('.new-jumlah-field');
                    kodeField.name = `new_items[${index}][kode_barang]`;
                    jumlahField.name = `new_items[${index}][jumlah]`;
                });
                // Form akan submit secara normal
            });
        }
    });
</script>
@endpush