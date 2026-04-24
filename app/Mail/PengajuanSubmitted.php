<?php

namespace App\Mail;

use App\Models\PengajuanGedung;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PengajuanSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public $pengajuan;

    public function __construct(PengajuanGedung $pengajuan)
    {
        $this->pengajuan = $pengajuan;
    }

    public function build()
    {
        return $this->subject('Pengajuan Penggunaan Gedung - ' . $this->pengajuan->kode_pengajuan)
                    ->view('emails.pengajuan-submitted');
    }
}
