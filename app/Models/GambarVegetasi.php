<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GambarVegetasi extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'gambar_vegetasis';

    public $fillable = [
        'vegetasi_id',
        'nama_file',
        'path_foto',
        'keterangan',
        'urutan'
    ];

    protected $casts = [
        'nama_file' => 'string',
        'path_foto' => 'string',
        'keterangan' => 'string',
        'urutan' => 'integer'
    ];

    public function vegetasi()
    {
        return $this->belongsTo(Vegetasi::class, 'vegetasi_id');
    }
}
