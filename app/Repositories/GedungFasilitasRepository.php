<?php

namespace App\Repositories;

use App\Models\GedungFasilitas;
use App\Repositories\BaseRepository;

/**
 * Class GedungFasilitasRepository
 * @package App\Repositories
 * @version April 14, 2026, 7:56 pm +07
*/

class GedungFasilitasRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'gedung_id',
        'nama_fasilitas',
        'kategori',
        'keterangan',
        'is_aktif',
        'latitude',
        'longitude',
        'foto_ruangan'
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
        return GedungFasilitas::class;
    }
}