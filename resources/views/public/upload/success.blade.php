<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foto Berhasil Dikirim</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-emerald-900 text-slate-800 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-2xl p-8 text-center space-y-6 border border-emerald-700/50">
        <div class="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center text-4xl mx-auto shadow-inner border border-emerald-200">
            ✅
        </div>

        <div class="space-y-2">
            <h1 class="text-2xl font-extrabold text-slate-900">Foto Berhasil Dikirim!</h1>
            <p class="text-xs text-slate-500 leading-relaxed">
                Terima kasih telah ikut mendokumentasikan kegiatan warga perumahan.
            </p>
        </div>

        <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 text-left space-y-1">
            <p class="text-[11px] text-slate-400 font-bold uppercase tracking-wider">Ringkasan Upload:</p>
            <p class="text-sm font-extrabold text-slate-800">{{ $resident->display_label }}</p>
            @if($event)
                <p class="text-xs text-emerald-700 font-semibold">Acara: {{ $event->name }}</p>
            @endif
        </div>

        @if($uploadedPhoto)
            <div class="aspect-video w-full rounded-2xl overflow-hidden border border-slate-200 bg-black">
                <img src="{{ $uploadedPhoto->thumbnail_url }}" alt="Foto yang dikirim" class="w-full h-full object-cover">
            </div>
        @endif

        <div class="pt-2 space-y-3">
            <a href="{{ route('resident.upload', ['token' => $resident->upload_token]) }}" class="block w-full bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold py-3.5 px-4 rounded-xl text-xs transition">
                🔄 Edit / Ganti Foto
            </a>

            @if($event)
                <a href="{{ route('events.show', $event->uuid) }}" class="block w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3.5 px-4 rounded-xl text-xs shadow-lg transition">
                    🖼️ Lihat Album Dokumentasi Acara
                </a>
            @endif
        </div>
    </div>
</body>
</html>
