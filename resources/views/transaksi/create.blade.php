@extends('layouts.app')

@section('content')
<style>
    /* ... (Semua CSS Anda tetap sama persis, saya sembunyikan agar tidak terlalu panjang) ... */
    :root { --primary: #9CA3AF; --primary-light: #F9FAFB; --primary-dark: #E5E7EB; --secondary: #6B7280; --secondary-light: #4B5563; --secondary-dark: #374151; --success: #10B981; --error: #ef4444; --warning: #f59e0b; --info: #3b82f6; --light: #1F2937; --dark: #F9FAFB; --gray: #374151; --gray-dark: #4B5563; --border-radius: 0.75rem; --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05); --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); --shadow-md: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); --shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); --transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); }
    * { box-sizing: border-box; }
    .form-container { max-width: 960px; margin: 0 auto; padding: 0; width: 100%; }
    .form-card { background: #111827; border-radius: var(--border-radius); box-shadow: var(--shadow-lg); border: 1px solid var(--gray); overflow: hidden; transition: var(--transition); margin-bottom: 1rem; }
    .form-card:hover { box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); }
    .form-inner { padding: 1.5rem; }
    .section-title { display: flex; align-items: center; gap: 0.5rem; font-size: 1.125rem; font-weight: 700; color: var(--dark); margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid var(--gray); letter-spacing: -0.01em; }
    .section-title i { font-size: 1.1em; color: var(--primary-light); background: var(--secondary-dark); width: 2rem; height: 2rem; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; }
    .form-grid { display: grid; grid-template-columns: 1fr; gap: 1rem; margin-bottom: 1.5rem; }
    .input-group { position: relative; margin-bottom: 1rem; }
    .input-group label { font-size: 0.875rem; font-weight: 600; color: #D1D5DB; margin-bottom: 0.25rem; display: block; }
    .input-group input, .input-group select { width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--gray); border-radius: var(--border-radius); font-size: 0.9375rem; background: var(--light); color: var(--dark); transition: var(--transition); margin-top: 0.125rem; }
    .input-group .harga { text-align: left; }
    .input-group input:focus, .input-group select:focus { outline: none; border-color: var(--primary); background: #374151; box-shadow: 0 0 0 3px rgba(156, 163, 175, 0.2); }
    .input-group input::placeholder { color: var(--secondary-light); opacity: 1; }
    .button-group { display: flex; flex-direction: column; gap: 0.75rem; margin-top: 1.5rem; }
    .btn { padding: 0.75rem 1.25rem; border-radius: var(--border-radius); font-weight: 600; font-size: 0.9375rem; border: none; cursor: pointer; transition: var(--transition); display: flex; align-items: center; justify-content: center; gap: 0.5rem; box-shadow: var(--shadow-sm); width: 100%; text-align: center; }
    .btn-primary { background: #E5E7EB; color: #111827; }
    .btn-primary:hover, .btn-primary:focus { background: #F3F4F6; box-shadow: var(--shadow); }
    .btn-secondary { background: #374151; color: #F9FAFB; border: 1px solid #4B5563; }
    .btn-secondary:hover, .btn-secondary:focus { background: #4B5563; color: #FFF; border-color: #6B7280; }
    .btn-danger { background: #312626; color: #F87171; border: 1px solid #7f1d1d; }
    .btn-danger:hover, .btn-danger:focus { background: #450a0a; color: #FCA5A5; }
    .alert { padding: 0.75rem 1rem; border-radius: var(--border-radius); margin-bottom: 1rem; font-size: 0.9375rem; display: flex; align-items: flex-start; gap: 0.5rem; border-left: 4px solid; }
    .alert i { font-size: 1.125rem; margin-top: 0.125rem; }
    .alert-content { flex: 1; }
    .alert-success { background: #064E3B; color: #A7F3D0; border-left-color: var(--success); }
    .alert-error { background: #450a0a; color: #FCA5A5; border-left-color: var(--error); }
    .alert ul { margin-top: 0.25rem; padding-left: 1rem; }
    .tab-container { display: flex; flex-direction: column; height: 100%; }
    .tab-header { display: flex; flex-wrap: wrap; border-bottom: 1px solid var(--gray); margin-bottom: 1rem; background: var(--light); border-radius: var(--border-radius) var(--border-radius) 0 0; }
    .tab-button { padding: 0.75rem 1.5rem; background: none; border: none; cursor: pointer; font-weight: 600; color: var(--secondary); border-bottom: 3px solid transparent; transition: var(--transition); font-size: 0.9375rem; display: flex; align-items: center; gap: 0.5rem; flex: 1; text-align: center; justify-content: center; }
    .tab-button.active, .tab-button:focus { color: var(--dark); background: #111827; border-bottom: 3px solid var(--primary-light); }
    .tab-button:hover:not(.active) { background: #374151; color: var(--primary-light); }
    .tab-content { display: none; flex: 1; padding: 1rem 0 0 0; background: transparent; }
    .tab-content.active { display: block; }
    .file-upload-wrapper { position: relative; margin-bottom: 1rem; }
    .file-upload-input { position: absolute; left: 0; top: 0; opacity: 0; width: 100%; height: 100%; cursor: pointer; }
    .file-upload-label { display: block; border: 2px dashed var(--gray); border-radius: var(--border-radius); padding: 1.5rem; text-align: center; cursor: pointer; background: var(--light); transition: all 0.3s ease; position: relative; overflow: hidden; }
    .file-upload-label:hover { border-color: var(--primary); background: #374151; box-shadow: var(--shadow); }
    .file-upload-icon { font-size: 2rem; color: var(--primary-light); margin-bottom: 0.75rem; }
    .file-upload-title { font-size: 1rem; font-weight: 600; color: var(--dark); margin-bottom: 0.25rem; }
    .file-upload-description { color: var(--secondary); margin-bottom: 0.25rem; font-size: 0.875rem; }
    .file-upload-requirements { color: var(--secondary-light); font-size: 0.75rem; }
    .file-preview-container { margin-top: 1rem; animation: fadeIn 0.3s ease; }
    .file-preview-image { max-width: 100%; max-height: 150px; border-radius: calc(var(--border-radius) - 0.25rem); box-shadow: var(--shadow-sm); margin: 0 auto; display: block; cursor: pointer; transition: all 0.3s ease; border: 1px solid var(--gray); }
    .file-preview-image:hover { transform: scale(1.02); box-shadow: var(--shadow); }
    .file-preview-info { margin-top: 0.75rem; display: flex; align-items: center; justify-content: center; gap: 0.75rem; }
    .file-name { font-size: 0.875rem; color: var(--dark); max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .remove-file-btn { background: none; border: none; color: var(--error); font-size: 0.875rem; cursor: pointer; font-weight: 600; transition: var(--transition); }
    .remove-file-btn:hover { color: #FF0000; }
    .select2-container--default .select2-selection--single { height: 44px; padding: 0.5rem 1rem; border: 1px solid var(--gray); border-radius: var(--border-radius); background: var(--light); color: var(--dark); transition: var(--transition); outline: none; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 1.5rem; color: var(--dark); padding: 0; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 42px; top: 1px; right: 5px; }
    .select2-container--default.select2-container--focus .select2-selection--single { border-color: var(--primary); background: #374151; box-shadow: 0 0 0 3px rgba(156, 163, 175, 0.2); }
    .select2-dropdown { background: #1F2937; border: 1px solid var(--gray); border-radius: var(--border-radius); box-shadow: var(--shadow-md); overflow: hidden; }
    .select2-search--dropdown .select2-search__field { border: 1px solid var(--gray-dark); background: var(--light); color: var(--dark); padding: 0.5rem; border-radius: var(--border-radius); }
    .select2-results__option { padding: 0.75rem 1rem; font-size: 0.9375rem; border-bottom: 1px solid var(--gray-dark); color: #D1D5DB; }
    .select2-results__option:last-child { border-bottom: none; }
    .select2-results__option--highlighted { background: #374151 !important; color: #FFF !important; }
    .select2-results__option--selected { background: #4B5563 !important; color: #fff !important; font-weight: 600; }
    .select2-container--default .select2-selection--single .select2-selection__placeholder { color: var(--secondary-light); }
    .select2-container--default .select2-selection--single .select2-selection__clear { color: var(--error); font-size: 1.125rem; margin-right: 0.5rem; }

    @media (min-width: 768px) {
        .modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(17, 24, 39, 0.7); z-index: 1000; display: flex; align-items: center; justify-content: center; opacity: 0; visibility: hidden; transition: var(--transition); overflow-x: hidden; }
        .modal-overlay.active { opacity: 1; visibility: visible; }
        .modal-content { background: #1F2937; border-radius: var(--border-radius); box-shadow: var(--shadow-lg); width: 95%; max-width: 600px; max-height: 85vh; overflow-y: auto; transform: translateY(20px); transition: var(--transition); border: 1px solid #374151; }
        .modal-overlay.active .modal-content { transform: translateY(0); }
        .modal-header { padding: 1rem; border-bottom: 1px solid var(--gray); display: flex; align-items: center; justify-content: space-between; }
        .modal-title { font-size: 1.125rem; font-weight: 600; color: var(--dark); }
        .modal-close { background: none; border: none; font-size: 1.25rem; cursor: pointer; color: var(--secondary); transition: var(--transition); }
        .modal-close:hover { color: var(--error); }
        .modal-body { padding: 1rem; }
        .modal-image { width: 100%; height: auto; max-height: 60vh; object-fit: contain; border-radius: calc(var(--border-radius) - 0.25rem); }
        .item-table { width: 100%; border-collapse: collapse; margin-bottom: 1rem; background: #111827; border-radius: var(--border-radius); overflow: hidden; box-shadow: var(--shadow-sm); border: 1px solid var(--gray); }
        .item-table th, .item-table td { border-bottom: 1px solid var(--gray); padding: 0.5rem; vertical-align: middle; text-align: left; }
        .item-table th { background: var(--light); color: var(--secondary-dark); font-weight: 600; font-size: 0.875rem; }
        .item-table td { color: var(--dark); }
        .income-table th:nth-child(1), .income-table td:nth-child(1) { width: 80%; }
        .income-table th:nth-child(2), .income-table td:nth-child(2) { width: 20%; text-align: center; }
        .item-table .input-group { margin-bottom: 0.5rem; }
        .item-table .input-group input { padding: 0.5rem; font-size: 0.875rem; width: 100%; }
        .item-table .select2-container--default .select2-selection--single { height: 36px; padding: 0.25rem 0.5rem; font-size: 0.875rem; }
        .item-table .btn-danger { padding: 0.5rem; width: 100%; height: 100%; display: flex; justify-content: center; align-items: center; }
        .item-table .btn-danger i { margin: 0; }
        .stok-info { display: block; font-size: 0.75rem; color: var(--secondary); margin-top: 0.25rem; text-align: left; }
        .income-table td:nth-child(2) .input-group input, .income-table td:nth-child(3) .input-group input { text-align: center; width: 100%; min-width: 0; }
        .income-table td:nth-child(4) .subtotal-display { text-align: right; }
        .input-group .subtotal-display { text-align: right; display: block; padding: 0.5rem 0.25rem; font-weight: 600; color: var(--primary-light); background: #1F2937; border-radius: var(--border-radius); margin-top: 0.125rem; }
        .total-display { text-align: right; font-size: 1rem; margin-bottom: 1rem; padding: 0.75rem; background: var(--light); border-radius: var(--border-radius); border: 1px solid var(--gray); color: var(--dark); }

        @media (min-width: 768px) {
            .form-grid { grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); }
            .form-inner { padding: 2rem; }
            .button-group { flex-direction: row; justify-content: flex-end; }
            .btn { width: auto; }
            .tab-button { flex: 0 1 auto; }
        }

        @media (max-width: 767px) {
            .item-table, .item-table thead, .item-table tbody, .item-table tr { display: block; width: 100%; }
            .item-table thead { display: none; }
            .item-table tr { margin-bottom: 1rem; border: 1px solid var(--gray); border-radius: var(--border-radius); box-shadow: var(--shadow-sm); background: #1F2937; padding: 0.5rem; max-width: 100%; }
            .item-table td { display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0.25rem !important; font-size: 0.85rem; border: none; position: relative; box-sizing: border-box; word-break: break-word; max-width: 100%; }
            .item-table td::before { content: attr(data-label); flex: 0 0 110px; font-weight: 600; color: var(--secondary-dark); margin-right: 0.5rem; font-size: 0.85rem; text-align: left; min-width: 90px; max-width: 40vw; word-break: break-word; }
            .item-table .input-group input, .item-table .input-group select { width: 100%; min-width: 0; max-width: 100%; font-size: 0.85rem; box-sizing: border-box; }
            .item-table .btn-danger { min-width: 36px; padding: 0.5rem !important; font-size: 1rem !important; margin: 0 auto; display: flex; justify-content: center; align-items: center; }
            .subtotal-display { font-size: 0.85rem !important; text-align: left; word-break: break-word; color: #FFF; }
            .section-title { font-size: 1rem; }
            .tab-button { padding: 0.75rem 1rem; font-size: 0.875rem; }
        }
    }
</style>

<div class="form-container">
    <div class="form-card">
        <div class="form-inner">
            {{-- Notifications --}}
            @if (session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <div class="alert-content">
                    {{ session('success') }}
                </div>
            </div>
            @endif

            @if ($errors->any())
            <div class="alert alert-error">
                <i class="fas fa-exclamation-triangle"></i>
                <div class="alert-content">
                    <strong>Terjadi kesalahan:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            {{-- Tab Container --}}
            <div class="tab-container">
                <div class="tab-header">
                    <button class="tab-button active" data-tab="income-tab">
                        <i class="fas fa-sign-in-alt"></i> Pemasukan
                    </button>
                    <button class="tab-button" data-tab="expense-tab">
                        <i class="fas fa-sign-out-alt"></i> Pengeluaran
                    </button>
                </div>

                {{-- Income Tab Content --}}
                <div id="income-tab" class="tab-content active">
                    <form action="{{ route('struks.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="section-title">
                            <i class="fas fa-money-bill-wave"></i>
                            <span>Buat Pemasukan</span>
                        </div>

                        <div class="form-grid">
                            <div class="input-group">
                                <label for="nama_toko">
                                    <i class="fas fa-store mr-1"></i>
                                    Nama Toko
                                </label>
                                <input type="text" name="nama_toko" id="nama_toko" placeholder="Masukkan nama toko" required value="{{ old('nama_toko') }}">
                            </div>

                            <div class="input-group">
                                <label for="nomor_struk">
                                    <i class="fas fa-receipt mr-1"></i>
                                    Nomor Struk
                                </label>
                                <input type="text" name="nomor_struk" id="nomor_struk" placeholder="Masukkan nomor struk" required value="{{ old('nomor_struk') }}">
                            </div>

                            <div class="input-group">
                                <label for="tanggal_struk">
                                    <i class="fas fa-calendar-alt mr-1"></i>
                                    Tanggal Masuk
                                </label>
                                <input type="date" name="tanggal_struk" id="tanggal_struk" required value="{{ old('tanggal_struk', date('Y-m-d')) }}" style="color-scheme: dark;">
                            </div>

                            <div class="input-group">
                                <label for="status">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Status
                                </label>
                                <select name="status" id="status" class="form-control" required>
                                    <option value="progress" {{ old('status') == 'progress' ? 'selected' : '' }}>Progress</option>
                                    <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                            </div>
                        </div>

                        {{-- Items --}}
                        <div class="section-title">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Daftar Barang</span>
                        </div>

                        <table class="item-table income-table">
                            <thead>
                                <tr>
                                    <th>Detail Barang</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="income-items-container">
                                @php
                                    $oldItems = old('items', [[]]);
                                    $initialIndex = 0;
                                @endphp
                                @foreach ($oldItems as $index => $item)
                                    <tr class="item-row" data-item="{{ $index }}">
                                        <td data-label="Detail Barang">
                                            <div class="input-group">
                                                <label>Barang</label>
                                                <select name="items[{{ $index }}][nama]" class="select-barang" required>
                                                    <option value="">Pilih Barang</option>
                                                    @foreach ($barangList as $barang)
                                                    <option value="{{ $barang->kode_barang }}" 
                                                            data-stok="{{ $barang->jumlah }}"
                                                            {{ old("items.$index.nama") == $barang->kode_barang ? 'selected' : '' }}>
                                                        {{ $barang->nama_barang }} ({{ $barang->kode_barang }})
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="input-group">
                                                <label>Jumlah</label>
                                                <input type="number" name="items[{{ $index }}][jumlah]" class="jumlah" min="1" required 
                                                       value="{{ old("items.$index.jumlah", 1) }}">
                                            </div>
                                            <div class="input-group">
                                                <label>Harga Satuan</label>
                                                <input type="text" name="items[{{ $index }}][harga]" class="harga" min="0" required placeholder="0"
                                                       value="{{ old("items.$index.harga", 0) }}" oninput="formatAndCalculateSubtotal(this)">
                                            </div>
                                            <div class="input-group">
                                                <label>Subtotal</label>
                                                <div class="subtotal-display" id="subtotal-{{ $index }}">Rp 0</div>
                                                <input type="hidden" name="items[{{ $index }}][subtotal]" class="subtotal" value="{{ old("items.$index.subtotal", 0) }}">
                                            </div>
                                        </td>
                                        <td data-label="Aksi" style="width: 60px;">
                                            <button type="button" onclick="removeIncomeItem(this)" class="btn btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @php $initialIndex = max($initialIndex, $index); @endphp
                                @endforeach
                            </tbody>
                        </table>

                        <div class="text-center mb-4">
                            <button type="button" onclick="addIncomeItem()" class="btn btn-secondary">
                                <i class="fas fa-plus mr-2"></i>Tambah Barang
                            </button>
                        </div>

                        <div class="total-display">
                            <strong>Total Pemasukan:</strong> <span id="income-total">Rp 0</span>
                            <input type="hidden" name="total_harga" id="total_harga" value="{{ old('total_harga', 0) }}">
                        </div>

                        {{-- Receipt Photo --}}
                        <div class="section-title">
                            <i class="fas fa-camera"></i>
                            <span>Foto Struk</span>
                        </div>

                        <div class="file-upload-wrapper">
                            <input type="file" name="foto_struk" id="foto_struk" accept="image/*" class="file-upload-input" onchange="previewUploadedImage(this, 'income')">
                            <label for="foto_struk" class="file-upload-label" id="file-upload-label">
                                <div class="file-upload-content text-center">
                                    <div class="file-upload-icon">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                    </div>
                                    <h4 class="file-upload-title">Upload Foto Struk</h4>
                                    <p class="file-upload-description">Seret & lepas file di sini atau klik untuk memilih</p>
                                    <p class="file-upload-requirements">Format: JPG, PNG (Maks. 2MB)</p>
                                </div>
                                <div class="file-preview-container hidden" id="file-preview-container">
                                    <img id="preview-image" src="#" alt="Preview" class="file-preview-image">
                                    <div class="file-preview-info">
                                        <span id="file-name" class="file-name"></span>
                                        <button type="button" onclick="removePhoto()" class="remove-file-btn">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="button-group">
                            <a href="{{ route('struks.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left mr-2"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-2"></i>Simpan Pemasukan
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Expense Tab Content --}}
                <div id="expense-tab" class="tab-content">
                    <form action="{{ route('pengeluarans.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="section-title">
                            <i class="fas fa-money-bill-wave"></i>
                            <span>Buat Pengeluaran</span>
                        </div>

                        <div class="form-grid">
                            <div class="input-group">
                                <label for="expense_nama_spk">
                                    <i class="fas fa-file-signature mr-1"></i>
                                    Nama SPK
                                </label>
                                <input type="text" name="nama_spk" id="expense_nama_spk" placeholder="Nama SPK akan terisi otomatis" readonly>
                            </div>

                            <div class="input-group">
                                <label for="pegawai_id">
                                    <i class="fas fa-user-tie mr-1"></i>
                                    Pegawai
                                </label>
                                <select name="pegawai_id" id="pegawai_id" class="form-control" required>
                                    <option value="">Pilih Pegawai</option>
                                    @foreach ($pegawais as $pegawai)
                                    <option value="{{ $pegawai->id }}" data-divisi="{{ $pegawai->divisi }}">{{ $pegawai->nama }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="input-group">
                                <label for="divisi_display">
                                    <i class="fas fa-building mr-1"></i>
                                    Divisi
                                </label>
                                <input type="text" id="divisi_display" placeholder="Divisi akan terisi otomatis" readonly>
                            </div>

                            <div class="input-group">
                                <label for="tanggal">
                                    <i class="fas fa-calendar-alt mr-1"></i>
                                    Tanggal Keluar
                                </label>
                                <input type="date" name="tanggal" id="tanggal" required value="{{ old('tanggal', date('Y-m-d')) }}" style="color-scheme: dark;">
                            </div>

                            <div class="input-group">
                                <label for="expense_nomor_struk">
                                    <i class="fas fa-receipt mr-1"></i>
                                    Nomor Struk
                                </label>
                                <input type="text" name="nomor_struk" id="expense_nomor_struk" placeholder="Nomor struk akan terisi otomatis" readonly>
                            </div>

                            <div class="input-group">
                                <label for="status_pengeluaran">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Status
                                </label>
                                <select name="status" id="status_pengeluaran" class="form-control" required>
                                    <option value="progress">Progress</option>
                                    <option value="completed">Completed</option>
                                </select>
                            </div>
                        </div>

                        {{-- Items --}}
                        <div class="section-title">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Daftar Barang</span>
                        </div>

                        {{-- PERBAIKAN: Tabel Pengeluaran dibuat sama persis dengan Pemasukan --}}
                        <table class="item-table expense-table">
                            <thead>
                                <tr>
                                    <th>Detail Barang</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="expense-items-container">
                                <tr class="item-row" data-item="0">
                                    <td data-label="Detail Barang">
                                        <div class="input-group">
                                            <label>Barang</label>
                                            <select name="items[0][nama]" class="select-barang" required>
                                                <option value="">Pilih Barang</option>
                                                @foreach ($barangList as $barang)
                                                    {{-- OPSI C: HANYA TAMPILKAN BARANG YANG STOKNYA > 0 --}}
                                                    @if($barang->jumlah > 0)
                                                    <option value="{{ $barang->kode_barang }}" 
                                                            data-stok="{{ $barang->jumlah }}"
                                                            data-harga="{{ $barang->harga_satuan ?? $barang->harga ?? 0 }}">
                                                        {{ $barang->nama_barang }} ({{ $barang->kode_barang }}) – Stok: {{ $barang->jumlah }}
                                                    </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="input-group">
                                            <label>Jumlah</label>
                                            <input type="number" name="items[0][jumlah]" class="jumlah" min="1" value="1" required>
                                        </div>
                                        <div class="input-group">
                                            <label>Harga Satuan</label>
                                            <input type="text" name="items[0][harga]" class="harga" min="0" required placeholder="0" value="0" oninput="formatAndCalculateExpenseSubtotal(this)">
                                        </div>
                                        <div class="input-group">
                                            <label>Subtotal</label>
                                            <div class="subtotal-display" id="expense-subtotal-0">Rp 0</div>
                                            <input type="hidden" name="items[0][subtotal]" class="subtotal" value="0">
                                        </div>
                                    </td>
                                    <td data-label="Aksi" style="width: 60px;">
                                        <button type="button" onclick="removeExpenseItem(this)" class="btn btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="text-center mb-4">
                            <button type="button" onclick="addExpenseItem()" class="btn btn-secondary">
                                <i class="fas fa-plus mr-2"></i>Tambah Barang
                            </button>
                        </div>

                        <div class="total-display">
                            <strong>Total Pengeluaran:</strong> <span id="expense-total">Rp 0</span>
                            <input type="hidden" name="total_harga" id="expense_total_harga" value="0">
                        </div>

                        {{-- Receipt Photo --}}
                        <div class="section-title">
                            <i class="fas fa-camera"></i>
                            <span>Foto Bukti Pembayaran</span>
                        </div>

                        <div class="file-upload-wrapper">
                            <input type="file" name="bukti_pembayaran" id="bukti_pembayaran" accept="image/*" class="file-upload-input" onchange="previewUploadedImage(this, 'expense')">
                            <label for="bukti_pembayaran" class="file-upload-label" id="expense-file-upload-label">
                                <div class="file-upload-content text-center">
                                    <div class="file-upload-icon">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                    </div>
                                    <h4 class="file-upload-title">Upload Foto Bukti Pembayaran</h4>
                                    <p class="file-upload-description">Seret & lepas file di sini atau klik untuk memilih</p>
                                    <p class="file-upload-requirements">Format: JPG, PNG (Maks. 2MB)</p>
                                </div>
                                <div class="file-preview-container hidden" id="expense-file-preview-container">
                                    <img id="expense-preview-image" src="#" alt="Preview" class="file-preview-image">
                                    <div class="file-preview-info">
                                        <span id="expense-file-name" class="file-name"></span>
                                        <button type="button" onclick="removeExpensePhoto()" class="remove-file-btn">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="button-group">
                            <a href="{{ route('pengeluarans.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left mr-2"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-2"></i>Simpan Pengeluaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Image Modal --}}
<div class="modal-overlay" id="imageModal">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="modalImageTitle">Gambar</h5>
            <button type="button" class="modal-close" onclick="closeImageModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body text-center">
            <img id="modalImageContent" src="" alt="Gambar Struk" class="modal-image">
        </div>
    </div>
</div>

<script>
    let incomeIndex = {{ $initialIndex + 1 }}; 
    let expenseItemIndex = 1; 
    
    // ===============================================
    // FUNGSI UTILITY (Format Rupiah, Clean Price, DLL)
    // ===============================================

    function formatRupiah(angka) {
        const number = parseFloat(angka);
        if (isNaN(number)) return 'Rp 0';
        
        const number_string = number.toFixed(0).toString().replace(/[^,\d]/g, ''),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        let result = '';
        if (ribuan) {
            separator = sisa ? '.' : '';
            result += separator + ribuan.join('.');
        }

        result = split[1] != undefined ? result + ',' + split[1] : result;
        return 'Rp ' + (rupiah + result);
    }
    
    function cleanPriceValue(value) {
        if (typeof value === 'string') {
            return value.replace(/[^0-9]/g, '');
        }
        return value.toString().replace(/[^0-9]/g, '');
    }

    function calculateOptimalDropdownWidth(element) {
        const $element = $(element);
        const containerWidth = $element.closest('.input-group').width();
        return containerWidth;
    }

    // ===============================================
    // FUNGSI UMUM (PREVIEW, MODAL)
    // ===============================================

    function previewUploadedImage(input, type) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(e) {
                let previewId, fileNameId, containerId, labelId;

                if (type === 'income') {
                    previewId = '#preview-image';
                    fileNameId = '#file-name';
                    containerId = '#file-preview-container';
                    labelId = '#file-upload-label';
                } else {
                    previewId = '#expense-preview-image';
                    fileNameId = '#expense-file-name';
                    containerId = '#expense-file-preview-container';
                    labelId = '#expense-file-upload-label';
                }

                $(previewId).attr('src', e.target.result);
                $(fileNameId).text(file.name);
                $(containerId).removeClass('hidden');
                $(labelId).addClass('has-file');
            };
            reader.readAsDataURL(file);
        }
    }

    function removePhoto() {
        $('#foto_struk').val('');
        $('#preview-image').attr('src', '#');
        $('#file-name').text('');
        $('#file-preview-container').addClass('hidden');
        $('#file-upload-label').removeClass('has-file');
    }

    function removeExpensePhoto() {
        $('#bukti_pembayaran').val('');
        $('#expense-preview-image').attr('src', '#');
        $('#expense-file-name').text('');
        $('#expense-file-preview-container').addClass('hidden');
        $('#expense-file-upload-label').removeClass('has-file');
    }

    function openImageModal(imageUrl, title) {
        if (!imageUrl) return;
        $('#modalImageContent').attr('src', imageUrl);
        $('#modalImageTitle').text(title || 'Gambar');
        $('#imageModal').addClass('active');
        $('body').css('overflow', 'hidden');
    }

    function closeImageModal() {
        $('#imageModal').removeClass('active');
        $('#modalImageContent').attr('src', '');
        $('#modalImageTitle').text('');
        $('body').css('overflow', 'auto');
    }

    function initializeSelect2ForElement(element) {
        const $element = $(element);
        const isProgressSelect = $element.is('#status, #status_pengeluaran');

        let options = {
            width: '100%',
            closeOnSelect: true,
            dropdownParent: $('body'),
            dropdownCssClass: 'select2-dropdown-dark'
        };

        if (isProgressSelect) {
            options.minimumResultsForSearch = Infinity;
            options.placeholder = "Pilih Status";
        } else if ($element.is('#pegawai_id')) {
            options.placeholder = "Pilih Pegawai";
        } else if ($element.hasClass('select-barang')) {
            options.placeholder = "Pilih Barang";
        }

        $element.select2(options);
    }
    
    // ===============================================
    // FUNGSI KHUSUS PEMASUKAN (Income)
    // ===============================================
    
    function formatAndCalculateSubtotal(input) {
        const row = $(input).closest('.item-row');
        let cleanedValue = cleanPriceValue(input.value);
        let numericValue = parseFloat(cleanedValue) || 0;
        
        if (cleanedValue) {
            input.value = numericValue.toLocaleString('id-ID');
        } else {
            input.value = '';
        }

        row.data('price-value', numericValue);
        updateIncomeSubtotal(row);
    }
    
    function updateIncomeSubtotal(row) {
        const itemId = row.data('item');
        const quantity = parseFloat(row.find('.jumlah').val()) || 0;
        const price = parseFloat(row.data('price-value')) || 0;
        const subtotal = quantity * price;

        $(`#subtotal-${itemId}`).text(formatRupiah(subtotal));
        row.find('.subtotal').val(subtotal);
        updateIncomeTotal();
    }

    function updateIncomeTotal() {
        let total = 0;
        $('#income-items-container .subtotal').each(function() {
            total += parseFloat($(this).val()) || 0;
        });

        $('#total_harga').val(total);
        $('#income-total').text(formatRupiah(total));
    }

    function addIncomeItem() {
        const container = document.getElementById('income-items-container');
        if (!container) return;

        const lastRow = $('#income-items-container .item-row').last();
        if (lastRow.length) {
            const lastIndex = parseInt(lastRow.data('item'));
            incomeIndex = lastIndex + 1;
        } else {
            incomeIndex = 0;
        }
        
        const template = `
            <tr class="item-row" data-item="${incomeIndex}">
                <td data-label="Detail Barang">
                    <div class="input-group">
                        <label>Barang</label>
                        <select name="items[${incomeIndex}][nama]" class="select-barang" required>
                            <option value="">Pilih Barang</option>
                            @foreach ($barangList as $barang)
                            <option value="{{ $barang->kode_barang }}" data-stok="{{ $barang->jumlah }}">
                                {{ $barang->nama_barang }} ({{ $barang->kode_barang }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="input-group">
                        <label>Jumlah</label>
                        <input type="number" name="items[${incomeIndex}][jumlah]" class="jumlah" min="1" value="1" required>
                    </div>
                    <div class="input-group">
                        <label>Harga Satuan</label>
                        <input type="text" name="items[${incomeIndex}][harga]" class="harga" min="0" required placeholder="0" value="0" oninput="formatAndCalculateSubtotal(this)">
                    </div>
                    <div class="input-group">
                        <label>Subtotal</label>
                        <div class="subtotal-display" id="subtotal-${incomeIndex}">Rp 0</div>
                        <input type="hidden" name="items[${incomeIndex}][subtotal]" class="subtotal" value="0">
                    </div>
                </td>
                <td data-label="Aksi" style="width: 60px;">
                    <button type="button" onclick="removeIncomeItem(this)" class="btn btn-danger">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;

        const $newRow = $(template);
        $(container).append($newRow);
        
        const select = $newRow.find('.select-barang')[0];
        initializeSelect2ForElement(select);

        $newRow.find('.jumlah').on('input', function() {
            updateIncomeSubtotal($newRow);
        });
        
        incomeIndex++;
        updateIncomeSubtotal($newRow);
    }


    function removeIncomeItem(button) {
        const row = $(button).closest('.item-row');
        const container = document.getElementById('income-items-container');
        if (container.querySelectorAll('.item-row').length > 1) {
            row.fadeOut(300, function() {
                row.remove();
                updateIncomeTotal();
            });
        } else {
            if (typeof Swal !== 'undefined') {
                 Swal.fire({ title: 'Aksi Diblokir', text: 'Minimal harus ada satu item barang.', icon: 'warning', background: '#1F2937', color: '#F9FAFB' });
            } else {
                alert('Minimal harus ada satu item barang.');
            }
        }
    }
    
    // ===============================================
    // FUNGSI KHUSUS PENGELUARAN (Expense)
    // ===============================================

    // [BARU] Fungsi Wrapper untuk Input Harga Pengeluaran
    function formatAndCalculateExpenseSubtotal(input) {
        const row = $(input).closest('.item-row');
        let cleanedValue = cleanPriceValue(input.value); 
        let numericValue = parseFloat(cleanedValue) || 0;
        
        if (cleanedValue) {
            input.value = numericValue.toLocaleString('id-ID');
        } else {
            input.value = '';
        }

        row.data('price-value', numericValue);
        updateExpenseSubtotal(row);
    }

    // [BARU] Hitung Subtotal per Baris Pengeluaran
    function updateExpenseSubtotal(row) {
        const itemId = row.data('item');
        const quantity = parseFloat(row.find('.jumlah').val()) || 0;
        const price = parseFloat(row.data('price-value')) || 0; // Ambil dari data-row
        const subtotal = quantity * price;

        $(`#expense-subtotal-${itemId}`).text(formatRupiah(subtotal));
        row.find('.subtotal').val(subtotal);
        
        updateExpenseTotal();
    }

    // [BARU] Update Total Keseluruhan Pengeluaran
    function updateExpenseTotal() {
        let total = 0;
        $('#expense-items-container .subtotal').each(function() {
            total += parseFloat($(this).val()) || 0;
        });

        const formatted = total.toLocaleString('id-ID');
        $('#expense-total').text('Rp ' + formatted);
        $('#expense_total_harga').val(total);
    }

    function updateStokExpense(selectElement) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        if (!selectedOption) return; 

        const stokAsli = parseInt(selectedOption.getAttribute('data-stok')) || 0;
        const row = $(selectElement).closest('.item-row');
        const jumlahInput = row.find('.jumlah')[0];
        let stokInfo = row.find('.stok-info')[0];

        if (!stokInfo) {
            const jumlahInputGroup = jumlahInput.closest('.input-group');
            stokInfo = document.createElement('span');
            stokInfo.classList.add('stok-info');
            $(jumlahInputGroup).append(stokInfo);
        }
        
        $(jumlahInput).attr('max', stokAsli);

        const jumlah = parseInt(jumlahInput.value || 0);
        const sisa = stokAsli - jumlah;

        stokInfo.textContent = `Stok: ${sisa >= 0 ? sisa : 0}`;

        if (jumlahInput.stockUpdateHandler) {
            $(jumlahInput).off('input', jumlahInput.stockUpdateHandler);
        }

        jumlahInput.stockUpdateHandler = function() {
            const inputJumlah = parseInt(this.value) || 0;
            
            if (inputJumlah > stokAsli) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ title: 'Stok Tidak Cukup', text: `Stok tersedia hanya ${stokAsli}.`, icon: 'error', background: '#1F2937', color: '#F9FAFB' });
                } else {
                    alert(`Stok Tidak Cukup. Stok tersedia hanya ${stokAsli}.`);
                }
                this.value = stokAsli; 
            }
            
            const sisaBaru = stokAsli - parseInt(this.value || 0);
            stokInfo.textContent = `Stok: ${sisaBaru >= 0 ? sisaBaru : 0}`;
            
            // Hitung ulang subtotal
            updateExpenseSubtotal(row);
        };

        $(jumlahInput).on('input', jumlahInput.stockUpdateHandler);
        
        // Panggil handler sekali untuk inisialisasi visual
        jumlahInput.stockUpdateHandler();
    }
    
    // [BARU] Fungsi untuk menambah baris item baru di Pengeluaran
    function addExpenseItem() {
        const container = $('#expense-items-container');
        if (!container.length) return;

        const newItemIndex = expenseItemIndex; 

        // Template disesuaikan agar sama persis dengan Pemasukan
        const template = `
            <tr class="item-row" data-item="${newItemIndex}">
                <td data-label="Detail Barang">
                    <div class="input-group">
                        <label>Barang</label>
                        <select name="items[${newItemIndex}][nama]" class="select-barang" required>
                            <option value="">Pilih Barang</option>
                            @foreach ($barangList as $barang)
                                {{-- OPSI C: HANYA TAMPILKAN BARANG YANG STOKNYA > 0 --}}
                                @if($barang->jumlah > 0)
                                <option value="{{ $barang->kode_barang }}" 
                                        data-stok="{{ $barang->jumlah }}"
                                        data-harga="{{ $barang->harga_satuan ?? $barang->harga ?? 0 }}">
                                    {{ $barang->nama_barang }} ({{ $barang->kode_barang }}) – Stok: {{ $barang->jumlah }}
                                </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="input-group">
                        <label>Jumlah</label>
                        <input type="number" name="items[${newItemIndex}][jumlah]" class="jumlah" min="1" value="1" required>
                    </div>
                    <div class="input-group">
                        <label>Harga Satuan</label>
                        <input type="text" name="items[${newItemIndex}][harga]" class="harga" min="0" required placeholder="0" value="0" oninput="formatAndCalculateExpenseSubtotal(this)">
                    </div>
                    <div class="input-group">
                        <label>Subtotal</label>
                        <div class="subtotal-display" id="expense-subtotal-${newItemIndex}">Rp 0</div>
                        <input type="hidden" name="items[${newItemIndex}][subtotal]" class="subtotal" value="0">
                    </div>
                </td>
                <td data-label="Aksi" style="width: 60px;">
                    <button type="button" onclick="removeExpenseItem(this)" class="btn btn-danger">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;

        const $newRow = $(template);
        container.append($newRow);
        
        const select = $newRow.find('.select-barang')[0];
        initializeSelect2ForElement(select);
        const row = $newRow;

        // === LOGIKA AUTOFILL HARGA DYNAMIC ROW ===
        $(select).on('change', function() {
            updateStokExpense(this);
            
            const selectedOption = this.options[this.selectedIndex];
            const hargaDasar = parseFloat(selectedOption.getAttribute('data-harga')) || 0;
            const hargaInput = row.find('.harga');
            
            // 1. Set harga otomatis ke input (dengan format Rupiah)
            hargaInput.val(hargaDasar.toLocaleString('id-ID'));
            
            // 2. Simpan nilai numerik (harga bersih) di data row
            row.data('price-value', hargaDasar);
            
            // 3. Hitung ulang
            updateExpenseSubtotal(row);
        });
        // =========================================
        
        // Event Listener Ganti Jumlah & Harga Manual
        $newRow.find('.jumlah').on('input', function() {
            updateStokExpense(select);
            updateExpenseSubtotal(row);
        });
        
        $newRow.find('.harga').on('input', function() {
             formatAndCalculateExpenseSubtotal(this); // Dipanggil juga dari oninput HTML
        });

        expenseItemIndex++;
        updateExpenseTotal();
    }

    function removeExpenseItem(button) {
        const row = $(button).closest('.item-row');
        const container = document.getElementById('expense-items-container');
        if (container.querySelectorAll('.item-row').length > 1) {
            row.fadeOut(300, function() {
                row.remove();
                updateExpenseTotal(); // [PERBAIKAN] Hitung ulang total setelah hapus
            });
        } else {
            if (typeof Swal !== 'undefined') {
                 Swal.fire({ title: 'Aksi Diblokir', text: 'Minimal harus ada satu item barang.', icon: 'warning', background: '#1F2937', color: '#F9FAFB' });
            } else {
                 alert('Minimal harus ada satu item barang.');
            }
        }
    }


    function initSelect2() {
        $('.select-barang').each(function() {
            initializeSelect2ForElement(this);
        });

        if ($('#pegawai_id').length) {
            initializeSelect2ForElement($('#pegawai_id')[0]);
        }
        if ($('#status').length) {
            initializeSelect2ForElement($('#status')[0]);
        }
        if ($('#status_pengeluaran').length) {
            initializeSelect2ForElement($('#status_pengeluaran')[0]);
        }
    }

    function initIncomeRowListeners() {
        $('#income-items-container .item-row').each(function() {
            const row = $(this);
            const hargaInput = row.find('.harga')[0];
            
            if (hargaInput) {
                let initialValue = $(hargaInput).val();
                let cleanedValue = cleanPriceValue(initialValue);
                let numericValue = parseFloat(cleanedValue) || 0;
                
                if (cleanedValue) {
                    hargaInput.value = numericValue.toLocaleString('id-ID');
                } else {
                    hargaInput.value = '';
                }
                
                row.data('price-value', numericValue);
            }
            
            row.find('.jumlah').off('input').on('input', function() {
                updateIncomeSubtotal(row);
            });
            
            updateIncomeSubtotal(row); 
        });
    }


    $(document).ready(function() {
        initSelect2();
        initIncomeRowListeners();

        // === INIALISASI AUTOFILL HARGA UNTUK BARIS PERTAMA PENGELUARAN ===
        const firstExpenseRow = $('#expense-items-container .item-row[data-item="0"]');
        if (firstExpenseRow.length) {
            const firstSelect = firstExpenseRow.find('.select-barang');
            const firstJumlah = firstExpenseRow.find('.jumlah');
            const firstHarga = firstExpenseRow.find('.harga');

            // Event saat ganti barang di baris pertama
            firstSelect.on('change', function() {
                const row = firstExpenseRow;
                updateStokExpense(this);
                
                const selectedOption = this.options[this.selectedIndex];
                const hargaDasar = parseFloat(selectedOption.getAttribute('data-harga')) || 0;
                
                // 1. Set harga otomatis ke input (dengan format Rupiah)
                firstHarga.val(hargaDasar.toLocaleString('id-ID'));
                
                // 2. Simpan nilai numerik (harga bersih) di data row
                row.data('price-value', hargaDasar);
                
                // 3. Hitung ulang
                updateExpenseSubtotal(row);
            });

            // Event saat ganti jumlah di baris pertama
            firstJumlah.on('input', function() {
                updateStokExpense(firstSelect[0]);
                updateExpenseSubtotal(firstExpenseRow);
            });
            
            // Event saat ganti harga manual di baris pertama
            firstHarga.on('input', function() {
                formatAndCalculateExpenseSubtotal(this);
            });
            
            // Jika baris pertama sudah terisi dari old() saat load, trigger change
            if (firstSelect.val()) {
                firstSelect.trigger('change');
            } else {
                // Pastikan total terhitung Rp 0 di awal jika belum ada barang
                updateExpenseTotal();
            }
        }
        // ====================================================================

        $('.tab-button').on('click', function(e) {
            e.preventDefault();
            const tabId = $(this).data('tab');
            $('.tab-button').removeClass('active');
            $(this).addClass('active');
            $('.tab-content').removeClass('active');
            $('#' + tabId).addClass('active');
            
            // [PERBAIKAN] Hitung total saat pindah ke tab Pengeluaran
            if (tabId === 'expense-tab') {
                setTimeout(() => {
                    const selectElement = $('#expense-items-container .item-row[data-item="0"] .select-barang')[0];
                    if (selectElement && selectElement.value) {
                        // Hanya trigger kalau sudah ada barang terpilih
                        $(selectElement).trigger('change'); 
                    } else {
                        updateExpenseTotal();
                    }
                }, 100);
            }
        });
        
        // ... (Logika Image Modal, Select2 Blur, dan Pegawai/SPK/Struk Generation tetap sama) ...
        
        $('#imageModal').click(function(e) {
            if (e.target === this) {
                closeImageModal();
            }
        });
        
        // Logika Generate SPK dan Nomor Struk
        if ($('#pegawai_id').length) {
            $('#pegawai_id').on('change', function() {
                const pegawaiId = $(this).val();
                const divisi = $(this).find(':selected').data('divisi');

                $('#divisi_display').val(divisi || '');
                $('#expense_nama_spk').val('');
                $('#expense_nomor_struk').val('');

                if (pegawaiId) {
                    $.ajax({
                        url: '/generate-spk',
                        method: 'POST',
                        data: {
                            pegawai_id: pegawaiId,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            $('#expense_nama_spk').val(response.nama_spk || 'Tidak ada nama SPK');
                            $('#divisi_display').val(response.divisi || 'Tidak diketahui');
                        },
                        error: function(xhr) {
                            console.error('Error generating SPK:', xhr.responseText);
                            $('#expense_nama_spk').val('Gagal generate Nama SPK');
                            $('#divisi_display').val('Gagal memuat divisi');
                        }
                    });

                    $.ajax({
                        url: '/pengeluarans/generate-nomor-struk',
                        method: 'GET',
                        data: { pegawai_id: pegawaiId },
                        success: function(response) {
                            $('#expense_nomor_struk').val(response.nomor_struk || 'Tidak ada nomor struk');
                        },
                        error: function(xhr) {
                            console.error('Error generating nomor struk:', xhr.responseText);
                            $('#expense_nomor_struk').val('Gagal generate nomor struk');
                        }
                    });
                }
            });
        }

        $(document).on('select2:close', '.select-barang, #pegawai_id, #status', function() {
            $(this).blur();
        });

        $(document).on('click', function(e) {
            if (!$(e.target).closest('.select2-container, .select2-dropdown').length) {
                $('.select2-container--open').find('.select2-selection').trigger('blur');
            }
        });

        $(window).on('resize', function() {
            $('.select-barang').each(function() {
                if ($(this).hasClass('select2-hidden-accessible')) {
                    const optimalWidth = calculateOptimalDropdownWidth(this);
                    $(this).data('optimal-width', optimalWidth);
                }
            });
        });
    });
</script>
@endsection
