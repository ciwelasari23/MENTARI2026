<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWilayahRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            // Gunakan id_wilayah, hapus jika ada kode_wilayah
            'id_wilayah' => 'required|string|max:50|unique:mst_wilayah,id_wilayah', 
            'nama_wilayah' => 'required|string|max:100',
            'level_wilayah' => 'required|integer',
            'id_parent_wilayah' => 'nullable|string',
        ];
    }
}