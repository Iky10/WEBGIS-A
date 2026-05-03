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

            // Cek 1: ruangan aktif (tidak sedang perbaikan / nonaktif)
            if ($ruangan && !$ruangan->is_aktif) {
                $validator->errors()->add(
                    'gedung_fasilitas_id',
                    'Ruangan ini sedang tidak aktif (mungkin dalam perbaikan). Silakan pilih ruangan lain.'
                );
                return;
            }

            // Cek 2: ruangan ini boleh diajukan (admin sudah opt-in via toggle bisa_diajukan).
            // Defense-in-depth: form sudah filter, tapi cek server-side cegah direct API call.
            if ($ruangan && !$ruangan->bisa_diajukan) {
                $validator->errors()->add(
                    'gedung_fasilitas_id',
                    'Ruangan ini tidak dibuka untuk pengajuan publik. Silakan pilih ruangan lain.'
                );
                return;
            }

            // Cek 3: gedung induk dibuka untuk pengajuan (level gedung)
            if ($ruangan && $ruangan->gedung && !$ruangan->gedung->bisa_diajukan) {
                $validator->errors()->add(
                    'gedung_fasilitas_id',
                    'Gedung induk ruangan ini tidak dibuka untuk pengajuan umum.'
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

            // Throttle: max 10 pengajuan per user dalam 1 jam terakhir (cegah spam).
            // Dinaikkan dari 5 ke 10 supaya user yang book event multi-hari / multi-slot tidak kebablasan.
            if (Auth::check()) {
                $jumlahPengajuan = PengajuanRuangan::milikUser(Auth::id())
                    ->where('created_at', '>=', now()->subHour())
                    ->count();

                if ($jumlahPengajuan >= 10) {
                    $validator->errors()->add(
                        'gedung_fasilitas_id',
                        'Anda sudah membuat 10 pengajuan dalam 1 jam terakhir. Silakan tunggu sebelum mengajukan lagi.'
                    );
                    return;
                }
            }

            // Cek bentrok pengajuan pada RUANGAN yang sama (granular — bukan level gedung)
            // Hanya cek terhadap pengajuan berstatus 'diproses' atau 'disetujui'
            $overlap = PengajuanRuangan::where('gedung_fasilitas_id', $ruanganId)
                ->whereIn('status', [
                    PengajuanRuangan::STATUS_DIPROSES,
                    PengajuanRuangan::STATUS_DISETUJUI,
                ])
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
