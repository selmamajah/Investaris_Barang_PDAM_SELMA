<?php

namespace App\Http\Controllers;

use App\Models\MasterBarang;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    // Tampilkan form tambah master barang
    public function masterBarangCreate()
    {
        return view('master_barang.create');
    }

    // Simpan data master barang
    public function masterBarangStore(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255',
        ]);

        MasterBarang::create([
            'nama_barang' => $request->nama_barang,
        ]);

        return redirect()->back()->with('success', 'Barang berhasil ditambahkan.');
    }
}
