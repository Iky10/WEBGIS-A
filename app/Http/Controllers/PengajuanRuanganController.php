<?php

namespace App\Http\Controllers;

use App\Repositories\PengajuanRuanganRepository;
use App\Http\Requests\CreatePengajuanRuanganRequest;
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
        $input['status']         = 'diproses';

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
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status'        => 'required|in:disetujui,ditolak',
            'catatan_admin' => 'required_if:status,ditolak|nullable|string|max:1000',
        ], [
            'catatan_admin.required_if' => 'Catatan wajib diisi saat menolak pengajuan (sebagai alasan untuk pemohon).',
        ]);

        $pengajuan = PengajuanRuangan::with('ruangan.gedung')->findOrFail($id);
        $pengajuan->update(array_merge($validated, [
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]));

        // Flush cache status — penting agar perubahan status pengajuan langsung tercermin di peta.
        // Flush baik ruangan maupun gedung induknya (karena status gedung derived dari ruangan).
        if ($pengajuan->ruangan) {
            $pengajuan->ruangan->flushStatusCache();

            if ($pengajuan->ruangan->gedung) {
                $pengajuan->ruangan->gedung->flushStatusCache();
            }
        }

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

        $statusLabel = $validated['status'] === 'disetujui' ? 'disetujui' : 'ditolak';
        Flash::success("Pengajuan {$pengajuan->kode_pengajuan} telah {$statusLabel}.");

        return redirect(route('pengajuan_ruangans.index'));
    }

    /**
     * User: Daftar pengajuan milik user yang login.
     */
    public function riwayat()
    {
        $pengajuanRuangans = PengajuanRuangan::with('ruangan.gedung')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        $view = Auth::user()->isAdmin()
            ? 'dashboard.pengajuan_ruangans.riwayat'
            : 'public.pengajuan_ruangan.riwayat';

        return view($view)
            ->with('pengajuanRuangans', $pengajuanRuangans);
    }

    /**
     * Admin: Hapus pengajuan.
     * Dilindungi oleh middleware 'admin' di routes/web.php.
     */
    public function destroy($id)
    {
        $pengajuanRuangan = $this->pengajuanRuanganRepository->find($id);

        if (empty($pengajuanRuangan)) {
            Flash::error('Pengajuan tidak ditemukan.');
            return redirect(route('pengajuan_ruangans.index'));
        }

        $this->pengajuanRuanganRepository->delete($id);

        Flash::success('Pengajuan berhasil dihapus.');

        return redirect(route('pengajuan_ruangans.index'));
    }

    /**
     * Admin: Hapus pengajuan secara massal (AJAX).
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'integer|exists:pengajuan_ruangans,id',
        ]);

        $count = PengajuanRuangan::whereIn('id', $request->ids)->count();
        PengajuanRuangan::whereIn('id', $request->ids)->delete();

        return response()->json([
            'success' => true,
            'message' => $count . ' pengajuan berhasil dihapus.',
        ]);
    }

    /**
     * AJAX endpoint: Cek ketersediaan ruangan pada tanggal + jam tertentu.
     * Dipakai oleh form pengajuan untuk live availability check.
     *
     * Response:
     *   { available: true/false, conflicts: [...] }
     */
    public function cekKetersediaan(Request $request)
    {
        $request->validate([
            'gedung_fasilitas_id' => 'required|exists:gedung_fasilitas,id',
            'tanggal_mulai'       => 'required|date',
            'tanggal_selesai'     => 'required|date|after_or_equal:tanggal_mulai',
            'jam_mulai'           => 'required',
            'jam_selesai'         => 'required',
        ]);

        $ruanganId      = $request->input('gedung_fasilitas_id');
        $tanggalMulai   = $request->input('tanggal_mulai');
        $tanggalSelesai = $request->input('tanggal_selesai');
        $jamMulai       = $request->input('jam_mulai');
        $jamSelesai     = $request->input('jam_selesai');

        $conflicts = PengajuanRuangan::with('ruangan')
            ->where('gedung_fasilitas_id', $ruanganId)
            ->whereIn('status', ['diproses', 'disetujui'])
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
