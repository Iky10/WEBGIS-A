<?php

namespace App\Repositories;

use App\Models\Gedung;
use App\Repositories\BaseRepository;

/**
 * Class GedungRepository
 * @package App\Repositories
 * @version March 11, 2026, 1:15 pm UTC
*/

class GedungRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'nama_gedung',
        'alamat',
        'deskripsi',
        'fungsi',
        'jumlah_lantai',
        'tahun_berdiri',
        'kondisi',
        'x',
        'y'
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
        return Gedung::class;
    }
}
