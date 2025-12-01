<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman profil user yang sedang login.
     */
    public function show()
    {
        $user = Auth::user()->load('pegawai.divisi');
        return view('layouts.profile', compact('user'));
    }

    /**
     * Memperbarui data profil user.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        // Update nama
        $user->name = $request->name;

        // Jika user mengisi password baru
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // Logout agar user login ulang dengan data baru
        Auth::logout();

        // Invalidate session lama
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect ke halaman login dengan pesan sukses
        return redirect()
            ->route('login')
            ->with('success', 'Profil berhasil diperbarui. Silakan login kembali.');
    }
}