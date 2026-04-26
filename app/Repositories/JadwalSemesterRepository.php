<?php

namespace App\Repositories;

use App\Models\JadwalSemester;
use App\Repositories\BaseRepository;

/**
 * Class JadwalSemesterRepository
 * @package App\Repositories
 * @version April 24, 2026
*/

class JadwalSemesterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'gedung_fasilitas_id',
        'semester',
        'tahun_ajaran',
        'file_jadwal',
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
        return JadwalSemester::class;
    }
}
