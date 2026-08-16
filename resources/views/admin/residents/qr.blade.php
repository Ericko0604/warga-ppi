@extends('layouts.admin')

@section('title', 'QR Code - ' . $resident->display_label)
@section('page-header', 'QR Code Warga')

@section('content')
<div class="max-w-md mx-auto bg-white rounded-3xl border border-slate-200 p-8 shadow-xl text-center space-y-6">
    <div class="space-y-1">
        <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider block">QR Code Upload Token</span>
        <h2 class="text-2xl font-extrabold text-slate-900">{{ $resident->display_label }}</h2>
    </div>

    <!-- QR Code SVG Render -->
    <div class="p-6 bg-slate-50 border border-slate-200 rounded-2xl flex items-center justify-center shadow-inner inline-block">
        {!! $qrSvg !!}
    </div>

    <div class="bg-slate-100 rounded-xl p-3 text-[11px] font-mono text-slate-600 break-all select-all">
        {{ $uploadUrl }}
    </div>

    <div class="flex items-center justify-center gap-3 pt-2">
        <a href="{{ route('admin.residents.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition">
            ← Kembali
        </a>
        <button onclick="window.print()" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs shadow-md transition flex items-center gap-2">
            <span>🖨️</span> Cetak QR Ini
        </button>
    </div>
</div>
@endsection
