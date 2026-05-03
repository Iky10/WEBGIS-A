<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\PengajuanRuangan;
use App\Models\GedungFasilitas;
use Illuminate\Support\Facades\Auth;

class CreatePengajuanRuanganRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return PengajuanRuangan::$rules;
    }

    /**
     * Additional validation after standard rules pass.
     * Cek bentrok jadwal (double booking) pada RUANGAN yang sama,
     * throttle pengajuan, dan validasi jam masa lalu.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Skip jika sudah ada error dasar
            if ($validator->errors()->any()) {
                return;
            }

            $ruanganId      = $this->input('gedung_fasilitas_id');
            $tanggalMulai   = $this->input('tanggal_mulai');
            $tanggalSelesai = $this->input('tanggal_selesai');
            $jamMulai       = $this->input('jam_mulai');
            $jamSelesai     = $this->input('jam_selesai');

            // Cek apakah ruangan (dan gedung induk) bisa diajukan
            $ruangan = GedungFasilitas::with('gedung')->find($ruanganId);
            if ($ruangan && $ruangan->gedung && !$ruangan->gedung->bisa_diajukan) {
                $validator->errors()->add(
                    'gedung_fasilitas_id',
                    'Ruangan ini tidak tersedia untuk pengajuan (gedung induk tidak dibuka untuk umum).'
                );
                return;
            }

            // Validasi jam_mulai tidak boleh di masa lalu jika tanggal_mulai = hari ini
            if ($tanggalMulai === now()->toDateString() && $jamMulai) {
                $waktuSekarang = now()->format('H:i');
                if ($jamMulai <= $waktuSekarang) {
                    $validator->errors()->add(
                        'jam_mulai',
                        'Jam mulai harus setelah waktu sekarang (' . $waktuSekarang . ') untuk pengajuan hari ini.'
                    );
                    return;
                }
            }

            // Throttle: max 5 pengajuan per user dalam 1 jam terakhir (cegah spam)
            if (Auth::check()) {
                $jumlahPengajuan = PengajuanRuangan::where('user_id', Auth::id())
                    ->where('created_at', '>=', now()->subHour())
                    ->count();

                if ($jumlahPengajuan >= 5) {
                    $validator->errors()->add(
                        'gedung_fasilitas_id',
                        'Anda sudah membuat 5 pengajuan dalam 1 jam terakhir. Silakan tunggu sebelum mengajukan lagi.'
                    );
                    return;
                }
            }

            // Cek bentrok pengajuan pada RUANGAN yang sama (granular — bukan level gedung)
            // Hanya cek terhadap pengajuan berstatus 'diproses' atau 'disetujui'
            $overlap = PengajuanRuangan::where('gedung_fasilitas_id', $ruanganId)
                ->whereIn('status', ['diproses', 'disetujui'])
                ->where(function ($q) use ($tanggalMulai, $tanggalSelesai) {
                    // Overlap tanggal: rentang tanggal baru bersinggungan dengan yang ada
                    $q->where('tanggal_mulai', '<=', $tanggalSelesai)
                      ->where('tanggal_selesai', '>=', $tanggalMulai);
                })
                ->where(function ($q) use ($jamMulai, $jamSelesai) {
                    // Overlap jam: exclusive pada boundary (jam_selesai == jam_mulai orang lain = OK)
                    $q->where('jam_mulai', '<', $jamSelesai)
                      ->where('jam_selesai', '>', $jamMulai);
                })
                ->exists();

            if ($overlap) {
                $validator->errors()->add(
                    'gedung_fasilitas_id',
                    'Ruangan ini sudah memiliki pengajuan pada tanggal dan jam yang sama. Silakan pilih waktu lain atau ruangan lain.'
                );
            }
        });
    }

    public function messages()
    {
        return [
            'gedung_fasilitas_id.required'   => 'Ruangan wajib dipilih.',
            'gedung_fasilitas_id.exists'     => 'Ruangan tidak ditemukan.',
            'nama_pemohon.required'          => 'Nama pemohon wajib diisi.',
            'email_pemohon.required'         => 'Email wajib diisi.',
            'email_pemohon.email'            => 'Format email tidak valid.',
            'no_telepon.required'            => 'Nomor telepon wajib diisi.',
            'asal_instansi.required'         => 'Asal instansi wajib diisi.',
            'jenis_kegiatan.required'        => 'Jenis kegiatan wajib dipilih.',
            'nama_kegiatan.required'         => 'Nama kegiatan wajib diisi.',
            'tanggal_mulai.required'         => 'Tanggal mulai wajib diisi.',
            'tanggal_mulai.after_or_equal'   => 'Tanggal mulai tidak boleh di masa lalu.',
            'tanggal_selesai.required'       => 'Tanggal selesai wajib diisi.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
            'jam_mulai.required'             => 'Jam mulai wajib diisi.',
            'jam_selesai.required'           => 'Jam selesai wajib diisi.',
            'jam_selesai.after'              => 'Jam selesai harus setelah jam mulai.',
        ];
    }
}
