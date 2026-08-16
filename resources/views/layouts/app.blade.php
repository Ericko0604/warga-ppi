<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dokumentasi Warga Perumahan') - Album Foto Warga</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
        }
    </style>
    @yield('styles')
</head>
<body class="bg-slate-50 text-slate-800 flex flex-col min-h-screen">
    <!-- Public Header -->
    <header class="bg-emerald-700 text-white shadow-md sticky top-0 z-40">
        <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2 font-bold text-lg sm:text-xl tracking-tight">
                <span class="bg-white text-emerald-700 p-2 rounded-xl text-xl shadow-sm">📷</span>
                <span>Dokumentasi Warga</span>
            </a>
            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}" class="px-3 py-1.5 rounded-lg text-sm font-semibold hover:bg-emerald-600 transition">
                    Beranda
                </a>
                <a href="{{ route('admin.login') }}" class="text-xs bg-emerald-800 hover:bg-emerald-900 px-3 py-1.5 rounded-lg text-emerald-100 font-medium transition">
                    Admin
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 py-8 text-center text-sm border-t border-slate-800 mt-12">
        <div class="max-w-6xl mx-auto px-4">
            <p class="font-semibold text-slate-300">Album Foto & Dokumentasi Kegiatan Warga Perumahan</p>
            <p class="text-xs text-slate-500 mt-1">&copy; {{ date('Y') }} Warga PPI. Semua hak dilindungi.</p>
        </div>
    </footer>

    @yield('scripts')
</body>
</html>
