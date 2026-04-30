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

    public function jadwalRuangans()
    {
        return $this->hasMany(JadwalRuangan::class, 'gedung_fasilitas_id');
    }

    public function getStatusDipakaiAttribute()
    {
        // Cek jam operasional gedung induk terlebih dahulu
        $gedung = $this->gedung;
        if ($gedung && $gedung->jam_buka && $gedung->jam_tutup) {
            $now = \Carbon\Carbon::now();
            $jamBuka = \Carbon\Carbon::createFromFormat('H:i:s', $gedung->jam_buka);
            $jamTutup = \Carbon\Carbon::createFromFormat('H:i:s', $gedung->jam_tutup);

            if ($now->lt($jamBuka) || $now->gt($jamTutup)) {
                return 'Tutup';
            }
        }

        // Jika gedung buka, cek jadwal ruangan
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
