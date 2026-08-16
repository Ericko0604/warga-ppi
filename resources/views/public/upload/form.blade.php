<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Foto Dokumentasi Warga</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-emerald-900 text-slate-800 min-h-screen flex flex-col justify-between p-4 sm:p-6">
    <div class="max-w-md w-auto mx-auto my-auto bg-white rounded-3xl shadow-2xl overflow-hidden border border-emerald-700/50">
        <!-- Header Banner -->
        <div class="bg-gradient-to-r from-emerald-600 to-teal-700 text-white p-6 text-center space-y-1">
            <span class="text-4xl block mb-1">📷</span>
            <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight">Dokumentasi Warga</h1>
            <p class="text-xs text-emerald-100 font-medium">Kirim foto dokumentasi rumah Anda</p>
        </div>

        <div class="p-6 space-y-6">
            <!-- Resident Identity Card -->
            <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 text-center">
                <span class="text-xs text-emerald-700 font-bold uppercase tracking-wider block mb-0.5">Identitas Rumah Anda:</span>
                <span class="text-xl font-extrabold text-emerald-900 block">
                    {{ $resident->display_label }}
                </span>
            </div>

            <!-- Error Banner -->
            @if(session('error_message'))
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs font-semibold flex items-center gap-2">
                    <span class="text-lg">⚠️</span>
                    <div>{{ session('error_message') }}</div>
                </div>
            @endif

            @if(!$currentEvent)
                <div class="text-center py-6 text-slate-500 space-y-2">
                    <span class="text-4xl block">📭</span>
                    <p class="text-sm font-bold text-slate-700">Saat Ini Belum Ada Acara Aktif</p>
                    <p class="text-xs">Pengisian foto dokumentasi belum dibuka oleh pengurus.</p>
                </div>
            @else
                <!-- Form -->
                <form action="{{ route('resident.upload.store', ['token' => $resident->upload_token]) }}" method="POST" enctype="multipart/form-data" x-data="uploadForm()" @submit="handleSubmit">
                    @csrf

                    <!-- Event Select / Display -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Pilih Acara / Kegiatan:
                        </label>

                        @if($availableEvents->count() > 1)
                            <select name="event_id" @change="onEventChange" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-emerald-500 outline-none">
                                @foreach($availableEvents as $eventItem)
                                    <option value="{{ $eventItem->id }}" {{ $currentEvent && $currentEvent->id === $eventItem->id ? 'selected' : '' }}>
                                        {{ $eventItem->name }} ({{ $eventItem->event_date->format('d/m/Y') }})
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <input type="hidden" name="event_id" value="{{ $currentEvent->id }}">
                            <div class="bg-slate-100 border border-slate-200 rounded-xl p-3 text-sm font-bold text-slate-800">
                                {{ $currentEvent->name }}
                                <span class="block text-xs font-normal text-slate-500 mt-0.5">📅 {{ $currentEvent->event_date->translatedFormat('d F Y') }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Existing Photo Notice (Replace mode) -->
                    @if($existingPhoto)
                        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 space-y-2 text-center">
                            <p class="text-xs font-bold text-amber-900">
                                ℹ️ Foto rumah Anda untuk acara ini sudah terdaftar:
                            </p>
                            <div class="aspect-video w-full rounded-xl overflow-hidden border border-amber-300 bg-black">
                                <img src="{{ $existingPhoto->thumbnail_url }}" alt="Foto lama" class="w-full h-full object-cover">
                            </div>
                            <p class="text-[11px] text-amber-800">
                                Mengunggah foto baru akan **mengganti** foto lama secara otomatis.
                            </p>
                        </div>
                    @endif

                    <!-- Landscape Requirement Notice -->
                    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-3 text-xs text-blue-900 font-medium flex items-center gap-2">
                        <span class="text-base">📱</span>
                        <span>Foto WAJIB berformat **landscape** (HP posisi tidur / melebarkan samping).</span>
                    </div>

                    <!-- File Picker Button -->
                    <div class="space-y-3 pt-2">
                        <label class="block cursor-pointer">
                            <div class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold py-4 px-6 rounded-2xl text-center shadow-lg transition transform active:scale-95 flex items-center justify-center gap-3 text-base">
                                <span>📷</span>
                                <span x-text="previewUrl ? 'Ganti Pilihan Foto' : 'PILIH FOTO DARI HP'"></span>
                            </div>
                            <input type="file" name="photo" accept="image/*" capture="environment" @change="handleFileSelect" class="hidden" required>
                        </label>

                        <!-- Error Message if Portrait -->
                        <div x-show="errorMessage" x-cloak class="p-3 bg-rose-50 border border-rose-300 text-rose-800 rounded-xl text-xs font-bold flex items-center gap-2">
                            <span>⚠️</span>
                            <span x-text="errorMessage"></span>
                        </div>

                        <!-- Instant Preview -->
                        <div x-show="previewUrl" x-cloak class="space-y-2">
                            <p class="text-xs font-bold text-slate-700 text-center">Preview Foto Yang Akan Dikirim:</p>
                            <div class="aspect-video w-full rounded-2xl overflow-hidden border-2 border-emerald-500 bg-slate-900">
                                <img :src="previewUrl" class="w-full h-full object-cover">
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" :disabled="isSubmitting || !!errorMessage || !previewUrl" :class="(!isSubmitting && !errorMessage && previewUrl) ? 'bg-slate-900 hover:bg-black text-white shadow-xl cursor-pointer' : 'bg-slate-300 text-slate-500 cursor-not-allowed'" class="w-full font-extrabold py-4 px-6 rounded-2xl text-center transition transform active:scale-95 text-base flex items-center justify-center gap-2 mt-4">
                            <template x-if="isSubmitting">
                                <span>⏳ Sedang Mengirim Foto...</span>
                            </template>
                            <template x-if="!isSubmitting">
                                <span>🚀 {{ $existingPhoto ? 'SIMPAN & GANTI FOTO' : 'KIRIM FOTO DOKUMENTASI' }}</span>
                            </template>
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>

    <script>
        function uploadForm() {
            return {
                previewUrl: null,
                errorMessage: '',
                isSubmitting: false,

                onEventChange(e) {
                    const eventId = e.target.value;
                    const url = new URL(window.location.href);
                    url.searchParams.set('event_id', eventId);
                    window.location.href = url.toString();
                },

                handleFileSelect(e) {
                    const file = e.target.files[0];
                    if (!file) return;

                    this.errorMessage = '';
                    this.previewUrl = null;

                    const img = new Image();
                    img.src = URL.createObjectURL(file);

                    img.onload = () => {
                        if (img.width <= img.height) {
                            this.errorMessage = 'Foto harus berformat landscape (posisi tidur). Silakan pilih foto lain.';
                            return;
                        }
                        this.previewUrl = img.src;
                    };
                },

                handleSubmit() {
                    if (this.previewUrl && !this.errorMessage) {
                        this.isSubmitting = true;
                    }
                }
            }
        }
    </script>
</body>
</html>
