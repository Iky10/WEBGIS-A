<?php

namespace App\Repositories;

use App\Models\PengajuanGedung;
use App\Repositories\BaseRepository;

class PengajuanGedungRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'kode_pengajuan',
        'gedung_id',
        'user_id',
        'nama_pemohon',
        'email_pemohon',
        'jenis_kegiatan',
        'nama_kegiatan',
        'status',
    ];

    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    public function model()
    {
        return PengajuanGedung::class;
    }
}
