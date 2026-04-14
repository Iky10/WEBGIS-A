<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GedungFasilitas extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'gedung_fasilitas';

    public $fillable = [
        'gedung_id',
        'nama_fasilitas',
        'kategori',
        'keterangan'
    ];

    protected $casts = [
        'nama_fasilitas' => 'string',
        'kategori' => 'string',
        'keterangan' => 'string'
    ];

    public function gedung()
    {
        return $this->belongsTo(Gedung::class, 'gedung_id');
    }
}
