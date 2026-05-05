<?php

namespace App\Repositories;

use App\Models\Vegetasi;
use App\Repositories\BaseRepository;

/**
 * Class VegetasiRepository
 * @package App\Repositories
*/

class VegetasiRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'gedung_id',
        'nama_vegetasi',
        'kategori',
        'keterangan',
        'latitude',
        'longitude',
        'foto_utama'
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
        return Vegetasi::class;
    }
}
