<?php

namespace App\Mail;

use App\Models\PengajuanGedung;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PengajuanStatusUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $pengajuan;

    public function __construct(PengajuanGedung $pengajuan)
    {
        $this->pengajuan = $pengajuan;
    }

    public function build()
    {
        $statusLabel = ucfirst($this->pengajuan->status);

        return $this->subject("Pengajuan Gedung Anda: {$statusLabel} - " . $this->pengajuan->kode_pengajuan)
                    ->view('emails.pengajuan-status-updated');
    }
}
