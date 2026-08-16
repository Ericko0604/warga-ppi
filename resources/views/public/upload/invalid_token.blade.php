<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Link Upload Tidak Valid</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="bg-slate-900 text-slate-800 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-2xl p-8 text-center space-y-6">
        <div class="w-20 h-20 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center text-4xl mx-auto shadow-inner">
            ⚠️
        </div>

        <div class="space-y-2">
            <h1 class="text-xl font-extrabold text-slate-900">Link Upload Tidak Ditemukan</h1>
            <p class="text-xs text-slate-500 leading-relaxed">
                Link atau QR Code yang Anda scan tidak valid, telah kadaluarsa, atau dinonaktifkan oleh pengurus.
            </p>
        </div>

        <div class="pt-2">
            <a href="{{ route('home') }}" class="inline-block w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3.5 px-4 rounded-xl text-xs shadow-lg transition">
                🌐 Kembali ke Beranda Utama
            </a>
        </div>
    </div>
</body>
</html>
