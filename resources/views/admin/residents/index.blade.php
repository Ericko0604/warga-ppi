@extends('layouts.admin')

@section('title', 'Kelola Data Warga')
@section('page-header', 'Data Warga Perumahan')

@section('content')
<div x-data="{ showAddModal: false, editModalData: null }">
    <!-- Header Controls -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <!-- Search & Filters -->
        <form action="{{ route('admin.residents.index') }}" method="GET" class="flex flex-wrap items-center gap-2 flex-grow">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama / nomor rumah..." class="bg-white border border-slate-300 rounded-xl px-4 py-2 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500 outline-none w-full sm:w-64 shadow-sm">

            <div class="flex items-center gap-1 overflow-x-auto">
                <a href="{{ route('admin.residents.index', ['search' => $search]) }}" class="px-3 py-2 rounded-xl text-xs font-semibold shadow-sm {{ empty($blockFilter) ? 'bg-slate-900 text-white' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
                    Semua
                </a>
                @foreach(['A1', 'A2', 'A3', 'A4', 'KAVLING'] as $b)
                    <a href="{{ route('admin.residents.index', ['block' => $b, 'search' => $search]) }}" class="px-3 py-2 rounded-xl text-xs font-semibold shadow-sm {{ $blockFilter === $b ? 'bg-emerald-600 text-white' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
                        {{ $b }}
                    </a>
                @endforeach
            </div>
        </form>

        <!-- Action Buttons -->
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.residents.qr_print', ['block' => $blockFilter]) }}" target="_blank" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-bold transition shadow-sm flex items-center gap-2">
                <span>🖨️</span> Cetak Daftar QR
            </a>
            <button @click="showAddModal = true" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-md flex items-center gap-2">
                <span>➕</span> Tambah Warga
            </button>
        </div>
    </div>

    <!-- Residents Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4">Blok & Rumah / Identitas</th>
                        <th class="px-6 py-4">Nama Kepala Keluarga</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Link Upload Token</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($residents as $resident)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 font-extrabold text-slate-900">
                                {{ $resident->display_label }}
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-700">
                                {{ $resident->family_head_name ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                @if($resident->status === 'ACTIVE')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        AKTIF
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                        NONAKTIF
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-mono text-[11px] text-slate-500">
                                <span class="bg-slate-100 px-2 py-1 rounded border border-slate-200">
                                    {{ Str::limit($resident->upload_token, 12, '...') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-1 whitespace-nowrap">
                                <a href="{{ route('admin.residents.qr', $resident->id) }}" class="inline-flex items-center gap-1 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 font-bold px-2.5 py-1.5 rounded-lg border border-indigo-200 transition">
                                    <span>📱</span> QR
                                </a>

                                <button @click="editModalData = {{ json_encode($resident) }}" class="inline-flex items-center gap-1 bg-slate-100 text-slate-700 hover:bg-slate-200 font-bold px-2.5 py-1.5 rounded-lg border border-slate-200 transition">
                                    <span>✏️</span> Edit
                                </button>

                                <form action="{{ route('admin.residents.regenerate_token', $resident->id) }}" method="POST" class="inline" onsubmit="return confirm('Regenerasi token upload warga ini?')">
                                    @csrf
                                    <button type="submit" title="Regenerasi Token" class="inline-flex items-center gap-1 bg-amber-50 text-amber-800 hover:bg-amber-100 font-bold px-2.5 py-1.5 rounded-lg border border-amber-200 transition">
                                        <span>🔑</span> Reset Token
                                    </button>
                                </form>

                                <form action="{{ route('admin.residents.toggle_status', $resident->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1 {{ $resident->status === 'ACTIVE' ? 'bg-rose-50 text-rose-700 hover:bg-rose-100 border-rose-200' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border-emerald-200' }} font-bold px-2.5 py-1.5 rounded-lg border transition">
                                        <span>{{ $resident->status === 'ACTIVE' ? '🚫 Matikan' : '✅ Aktifkan' }}</span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                Tidak ada data warga yang sesuai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($residents->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $residents->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Tambah Warga -->
    <div x-show="showAddModal" x-cloak class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 space-y-4 shadow-2xl border border-slate-200" @click.stop x-data="{ blockSelect: 'A1' }">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="font-extrabold text-slate-900 text-base">Tambah Data Warga Baru</h3>
                <button @click="showAddModal = false" class="text-slate-400 hover:text-slate-700">✕</button>
            </div>

            <form action="{{ route('admin.residents.store') }}" method="POST" class="space-y-4">
                @csrf

                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700">Pilih Blok:</label>
                    <select name="block" x-model="blockSelect" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-800 outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="A1">Blok A1</option>
                        <option value="A2">Blok A2</option>
                        <option value="A3">Blok A3</option>
                        <option value="A4">Blok A4</option>
                        <option value="KAVLING">Blok Kavling</option>
                    </select>
                </div>

                <div x-show="blockSelect !== 'KAVLING'" class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700">Nomor Rumah (misal: 07):</label>
                    <input type="text" name="house_number" placeholder="01" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-xs font-semibold text-slate-800 outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700">Nama Kepala Keluarga (Opsional untuk A1-A4, Wajib untuk Kavling):</label>
                    <input type="text" name="family_head_name" placeholder="Bapak Ahmad" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-xs font-semibold text-slate-800 outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="showAddModal = false" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs shadow-md">
                        Simpan Warga
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Warga -->
    <div x-show="editModalData" x-cloak class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 space-y-4 shadow-2xl border border-slate-200" @click.stop x-data="{ blockSelect: '' }" x-init="$watch('editModalData', v => { if(v) blockSelect = v.block })">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="font-extrabold text-slate-900 text-base">Edit Data Warga</h3>
                <button @click="editModalData = null" class="text-slate-400 hover:text-slate-700">✕</button>
            </div>

            <template x-if="editModalData">
                <form :action="'{{ url('/admin/residents') }}/' + editModalData.id" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-slate-700">Pilih Blok:</label>
                        <select name="block" x-model="blockSelect" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-800 outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="A1">Blok A1</option>
                            <option value="A2">Blok A2</option>
                            <option value="A3">Blok A3</option>
                            <option value="A4">Blok A4</option>
                            <option value="KAVLING">Blok Kavling</option>
                        </select>
                    </div>

                    <div x-show="blockSelect !== 'KAVLING'" class="space-y-1">
                        <label class="block text-xs font-bold text-slate-700">Nomor Rumah:</label>
                        <input type="text" name="house_number" :value="editModalData.house_number" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-xs font-semibold text-slate-800 outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-slate-700">Nama Kepala Keluarga:</label>
                        <input type="text" name="family_head_name" :value="editModalData.family_head_name" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-xs font-semibold text-slate-800 outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="editModalData = null" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs shadow-md">
                            Perbarui Data
                        </button>
                    </div>
                </form>
            </template>
        </div>
    </div>
</div>
@endsection
