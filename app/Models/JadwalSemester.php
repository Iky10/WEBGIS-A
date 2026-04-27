<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JadwalSemester extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'jadwal_semester';

    public $fillable = [
        'gedung_id',
        'semester',
        'tahun_ajaran',
        'file_jadwal',
        'keterangan'
    ];

    protected $casts = [
        'semester' => 'integer',
        'tahun_ajaran' => 'string',
        'file_jadwal' => 'string',
        'keterangan' => 'string'
    ];

    public function gedung()
    {
        return $this->belongsTo(Gedung::class, 'gedung_id');
    }
}
