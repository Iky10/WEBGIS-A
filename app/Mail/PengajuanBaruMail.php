<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\PengajuanGedung;

class PengajuanBaruMail extends Mailable
{
    use Queueable, SerializesModels;

    public $pengajuan;

    /**
     * Create a new message instance.
     */
    public function __construct(PengajuanGedung $pengajuan)
    {
        $this->pengajuan = $pengajuan;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject("Pengajuan Baru: {$this->pengajuan->kode_pengajuan}")
                    ->view('emails.pengajuan_baru');
    }
}
