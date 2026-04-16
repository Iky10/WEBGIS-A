<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JadwalRuangan extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'jadwal_ruangans';

    public $fillable = [
        'gedung_fasilitas_id',
        'nama_kegiatan',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'keterangan'
    ];

    protected $casts = [
        'nama_kegiatan' => 'string',
        'hari' => 'string',
        'keterangan' => 'string'
    ];

    public function fasilitas()
    {
        return $this->belongsTo(GedungFasilitas::class, 'gedung_fasilitas_id');
    }
}