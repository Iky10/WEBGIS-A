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
        'x' => 'decimal:2',
        'y' => 'decimal:2'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'nama_gedung' => 'required',
        'alamat' => 'required',
        'x' => 'required',
        'y' => 'required'
    ];

    public function fasilitas()
    {
        return $this->hasMany(GedungFasilitas::class, 'gedung_id');
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

        $fasilitasIds = $this->fasilitas()->pluck('id');
        
        if ($fasilitasIds->isEmpty()) {
            return 'Kosong';
        }

        $sedangDipakai = \App\Models\JadwalRuangan::whereIn('gedung_fasilitas_id', $fasilitasIds)
            ->where('hari', $hariIni)
            ->where('jam_mulai', '<=', $waktuSekarang)
            ->where('jam_selesai', '>=', $waktuSekarang)
            ->exists();

        return $sedangDipakai ? 'Sedang Dipakai' : 'Kosong';
    }
}
