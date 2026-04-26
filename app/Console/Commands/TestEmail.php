<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PengajuanGedung;
use App\Mail\PengajuanStatusMail;
use App\Mail\PengajuanBaruMail;
use Illuminate\Support\Facades\Mail;

class TestEmail extends Command
{
    protected $signature = 'test:email';
    protected $description = 'Test email rendering dan pengiriman';

    public function handle()
    {
        $pengajuan = PengajuanGedung::with('gedung')->first();

        if (!$pengajuan) {
            $this->error('Tidak ada data pengajuan.');
            return 1;
        }

        // Test render PengajuanStatusMail
        $this->info("Testing PengajuanStatusMail render...");
        try {
            $mail = new PengajuanStatusMail($pengajuan);
            $rendered = $mail->render();
            $this->info("✓ PengajuanStatusMail renders OK (" . strlen($rendered) . " bytes)");
        } catch (\Exception $e) {
            $this->error("✗ PengajuanStatusMail ERROR: " . $e->getMessage());
        }

        // Test render PengajuanBaruMail
        $this->info("Testing PengajuanBaruMail render...");
        try {
            $mail2 = new PengajuanBaruMail($pengajuan);
            $rendered2 = $mail2->render();
            $this->info("✓ PengajuanBaruMail renders OK (" . strlen($rendered2) . " bytes)");
        } catch (\Exception $e) {
            $this->error("✗ PengajuanBaruMail ERROR: " . $e->getMessage());
        }

        // Test SMTP send
        $this->info("Testing SMTP send...");
        try {
            Mail::to('test@example.com')->send(new PengajuanStatusMail($pengajuan));
            $this->info("✓ Email sent successfully!");
        } catch (\Exception $e) {
            $this->warn("✗ SMTP error (expected jika credential belum valid): " . substr($e->getMessage(), 0, 150));
        }

        $this->info("Done.");
        return 0;
    }
}
