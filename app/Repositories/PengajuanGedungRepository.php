<?php

namespace App\Repositories;

use App\Models\PengajuanGedung;
use App\Repositories\BaseRepository;

/**
 * Class PengajuanGedungRepository
 * @package App\Repositories
 * @version April 22, 2026
 */

class PengajuanGedungRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'nama_pemohon',
        'email_pemohon',
        'jenis_kegiatan',
        'nama_kegiatan',
        'status',
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
        return PengajuanGedung::class;
    }
}
