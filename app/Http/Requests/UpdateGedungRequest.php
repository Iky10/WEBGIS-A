<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Gedung;

class UpdateGedungRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = Gedung::$rules;
        
        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'nama_gedung.required' => 'Nama gedung wajib diisi.',
            'alamat.required' => 'Alamat wajib diisi.',
            'x.required' => 'Koordinat X (Latitude) wajib diisi.',
            'y.required' => 'Koordinat Y (Longitude) wajib diisi.'
        ];
    }
}
