<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vegetasi extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'vegetasis';

    public $fillable = [
        'gedung_id',
        'nama_vegetasi',
        'kategori',
        'keterangan',
        'latitude',
        'longitude',
        'foto_utama'
    ];

    protected $casts = [
        'nama_vegetasi' => 'string',
        'kategori' => 'string',
        'keterangan' => 'string',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8'
    ];

    public function gedung()
    {
        return $this->belongsTo(Gedung::class, 'gedung_id');
    }

    public function gambarVegetasis()
    {
        return $this->hasMany(GambarVegetasi::class, 'vegetasi_id');
    }
}
