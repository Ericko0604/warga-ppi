@extends('layouts.app')

@section('title', 'Beranda - Album Foto Warga')

@section('content')
<!-- Hero Section -->
@if($featuredEvent)
<section class="bg-gradient-to-br from-emerald-800 to-teal-950 text-white py-8 sm:py-12 px-4 shadow-inner">
    <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-12 gap-6 items-center">
        <div class="md:col-span-7 space-y-3 sm:space-y-4">
            <div class="inline-flex items-center gap-2 bg-emerald-700/60 backdrop-blur-sm border border-emerald-500/30 text-emerald-200 px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider">
                <span>🔥</span>
                <span>Dokumentasi Terbaru</span>
            </div>
            <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight leading-tight text-white">
                {{ $featuredEvent->name }}
            </h1>
            <p class="text-emerald-100/90 text-sm sm:text-base line-clamp-3 font-normal leading-relaxed">
                {{ $featuredEvent->description ?? 'Dokumentasi kegiatan dan acara warga perumahan.' }}
            </p>
            <div class="flex items-center gap-4 text-xs sm:text-sm text-emerald-200 font-medium pt-1">
                <div class="flex items-center gap-1.5">
                    <span>📅</span> {{ $featuredEvent->event_date->translatedFormat('d F Y') }}
                </div>
                <div class="flex items-center gap-1.5 bg-emerald-900/60 px-2.5 py-0.5 rounded-md border border-emerald-700/50">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    <span>{{ $featuredEvent->type }}</span>
                </div>
            </div>
            <div class="pt-2">
                <a href="{{ route('events.show', $featuredEvent->uuid) }}" class="inline-flex items-center gap-2 bg-white text-emerald-900 hover:bg-emerald-50 font-bold px-5 py-2.5 rounded-xl shadow-lg hover:shadow-xl transition transform active:scale-95 text-sm">
                    <span>🖼️ Lihat Album Dokumentasi</span>
                    <span>→</span>
                </a>
            </div>
        </div>
        <div class="md:col-span-5">
            <a href="{{ route('events.show', $featuredEvent->uuid) }}" class="block group relative rounded-2xl overflow-hidden shadow-2xl border border-white/10 aspect-video bg-emerald-900">
                @if($featuredEvent->thumbnail_path)
                    <img src="{{ asset('storage/' . $featuredEvent->thumbnail_path) }}" alt="{{ $featuredEvent->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                @else
                    <div class="w-full h-full flex flex-col items-center justify-center text-emerald-200 p-6 text-center">
                        <span class="text-5xl mb-2">📷</span>
                        <span class="text-xs font-semibold">Album Foto Perumahan</span>
                    </div>
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
            </a>
        </div>
    </div>
</section>
@endif

<!-- Content Section -->
<section class="max-w-6xl mx-auto px-4 py-8">
    <!-- Filter & Search Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Riwayat Acara & Kegiatan</h2>
            <p class="text-xs sm:text-sm text-slate-500">Pilih acara untuk melihat dokumentasi foto warga perumahan.</p>
        </div>

        <!-- Filter Buttons -->
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('home') }}" class="px-4 py-2 rounded-xl text-xs font-semibold transition shadow-sm {{ empty($typeFilter) ? 'bg-emerald-600 text-white' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
                Semua
            </a>
            <a href="{{ route('home', ['type' => 'ACARA']) }}" class="px-4 py-2 rounded-xl text-xs font-semibold transition shadow-sm {{ $typeFilter === 'ACARA' ? 'bg-emerald-600 text-white' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
                🎉 Acara
            </a>
            <a href="{{ route('home', ['type' => 'KEGIATAN']) }}" class="px-4 py-2 rounded-xl text-xs font-semibold transition shadow-sm {{ $typeFilter === 'KEGIATAN' ? 'bg-emerald-600 text-white' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
                🛠️ Kegiatan
            </a>
        </div>
    </div>

    <!-- Events Grid -->
    @if($events->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($events as $event)
                <a href="{{ route('events.show', $event->uuid) }}" class="group bg-white rounded-2xl overflow-hidden border border-slate-200/80 shadow-sm hover:shadow-md transition-all duration-300 flex flex-col">
                    <!-- Thumbnail -->
                    <div class="aspect-video relative bg-slate-100 overflow-hidden">
                        @if($event->thumbnail_path)
                            <img src="{{ asset('storage/' . $event->thumbnail_path) }}" alt="{{ $event->name }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center text-slate-400 p-4 text-center">
                                <span class="text-4xl mb-1">🖼️</span>
                                <span class="text-[11px] font-medium text-slate-400">Belum ada thumbnail</span>
                            </div>
                        @endif

                        <!-- Type Badge -->
                        <div class="absolute top-3 left-3">
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold uppercase tracking-wider shadow-md backdrop-blur-md {{ $event->type === 'ACARA' ? 'bg-emerald-600/90 text-white' : 'bg-amber-600/90 text-white' }}">
                                {{ $event->type }}
                            </span>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="p-5 flex-grow flex flex-col justify-between space-y-3">
                        <div>
                            <div class="text-xs font-medium text-slate-400 mb-1 flex items-center gap-1">
                                <span>📅</span> {{ $event->event_date->translatedFormat('d F Y') }}
                            </div>
                            <h3 class="font-bold text-slate-800 text-base group-hover:text-emerald-700 transition line-clamp-2">
                                {{ $event->name }}
                            </h3>
                            @if($event->description)
                                <p class="text-xs text-slate-500 mt-1.5 line-clamp-2 font-normal">
                                    {{ $event->description }}
                                </p>
                            @endif
                        </div>

                        <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs text-emerald-700 font-semibold">
                            <span>Lihat Dokumentasi</span>
                            <span class="group-hover:translate-x-1 transition">→</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $events->links() }}
        </div>
    @else
        <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center max-w-md mx-auto shadow-sm">
            <span class="text-5xl mb-3 block">📭</span>
            <h3 class="text-lg font-bold text-slate-800">Belum Ada Dokumentasi</h3>
            <p class="text-xs text-slate-500 mt-1">Belum ada acara atau kegiatan yang ditambahkan dalam kategori ini.</p>
        </div>
    @endif
</section>
@endsection
