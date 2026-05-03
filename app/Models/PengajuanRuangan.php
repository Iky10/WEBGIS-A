<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

/**
 * Pengajuan penggunaan ruangan spesifik (bukan gedung utuh).
 * Mengacu ke GedungFasilitas (= tabel ruangan) sebagai unit booking.
 */
class PengajuanRuangan extends Model
{
    use SoftDeletes, HasFactory;

    /**
     * Prefix kode pengajuan (PR = Pengajuan Ruangan).
     * Diekstrak jadi constant supaya reusable & maintainable.
     */
    const KODE_PREFIX = 'PR-';

    /**
     * Status values — pakai constant supaya tidak ada typo di controller / view.
     */
    const STATUS_DIPROSES  = 'diproses';
    const STATUS_DISETUJUI = 'disetujui';
    const STATUS_DITOLAK   = 'ditolak';

    public $table = 'pengajuan_ruangans';

    public $fillable = [
        'kode_pengajuan',
        'gedung_fasilitas_id',
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
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'nama_pemohon'    => 'string',
        'email_pemohon'   => 'string',
        'no_telepon'      => 'string',
        'asal_instansi'   => 'string',
        'jenis_kegiatan'  => 'string',
        'nama_kegiatan'   => 'string',
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
        'jumlah_peserta'  => 'integer',
        'approved_at'     => 'datetime',
        'deleted_at'      => 'datetime',
    ];

    public static $rules = [
        'gedung_fasilitas_id' => 'required|exists:gedung_fasilitas,id',
        'nama_pemohon'        => 'required|string|max:255',
        'email_pemohon'       => 'required|email|max:255',
        'no_telepon'          => 'required|string|max:20',
        'asal_instansi'       => 'required|string|max:255',
        'jenis_kegiatan'      => 'required|string|max:255',
        'nama_kegiatan'       => 'required|string|max:255',
        'tanggal_mulai'       => 'required|date|after_or_equal:today',
        'tanggal_selesai'     => 'required|date|after_or_equal:tanggal_mulai',
        'jam_mulai'           => 'required',
        'jam_selesai'         => 'required|after:jam_mulai',
        'jumlah_peserta'      => 'nullable|integer|min:1',
        'keperluan'           => 'nullable|string|max:1000',
    ];

    /**
     * Generate kode pengajuan otomatis: PR-YYYYMMDD-XXX
     *
     * Dibungkus DB::transaction + lockForUpdate untuk mencegah race condition
     * saat dua user submit pengajuan pada detik yang sama (kode duplicate).
     */
    public static function generateKode()
    {
        return DB::transaction(function () {
            $prefix = self::KODE_PREFIX . now()->format('Ymd') . '-';

            $last = static::withTrashed()
                ->where('kode_pengajuan', 'like', "{$prefix}%")
                ->lockForUpdate()
                ->orderBy('kode_pengajuan', 'desc')
                ->first();

            $nextNum = $last
                ? ((int) substr($last->kode_pengajuan, -3)) + 1
                : 1;

            return $prefix . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
        });
    }

    // ── Query Scopes ──

    public function scopeDiproses($query)
    {
        return $query->where('status', self::STATUS_DIPROSES);
    }

    public function scopeDisetujui($query)
    {
        return $query->where('status', self::STATUS_DISETUJUI);
    }

    public function scopeDitolak($query)
    {
        return $query->where('status', self::STATUS_DITOLAK);
    }

    public function scopeMilikUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // ── Relasi ──

    /**
     * Ruangan yang diajukan.
     */
    public function ruangan()
    {
        return $this->belongsTo(GedungFasilitas::class, 'gedung_fasilitas_id');
    }

    /**
     * Gedung induk (via ruangan) — shortcut untuk query.
     * Gunakan eager loading: with(['ruangan.gedung']).
     */
    public function gedung()
    {
        return $this->hasOneThrough(
            Gedung::class,
            GedungFasilitas::class,
            'id',               // FK di gedung_fasilitas (local)
            'id',               // FK di gedungs (target)
            'gedung_fasilitas_id', // FK di pengajuan_ruangans
            'gedung_id'         // FK di gedung_fasilitas
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Admin yang menyetujui/menolak pengajuan ini.
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
