@extends('layouts.admin')

@section('title', $event->name . ' - Detail Admin')
@section('page-header', 'Detail Acara / Kegiatan')

@section('content')
<div class="space-y-8" x-data="{ selectedBlockTab: 'A1' }">
    <!-- Header Summary Card -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="space-y-2">
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider {{ $event->type === 'ACARA' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                    {{ $event->type }}
                </span>
                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                    {{ $event->status }}
                </span>
                <span class="text-xs text-slate-400 font-medium">📅 {{ $event->event_date->translatedFormat('d F Y') }}</span>
            </div>

            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">{{ $event->name }}</h1>

            @if($event->description)
                <p class="text-slate-600 text-xs sm:text-sm max-w-3xl leading-relaxed">{{ $event->description }}</p>
            @endif
        </div>

        <div class="flex items-center gap-2 self-start md:self-auto">
            <a href="{{ route('events.show', $event->uuid) }}" target="_blank" class="px-4 py-2.5 bg-slate-900 hover:bg-black text-white text-xs font-bold rounded-xl shadow-md transition">
                🌐 Lihat Tampilan Warga
            </a>
            <a href="{{ route('admin.events.edit', $event->id) }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl border border-slate-200 transition">
                ✏️ Edit Event
            </a>
        </div>
    </div>

    <!-- Section 1: Block Upload Progress Statistics (Prompt Section 28) -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-4">
            <div>
                <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <span>📊</span> Progres Upload Foto Warga Per Blok
                </h2>
                <p class="text-xs text-slate-500">Total Warga Upload: {{ $totalUploadedCount }} dari {{ $totalActiveResidents }} Rumah ({{ $totalActiveResidents > 0 ? round(($totalUploadedCount / $totalActiveResidents) * 100) : 0 }}%)</p>
            </div>
        </div>

        <!-- Block Progress Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
            @foreach(['A1', 'A2', 'A3', 'A4', 'KAVLING'] as $b)
                @php
                    $stats = $blocksStats[$b] ?? ['total' => 0, 'uploaded' => 0];
                    $percent = $stats['total'] > 0 ? round(($stats['uploaded'] / $stats['total']) * 100) : 0;
                @endphp
                <button @click="selectedBlockTab = '{{ $b }}'" :class="selectedBlockTab === '{{ $b }}' ? 'border-emerald-600 bg-emerald-50 ring-2 ring-emerald-500/20' : 'border-slate-200 bg-slate-50 hover:bg-slate-100'" class="p-4 rounded-2xl border text-left transition space-y-2">
                    <div class="flex items-center justify-between text-xs font-extrabold text-slate-800">
                        <span>Blok {{ $b }}</span>
                        <span class="text-emerald-700 font-bold">{{ $stats['uploaded'] }}/{{ $stats['total'] }}</span>
                    </div>

                    <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden">
                        <div class="bg-emerald-600 h-full rounded-full transition-all duration-300" style="width: {{ $percent }}%"></div>
                    </div>
                    <span class="text-[10px] font-bold text-slate-500 block text-right">{{ $percent }}%</span>
                </button>
            @endforeach
        </div>

        <!-- Detailed Resident Upload Status per Block -->
        @foreach($blocksStats as $b => $bData)
            <div x-show="selectedBlockTab === '{{ $b }}'" class="space-y-4 pt-2">
                <h3 class="text-sm font-extrabold text-slate-800 border-b border-slate-100 pb-2">
                    Detail Status Rumah — Blok {{ $b }}
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($bData['residents'] as $item)
                        @php
                            $res = $item['resident'];
                            $photo = $item['photo'];
                            $hasUploaded = $item['has_uploaded'];
                        @endphp
                        <div class="p-3.5 rounded-xl border flex items-center justify-between text-xs {{ $hasUploaded ? 'bg-emerald-50/60 border-emerald-200' : 'bg-slate-50 border-slate-200' }}">
                            <div>
                                <p class="font-extrabold text-slate-900">{{ $res->display_label }}</p>
                                <span class="text-[10px] font-semibold {{ $hasUploaded ? 'text-emerald-700' : 'text-slate-400' }}">
                                    {{ $hasUploaded ? '✅ Foto Sudah Diunggah' : '❌ Belum Ada Foto' }}
                                </span>
                            </div>

                            @if($hasUploaded && $photo)
                                <div class="flex items-center gap-2">
                                    <img src="{{ $photo->thumbnail_url }}" alt="Uploaded" class="w-10 h-10 object-cover rounded-lg border border-emerald-300">
                                    <form action="{{ route('admin.photos.destroy', $photo->id) }}" method="POST" onsubmit="return confirm('Hapus foto warga ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Hapus Foto Warga" class="text-rose-600 hover:text-rose-800 text-xs font-bold p-1">
                                            🗑️
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    <!-- Section 2: Admin Photos (Max 10 enforcement, Prompt Section 13) -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
            <div>
                <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <span>📸</span> Foto Dokumentasi Panitia / Admin (Maksimal 10 Foto)
                </h2>
                <p class="text-xs text-slate-500">Jumlah foto admin terunggah: {{ $adminPhotos->count() }} / 10 foto.</p>
            </div>

            <!-- Upload Admin Photo Form -->
            @if($adminPhotos->count() < 10)
                <form action="{{ route('admin.events.photos.store', $event->id) }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2">
                    @csrf
                    <input type="file" name="photo" accept="image/*" required class="text-xs text-slate-600 bg-slate-50 border border-slate-300 rounded-xl p-2">
                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs shadow-md whitespace-nowrap">
                        Upload Foto Admin
                    </button>
                </form>
            @else
                <div class="px-4 py-2 bg-amber-100 border border-amber-300 text-amber-900 font-extrabold rounded-xl text-xs">
                    ⚠️ Batas foto admin sudah mencapai 10 foto.
                </div>
            @endif
        </div>

        <!-- Admin Photos Grid -->
        @if($adminPhotos->count() > 0)
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
                @foreach($adminPhotos as $photo)
                    <div class="bg-slate-50 rounded-2xl border border-slate-200 overflow-hidden shadow-sm flex flex-col justify-between">
                        <div class="aspect-video relative bg-black">
                            <img src="{{ $photo->thumbnail_url }}" alt="Foto Admin" class="w-full h-full object-cover">
                        </div>
                        <div class="p-2.5 flex items-center justify-between bg-white border-t border-slate-100">
                            <span class="text-[10px] text-slate-400 font-medium">{{ $photo->created_at->format('d/m H:i') }}</span>
                            <form action="{{ route('admin.photos.destroy', $photo->id) }}" method="POST" onsubmit="return confirm('Hapus foto admin ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-rose-600 hover:text-rose-800 font-bold">
                                    🗑️ Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-xs text-slate-400 text-center py-6">Belum ada foto dokumentasi admin.</p>
        @endif
    </div>

    <!-- Section 3: Thumbnail Management (Prompt Section 17) -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-4">
        <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
            <span>🖼️</span> Kelola Thumbnail Acara (Rasio 16:9)
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-center">
            <div class="md:col-span-6">
                @if($event->thumbnail_path)
                    <div class="aspect-video rounded-2xl overflow-hidden border border-slate-300 shadow-md bg-black">
                        <img src="{{ asset('storage/' . $event->thumbnail_path) }}" alt="Thumbnail" class="w-full h-full object-cover">
                    </div>
                @else
                    <div class="aspect-video rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 flex items-center justify-center text-slate-400 text-xs font-semibold">
                        Belum ada foto thumbnail 16:9
                    </div>
                @endif
            </div>

            <div class="md:col-span-6 space-y-3">
                <form action="{{ route('admin.events.thumbnail.update', $event->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-slate-700">Ganti Foto Thumbnail Baru:</label>
                        <input type="file" name="thumbnail" accept="image/*" required class="w-full text-xs text-slate-600 bg-slate-50 border border-slate-300 rounded-xl p-2.5">
                    </div>
                    <button type="submit" class="px-5 py-2.5 bg-slate-900 hover:bg-black text-white font-bold rounded-xl text-xs shadow-md">
                        Simpan Thumbnail 16:9
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
