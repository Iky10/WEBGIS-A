<?php

namespace App\Repositories;

use App\Models\JadwalRuangan;
use App\Repositories\BaseRepository;

/**
 * Class JadwalRuanganRepository
 * @package App\Repositories
 * @version April 14, 2026, 8:16 pm +07
*/

class JadwalRuanganRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'gedung_fasilitas_id',
        'nama_kegiatan',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'keterangan'
    ];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return JadwalRuangan::class;
    }
}