<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * AJAX: cek ketersediaan ruangan pada rentang tanggal + jam tertentu.
 * Dipakai saat live availability check di form pengajuan.
 */
class CekKetersediaanRequest extends FormRequest
{
    public function authorize()
    {
        // Route sudah dilindungi middleware auth; cukup cek user login.
        return $this->user() !== null;
    }

    public function rules()
    {
        return [
            'gedung_fasilitas_id' => 'required|exists:gedung_fasilitas,id',
            'tanggal_mulai'       => 'required|date',
            'tanggal_selesai'     => 'required|date|after_or_equal:tanggal_mulai',
            'jam_mulai'           => 'required',
            'jam_selesai'         => 'required|after:jam_mulai',
        ];
    }

    public function messages()
    {
        return [
            'gedung_fasilitas_id.required' => 'Ruangan wajib dipilih.',
            'gedung_fasilitas_id.exists'   => 'Ruangan tidak ditemukan.',
            'tanggal_mulai.required'       => 'Tanggal mulai wajib diisi.',
            'tanggal_selesai.required'     => 'Tanggal selesai wajib diisi.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
            'jam_mulai.required'           => 'Jam mulai wajib diisi.',
            'jam_selesai.required'         => 'Jam selesai wajib diisi.',
            'jam_selesai.after'            => 'Jam selesai harus setelah jam mulai.',
        ];
    }
}
