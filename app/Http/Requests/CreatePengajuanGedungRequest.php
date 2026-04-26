<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\PengajuanGedung;

class CreatePengajuanGedungRequest extends FormRequest
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
        return PengajuanGedung::$rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'gedung_id.required'       => 'Gedung wajib dipilih.',
            'nama_pemohon.required'    => 'Nama pemohon wajib diisi.',
            'email_pemohon.required'   => 'Email wajib diisi.',
            'email_pemohon.email'      => 'Format email tidak valid.',
            'no_telepon.required'      => 'Nomor telepon wajib diisi.',
            'asal_instansi.required'   => 'Asal instansi wajib diisi.',
            'jenis_kegiatan.required'  => 'Jenis kegiatan wajib dipilih.',
            'nama_kegiatan.required'   => 'Nama kegiatan wajib diisi.',
            'tanggal_mulai.required'   => 'Tanggal mulai wajib diisi.',
            'tanggal_selesai.required' => 'Tanggal selesai wajib diisi.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
            'jam_mulai.required'       => 'Jam mulai wajib diisi.',
            'jam_selesai.required'     => 'Jam selesai wajib diisi.',
        ];
    }
}
