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

    /**
     * Status realtime ruangan: Tutup | Sedang Dipakai | Kosong.
     *
     * Urutan pengecekan:
     *   0. Jam operasional gedung induk → di luar jam = Tutup
     *   1. Jadwal ruangan reguler (semester) overlap waktu sekarang = Sedang Dipakai
     *   2. Pengajuan gedung induk yang disetujui & sedang aktif = Sedang Dipakai
     *      (ruangan inherit status dari gedung kalau gedung sedang dipakai)
     *   3. Default = Kosong
     *
     * Pakai string compare untuk konsistensi dengan Gedung::getStatusDipakaiAttribute().
     */
    public function getStatusDipakaiAttribute()
    {
        $waktuSekarang = date('H:i:s');
        $tanggalHariIni = now()->toDateString();

        // 0. Cek jam operasional gedung induk
        $gedung = $this->gedung;
        if ($gedung && $gedung->jam_buka && $gedung->jam_tutup) {
            if ($waktuSekarang < $gedung->jam_buka || $waktuSekarang > $gedung->jam_tutup) {
                return 'Tutup';
            }
        }

        $hariMap = [
            'Sunday'    => 'Minggu',
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu',
        ];
        $hariIni = $hariMap[date('l')];

        // 1. Cek jadwal ruangan reguler
        $sedangDipakaiRutin = \App\Models\JadwalRuangan::where('gedung_fasilitas_id', $this->id)
            ->where('hari', $hariIni)
            ->where('jam_mulai', '<=', $waktuSekarang)
            ->where('jam_selesai', '>', $waktuSekarang)
            ->exists();

        if ($sedangDipakaiRutin) {
            return 'Sedang Dipakai';
        }

        // 2. Inherit dari pengajuan gedung induk yang disetujui & aktif
        if ($this->gedung_id) {
            $sedangDipakaiPengajuan = \App\Models\PengajuanGedung::where('gedung_id', $this->gedung_id)
                ->where('status', 'disetujui')
                ->where('tanggal_mulai', '<=', $tanggalHariIni)
                ->where('tanggal_selesai', '>=', $tanggalHariIni)
                ->where('jam_mulai', '<=', $waktuSekarang)
                ->where('jam_selesai', '>', $waktuSekarang)
                ->exists();

            if ($sedangDipakaiPengajuan) {
                return 'Sedang Dipakai';
            }
        }

        return 'Kosong';
    }
}
