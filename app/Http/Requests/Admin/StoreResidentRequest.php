<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreResidentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'block' => 'required|in:A1,A2,A3,A4,KAVLING',
            'house_number' => 'nullable|string|max:10',
            'family_head_name' => 'nullable|string|max:100',
        ];

        if ($this->input('block') === 'KAVLING') {
            $rules['family_head_name'] = 'required|string|max:100';
        } else {
            $rules['house_number'] = 'required|string|max:10';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'block.required' => 'Pilih blok perumahan.',
            'block.in' => 'Blok tidak valid.',
            'house_number.required' => 'Nomor rumah wajib diisi untuk Blok A1-A4.',
            'family_head_name.required' => 'Nama Kepala Keluarga wajib diisi untuk Blok Kavling.',
        ];
    }
}
