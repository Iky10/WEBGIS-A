<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\PengajuanRuangan;

use Illuminate\Contracts\Queue\ShouldQueue;

class PengajuanRuanganStatusMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $pengajuan;
    public $statusLabel;

    public function __construct(PengajuanRuangan $pengajuan)
    {
        $this->pengajuan = $pengajuan;
        $this->statusLabel = $pengajuan->status === 'disetujui' ? 'Disetujui' : 'Ditolak';
    }

    public function build()
    {
        $subject = "Pengajuan Ruangan {$this->pengajuan->kode_pengajuan} — {$this->statusLabel}";

        return $this->subject($subject)
                    ->view('emails.pengajuan_ruangan_status');
    }
}
