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
        'is_aktif',
        'bisa_diajukan',
        'latitude',
        'longitude',
        'foto_ruangan'
    ];

    protected $casts = [
        'nama_fasilitas' => 'string',
        'kategori' => 'string',
        'keterangan' => 'string',
        'is_aktif' => 'boolean',
        'bisa_diajukan' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8'
    ];

    /**
     * Scope: ruangan yang boleh diajukan user untuk penggunaan ad-hoc.
     * Kombinasikan dengan is_aktif untuk filter form pengajuan:
     *   GedungFasilitas::bisaDiajukan()->aktif()->get()
     */
    public function scopeBisaDiajukan($query)
    {
        return $query->where('bisa_diajukan', true);
    }

    /**
     * Scope: ruangan yang sedang aktif operasional (tidak dalam perbaikan).
     */
    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true);
    }

    public function gedung()
    {
        return $this->belongsTo(Gedung::class, 'gedung_id');
    }

    public function jadwalRuangans()
    {
        return $this->hasMany(JadwalRuangan::class, 'gedung_fasilitas_id');
    }

    public function pengajuanRuangans()
    {
        return $this->hasMany(PengajuanRuangan::class, 'gedung_fasilitas_id');
    }

    /**
     * Cache key untuk status_dipakai ruangan ini (di-shard per menit).
     */
    public function statusCacheKey()
    {
        return 'ruangan.status.' . $this->id . '.' . now()->format('YmdHi');
    }

    /**
     * Hapus cache status realtime ruangan ini.
     * Dipanggil setelah pengajuan baru dibuat, disetujui, ditolak, atau dihapus.
     */
    public function flushStatusCache()
    {
        \Cache::forget('ruangan.status.' . $this->id . '.' . now()->format('YmdHi'));
        \Cache::forget('ruangan.status.' . $this->id . '.' . now()->subMinute()->format('YmdHi'));
    }

    /**
     * Status realtime ruangan: Tutup | Sedang Dipakai | Kosong.
     *
     * Urutan pengecekan:
     *   0. Jam operasional gedung induk → di luar jam = Tutup
     *   1. Jadwal ruangan reguler (semester) overlap waktu sekarang = Sedang Dipakai
     *   2. Pengajuan ruangan ini yang disetujui & sedang aktif = Sedang Dipakai
     *   3. Default = Kosong
     */
    public function getStatusDipakaiAttribute()
    {
        return \Cache::remember($this->statusCacheKey(), 60, function () {
            return $this->computeStatusDipakai();
        });
    }

    /**
     * Hitung status realtime ruangan. Terpisah dari accessor untuk dukung caching.
     */
    protected function computeStatusDipakai()
    {
        $waktuSekarang  = date('H:i:s');
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

        // 1. Cek jadwal ruangan reguler (semester)
        $sedangDipakaiRutin = \App\Models\JadwalRuangan::where('gedung_fasilitas_id', $this->id)
            ->where('hari', $hariIni)
            ->where('jam_mulai', '<=', $waktuSekarang)
            ->where('jam_selesai', '>', $waktuSekarang)
            ->exists();

        if ($sedangDipakaiRutin) {
            return 'Sedang Dipakai';
        }

        // 2. Cek pengajuan ruangan ini yang disetujui & sedang aktif
        $sedangDipakaiPengajuan = \App\Models\PengajuanRuangan::where('gedung_fasilitas_id', $this->id)
            ->where('status', 'disetujui')
            ->where('tanggal_mulai', '<=', $tanggalHariIni)
            ->where('tanggal_selesai', '>=', $tanggalHariIni)
            ->where('jam_mulai', '<=', $waktuSekarang)
            ->where('jam_selesai', '>', $waktuSekarang)
            ->exists();

        if ($sedangDipakaiPengajuan) {
            return 'Sedang Dipakai';
        }

        return 'Kosong';
    }
}
