<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - Dokumentasi Warga</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
    @yield('styles')
</head>
<body class="bg-slate-100 text-slate-800 min-h-screen flex" x-data="{ sidebarOpen: false }">
    <!-- Mobile Sidebar Backdrop -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-sm lg:hidden"></div>

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 text-slate-300 transform lg:translate-x-0 lg:static lg:inset-0 transition-transform duration-200 ease-in-out flex flex-col justify-between shadow-xl">
        <div>
            <!-- Sidebar Header -->
            <div class="h-16 flex items-center justify-between px-6 bg-slate-950 border-b border-slate-800">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 font-bold text-white text-lg tracking-tight">
                    <span class="bg-emerald-600 text-white p-1.5 rounded-lg text-lg">⚙️</span>
                    <span>Admin Warga</span>
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white">✕</button>
            </div>

            <!-- Navigation Links -->
            <nav class="p-4 space-y-1.5">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-900/30' : 'hover:bg-slate-800 hover:text-white' }}">
                    <span>📊</span> Dashboard
                </a>
                <a href="{{ route('admin.events.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.events.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-900/30' : 'hover:bg-slate-800 hover:text-white' }}">
                    <span>🎉</span> Acara & Kegiatan
                </a>
                <a href="{{ route('admin.residents.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.residents.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-900/30' : 'hover:bg-slate-800 hover:text-white' }}">
                    <span>🏠</span> Data Warga & QR
                </a>
                <a href="{{ route('home') }}" target="_blank" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium text-sm text-slate-400 hover:bg-slate-800 hover:text-white transition">
                    <span>🌐</span> Lihat Website Publik
                </a>
            </nav>
        </div>

        <!-- Sidebar Footer / Logout -->
        <div class="p-4 border-t border-slate-800">
            <div class="mb-3 px-3 py-2 bg-slate-800/60 rounded-lg flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-sm">
                    A
                </div>
                <div class="overflow-hidden">
                    <p class="text-xs font-semibold text-white truncate">{{ Auth::user()->name ?? 'Admin' }}</p>
                    <p class="text-[10px] text-slate-400 truncate">{{ Auth::user()->email ?? '' }}</p>
                </div>
            </div>
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-rose-950/80 hover:bg-rose-900 text-rose-300 rounded-xl text-xs font-semibold transition border border-rose-800/50">
                    🚪 Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-grow flex flex-col min-w-0">
        <!-- Top Navbar -->
        <header class="h-16 bg-white border-b border-slate-200 px-4 sm:px-8 flex items-center justify-between sticky top-0 z-30 shadow-sm">
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700">
                    ☰
                </button>
                <h1 class="text-base sm:text-lg font-bold text-slate-800 truncate">@yield('page-header', 'Dashboard')</h1>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs bg-emerald-100 text-emerald-800 font-semibold px-2.5 py-1 rounded-full border border-emerald-200">
                    Admin Session
                </span>
            </div>
        </header>

        <!-- Flash Messages -->
        <div class="p-4 sm:p-8 pb-0">
            @if(session('success'))
                <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-medium flex items-center gap-3 shadow-sm">
                    <span class="text-lg">✅</span>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            @if(session('error_message'))
                <div class="mb-4 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm font-medium flex items-center gap-3 shadow-sm">
                    <span class="text-lg">⚠️</span>
                    <div>{{ session('error_message') }}</div>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm font-medium shadow-sm">
                    <p class="font-bold mb-1">Harap perbaiki kesalahan berikut:</p>
                    <ul class="list-disc list-inside text-xs space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <!-- Page Content -->
        <main class="p-4 sm:p-8 flex-grow">
            @yield('content')
        </main>
    </div>

    @yield('scripts')
</body>
</html>
