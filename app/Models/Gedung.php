<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Gedung
 * @package App\Models
 * @version March 11, 2026, 1:15 pm UTC
 *
 * @property string $nama_gedung
 * @property string $alamat
 * @property string $deskripsi
 * @property string $fungsi
 * @property integer $jumlah_lantai
 * @property integer $tahun_berdiri
 * @property string $kondisi
 * @property number $x
 * @property number $y
 */
class Gedung extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'gedungs';
    

    protected $dates = ['deleted_at'];



    public $fillable = [
        'nama_gedung',
        'alamat',
        'deskripsi',
        'x',
        'y',
        'foto_utama',
        'bisa_diajukan',
        'jam_buka',
        'jam_tutup',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'nama_gedung' => 'string',
        'alamat' => 'string',
        'deskripsi' => 'string',
        'x' => 'decimal:8',
        'y' => 'decimal:8',
        'bisa_diajukan' => 'boolean',
    ];

    /**
     * Accessor: format jam_buka untuk form input (HH:MM)
     */
    public function getJamBukaFormattedAttribute()
    {
        return $this->jam_buka ? \Carbon\Carbon::parse($this->jam_buka)->format('H:i') : null;
    }

    /**
     * Accessor: format jam_tutup untuk form input (HH:MM)
     */
    public function getJamTutupFormattedAttribute()
    {
        return $this->jam_tutup ? \Carbon\Carbon::parse($this->jam_tutup)->format('H:i') : null;
    }

    /**
     * Scope: hanya gedung yang bisa diajukan penggunaan.
     */
    public function scopeBisaDiajukan($query)
    {
        return $query->where('bisa_diajukan', true);
    }

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'nama_gedung' => 'required',
        'alamat' => 'required',
        'x' => 'required',
        'y' => 'required',
        'jam_buka' => 'nullable|date_format:H:i',
        'jam_tutup' => 'nullable|date_format:H:i|after:jam_buka',
    ];

    public function fasilitas()
    {
        return $this->hasMany(GedungFasilitas::class, 'gedung_id');
    }

    public function jadwalSemester()
    {
        return $this->hasMany(JadwalSemester::class, 'gedung_id');
    }

    /**
     * Cache key untuk status_dipakai gedung ini (di-shard per menit).
     */
    public function statusCacheKey()
    {
        return 'gedung.status.' . $this->id . '.' . now()->format('YmdHi');
    }

    /**
     * Hapus cache status realtime gedung ini (dipanggil setelah update pengajuan/jadwal).
     */
    public function flushStatusCache()
    {
        // Hapus key untuk menit saat ini & menit sebelumnya (kalau pengajuan baru di-approve)
        \Cache::forget('gedung.status.' . $this->id . '.' . now()->format('YmdHi'));
        \Cache::forget('gedung.status.' . $this->id . '.' . now()->subMinute()->format('YmdHi'));
    }

    public function getStatusDipakaiAttribute()
    {
        // Cache 60 detik per gedung untuk hindari N+1 di endpoint /webgis/geojson
        return \Cache::remember($this->statusCacheKey(), 60, function () {
            return $this->computeStatusDipakai();
        });
    }

    /**
     * Hitung status realtime gedung. Terpisah dari accessor untuk dukung caching.
     */
    protected function computeStatusDipakai()
    {
        $waktuSekarang = date('H:i:s');

        // 0. Cek jam operasional — jika di luar jam buka, status = Tutup
        if ($this->jam_buka && $this->jam_tutup) {
            if ($waktuSekarang < $this->jam_buka || $waktuSekarang > $this->jam_tutup) {
                return 'Tutup';
            }
        }

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
        $tanggalHariIni = now()->toDateString();

        // 1. Cek jadwal ruangan reguler (semester)
        // Pakai '>' (exclusive) untuk jam_selesai supaya saat acara tepat selesai, status langsung jadi Kosong
        $fasilitasIds = $this->fasilitas()->pluck('id');

        if ($fasilitasIds->isNotEmpty()) {
            $sedangDipakaiRutin = \App\Models\JadwalRuangan::whereIn('gedung_fasilitas_id', $fasilitasIds)
                ->where('hari', $hariIni)
                ->where('jam_mulai', '<=', $waktuSekarang)
                ->where('jam_selesai', '>', $waktuSekarang)
                ->exists();

            if ($sedangDipakaiRutin) {
                return 'Sedang Dipakai';
            }
        }

        // 2. Cek pengajuan ruangan yang disetujui pada hari & jam ini
        //    (gedung dianggap "Sedang Dipakai" kalau minimal SATU ruangannya dipakai)
        if ($fasilitasIds->isNotEmpty()) {
            $sedangDipakaiPengajuan = \App\Models\PengajuanRuangan::whereIn('gedung_fasilitas_id', $fasilitasIds)
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
