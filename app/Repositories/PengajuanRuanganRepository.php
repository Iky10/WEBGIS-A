<?php

namespace App\Repositories;

use App\Models\PengajuanRuangan;
use App\Repositories\BaseRepository;

class PengajuanRuanganRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'kode_pengajuan',
        'gedung_fasilitas_id',
        'user_id',
        'nama_pemohon',
        'email_pemohon',
        'no_telepon',
        'asal_instansi',
        'jenis_kegiatan',
        'nama_kegiatan',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
    ];

    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    public function model()
    {
        return PengajuanRuangan::class;
    }
}
