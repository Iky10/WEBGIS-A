<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\PengajuanGedung;

class PengajuanStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $pengajuan;
    public $statusLabel;

    /**
     * Create a new message instance.
     */
    public function __construct(PengajuanGedung $pengajuan)
    {
        $this->pengajuan = $pengajuan;
        $this->statusLabel = $pengajuan->status === 'disetujui' ? 'Disetujui' : 'Ditolak';
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $subject = "Pengajuan {$this->pengajuan->kode_pengajuan} — {$this->statusLabel}";

        return $this->subject($subject)
                    ->view('emails.pengajuan_status');
    }
}
