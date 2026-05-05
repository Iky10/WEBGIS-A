<?php

namespace App\Repositories;

use App\Models\AppSetting;
use App\Repositories\BaseRepository;

/**
 * Class AppSettingRepository
 * @package App\Repositories
 * @version May 02, 2026
 */
class AppSettingRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'key',
        'value',
    ];

    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    public function model()
    {
        return AppSetting::class;
    }
}
