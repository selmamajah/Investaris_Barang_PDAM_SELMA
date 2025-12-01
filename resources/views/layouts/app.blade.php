<!DOCTYPE html>
<html lang="id" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- PERUBAHAN: Meta theme-color untuk browser chrome di mobile --}}
    <meta name="theme-color" content="#030712"> 
    
    <title>Gudang Inventaris Barang</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
        
        /* PERUBAHAN: Menghilangkan "white flash" dan menambah "fade-in" */
        html, body {
            background-color: #030712; /* Tailwind 'bg-gray-950' */
        }
        .fade-in {
            animation: fadeInAnimation 0.3s ease-out;
        }
        @keyframes fadeInAnimation {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        /* --- Selesai Perubahan --- */

        body, * { font-family: 'Inter', sans-serif; }
        
        /* Style SweetAlert untuk Dark Mode */
        .swal2-toast {
            animation: slideInRight 0.5s ease-out, fadeOut 0.5s ease-out 2.5s forwards !important;
            border-left: 4px solid;
            border-radius: 0.375rem !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
            background: #1F2937 !important; /* bg-gray-800 */
            color: #F9FAFB !important; /* text-gray-100 */
        }
        .swal2-popup {
            background: #1F2937 !important; /* bg-gray-800 */
            color: #F9FAFB !important; /* text-gray-100 */
        }
        .swal2-title {
            color: #F9FAFB !important; /* text-gray-100 */
        }
        .swal2-html-container {
            color: #D1D5DB !important; /* text-gray-300 */
        }
        /* --- */

        .swal2-toast.swal2-success { border-left-color: #10B981 !important; }
        .swal2-toast.swal2-info { border-left-color: #3B82F6 !important; }
        .swal2-toast.swal2-warning { border-left-color: #EF4444 !important; }
        .swal2-toast.swal2-error { border-left-color: #DC2626 !important; }
        @keyframes slideInRight { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        @keyframes fadeOut { from { opacity: 1; } to { opacity: 0; } }
        .sidebar-nav::-webkit-scrollbar { display: none; }
        .sidebar-nav { -ms-overflow-style: none; scrollbar-width: none; }

        /* Mencegah FOUC (Flash of Unstyled Content) saat Alpine memuat */
        [x-cloak] { display: none !important; }
    </style>
    @stack('styles')
</head>

{{-- PERUBAHAN: Ditambahkan class 'fade-in' --}}
<body class="min-h-screen bg-gray-950 fade-in" 
    x-data="{ 
        isDesktop: window.innerWidth >= 1024,
        sidebarOpen: window.innerWidth >= 1024, 
        mobileSidebarOpen: false,
        handleResize() {
            this.isDesktop = window.innerWidth >= 1024;
            if (this.isDesktop) {
                this.mobileSidebarOpen = false;
            } else {
                this.sidebarOpen = true; // Di mobile, sidebar selalu 'expanded' (full width)
            }
        }
    }"
    @resize.window.debounce.150ms="handleResize"
    x-init="handleResize">

    {{-- Overlay Mobile --}}
    <div x-show="mobileSidebarOpen" 
         @click="mobileSidebarOpen = false" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/60 z-40 lg:hidden" x-cloak></div>

    <div class="flex min-h-screen">
        
        {{-- Sidebar --}}
        <aside
            class="bg-gray-900 text-gray-300 flex flex-col h-screen overflow-hidden fixed lg:sticky inset-y-0 left-0 z-50 transform lg:translate-x-0 transition-all duration-300 ease-in-out"
            :class="{
                'w-64': true,
                'lg:w-20': !sidebarOpen && isDesktop,
                'translate-x-0': mobileSidebarOpen,
                '-translate-x-full': !mobileSidebarOpen && !isDesktop
            }">
            
            <div class="flex flex-col flex-1 overflow-y-auto sidebar-nav">
                
                {{-- Header Sidebar --}}
                <div class="flex items-center justify-between h-16 border-b border-gray-700" :class="sidebarOpen ? 'px-4' : 'px-3'">
                    
                    <a href="{{ url('/dashboard') }}" class="flex items-center gap-3" x-show="sidebarOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-cloak>
                        <div class="flex-shrink-0 p-2 bg-gray-800 rounded-lg">
                            <i class="fa-solid fa-boxes-stacked w-6 h-6 text-white text-center leading-6"></i>
                        </div>
                        <div class="overflow-hidden">
                            <h1 class="text-xl font-bold text-white">
                                <span class="block">Inventaris</span>
                                <span class="block text-sm font-medium text-gray-400">Manajemen</span>
                            </h1>
                        </div>
                    </a>
                    
                    {{-- Tombol Toggle Desktop --}}
                    <button @click="sidebarOpen = !sidebarOpen" 
                            class="hidden lg:block p-2 rounded-lg text-gray-400 hover:bg-gray-800 hover:text-white transition-colors"
                            :class="!sidebarOpen ? 'w-full' : ''">
                        <i class="fa-solid w-5 h-5 text-center" :class="sidebarOpen ? 'fa-angles-left' : 'fa-angles-right'"></i>
                    </button>
                </div>

                {{-- Navigasi Menu --}}
                <nav class="py-4" :class="sidebarOpen ? 'px-4' : 'px-2'">
                    <ul class="space-y-2 text-sm">
                        <li>
                            <a href="{{ url('/dashboard') }}"
                               class="flex items-center gap-3 px-3 py-3 rounded-lg transition-colors duration-150 {{ request()->is('dashboard') ? 'bg-blue-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}"
                               :class="sidebarOpen ? '' : 'justify-center'">
                                <i class="fa-solid fa-house-chimney w-5 h-5 flex-shrink-0 text-center"></i>
                                <div class="nav-text whitespace-nowrap overflow-hidden" x-show="sidebarOpen" x-cloak>
                                    <span class="font-medium block text-sm">Dashboard</span>
                                    <p class="text-xs {{ request()->is('dashboard') ? 'text-blue-200' : 'text-gray-500' }}">Beranda sistem</p>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('struks.index') }}"
                               class="flex items-center gap-3 px-3 py-3 rounded-lg transition-colors duration-150 {{ request()->is('struks*') ? 'bg-blue-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}"
                               :class="sidebarOpen ? '' : 'justify-center'">
                                <i class="fa-solid fa-box-open w-5 h-5 flex-shrink-0 text-center"></i>
                                <div class="nav-text whitespace-nowrap overflow-hidden" x-show="sidebarOpen" x-cloak>
                                    <span class="font-medium block text-sm">Pemasukan</span>
                                    <p class="text-xs {{ request()->is('struks*') ? 'text-blue-200' : 'text-gray-500' }}">Data barang masuk</p>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('pengeluarans.index') }}"
                               class="flex items-center gap-3 px-3 py-3 rounded-lg transition-colors duration-150 {{ request()->is('pengeluarans*') ? 'bg-blue-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}"
                               :class="sidebarOpen ? '' : 'justify-center'">
                                <i class="fa-solid fa-boxes-packing w-5 h-5 flex-shrink-0 text-center"></i>
                                <div class="nav-text whitespace-nowrap overflow-hidden" x-show="sidebarOpen" x-cloak>
                                    <span class="font-medium block text-sm">Pengeluaran</span>
                                    <p class="text-xs {{ request()->is('pengeluarans*') ? 'text-blue-200' : 'text-gray-500' }}">Data barang keluar</p>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('transaksi.create') }}"
                               class="flex items-center gap-3 px-3 py-3 rounded-lg transition-colors duration-150 {{ request()->is('transaksi/create') ? 'bg-blue-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}"
                               :class="sidebarOpen ? '' : 'justify-center'">
                                <i class="fa-solid fa-plus w-5 h-5 flex-shrink-0 text-center"></i>
                                <div class="nav-text whitespace-nowrap overflow-hidden" x-show="sidebarOpen" x-cloak>
                                    <span class="font-medium block text-sm">Tambah Transaksi</span>
                                    <p class="text-xs {{ request()->is('transaksi/create') ? 'text-blue-200' : 'text-gray-500' }}">Buat transaksi baru</p>
                                </div>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
            
            {{-- User/Profil Section --}}
            <div class="flex-shrink-0" :class="sidebarOpen ? 'p-4' : 'p-2'">
                <div class="border-t border-gray-700">
                    @auth
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" 
                                class="flex items-center w-full gap-3 p-2 rounded-lg hover:bg-gray-800 transition-colors focus:outline-none"
                                :class="!sidebarOpen ? 'justify-center' : ''">
                            <div class="relative flex-shrink-0">
                                <div class="w-10 h-10 rounded-full bg-gray-700 flex items-center justify-center text-white font-semibold text-lg">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <div class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-green-400 rounded-full border-2 border-gray-900 shadow-sm"></div>
                            </div>
                            <div class="nav-text text-left overflow-hidden" x-show="sidebarOpen" x-cloak>
                                <span class="font-medium text-white text-sm block whitespace-nowrap">{{ Auth::user()->name }}</span>
                                <span class="text-xs text-gray-400 whitespace-nowrap">Lihat Opsi</span>
                            </div>
                            <i class="nav-text fa-solid fa-chevron-up w-4 h-4 text-gray-400 ml-auto transition-transform" x-show="sidebarOpen" x-cloak :class="open ? 'rotate-0' : 'rotate-180'"></i>
                        </button>

                        <div x-show="open" @click.away="open = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute bottom-full left-0 mb-2 w-56 origin-bottom-left rounded-md shadow-lg bg-gray-800 border border-gray-700 ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
                             :class="!sidebarOpen ? 'left-1/2 -translate-x-1/2 w-48' : 'w-56'" x-cloak>
                            
                            <div class="px-4 py-3 border-b border-gray-700">
                                <span class="block text-sm font-medium text-white">{{ Auth::user()->name }}</span>
                                @if(Auth::user()->pegawai && Auth::user()->pegawai->nip)
                                <span class="block text-xs font-mono bg-blue-900 text-blue-300 px-2 py-0.5 rounded-full mt-1">NIP: {{ Auth::user()->pegawai->nip }}</span>
                                @endif
                            </div>

                            <div class="py-1">
                                <a href="{{ route('profile.edit') }}"
                                   class="flex items-center gap-2 px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 transition-colors">
                                    <i class="fa-solid fa-user-edit w-4 h-4 text-gray-400"></i>
                                    Profil Saya
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                            class="w-full text-left flex items-center gap-2 px-4 py-2 text-sm text-red-400 hover:bg-gray-700 transition-colors">
                                        <i class="fa-solid fa-right-from-bracket w-4 h-4 text-red-400"></i>
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @else
                    <a href="{{ route('login') }}"
                        class="flex items-center gap-3 px-3 py-2.5 w-full rounded-lg transition-colors duration-150 text-gray-400 hover:bg-gray-800 hover:text-white"
                        :class="!sidebarOpen ? 'justify-center' : ''">
                        <i class="fa-solid fa-right-to-bracket w-5 h-5 text-center"></i>
                        <span class="nav-text font-medium text-sm whitespace-nowrap" x-show="sidebarOpen" x-cloak>Login</span>
                    </a>
                    @endauth
                </div>
            </div>
        </aside>

        {{-- Content Area --}}
        <div class="flex-1 flex flex-col min-w-0">
            
            {{-- Mobile Header --}}
            <header class="lg:hidden bg-gray-900 px-4 py-3 shadow-sm border-b border-gray-700 sticky top-0 z-30">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-bold text-white">Inventaris</h2>
                    <button @click="mobileSidebarOpen = true" class="p-2 rounded-lg text-gray-400 hover:bg-gray-800 transition-colors">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </header>

            {{-- Main Content --}}
            <main class="flex-1 bg-gray-950">
                <div class="max-w-7xl mx-auto p-4 md:p-6">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    @stack('scripts')
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- Script untuk Notifikasi (SweetAlert) ---
            try {
                // Fungsi untuk notifikasi
                function showActionNotification(action, message) {
                    const settings = {
                        create: { icon: 'success', title: 'Data Berhasil Ditambahkan', color: '#10B981' },
                        update: { icon: 'info', title: 'Data Berhasil Diperbarui', color: '#3B82F6' },
                        delete: { icon: 'warning', title: 'Data Berhasil Dihapus', color: '#EF4444' },
                        error: { icon: 'error', title: 'Terjadi Kesalahan', color: '#DC2626' }
                    };
                    const config = settings[action] || { icon: 'info', title: 'Notifikasi' };
                    
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        background: '#1F2937', // bg-gray-800
                        iconColor: config.color,
                        color: '#F9FAFB', // text-gray-100
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer);
                            toast.addEventListener('mouseleave', Swal.resumeTimer);
                        }
                    });
                    Toast.fire({ icon: config.icon, title: config.title, text: message });
                }

                // Handle notifications dari session
                @if(session('created'))
                showActionNotification('create', '{{ session('created') }}');
                @endif
                @if(session('updated'))
                showActionNotification('update', '{{ session('updated') }}');
                @endif
                @if(session('deleted'))
                showActionNotification('delete', '{{ session('deleted') }}');
                @endif
                @if(session('error'))
                showActionNotification('error', '{{ session('error') }}');
                @endif

            } catch (error) {
                console.error("Notification error:", error);
            }
        });

        // Fungsi konfirmasi delete (Global)
        function confirmDelete(event, itemName = 'data') {
            event.preventDefault();
            const form = event.target.closest('form');
            Swal.fire({
                title: `Hapus ${itemName}?`,
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                background: '#1F2937', // bg-gray-800
                color: '#F9FAFB' // text-gray-100
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    </script>
</body>
</html>