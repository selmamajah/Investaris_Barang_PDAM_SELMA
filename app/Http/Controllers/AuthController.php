<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Pegawai;

class AuthController extends Controller
{
    // Menampilkan form login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Proses login
    public function login(Request $request)
    {
        // Validasi form login
        $credentials = $request->validate([
            'nip' => 'required|digits:8',
            'password' => 'required',
        ], [
            'nip.required' => 'NIP wajib diisi.',
            'nip.digits' => 'NIP harus 8 digit angka.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // Cari pegawai berdasarkan NIP
        $pegawai = Pegawai::where('nip', $request->nip)->with('user')->first();

// Verifikasi password dari tabel users
if ($pegawai && $pegawai->user && Hash::check($request->password, $pegawai->user->password)) {
    // Login user ke guard Laravel
    Auth::login($pegawai->user);

    // Simpan ke session tambahan
    $request->session()->regenerate();
    $request->session()->put('show_welcome', true);
    $request->session()->put('pegawai_id', $pegawai->id);
    $request->session()->put('user_id', $pegawai->user->id);

    return redirect()->intended('/dashboard');
}

// Jika gagal login
return back()->withErrors([
    'nip' => 'NIP atau password salah.',
])->withInput();
    }

    // Proses logout
    public function logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $request->session()->forget(['pegawai_id', 'user_id']);

        return redirect('/login');
    }

    public function index()
    {
        $showWelcome = session()->pull('show_welcome', false);
        return view('dashboard', compact('showWelcome'));
    }
}
