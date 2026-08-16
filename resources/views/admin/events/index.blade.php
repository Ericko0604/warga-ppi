@extends('layouts.admin')

@section('title', 'Kelola Acara & Kegiatan')
@section('page-header', 'Daftar Acara & Kegiatan')

@section('content')
<div class="space-y-6">
    <!-- Filters & Action Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <!-- Filters -->
        <form action="{{ route('admin.events.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
            <div class="flex items-center gap-1">
                <a href="{{ route('admin.events.index', ['status' => $statusFilter]) }}" class="px-3 py-2 rounded-xl text-xs font-semibold shadow-sm {{ empty($typeFilter) ? 'bg-slate-900 text-white' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
                    Semua Jenis
                </a>
                <a href="{{ route('admin.events.index', ['type' => 'ACARA', 'status' => $statusFilter]) }}" class="px-3 py-2 rounded-xl text-xs font-semibold shadow-sm {{ $typeFilter === 'ACARA' ? 'bg-emerald-600 text-white' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
                    🎉 Acara
                </a>
                <a href="{{ route('admin.events.index', ['type' => 'KEGIATAN', 'status' => $statusFilter]) }}" class="px-3 py-2 rounded-xl text-xs font-semibold shadow-sm {{ $typeFilter === 'KEGIATAN' ? 'bg-amber-600 text-white' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
                    🛠️ Kegiatan
                </a>
            </div>

            <select name="status" onchange="this.form.submit()" class="bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500 outline-none shadow-sm">
                <option value="">Semua Status</option>
                <option value="DRAFT" {{ $statusFilter === 'DRAFT' ? 'selected' : '' }}>DRAFT</option>
                <option value="PUBLISHED" {{ $statusFilter === 'PUBLISHED' ? 'selected' : '' }}>PUBLISHED</option>
                <option value="ARCHIVED" {{ $statusFilter === 'ARCHIVED' ? 'selected' : '' }}>ARCHIVED</option>
            </select>
        </form>

        <!-- Create Button -->
        <a href="{{ route('admin.events.create') }}" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-md flex items-center gap-2 self-start md:self-auto">
            <span>➕</span> Buat Acara / Kegiatan Baru
        </a>
    </div>

    <!-- Events List Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4">Nama & Tanggal</th>
                        <th class="px-6 py-4">Jenis</th>
                        <th class="px-6 py-4">Status Upload Warga</th>
                        <th class="px-6 py-4">Status Event</th>
                        <th class="px-6 py-4">Foto Warga / Admin</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($events as $event)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 space-y-0.5">
                                <a href="{{ route('admin.events.show', $event->id) }}" class="font-extrabold text-slate-900 hover:text-emerald-700 text-sm block">
                                    {{ $event->name }}
                                </a>
                                <span class="text-[11px] text-slate-400 font-medium block">
                                    📅 {{ $event->event_date->translatedFormat('d F Y') }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $event->type === 'ACARA' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-amber-100 text-amber-800 border border-amber-200' }}">
                                    {{ $event->type }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($event->type === 'ACARA' || $event->allow_resident_upload)
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-teal-100 text-teal-800 border border-teal-200">
                                        ✅ Warga Boleh Upload
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                        🔒 Hanya Admin
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusClasses = [
                                        'DRAFT' => 'bg-slate-100 text-slate-700 border-slate-300',
                                        'PUBLISHED' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                        'ARCHIVED' => 'bg-amber-100 text-amber-800 border-amber-200',
                                    ];
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $statusClasses[$event->status] ?? '' }}">
                                    {{ $event->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-semibold text-slate-700">
                                <span class="text-emerald-700">{{ $event->resident_photos_count }} Warga</span> /
                                <span class="text-indigo-700">{{ $event->admin_photos_count }}/10 Admin</span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-1 whitespace-nowrap">
                                <a href="{{ route('admin.events.show', $event->id) }}" class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-800 hover:bg-emerald-100 font-bold px-3 py-1.5 rounded-lg border border-emerald-200 transition">
                                    <span>📊 Progres</span>
                                </a>

                                <a href="{{ route('admin.events.edit', $event->id) }}" class="inline-flex items-center gap-1 bg-slate-100 text-slate-700 hover:bg-slate-200 font-bold px-2.5 py-1.5 rounded-lg border border-slate-200 transition">
                                    <span>✏️ Edit</span>
                                </a>

                                <form action="{{ route('admin.events.destroy', $event->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus acara ini beserta seluruh fotonya?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-1 bg-rose-50 text-rose-700 hover:bg-rose-100 font-bold px-2.5 py-1.5 rounded-lg border border-rose-200 transition">
                                        <span>🗑️</span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                Belum ada acara atau kegiatan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($events->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $events->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
