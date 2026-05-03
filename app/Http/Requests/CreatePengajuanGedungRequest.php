<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\PengajuanGedung;

class CreatePengajuanGedungRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return PengajuanGedung::$rules;
    }

    /**
     * Additional validation after standard rules pass.
     * Cek bentrok jadwal (double booking) pada gedung yang sama.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Skip jika sudah ada error dasar
            if ($validator->errors()->any()) {
                return;
            }

            $gedungId       = $this->input('gedung_id');
            $tanggalMulai   = $this->input('tanggal_mulai');
            $tanggalSelesai = $this->input('tanggal_selesai');
            $jamMulai       = $this->input('jam_mulai');
            $jamSelesai     = $this->input('jam_selesai');

            // Cek apakah gedung bisa diajukan
            $gedung = \App\Models\Gedung::find($gedungId);
            if ($gedung && !$gedung->bisa_diajukan) {
                $validator->errors()->add(
                    'gedung_id',
                    'Gedung ini tidak tersedia untuk pengajuan penggunaan.'
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

            // Cek throttle: max 5 pengajuan per user dalam 1 jam terakhir (cegah spam)
            if (\Illuminate\Support\Facades\Auth::check()) {
                $jumlahPengajuan = PengajuanGedung::where('user_id', \Illuminate\Support\Facades\Auth::id())
                    ->where('created_at', '>=', now()->subHour())
                    ->count();

                if ($jumlahPengajuan >= 5) {
                    $validator->errors()->add(
                        'gedung_id',
                        'Anda sudah membuat 5 pengajuan dalam 1 jam terakhir. Silakan tunggu sebelum mengajukan lagi.'
                    );
                    return;
                }
            }

            // Cek apakah ada pengajuan yang overlap pada gedung yang sama
            // Hanya cek terhadap pengajuan berstatus 'diproses' atau 'disetujui'
            $overlap = PengajuanGedung::where('gedung_id', $gedungId)
                ->whereIn('status', ['diproses', 'disetujui'])
                ->where(function ($q) use ($tanggalMulai, $tanggalSelesai) {
                    // Overlap tanggal: rentang tanggal baru bersinggungan dengan yang ada
                    $q->where('tanggal_mulai', '<=', $tanggalSelesai)
                      ->where('tanggal_selesai', '>=', $tanggalMulai);
                })
                ->where(function ($q) use ($jamMulai, $jamSelesai) {
                    // Overlap jam: rentang jam baru bersinggungan dengan yang ada
                    $q->where('jam_mulai', '<', $jamSelesai)
                      ->where('jam_selesai', '>', $jamMulai);
                })
                ->exists();

            if ($overlap) {
                $validator->errors()->add(
                    'gedung_id',
                    'Gedung ini sudah memiliki pengajuan pada tanggal dan jam yang sama. Silakan pilih waktu lain.'
                );
            }
        });
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'gedung_id.required'             => 'Gedung wajib dipilih.',
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
