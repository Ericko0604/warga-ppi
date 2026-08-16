@extends('layouts.admin')

@section('title', 'Buat Acara / Kegiatan Baru')
@section('page-header', 'Tambah Acara / Kegiatan Baru')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-sm">
    <form action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" x-data="{ selectedType: 'ACARA' }">
        @csrf

        <!-- Type Radio Selection -->
        <div class="space-y-2">
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Jenis Konten:</label>
            <div class="grid grid-cols-2 gap-3">
                <label class="cursor-pointer border-2 rounded-2xl p-4 flex flex-col items-center justify-center text-center transition" :class="selectedType === 'ACARA' ? 'border-emerald-600 bg-emerald-50 text-emerald-900 font-extrabold shadow-sm' : 'border-slate-200 text-slate-600 font-semibold hover:bg-slate-50'">
                    <input type="radio" name="type" value="ACARA" x-model="selectedType" class="hidden">
                    <span class="text-2xl mb-1">🎉</span>
                    <span class="text-sm">ACARA</span>
                    <span class="text-[10px] font-normal text-slate-500 mt-1">17 Agustus, Halal Bihalal, Tahun Baru (Warga otomatis dapat slot upload 1 foto)</span>
                </label>

                <label class="cursor-pointer border-2 rounded-2xl p-4 flex flex-col items-center justify-center text-center transition" :class="selectedType === 'KEGIATAN' ? 'border-amber-600 bg-amber-50 text-amber-900 font-extrabold shadow-sm' : 'border-slate-200 text-slate-600 font-semibold hover:bg-slate-50'">
                    <input type="radio" name="type" value="KEGIATAN" x-model="selectedType" class="hidden">
                    <span class="text-2xl mb-1">🛠️</span>
                    <span class="text-sm">KEGIATAN</span>
                    <span class="text-[10px] font-normal text-slate-500 mt-1">Kerja Bakti, Posyandu, Rapat Warga (Status upload warga dapat diatur)</span>
                </label>
            </div>
        </div>

        <!-- Name -->
        <div class="space-y-1">
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Nama Acara / Kegiatan:</label>
            <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: Peringatan HUT RI ke-81" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500 outline-none">
        </div>

        <!-- Date -->
        <div class="space-y-1">
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Tanggal Acara / Kegiatan:</label>
            <input type="date" name="event_date" value="{{ old('event_date', date('Y-m-d')) }}" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500 outline-none">
        </div>

        <!-- Description -->
        <div class="space-y-1">
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Deskripsi (Opsional):</label>
            <textarea name="description" rows="3" placeholder="Jelaskan detail singkat acara/kegiatan..." class="w-full bg-slate-50 border border-slate-300 rounded-xl p-4 text-sm font-medium text-slate-800 focus:ring-2 focus:ring-emerald-500 outline-none">{{ old('description') }}</textarea>
        </div>

        <!-- Resident Upload Toggle (Only shown for KEGIATAN) -->
        <div x-show="selectedType === 'KEGIATAN'" x-cloak class="p-4 bg-amber-50 border border-amber-200 rounded-2xl space-y-2">
            <label class="block text-xs font-bold text-amber-900 uppercase tracking-wider">
                Apakah Warga Perlu Mengupload Foto?
            </label>
            <div class="flex items-center gap-6">
                <label class="flex items-center gap-2 cursor-pointer font-bold text-xs text-amber-950">
                    <input type="radio" name="allow_resident_upload" value="1" checked class="text-amber-600 focus:ring-amber-500">
                    <span>( YA ) Warga dapat upload foto (1 foto per rumah)</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer font-bold text-xs text-amber-950">
                    <input type="radio" name="allow_resident_upload" value="0" class="text-amber-600 focus:ring-amber-500">
                    <span>( TIDAK ) Hanya admin yang mengunggah foto</span>
                </label>
            </div>
        </div>

        <!-- Status -->
        <div class="space-y-1">
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Status Acara / Kegiatan:</label>
            <select name="status" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-emerald-500 outline-none">
                <option value="PUBLISHED" selected>PUBLISHED (Terlihat di beranda & warga dapat upload)</option>
                <option value="DRAFT">DRAFT (Hanya terlihat oleh admin)</option>
                <option value="ARCHIVED">ARCHIVED (Dokumentasi diarsip)</option>
            </select>
        </div>

        <!-- Thumbnail Upload -->
        <div class="space-y-2">
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Foto Thumbnail Header (16:9):</label>
            <input type="file" name="thumbnail" accept="image/*" class="w-full text-xs text-slate-600 bg-slate-50 border border-slate-300 rounded-xl p-3">
            <p class="text-[11px] text-slate-400">Gambar akan dipotong otomatis menjadi rasio 16:9.</p>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('admin.events.index') }}" class="px-5 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold rounded-xl text-xs shadow-lg transition">
                🚀 Buat Acara / Kegiatan
            </button>
        </div>
    </form>
</div>
@endsection
