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
        'keterangan',
        'latitude',
        'longitude',
        'foto_ruangan'
    ];

    protected $casts = [
        'nama_fasilitas' => 'string',
        'kategori' => 'string',
        'keterangan' => 'string',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8'
    ];

    public function gedung()
    {
        return $this->belongsTo(Gedung::class, 'gedung_id');
    }

    public function getStatusDipakaiAttribute()
    {
        $hariMap = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];
        
        $hariIni = $hariMap[date('l')];
        $waktuSekarang = date('H:i:s');

        $sedangDipakai = \App\Models\JadwalRuangan::where('gedung_fasilitas_id', $this->id)
            ->where('hari', $hariIni)
            ->where('jam_mulai', '<=', $waktuSekarang)
            ->where('jam_selesai', '>=', $waktuSekarang)
            ->exists();

        return $sedangDipakai ? 'Sedang Dipakai' : 'Kosong';
    }
}
