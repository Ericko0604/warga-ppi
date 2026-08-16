<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inisialisasi Database - Warga PPI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-900 text-slate-800 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-lg w-full bg-white rounded-3xl shadow-2xl p-8 space-y-6">
        <div class="text-center space-y-2">
            <span class="text-5xl block mb-2">⚡</span>
            <h1 class="text-2xl font-extrabold text-slate-900">Inisialisasi Database Warga</h1>
            <p class="text-xs text-slate-500">Tabel database cloud belum dimigrasi atau koneksi database perlu disiapkan.</p>
        </div>

        @if(isset($errorMessage))
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 space-y-1">
                <span class="text-[11px] font-bold text-amber-800 uppercase tracking-wider block">Status Koneksi / Error:</span>
                <p class="text-xs font-mono text-amber-900 break-all">{{ $errorMessage }}</p>
            </div>
        @endif

        <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 space-y-2 text-xs text-slate-600">
            <p class="font-bold text-slate-800">Langkah yang dapat dilakukan:</p>
            <ol class="list-decimal list-inside space-y-1 text-[11px] text-slate-600">
                <li>Pastikan <code>DB_HOST</code>, <code>DB_PORT</code>, dan <code>DB_PASSWORD</code> di Vercel sudah benar mengarah ke MySQL/PostgreSQL Aiven.</li>
                <li>Klik tombol di bawah untuk menjalankan migrasi dan seeder database awal secara otomatis.</li>
            </ol>
        </div>

        <form action="{{ url('/setup-db') }}" method="POST" class="pt-2">
            @csrf
            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold py-4 px-6 rounded-2xl text-center shadow-lg transition text-sm flex items-center justify-center gap-2">
                <span>🚀</span> Jalankan Migrasi & Seeder Database
            </button>
        </form>
    </div>
</body>
</html>
