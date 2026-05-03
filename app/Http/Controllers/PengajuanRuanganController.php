<?php

namespace App\Http\Controllers;

use App\Repositories\PengajuanRuanganRepository;
use App\Http\Requests\CreatePengajuanRuanganRequest;
use App\Http\Requests\UpdateStatusPengajuanRuanganRequest;
use App\Http\Requests\BulkDeletePengajuanRuanganRequest;
use App\Http\Requests\CekKetersediaanRequest;
use App\Models\PengajuanRuangan;
use App\Models\Gedung;
use App\Models\GedungFasilitas;
use App\Models\User;
use App\Mail\PengajuanRuanganStatusMail;
use App\Mail\PengajuanRuanganBaruMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Flash;

class PengajuanRuanganController extends AppBaseController
{
    private $pengajuanRuanganRepository;

    public function __construct(PengajuanRuanganRepository $pengajuanRuanganRepo)
    {
        $this->pengajuanRuanganRepository = $pengajuanRuanganRepo;
    }

    /**
     * Admin: Daftar semua pengajuan ruangan.
     * Dilindungi oleh middleware 'admin' di routes/web.php.
     */
    public function index()
    {
        $pengajuanRuangans = PengajuanRuangan::with(['ruangan.gedung', 'user', 'approvedBy'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Dropdown filter di view (clean separation — no query in Blade)
        $gedungList = Gedung::orderBy('nama_gedung')->pluck('nama_gedung', 'id');

        return view('dashboard.pengajuan_ruangans.index')
            ->with('pengajuanRuangans', $pengajuanRuangans)
            ->with('gedungList', $gedungList);
    }

    /**
     * User: Tampilkan form pengajuan ruangan (wajib login)
     */
    public function create(Request $request)
    {
        // Admin tidak perlu mengajukan — redirect ke daftar pengajuan
        if (Auth::user()->isAdmin()) {
            Flash::error('Admin tidak dapat mengajukan penggunaan ruangan.');
            return redirect(route('pengajuan_ruangans.index'));
        }

        // Ambil gedung yang bisa diajukan + ruangan-ruangannya untuk cascade dropdown.
        // Filter: hanya tampilkan gedung yang punya minimal 1 ruangan aktif —
        // hindari user pilih gedung lalu Step 2 kosong.
        $gedungs = Gedung::bisaDiajukan()
            ->whereHas('fasilitas', function ($q) {
                $q->where('is_aktif', true);
            })
            ->with(['fasilitas' => function ($q) {
                $q->where('is_aktif', true)->orderBy('nama_fasilitas');
            }])
            ->orderBy('nama_gedung')
            ->get();

        // Pre-select dari query param (misal dari link di peta) atau dari old()
        // saat validation gagal dan user di-redirect balik ke form.
        $selectedGedung  = $request->query('gedung_id');
        $selectedRuangan = $request->query('ruangan_id');

        if (old('gedung_fasilitas_id')) {
            $ruanganFromOld = GedungFasilitas::find(old('gedung_fasilitas_id'));
            if ($ruanganFromOld) {
                $selectedGedung  = $ruanganFromOld->gedung_id;
                $selectedRuangan = $ruanganFromOld->id;
            }
        }

        return view('public.pengajuan_ruangan.create')
            ->with('gedungs', $gedungs)
            ->with('selectedGedung', $selectedGedung)
            ->with('selectedRuangan', $selectedRuangan);
    }

    /**
     * User: Simpan pengajuan baru.
     * Validasi via CreatePengajuanRuanganRequest sesuai pola project.
     */
    public function store(CreatePengajuanRuanganRequest $request)
    {
        $input = $request->validated();
        $input['user_id']        = Auth::id();
        $input['kode_pengajuan'] = PengajuanRuangan::generateKode();
        $input['status']         = PengajuanRuangan::STATUS_DIPROSES;

        $pengajuan = $this->pengajuanRuanganRepository->create($input);
        $pengajuan->load('ruangan.gedung');

        // Kirim email notifikasi ke admin (skip jika SMTP belum dikonfigurasi)
        if (config('mail.default') !== 'log') {
            try {
                $admins = User::where('role', 'admin')->pluck('email');
                if ($admins->isNotEmpty()) {
                    Mail::to($admins)->send(new PengajuanRuanganBaruMail($pengajuan));
                    Log::info('Email notifikasi pengajuan ruangan baru terkirim ke admin.');
                }
            } catch (\Throwable $e) {
                Log::error('Gagal mengirim email notifikasi pengajuan baru: ' . $e->getMessage());
            }
        } else {
            Log::info("Email admin dilewati (MAIL_MAILER=log). Pengajuan: {$pengajuan->kode_pengajuan}");
        }

        Flash::success('Pengajuan berhasil dikirim! Kode: ' . $pengajuan->kode_pengajuan);

        return redirect(route('pengajuan_ruangans.riwayat'));
    }

    /**
     * Detail pengajuan ruangan.
     * User hanya bisa lihat miliknya; admin bisa lihat semua.
     */
    public function show($id)
    {
        $pengajuanRuangan = PengajuanRuangan::with(['ruangan.gedung', 'user', 'approvedBy'])->find($id);

        if (empty($pengajuanRuangan)) {
            Flash::error('Pengajuan tidak ditemukan.');
            return redirect(route('pengajuan_ruangans.riwayat'));
        }

        // User biasa hanya boleh lihat miliknya sendiri
        if (!Auth::user()->isAdmin() && $pengajuanRuangan->user_id !== Auth::id()) {
            Flash::error('Akses ditolak.');
            return redirect(route('pengajuan_ruangans.riwayat'));
        }

        $view = Auth::user()->isAdmin()
            ? 'dashboard.pengajuan_ruangans.show'
            : 'public.pengajuan_ruangan.show';

        return view($view)
            ->with('pengajuanRuangan', $pengajuanRuangan);
    }

    /**
     * Admin: Update status pengajuan (disetujui/ditolak).
     * Dilindungi oleh middleware 'admin' di routes/web.php.
     *
     * Guard: hanya pengajuan berstatus 'diproses' yang boleh diubah —
     * mencegah admin bolak-balik update (disetujui → ditolak → disetujui)
     * yang bikin audit trail tidak jelas.
     */
    public function updateStatus(UpdateStatusPengajuanRuanganRequest $request, $id)
    {
        $validated = $request->validated();

        $pengajuan = PengajuanRuangan::with('ruangan.gedung')->findOrFail($id);

        // Guard: cegah update status dari final state
        if ($pengajuan->status !== PengajuanRuangan::STATUS_DIPROSES) {
            Flash::error("Pengajuan {$pengajuan->kode_pengajuan} sudah {$pengajuan->status}, tidak bisa diubah lagi.");
            return redirect(route('pengajuan_ruangans.index'));
        }

        $pengajuan->update(array_merge($validated, [
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]));

        // Flush cache status — penting agar perubahan status pengajuan langsung tercermin di peta.
        $this->flushStatusCacheForPengajuan($pengajuan);

        Log::info("Pengajuan {$pengajuan->kode_pengajuan} status diupdate menjadi: {$validated['status']} oleh admin: " . Auth::user()->name);

        // Kirim email notifikasi ke pemohon (skip jika SMTP belum dikonfigurasi)
        if (config('mail.default') !== 'log') {
            try {
                Mail::to($pengajuan->email_pemohon)->send(new PengajuanRuanganStatusMail($pengajuan));
                Log::info("Email notifikasi terkirim ke: {$pengajuan->email_pemohon}");
            } catch (\Throwable $e) {
                Log::warning('Gagal mengirim email status pengajuan: ' . $e->getMessage());
            }
        } else {
            Log::info("Email notifikasi dilewati (MAIL_MAILER=log). Pengajuan: {$pengajuan->kode_pengajuan}");
        }

        Flash::success("Pengajuan {$pengajuan->kode_pengajuan} telah {$validated['status']}.");

        return redirect(route('pengajuan_ruangans.index'));
    }

    /**
     * User: Daftar pengajuan milik user yang login.
     */
    public function riwayat()
    {
        $pengajuanRuangans = PengajuanRuangan::with('ruangan.gedung')
            ->milikUser(Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        $view = Auth::user()->isAdmin()
            ? 'dashboard.pengajuan_ruangans.riwayat'
            : 'public.pengajuan_ruangan.riwayat';

        return view($view)
            ->with('pengajuanRuangans', $pengajuanRuangans);
    }

    /**
     * User: Batalkan pengajuan miliknya sendiri.
     *
     * Guard:
     *   - Hanya pemilik pengajuan (user_id = auth id)
     *   - Hanya status 'diproses' (belum diputuskan admin)
     *
     * Tidak mempengaruhi cache status ruangan realtime karena status 'diproses'
     * tidak membuat ruangan "Sedang Dipakai" — tapi tetap flush untuk konsistensi.
     */
    public function cancel($id)
    {
        $pengajuan = PengajuanRuangan::with('ruangan.gedung')->find($id);

        if (empty($pengajuan)) {
            Flash::error('Pengajuan tidak ditemukan.');
            return redirect(route('pengajuan_ruangans.riwayat'));
        }

        if (!$pengajuan->canBeCanceledBy(Auth::user())) {
            Flash::error('Anda tidak dapat membatalkan pengajuan ini.');
            return redirect(route('pengajuan_ruangans.riwayat'));
        }

        $pengajuan->update([
            'status' => PengajuanRuangan::STATUS_DIBATALKAN,
        ]);

        $this->flushStatusCacheForPengajuan($pengajuan);

        Log::info("Pengajuan {$pengajuan->kode_pengajuan} dibatalkan oleh user: " . Auth::user()->name);

        Flash::success("Pengajuan {$pengajuan->kode_pengajuan} berhasil dibatalkan.");

        return redirect(route('pengajuan_ruangans.riwayat'));
    }

    /**
     * Admin: Hapus pengajuan.
     * Dilindungi oleh middleware 'admin' di routes/web.php.
     *
     * Flush cache status ruangan + gedung sebelum delete — agar perubahan
     * langsung tercermin di peta (tanpa tunggu cache expire 60 detik).
     */
    public function destroy($id)
    {
        $pengajuanRuangan = PengajuanRuangan::with('ruangan.gedung')->find($id);

        if (empty($pengajuanRuangan)) {
            Flash::error('Pengajuan tidak ditemukan.');
            return redirect(route('pengajuan_ruangans.index'));
        }

        // Flush cache sebelum delete (agar status peta langsung update)
        $this->flushStatusCacheForPengajuan($pengajuanRuangan);

        $this->pengajuanRuanganRepository->delete($id);

        Flash::success('Pengajuan berhasil dihapus.');

        return redirect(route('pengajuan_ruangans.index'));
    }

    /**
     * Admin: Hapus pengajuan secara massal (AJAX).
     *
     * Flush cache status untuk setiap ruangan yang terdampak — agar perubahan
     * langsung tercermin di peta tanpa tunggu cache expire.
     */
    public function bulkDelete(BulkDeletePengajuanRuanganRequest $request)
    {
        $ids = $request->validated()['ids'];

        // Load dulu untuk flush cache per ruangan terdampak
        $pengajuans = PengajuanRuangan::with('ruangan.gedung')->whereIn('id', $ids)->get();
        foreach ($pengajuans as $p) {
            $this->flushStatusCacheForPengajuan($p);
        }

        $count = $pengajuans->count();
        PengajuanRuangan::whereIn('id', $ids)->delete();

        return response()->json([
            'success' => true,
            'message' => $count . ' pengajuan berhasil dihapus.',
        ]);
    }

    /**
     * Helper: flush cache status ruangan + gedung induk dari sebuah pengajuan.
     * Dipakai di updateStatus, destroy, dan bulkDelete.
     */
    protected function flushStatusCacheForPengajuan(PengajuanRuangan $pengajuan): void
    {
        if ($pengajuan->ruangan) {
            $pengajuan->ruangan->flushStatusCache();

            if ($pengajuan->ruangan->gedung) {
                $pengajuan->ruangan->gedung->flushStatusCache();
            }
        }
    }

    /**
     * AJAX endpoint: Cek ketersediaan ruangan pada tanggal + jam tertentu.
     * Dipakai oleh form pengajuan untuk live availability check.
     *
     * Response:
     *   { available: true/false, conflicts: [...] }
     */
    public function cekKetersediaan(CekKetersediaanRequest $request)
    {
        $data = $request->validated();

        $ruanganId      = $data['gedung_fasilitas_id'];
        $tanggalMulai   = $data['tanggal_mulai'];
        $tanggalSelesai = $data['tanggal_selesai'];
        $jamMulai       = $data['jam_mulai'];
        $jamSelesai     = $data['jam_selesai'];

        $conflicts = PengajuanRuangan::with('ruangan')
            ->where('gedung_fasilitas_id', $ruanganId)
            ->whereIn('status', [
                PengajuanRuangan::STATUS_DIPROSES,
                PengajuanRuangan::STATUS_DISETUJUI,
            ])
            ->where(function ($q) use ($tanggalMulai, $tanggalSelesai) {
                $q->where('tanggal_mulai', '<=', $tanggalSelesai)
                  ->where('tanggal_selesai', '>=', $tanggalMulai);
            })
            ->where(function ($q) use ($jamMulai, $jamSelesai) {
                $q->where('jam_mulai', '<', $jamSelesai)
                  ->where('jam_selesai', '>', $jamMulai);
            })
            ->get(['id', 'kode_pengajuan', 'status', 'tanggal_mulai', 'tanggal_selesai', 'jam_mulai', 'jam_selesai', 'nama_kegiatan']);

        return response()->json([
            'available' => $conflicts->isEmpty(),
            'conflicts' => $conflicts,
        ]);
    }
}
