@extends('layouts.app')

@section('title', $event->name . ' - Dokumentasi Warga')

@section('content')
<div x-data="{ activeTab: 'A1', activeModalImage: null, activeModalCaption: '' }">
    <!-- Header Banner -->
    <section class="bg-slate-900 text-white py-8 sm:py-12 border-b border-slate-800">
        <div class="max-w-6xl mx-auto px-4">
            <div class="mb-3 flex items-center gap-2">
                <a href="{{ route('home') }}" class="text-xs text-slate-400 hover:text-emerald-400 font-medium transition">
                    ← Kembali ke Beranda
                </a>
                <span class="text-slate-600">/</span>
                <span class="text-xs text-slate-300 font-semibold truncate">{{ $event->name }}</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-center">
                <div class="md:col-span-8 space-y-3">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-md text-xs font-bold uppercase tracking-wider bg-emerald-600 text-white">
                            {{ $event->type }}
                        </span>
                        <span class="text-xs text-slate-400 font-medium">📅 {{ $event->event_date->translatedFormat('d F Y') }}</span>
                    </div>

                    <h1 class="text-2xl sm:text-4xl font-extrabold text-white tracking-tight">
                        {{ $event->name }}
                    </h1>

                    @if($event->description)
                        <p class="text-slate-300 text-sm sm:text-base leading-relaxed font-normal">
                            {{ $event->description }}
                        </p>
                    @endif
                </div>

                @if($event->thumbnail_path)
                <div class="md:col-span-4">
                    <div class="aspect-video rounded-2xl overflow-hidden border border-slate-700 shadow-xl">
                        <img src="{{ asset('storage/' . $event->thumbnail_path) }}" alt="{{ $event->name }}" class="w-full h-full object-cover">
                    </div>
                </div>
                @endif
            </div>
        </div>
    </section>

    <div class="max-w-6xl mx-auto px-4 py-8 space-y-10">
        <!-- Section 1: Admin Documentation Photos -->
        @if($adminPhotos->count() > 0)
        <section class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
            <div class="flex items-center gap-2 mb-4">
                <span class="text-xl">📸</span>
                <h2 class="text-lg font-bold text-slate-900">Dokumentasi Panitia / Admin</h2>
                <span class="text-xs font-semibold bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full">
                    {{ $adminPhotos->count() }} Foto
                </span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
                @foreach($adminPhotos as $photo)
                    <div @click="activeModalImage = '{{ $photo->file_url }}'; activeModalCaption = 'Dokumentasi Panitia/Admin'" class="cursor-pointer group aspect-video relative bg-slate-100 rounded-xl overflow-hidden border border-slate-200 shadow-sm hover:shadow-md transition">
                        <img src="{{ $photo->thumbnail_url }}" alt="Dokumentasi Admin" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition flex items-center justify-center">
                            <span class="text-white opacity-0 group-hover:opacity-100 font-bold text-xs bg-black/50 px-2 py-1 rounded-md">🔍 Perbesar</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
        @endif

        <!-- Section 2: Resident Photos Grouped by Housing Block -->
        <section class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-lg sm:text-xl font-bold text-slate-900 flex items-center gap-2">
                        <span>🏡</span> Dokumentasi Foto Rumah Warga
                    </h2>
                    <p class="text-xs text-slate-500">Pilih blok di bawah untuk melihat foto partisipasi warga perumahan.</p>
                </div>

                <!-- Block Tabs -->
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0">
                    @foreach(['A1', 'A2', 'A3', 'A4', 'KAVLING'] as $block)
                        <button @click="activeTab = '{{ $block }}'" :class="activeTab === '{{ $block }}' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-4 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap">
                            Blok {{ $block }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Block Content Grids -->
            @foreach($blocksData as $block => $items)
                <div x-show="activeTab === '{{ $block }}'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($items as $item)
                        @php
                            $resident = $item['resident'];
                            $photo = $item['photo'];
                        @endphp
                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 flex flex-col justify-between space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-xs text-slate-800">
                                    {{ $resident->display_label }}
                                </span>
                                @if($photo)
                                    <span class="text-[10px] bg-emerald-100 text-emerald-800 font-semibold px-2 py-0.5 rounded-full border border-emerald-200">
                                        Ada Foto
                                    </span>
                                @else
                                    <span class="text-[10px] bg-slate-200 text-slate-600 font-medium px-2 py-0.5 rounded-full">
                                        Belum Ada
                                    </span>
                                @endif
                            </div>

                            <!-- Photo Box -->
                            <div class="aspect-video bg-white rounded-lg overflow-hidden border border-slate-200 flex items-center justify-center relative">
                                @if($photo)
                                    <img src="{{ $photo->thumbnail_url }}" alt="{{ $resident->display_label }}" loading="lazy" class="w-full h-full object-cover cursor-pointer hover:scale-105 transition" @click="activeModalImage = '{{ $photo->file_url }}'; activeModalCaption = '{{ $resident->display_label }}'">
                                @else
                                    <div class="text-center p-3 text-slate-400">
                                        <span class="text-2xl block opacity-40">📷</span>
                                        <span class="text-[11px] font-medium text-slate-400">Belum ada foto</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </section>
    </div>

    <!-- Lightbox Modal -->
    <div x-show="activeModalImage" x-cloak @click="activeModalImage = null" class="fixed inset-0 z-50 bg-black/90 backdrop-blur-md flex flex-col items-center justify-center p-4">
        <div class="relative max-w-4xl max-h-[90vh] w-full flex flex-col items-center" @click.stop>
            <button @click="activeModalImage = null" class="absolute -top-10 right-0 text-white text-2xl font-bold bg-white/20 hover:bg-white/40 w-8 h-8 rounded-full flex items-center justify-center">✕</button>
            <img :src="activeModalImage" class="max-h-[80vh] w-auto max-w-full rounded-lg shadow-2xl object-contain border border-white/20">
            <p x-text="activeModalCaption" class="text-white text-sm font-semibold mt-3 bg-black/60 px-4 py-1.5 rounded-full border border-white/10"></p>
        </div>
    </div>
</div>
@endsection
