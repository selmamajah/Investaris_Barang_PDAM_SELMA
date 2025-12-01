<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Masuk - Inventaris Barang PDAM Surabaya</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* Grayscale Dark Palette */
        :root {
            --dark-bg-main: #111827; /* Body background: Gray 900 */
            --dark-bg-container: #1F2937; /* Main card/right column: Gray 800 */
            --dark-bg-accent: #374151; /* Left column/input bg: Gray 700 */
            --text-light: #F9FAFB; /* Near white text */
            --text-medium: #D1D5DB; /* Medium gray text */
            --text-dark: #9CA3AF; /* Darker gray text (placeholders, icons): Gray 400 */
            --border-gray: #4B5563; /* Border color: Gray 600 */
            --accent-gray: #9CA3AF; /* Primary button, links, focus border: Gray 400 */
            --accent-hover: #6B7280; /* Button/link hover: Gray 500 */
            --focus-ring: rgba(156, 163, 175, 0.4); /* Focus ring color: Gray 400 with opacity */
            --highlight: #E5E7EB; /* Highlight color (e.g., span in title): Gray 200 */
            --error-bg: rgba(220, 38, 38, 0.2);
            --error-text: #f87171;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--dark-bg-main);
            color: var(--text-medium);
        }

        /* Simplified Custom Classes using variables */
        .bg-card-accent { background-color: var(--dark-bg-accent); }
        .bg-container-dark { background-color: var(--dark-bg-container); }
        .text-light { color: var(--text-light); }
        .text-medium { color: var(--text-medium); }
        .text-dark { color: var(--text-dark); }
        .border-gray { border-color: var(--border-gray); }
        .bg-accent { background-color: var(--accent-gray); }
        .hover\:bg-accent-hover:hover { background-color: var(--accent-hover); }
        .text-accent { color: var(--accent-gray); }
        .hover\:text-light:hover { color: var(--text-light); }
        .focus\:ring-custom:focus {
            --tw-ring-color: var(--focus-ring);
            border-color: var(--accent-gray);
        }
        .text-highlight { color: var(--highlight); }

        /* Input Field adjustments (Optimized) */
        .input-field {
            background-color: var(--dark-bg-accent);
            color: var(--text-light);
            border: 1px solid var(--border-gray);
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
        }
        .input-field::placeholder { color: var(--text-dark); }
        .input-field:focus {
            background-color: #4B5563; /* Gray 600 on focus */
            outline: none;
        }

        /* Animations (Retained for flair but simplified) */
        @keyframes fadeInSlideUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes scaleIn {
            from { transform: scale(0.98); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        .animate-fadeInSlideUp { animation: fadeInSlideUp 0.6s ease-out forwards; }
        .animate-scaleIn { animation: scaleIn 0.5s ease-out forwards; }

        /* Watermark Styling (Removed) */
        .floating-watermark {
            display: none !important; /* Ensure it's hidden */
        }

        [x-cloak] { display: none !important; }
    </style>
</head>

<body class="bg-main">

    <div class="min-h-screen flex items-center justify-center p-4">

        <div
            class="relative w-full max-w-5xl mx-auto bg-container-dark rounded-xl shadow-2xl overflow-hidden flex flex-col lg:flex-row min-h-[550px] animate-scaleIn">

            <div class="relative lg:w-5/12 p-8 lg:p-10 flex flex-col justify-center text-light bg-card-accent overflow-hidden">
                {{-- <i class="fas fa-box floating-watermark hidden lg:block"></i> Removed this line --}}
                <div class="relative z-10">
                    <div class="flex items-center mb-6 animate-fadeInSlideUp" style="animation-delay: 0.1s;">
                        <div class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center mr-3 shadow-lg">
                            <i class="fas fa-box text-gray-400 text-xl"></i>
                        </div>
                        <span class="text-2xl font-bold text-light">Inventaris Barang</span>
                    </div>
                    <h1 class="text-3xl sm:text-4xl font-extrabold leading-snug mb-3 tracking-tight text-light animate-fadeInSlideUp" style="animation-delay: 0.2s;">
                        Manajemen Aset <span class="text-highlight">Efisien</span> PDAM Surabaya
                    </h1>
                    <p class="text-base text-medium animate-fadeInSlideUp" style="animation-delay: 0.3s;">
                        Sistem terintegrasi untuk mengelola stok, aset, dan inventaris perusahaan dengan mudah dan akurat.
                    </p>
                </div>
            </div>

            <div class="lg:w-7/12 p-8 lg:p-12 flex flex-col justify-center bg-container-dark relative z-10">
                <div class="text-center mb-8 animate-fadeInSlideUp" style="animation-delay: 0.4s;">
                    <h2 class="text-2xl font-bold text-light mb-1">Selamat Datang Kembali</h2>
                    <p class="text-medium text-sm">Silakan Masuk menggunakan NIP Anda</p>
                </div>

                @if ($errors->any())
                    <div class="bg-red-500 border border-red-600 text-error-text p-3 rounded-lg mb-6 text-sm animate-fadeInSlideUp" style="animation-delay: 0.5s;">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-circle mr-3 mt-0.5"></i>
                            <div>
                                @foreach ($errors->all() as $error)
                                    <p>{{ $error }}</p>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <div class="animate-fadeInSlideUp" style="animation-delay: 0.6s;">
                        <label for="nip" class="block text-xs font-medium text-medium mb-1">NIP</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="fas fa-id-card text-dark text-sm"></i>
                            </div>
                            <input type="text" id="nip" name="nip"
                                class="w-full pl-10 pr-4 py-2.5 text-sm rounded-lg input-field focus:ring-custom"
                                placeholder="Masukkan NIP Anda (8 digit)" value="{{ old('nip') }}" required autofocus
                                pattern="\d{8}" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 8)">
                        </div>
                    </div>

                    <div class="animate-fadeInSlideUp" style="animation-delay: 0.7s;">
                        <label for="password" class="block text-xs font-medium text-medium mb-1">Kata Sandi</label>
                        <div class="relative" x-data="{ show: false }">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-dark text-sm"></i>
                            </div>
                            <input :type="show ? 'text' : 'password'" id="password" name="password"
                                class="w-full pl-10 pr-10 py-2.5 text-sm rounded-lg input-field focus:ring-custom"
                                placeholder="••••••••" required>
                            <button type="button" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-dark hover:text-medium transition-colors duration-150"
                                @click="show = !show">
                                <i class="fas text-sm" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-xs animate-fadeInSlideUp" style="animation-delay: 0.8s;">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="remember"
                                class="h-4 w-4 text-accent border-gray rounded focus:ring-custom bg-card-accent focus:ring-opacity-50 focus:ring-offset-gray-800">
                            <span class="ml-2 text-medium">Ingat saya</span>
                        </label>
                        <a href="{{ route('password.request') }}"
                            class="text-accent hover:text-light font-medium transition-colors duration-150">Lupa kata sandi?</a>
                    </div>

                    <button type="submit"
                        class="w-full bg-accent text-gray-900 py-2.5 px-4 rounded-lg font-semibold hover:bg-accent-hover transition-all duration-200 shadow hover:shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent focus:ring-offset-gray-900 animate-fadeInSlideUp"
                        style="animation-delay: 0.9s;">
                        Masuk
                        <i class="fas fa-arrow-right ml-1.5 text-xs"></i>
                    </button>
                </form>

                <div class="text-center text-xs text-dark mt-6 animate-fadeInSlideUp" style="animation-delay: 1s;">
                    <p>NIP & Kata Sandi diberikan oleh Admin. Hubungi Kepegawaian jika ada kendala.</p>
                    <p class="mt-2">&copy; {{ date('Y') }} PDAM Surya Sembada Kota Surabaya</p>
                </div>
            </div>
        </div>

    </div>

    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        // Only AlpineJS is needed now. The vanilla JS password toggle was removed.
    </script>
</body>

</html>