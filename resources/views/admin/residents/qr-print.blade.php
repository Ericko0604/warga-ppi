<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Daftar QR Code Warga</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
            .page-break { page-break-after: always; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-900 p-6 min-h-screen">
    <!-- Non-printable Top Bar -->
    <div class="no-print max-w-5xl mx-auto mb-6 bg-slate-900 text-white p-4 rounded-2xl flex items-center justify-between shadow-xl">
        <div>
            <h1 class="font-bold text-sm">Lembar Cetak QR Code Warga</h1>
            <p class="text-xs text-slate-400">Total Warga: {{ count($residentsWithQr) }} Rumah</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-xl shadow-lg transition">
                🖨️ CETAK HALAMAN INI
            </button>
        </div>
    </div>

    <!-- Printable Grid -->
    <div class="max-w-5xl mx-auto grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
        @foreach($residentsWithQr as $item)
            <div class="bg-white border-2 border-slate-300 rounded-2xl p-4 text-center flex flex-col items-center justify-between space-y-2 shadow-sm break-inside-avoid">
                <div class="border-b border-slate-200 pb-2 w-full">
                    <span class="text-[10px] font-extrabold text-emerald-800 uppercase tracking-widest block">Dokumentasi Warga</span>
                    <h2 class="text-base font-extrabold text-slate-900">{{ $item['resident']->display_label }}</h2>
                </div>

                <div class="p-2 bg-slate-50 border border-slate-200 rounded-xl my-1">
                    {!! $item['qr_svg'] !!}
                </div>

                <div class="w-full">
                    <p class="text-[9px] font-semibold text-slate-500 uppercase tracking-wider">Scan QR Untuk Upload Foto</p>
                </div>
            </div>
        @endforeach
    </div>
</body>
</html>
