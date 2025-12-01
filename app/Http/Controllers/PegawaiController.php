<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pegawai;

class PegawaiController extends Controller
{
    public function index(Request $request)
{
    $query = Pegawai::query();

    if ($request->filled('q')) {
        $q = $request->q;
        $query->where(function ($sub) use ($q) {
            $sub->where('nama', 'like', "%$q%")
                ->orWhere('nip', 'like', "%$q%");
        });
    }

    // Urutkan berdasarkan waktu update terbaru
    $pegawais = $query->orderBy('updated_at', 'desc')->get();

    return view('pegawai.index', compact('pegawais'));
}
    public function create()
    {
        return view('pegawai.create');
    }

    public function store(Request $request)
{
    // Validasi input sebelum simpan
    $request->validate([
        'nama' => 'required|string|max:255',
        'nip' => 'required|numeric|digits_between:5,20|unique:pegawais,nip',
    ], [
        'nip.required' => 'NIP wajib diisi.',
        'nip.numeric' => 'NIP harus berupa angka.',
        'nip.digits_between' => 'NIP harus terdiri dari minimal 5 hingga maksimal 20 digit.',
        'nip.unique' => 'NIP sudah digunakan.',
    ]);

    Pegawai::create($request->only('nama', 'nip'));

    if ($request->action === 'save_and_continue') {
        return redirect()
            ->route('pegawai.create')
            ->with('created', 'Data pegawai berhasil ditambahkan. Silakan tambah data baru.');
    }

    return redirect()
        ->route('pegawai.index')
        ->with('created', 'Data pegawai berhasil ditambahkan.');
}

    public function edit(Pegawai $pegawai)
    {
        return view('pegawai.edit', compact('pegawai'));
    }

   public function update(Request $request, Pegawai $pegawai)
{
    $request->validate([
        'nama' => 'required|string|max:255',
        'nip' => 'required|numeric|digits_between:5,20|unique:pegawais,nip,' . $pegawai->id,
    ], [
        'nip.required' => 'NIP wajib diisi.',
        'nip.numeric' => 'NIP harus berupa angka.',
        'nip.digits_between' => 'NIP harus terdiri dari minimal 5 hingga maksimal 20 digit.',
        'nip.unique' => 'NIP sudah digunakan.',
    ]);

    // Simpan perubahan ke database
    $pegawai->update([
        'nama' => $request->nama,
        'nip' => $request->nip,
    ]);

    return redirect()
        ->route('pegawai.index')
        ->with('updated', 'Data pegawai berhasil diperbarui.');
}


    public function destroy(Pegawai $pegawai)
    {
        $pegawai->delete();

        return redirect()
            ->route('pegawai.index')
            ->with('deleted', 'Pegawai berhasil dihapus.');
    }

    public function bulkDelete(Request $request)
    {
        $ids = explode(',', $request->ids);
        if (!empty($ids)) {
            \App\Models\Pegawai::whereIn('id', $ids)->delete();
            return redirect()
                ->route('pegawai.index')
                ->with('deleted', 'Pegawai terpilih berhasil dihapus.');
        }
        return redirect()
            ->route('pegawai.index')
            ->with('created', 'Tidak ada pegawai yang dipilih.');
    }

    public function getDivisi($id)
{
    $pegawai = Pegawai::with('divisi')->findOrFail($id);
    return response()->json([
        'divisi' => $pegawai->divisi ? $pegawai->divisi->name : null
    ]);
}

}