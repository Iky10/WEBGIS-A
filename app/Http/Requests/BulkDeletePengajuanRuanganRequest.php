<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Admin: bulk delete pengajuan ruangan via AJAX.
 */
class BulkDeletePengajuanRuanganRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array|min:1',
            'ids.*' => 'integer|exists:pengajuan_ruangans,id',
        ];
    }

    public function messages()
    {
        return [
            'ids.required' => 'Minimal satu pengajuan harus dipilih.',
            'ids.array'    => 'Format data tidak valid.',
            'ids.min'      => 'Minimal satu pengajuan harus dipilih.',
            'ids.*.exists' => 'Salah satu pengajuan tidak ditemukan.',
        ];
    }
}
