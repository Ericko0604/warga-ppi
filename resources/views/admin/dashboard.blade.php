@extends('layouts.admin')

@section('title', 'Dashboard Admin')
@section('page-header', 'Dashboard Utama')

@section('content')
<div class="space-y-8">
    <!-- Stat Cards Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-2">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Warga</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl sm:text-3xl font-extrabold text-slate-900">{{ $totalResidents }}</span>
                <span class="text-xl">🏠</span>
            </div>
            <span class="text-[11px] text-emerald-600 font-semibold block">{{ $totalActiveHouses }} rumah aktif</span>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-2">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Acara</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl sm:text-3xl font-extrabold text-emerald-700">{{ $totalEvents }}</span>
                <span class="text-xl">🎉</span>
            </div>
            <span class="text-[11px] text-slate-400 font-medium block">Acara Besar</span>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-2">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Kegiatan</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl sm:text-3xl font-extrabold text-amber-700">{{ $totalActivities }}</span>
                <span class="text-xl">🛠️</span>
            </div>
            <span class="text-[11px] text-slate-400 font-medium block">Kerja bakti / rapat</span>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-2">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Foto</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl sm:text-3xl font-extrabold text-indigo-700">{{ $totalPhotos }}</span>
                <span class="text-xl">🖼️</span>
            </div>
            <span class="text-[11px] text-slate-400 font-medium block">Dokumentasi</span>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-2 col-span-2 sm:col-span-1">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Cetak QR Warga</span>
            <a href="{{ route('admin.residents.qr_print') }}" target="_blank" class="block w-full bg-slate-900 hover:bg-black text-white text-center py-2 rounded-xl text-xs font-bold transition shadow-sm">
                🖨️ Cetak Semua QR
            </a>
            <span class="text-[10px] text-slate-400 text-center block">Format Siap Cetak</span>
        </div>
    </div>

    <!-- Latest Event Progress Widget -->
    @if($latestEventProgress)
    <div class="bg-gradient-to-r from-slate-900 to-emerald-950 text-white rounded-3xl p-6 shadow-xl border border-slate-800 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <span class="text-xs font-bold text-emerald-400 uppercase tracking-wider block">Progres Upload Acara Terkini</span>
                <h3 class="text-lg sm:text-xl font-bold text-white">{{ $latestEventProgress['event']->name }}</h3>
            </div>
            <a href="{{ route('admin.events.show', $latestEventProgress['event']->id) }}" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold px-4 py-2 rounded-xl transition">
                <span>Detail & Progress Per Blok</span>
                <span>→</span>
            </a>
        </div>

        <!-- Progress Bar -->
        <div class="space-y-1.5">
            <div class="flex justify-between text-xs font-semibold">
                <span class="text-slate-300">Warga Yang Sudah Upload:</span>
                <span class="text-emerald-300 font-extrabold">{{ $latestEventProgress['uploaded'] }} dari {{ $latestEventProgress['total'] }} Rumah ({{ $latestEventProgress['percentage'] }}%)</span>
            </div>
            <div class="w-full bg-slate-800 rounded-full h-3.5 overflow-hidden p-0.5 border border-slate-700">
                <div class="bg-gradient-to-r from-emerald-500 to-teal-400 h-full rounded-full transition-all duration-500" style="width: {{ $latestEventProgress['percentage'] }}%"></div>
            </div>
        </div>
    </div>
    @endif

    <!-- Recent Upload Activity Gallery -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-base sm:text-lg font-bold text-slate-900 flex items-center gap-2">
                <span>📸</span> Upload Dokumentasi Terbaru
            </h2>
            <a href="{{ route('admin.events.index') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800">Lihat Semua →</a>
        </div>

        @if($recentUploads->count() > 0)
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                @foreach($recentUploads as $photo)
                    <div class="bg-slate-50 border border-slate-200 rounded-xl overflow-hidden shadow-sm flex flex-col justify-between">
                        <div class="aspect-video relative bg-black">
                            <img src="{{ $photo->thumbnail_url }}" alt="Upload terbaru" class="w-full h-full object-cover">
                            <span class="absolute top-2 left-2 text-[10px] font-bold px-2 py-0.5 rounded-md shadow-md {{ $photo->uploader_type === 'ADMIN' ? 'bg-indigo-600 text-white' : 'bg-emerald-600 text-white' }}">
                                {{ $photo->uploader_type }}
                            </span>
                        </div>
                        <div class="p-3 text-xs space-y-1">
                            <p class="font-extrabold text-slate-800 truncate">
                                {{ $photo->resident ? $photo->resident->display_label : 'Panitia / Admin' }}
                            </p>
                            <p class="text-[11px] text-slate-500 truncate">
                                {{ $photo->event ? $photo->event->name : '-' }}
                            </p>
                            <p class="text-[10px] text-slate-400 pt-1 border-t border-slate-200">
                                {{ $photo->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-xs text-slate-400 text-center py-8">Belum ada foto yang diunggah.</p>
        @endif
    </div>
</div>
@endsection
