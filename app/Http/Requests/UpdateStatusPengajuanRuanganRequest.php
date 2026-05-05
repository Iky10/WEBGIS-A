<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Admin: update status pengajuan ruangan (disetujui/ditolak).
 * Catatan admin wajib diisi jika status = ditolak.
 */
class UpdateStatusPengajuanRuanganRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules()
    {
        return [
            'status'        => 'required|in:disetujui,ditolak',
            'catatan_admin' => 'required_if:status,ditolak|nullable|string|max:1000',
        ];
    }

    public function messages()
    {
        return [
            'status.required'           => 'Status wajib dipilih.',
            'status.in'                 => 'Status hanya boleh disetujui atau ditolak.',
            'catatan_admin.required_if' => 'Catatan wajib diisi saat menolak pengajuan (sebagai alasan untuk pemohon).',
            'catatan_admin.max'         => 'Catatan maksimal 1000 karakter.',
        ];
    }
}
