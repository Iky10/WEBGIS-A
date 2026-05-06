<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PengajuanRuangan;
use App\Mail\PengajuanRuanganStatusMail;
use App\Mail\PengajuanRuanganBaruMail;
use Illuminate\Support\Facades\Mail;

class TestEmail extends Command
{
    protected $signature = 'test:email';
    protected $description = 'Test email rendering dan pengiriman';

    public function handle()
    {
        $pengajuan = PengajuanRuangan::with('ruangan.gedung')->first();

        if (!$pengajuan) {
            $this->error('Tidak ada data pengajuan.');
            return 1;
        }

        // Test render PengajuanRuanganStatusMail
        $this->info("Testing PengajuanRuanganStatusMail render...");
        try {
            $mail = new PengajuanRuanganStatusMail($pengajuan);
            $rendered = $mail->render();
            $this->info("✓ PengajuanRuanganStatusMail renders OK (" . strlen($rendered) . " bytes)");
        } catch (\Exception $e) {
            $this->error("✗ PengajuanRuanganStatusMail ERROR: " . $e->getMessage());
        }

        // Test render PengajuanRuanganBaruMail
        $this->info("Testing PengajuanRuanganBaruMail render...");
        try {
            $mail2 = new PengajuanRuanganBaruMail($pengajuan);
            $rendered2 = $mail2->render();
            $this->info("✓ PengajuanRuanganBaruMail renders OK (" . strlen($rendered2) . " bytes)");
        } catch (\Exception $e) {
            $this->error("✗ PengajuanRuanganBaruMail ERROR: " . $e->getMessage());
        }

        // Test SMTP send
        $this->info("Testing SMTP send...");
        try {
            Mail::to('test@example.com')->send(new PengajuanRuanganStatusMail($pengajuan));
            $this->info("✓ Email sent successfully!");
        } catch (\Exception $e) {
            $this->warn("✗ SMTP error (expected jika credential belum valid): " . substr($e->getMessage(), 0, 150));
        }

        $this->info("Done.");
        return 0;
    }
}
