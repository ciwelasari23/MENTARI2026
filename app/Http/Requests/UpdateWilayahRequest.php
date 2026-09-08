<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWilayahRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Menggunakan $this->id atau route untuk mengabaikan unique pada data miliknya sendiri
        $id = $this->route('wilayah'); 

        return [
            'nama_wilayah' => 'required|string|max:100',
            'level_wilayah' => 'required|integer',
        ];
    }
}