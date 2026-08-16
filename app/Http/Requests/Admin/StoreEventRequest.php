<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => 'required|in:ACARA,KEGIATAN',
            'name' => 'required|string|max:150',
            'event_date' => 'required|date',
            'description' => 'nullable|string|max:2000',
            'allow_resident_upload' => 'boolean',
            'status' => 'required|in:DRAFT,PUBLISHED,ARCHIVED',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Jenis konten wajib dipilih.',
            'name.required' => 'Nama acara/kegiatan wajib diisi.',
            'event_date.required' => 'Tanggal acara/kegiatan wajib diisi.',
            'thumbnail.image' => 'File thumbnail harus berupa gambar.',
            'thumbnail.max' => 'Ukuran thumbnail maksimal 10MB.',
        ];
    }
}
