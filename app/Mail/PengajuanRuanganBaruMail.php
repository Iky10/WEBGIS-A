<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\PengajuanRuangan;

use Illuminate\Contracts\Queue\ShouldQueue;

class PengajuanRuanganBaruMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $pengajuan;

    public function __construct(PengajuanRuangan $pengajuan)
    {
        $this->pengajuan = $pengajuan;
    }

    public function build()
    {
        return $this->subject("Pengajuan Ruangan Baru: {$this->pengajuan->kode_pengajuan}")
                    ->view('emails.pengajuan_ruangan_baru');
    }
}
