<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class GambarGedung
 * @package App\Models
 * @version March 11, 2026, 1:31 pm UTC
 *
 * @property integer $gedung_id
 * @property string $nama_file
 * @property string $path_foto
 * @property string $keterangan
 * @property integer $urutan
 */
class GambarGedung extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'gambar_gedungs';
    

    protected $dates = ['deleted_at'];



    public $fillable = [
        'gedung_id',
        'nama_file',
        'path_foto',
        'keterangan',
        'urutan'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'gedung_id' => 'integer',
        'nama_file' => 'string',
        'path_foto' => 'string',
        'keterangan' => 'string',
        'urutan' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'gedung_id' => 'required',
        'nama_file' => 'required',
        'path_foto' => 'required',
        'urutan' => 'required'
    ];

    
}
