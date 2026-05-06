<?php

namespace App\Console\Commands;

use App\Models\GedungFasilitas;
use App\Models\JadwalRuangan;
use Illuminate\Console\Command;

class TestRealtimeStatus extends Command
{
    protected $signature = 'test:realtime-status {--cleanup : Hapus semua jadwal test yang dibuat command ini}';

    protected $description = 'Buat jadwal dummy 2 menit dari sekarang untuk verify status ruangan auto-update';

    public function handle()
    {
        if ($this->option('cleanup')) {
            return $this->cleanup();
        }

        $this->info('=== Test Realtime Status Change ===');
        $this->newLine();

        $this->line('Laravel timezone:  ' . config('app.timezone'));
        $this->line('Carbon now:        ' . now()->format('Y-m-d H:i:s'));
        $this->line('Hari ini:          ' . now()->locale('id')->isoFormat('dddd'));
        $this->newLine();

        $ruangan = GedungFasilitas::with('gedung')
            ->whereHas('gedung')
            ->first();

        if (!$ruangan) {
            $this->error('Tidak ada ruangan di database. Tambah dulu via admin.');
            return 1;
        }

        $this->line('Ruangan target:    ' . $ruangan->nama_fasilitas);
        $this->line('Gedung:            ' . optional($ruangan->gedung)->nama_gedung);
        $this->line('Status sekarang:   ' . $ruangan->status_dipakai);
        $this->newLine();

        $deleted = JadwalRuangan::where('gedung_fasilitas_id', $ruangan->id)
            ->where('nama_kegiatan', 'TEST REALTIME STATUS')
            ->delete();
        if ($deleted > 0) {
            $this->line("Cleaned {$deleted} jadwal test lama.");
        }

        $hariMap = [
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];
        $hariIni = $hariMap[date('l')];

        $jamMulai = now()->addMinutes(2)->format('H:i:s');
        $jamSelesai = now()->addMinutes(30)->format('H:i:s');

        $jadwal = JadwalRuangan::create([
            'gedung_fasilitas_id' => $ruangan->id,
            'nama_kegiatan' => 'TEST REALTIME STATUS',
            'hari' => $hariIni,
            'jam_mulai' => $jamMulai,
            'jam_selesai' => $jamSelesai,
            'keterangan' => 'Auto-generated test data — boleh dihapus.',
        ]);

        if (method_exists($ruangan, 'flushStatusCache')) {
            $ruangan->flushStatusCache();
        }
        if ($ruangan->gedung && method_exists($ruangan->gedung, 'flushStatusCache')) {
            $ruangan->gedung->flushStatusCache();
        }

        $this->info("Jadwal test ber-ID {$jadwal->id} berhasil dibuat:");
        $this->line("   Ruangan:       {$ruangan->nama_fasilitas}");
        $this->line("   Hari:          {$hariIni}");
        $this->line("   Jam mulai:     {$jamMulai}");
        $this->line("   Jam selesai:   {$jamSelesai}");
        $this->newLine();

        $this->info('=== Timeline ===');
        $statusSekarang = $ruangan->fresh()->status_dipakai;
        $this->line('Sekarang (' . now()->format('H:i:s') . "):       Status = {$statusSekarang} (jadwal belum mulai)");
        $this->line("Jam {$jamMulai}:           Status akan jadi 'Sedang Dipakai'");
        $this->line("Jam {$jamSelesai}:           Status akan kembali ke 'Kosong'");
        $this->newLine();

        $this->info('=== Instruksi Test ===');
        $this->line('1. BUKA peta: http://127.0.0.1:8000/ atau URL dev kamu');
        $this->line('2. CARI marker gedung "' . optional($ruangan->gedung)->nama_gedung . '"');
        $this->line('3. SEKARANG marker harus HIJAU (Terbuka)');
        $this->line("4. TUNGGU sampai jam {$jamMulai} (sekitar 2 menit)");
        $this->line('5. REFRESH peta (Ctrl+F5) → marker harus berubah ke BIRU (Sedang Dipakai)');
        $this->line("6. TUNGGU sampai jam {$jamSelesai} → refresh → marker kembali HIJAU");
        $this->newLine();

        $this->comment('Untuk cleanup: php artisan test:realtime-status --cleanup');

        return 0;
    }

    protected function cleanup()
    {
        $count = JadwalRuangan::where('nama_kegiatan', 'TEST REALTIME STATUS')->count();
        if ($count === 0) {
            $this->info('Tidak ada jadwal test untuk di-cleanup.');
            return 0;
        }

        JadwalRuangan::where('nama_kegiatan', 'TEST REALTIME STATUS')->forceDelete();
        $this->info("{$count} jadwal test dihapus.");

        GedungFasilitas::with('gedung')->get()->each(function ($r) {
            if (method_exists($r, 'flushStatusCache')) {
                $r->flushStatusCache();
            }
            if ($r->gedung && method_exists($r->gedung, 'flushStatusCache')) {
                $r->gedung->flushStatusCache();
            }
        });

        return 0;
    }
}
