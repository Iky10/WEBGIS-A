<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class PengajuanGedung
 * @package App\Models
 * @version April 22, 2026
 *
 * @property string $nama_pemohon
 * @property string $email_pemohon
 * @property string $no_telepon
 * @property string $asal_instansi
 * @property string $jenis_kegiatan
 * @property string $nama_kegiatan
 * @property string $tanggal_mulai
 * @property string $tanggal_selesai
 * @property string $jam_mulai
 * @property string $jam_selesai
 * @property integer $jumlah_peserta
 * @property string $keperluan
 * @property string $status
 * @property string $catatan_admin
 */
class PengajuanGedung extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'pengajuan_gedungs';

    protected $dates = ['deleted_at'];

    public $fillable = [
        'gedung_id',
        'user_id',
        'nama_pemohon',
        'email_pemohon',
        'no_telepon',
        'asal_instansi',
        'jenis_kegiatan',
        'nama_kegiatan',
        'tanggal_mulai',
        'tanggal_selesai',
        'jam_mulai',
        'jam_selesai',
        'jumlah_peserta',
        'keperluan',
        'status',
        'catatan_admin',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'nama_pemohon' => 'string',
        'email_pemohon' => 'string',
        'no_telepon' => 'string',
        'asal_instansi' => 'string',
        'jenis_kegiatan' => 'string',
        'nama_kegiatan' => 'string',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'jam_mulai' => 'string',
        'jam_selesai' => 'string',
        'jumlah_peserta' => 'integer',
        'keperluan' => 'string',
        'status' => 'string',
        'catatan_admin' => 'string',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'gedung_id' => 'required|exists:gedungs,id',
        'nama_pemohon' => 'required|string|max:255',
        'email_pemohon' => 'required|email|max:255',
        'jenis_kegiatan' => 'required|string|max:255',
        'nama_kegiatan' => 'required|string|max:255',
        'tanggal_mulai' => 'required|date',
        'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        'jam_mulai' => 'required',
        'jam_selesai' => 'required',
    ];

    /**
     * Relasi ke Gedung
     */
    public function gedung()
    {
        return $this->belongsTo(Gedung::class, 'gedung_id');
    }

    /**
     * Relasi ke User (pemohon)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
