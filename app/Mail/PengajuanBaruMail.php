<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\PengajuanGedung;

use Illuminate\Contracts\Queue\ShouldQueue;

class PengajuanBaruMail extends Mailable implements ShouldQueue
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
