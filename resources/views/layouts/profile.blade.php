@extends('layouts.app')

@section('content')
{{-- PERBAIKAN: Wrapper layout yang salah (min-h-screen, dll) DIHAPUS --}}
<div class="max-w-2xl mx-auto mb-10">
    {{-- PERBAIKAN: Kartu diubah ke dark mode --}}
    <div class="bg-gray-900 p-8 rounded-lg shadow-lg border border-gray-700">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-3xl font-bold text-white">Profil Pengguna</h2>
                <p class="text-gray-400">Kelola informasi profil Anda</p>
            </div>
            {{-- PERBAIKAN: Ikon diubah ke dark mode --}}
            <div class="bg-gray-700 p-3 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-100" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
        </div>

        @if(session('success'))
        {{-- PERBAIKAN: Notifikasi diubah ke dark mode --}}
        <div class="bg-green-900 border-l-4 border-green-500 p-4 mb-6 rounded">
            <div class="flex items-center">
                <svg class="h-5 w-5 text-green-400 mr-3" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                </svg>
                <p class="text-green-200 font-medium">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        <form action="{{ route('profile.update') }}" method="POST" autocomplete="off">
            @csrf

            <div class="grid md:grid-cols-2 gap-6">
                {{-- Nama --}}
                <div class="mb-5 md:col-span-2">
                    <label class="block text-sm font-medium text-gray-300 mb-1">Nama Lengkap</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" name="name_display" value="{{ $user->name }}"
                            class="pl-10 w-full border bg-gray-700 cursor-not-allowed border-gray-600 rounded-lg px-4 py-2 text-gray-400"
                            disabled>
                        <input type="hidden" name="name" value="{{ $user->name }}">
                    </div>
                </div>

                {{-- NIP --}}
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-300 mb-1">NIP</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" value="{{ $user->pegawai->nip ?? '-' }}"
                            class="pl-10 w-full border bg-gray-700 cursor-not-allowed border-gray-600 rounded-lg px-4 py-2 text-gray-400"
                            disabled>
                    </div>
                </div>

                {{-- Divisi --}}
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-300 mb-1">Divisi</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" value="{{ $user->pegawai->divisi->name ?? '-' }}"
                            class="pl-10 w-full border bg-gray-700 cursor-not-allowed border-gray-600 rounded-lg px-4 py-2 text-gray-400"
                            disabled>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-700 my-6"></div>

            <h3 class="text-lg font-medium text-white mb-4">Ubah Kata Sandi</h3>
            <p class="text-gray-400 text-sm mb-5">Biarkan kosong jika tidak ingin mengubah kata sandi</p>

            <div class="grid md:grid-cols-2 gap-6">
                {{-- Password Baru --}}
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-300 mb-1">Kata Sandi Baru</label>
                    <div class="relative">
                        <input type="password" name="password" id="password" autocomplete="new-password"
                            class="w-full border border-gray-600 bg-gray-800 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-gray-500 focus:border-gray-500 @error('password') border-red-500 @enderror"
                            placeholder="••••••••">
                        <button type="button" onclick="togglePassword('password')"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-200">
                            <svg id="eye-icon-password" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg"
                                fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                <path fill-rule="evenodd"
                                    d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                    @error('password')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Konfirmasi Password --}}
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-300 mb-1">Konfirmasi Kata Sandi</label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" id="password_confirmation" autocomplete="new-password"
                            class="w-full border border-gray-600 bg-gray-800 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                            placeholder="••••••••">
                        <button type="button" onclick="togglePassword('password_confirmation')"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-200">
                            <svg id="eye-icon-password_confirmation" class="h-5 w-5"
                                xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                <path fill-rule="evenodd"
                                    d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <a href="{{ route('dashboard') }}"
                    class="mr-3 px-4 py-2 border border-gray-600 rounded-lg text-gray-300 hover:bg-gray-700">
                    Batal
                </a>
                <button type="submit"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-900 px-6 py-2 rounded-lg shadow-sm flex items-center font-medium">
                    <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                            clip-rule="evenodd" />
                    </svg>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const eyeIcon = document.getElementById(`eye-icon-${fieldId}`);
        if (field.type === 'password') {
            field.type = 'text';
            eyeIcon.innerHTML = `
                <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd" />
                <path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z" />
            `;
        } else {
            field.type = 'password';
            eyeIcon.innerHTML = `
                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
            `;
        }
    }
</script>
@endsection