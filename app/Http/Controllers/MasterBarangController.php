<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;

class MasterBarangController extends Controller
{
    public function index(Request $request)
    {
        $query = Barang::query();

        if ($request->filled('q')) {
            $query->where('nama_barang', 'like', '%' . $request->q . '%');
        }

        $barangs = $query->get();

        return view('master_barang.index', compact('barangs'))->with('success', 'Barang berhasil ditambahkan');
    }

    public function create()
    {
        return view('master_barang.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255|unique:master_barangs,nama_barang',
            'jumlah' => 'required|integer|min:1',
        ]);

        // Generate kode unik
        $namaBarang = strtoupper($request->nama_barang);
        $words = preg_split('/[\s\-_,.]+/', $namaBarang);
        $inisial = '';

        foreach ($words as $word) {
            if (!is_numeric($word)) {
                $inisial .= substr($word, 0, 1);
            }
        }

        $timestamp = now()->format('ymdHis');
        $randomStr = strtoupper(substr(bin2hex(random_bytes(2)), 0, 3));
        $kodeBarang = $inisial . '-' . $timestamp . '-' . $randomStr;

        Barang::create([
            'nama_barang' => $request->nama_barang,
            'kode_barang' => $kodeBarang,
            'jumlah' => $request->jumlah,
        ]);

        return $request->action === 'save_and_continue'
            ? redirect()->back()->with('created', 'Barang berhasil ditambahkan. Silakan tambah lagi.')
            : redirect()->route('master-barang.index')->with('created', 'Barang berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $barang = Barang::findOrFail($id);
        return view('master_barang.edit', compact('barang'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255|unique:master_barangs,nama_barang,' . $id,
            'jumlah' => 'required|integer|min:1',
        ]);

        $barang = Barang::findOrFail($id);

        $namaBarang = strtoupper($request->nama_barang);
        $words = preg_split('/[\s\-_,.]+/', $namaBarang);
        $inisial = '';

        foreach ($words as $word) {
            if (!is_numeric($word)) {
                $inisial .= substr($word, 0, 1);
            }
        }

        $timestamp = now()->format('ymdHis');
        $randomStr = strtoupper(substr(bin2hex(random_bytes(2)), 0, 3));
        $kodeBarang = $inisial . '-' . $timestamp . '-' . $randomStr;

        $barang->update([
            'nama_barang' => $request->nama_barang,
            'kode_barang' => $kodeBarang,
            'jumlah' => $request->jumlah,
        ]);

        return redirect()->route('master-barang.index')->with('updated', 'Barang berhasil diperbarui dengan kode baru.');
    }

    public function destroy(string $id)
    {
        $barang = Barang::findOrFail($id);
        $barang->delete();

        return redirect()->route('master-barang.index')->with('deleted', 'Barang berhasil dihapus.');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids');

        if ($ids) {
            Barang::whereIn('id', $ids)->delete();
            return redirect()->route('master-barang.index')->with('success', 'Barang terpilih berhasil dihapus.');
        }

        return redirect()->route('master-barang.index')->with('success', 'Tidak ada barang yang dipilih.');
    }
}
