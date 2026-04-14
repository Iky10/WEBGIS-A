<?php

namespace App\Repositories;

use App\Models\GambarGedung;
use App\Repositories\BaseRepository;

/**
 * Class GambarGedungRepository
 * @package App\Repositories
 * @version March 11, 2026, 1:31 pm UTC
*/

class GambarGedungRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'gedung_id',
        'nama_file',
        'path_foto',
        'keterangan',
        'urutan'
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
        return GambarGedung::class;
    }
}
