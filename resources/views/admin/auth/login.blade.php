<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Dokumentasi Warga</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-900 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-2xl p-8 space-y-6">
        <div class="text-center space-y-2">
            <div class="w-16 h-16 bg-emerald-100 text-emerald-700 rounded-2xl flex items-center justify-center text-3xl mx-auto shadow-inner">
                ⚙️
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900">Login Administrator</h1>
            <p class="text-xs text-slate-500">Sistem Pengelolaan Dokumentasi Kegiatan Warga</p>
        </div>

        @if($errors->any())
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs font-semibold">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-4">
            @csrf

            <div class="space-y-1">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Email Admin:</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="admin@warga.local" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500 outline-none">
            </div>

            <div class="space-y-1">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Password:</label>
                <input type="password" name="password" required placeholder="••••••••" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500 outline-none">
            </div>

            <div class="flex items-center justify-between text-xs text-slate-600 font-medium">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                    <span>Ingat Saya</span>
                </label>
            </div>

            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold py-3.5 px-4 rounded-xl shadow-lg transition text-sm">
                Masuk ke Panel Admin →
            </button>
        </form>

        <div class="text-center pt-2 border-t border-slate-100">
            <a href="{{ route('home') }}" class="text-xs text-slate-400 hover:text-slate-600 font-medium">
                ← Kembali ke Website Utama
            </a>
        </div>
    </div>
</body>
</html>
