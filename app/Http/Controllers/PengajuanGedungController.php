<?php

namespace App\Http\Controllers;

use App\Repositories\PengajuanGedungRepository;
use App\Http\Requests\CreatePengajuanGedungRequest;
use App\Models\PengajuanGedung;
use App\Models\Gedung;
use App\Models\User;
use App\Mail\PengajuanStatusMail;
use App\Mail\PengajuanBaruMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Flash;
use Response;

class PengajuanGedungController extends AppBaseController
{
    private $pengajuanGedungRepository;

    public function __construct(PengajuanGedungRepository $pengajuanGedungRepo)
    {
        $this->pengajuanGedungRepository = $pengajuanGedungRepo;
    }

    /**
     * Admin: Daftar semua pengajuan
     */
    public function index(Request $request)
    {
        // Hanya admin yang boleh lihat semua pengajuan
        if (!Auth::user()->isAdmin()) {
            Flash::error('Akses ditolak.');
            return redirect(route('pengajuan_gedungs.riwayat'));
        }

        $pengajuanGedungs = PengajuanGedung::with(['gedung', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dashboard.pengajuan_gedungs.index')
            ->with('pengajuanGedungs', $pengajuanGedungs);
    }

    /**
     * User: Tampilkan form pengajuan (wajib login)
     */
    public function create(Request $request)
    {
        $gedungs = Gedung::orderBy('nama_gedung')->pluck('nama_gedung', 'id');
        $selectedGedung = $request->query('gedung_id');

        return view('dashboard.pengajuan_gedungs.create')
            ->with('gedungs', $gedungs)
            ->with('selectedGedung', $selectedGedung);
    }

    /**
     * User: Simpan pengajuan baru
     * Validasi menggunakan CreatePengajuanGedungRequest sesuai pola project
     */
    public function store(CreatePengajuanGedungRequest $request)
    {
        $input = $request->all();
        $input['user_id'] = Auth::id();
        $input['kode_pengajuan'] = PengajuanGedung::generateKode();
        $input['status'] = 'diproses';

        $pengajuan = $this->pengajuanGedungRepository->create($input);
        $pengajuan->load('gedung');

        // Kirim email notifikasi ke semua admin
        try {
            $admins = User::where('role', 'admin')->pluck('email');
            if ($admins->isNotEmpty()) {
                Mail::to($admins)->send(new PengajuanBaruMail($pengajuan));
            }
        } catch (\Exception $e) {
            Log::warning('Gagal mengirim email notifikasi pengajuan baru: ' . $e->getMessage());
        }

        Flash::success('Pengajuan berhasil dikirim! Kode: ' . $pengajuan->kode_pengajuan);

        return redirect(route('pengajuan_gedungs.riwayat'));
    }

    /**
     * Detail pengajuan
     * User hanya bisa lihat miliknya, admin bisa lihat semua
     */
    public function show($id)
    {
        $pengajuanGedung = PengajuanGedung::with(['gedung', 'user'])->find($id);

        if (empty($pengajuanGedung)) {
            Flash::error('Pengajuan tidak ditemukan.');
            return redirect(route('pengajuan_gedungs.riwayat'));
        }

        // User biasa hanya boleh lihat miliknya sendiri
        if (!Auth::user()->isAdmin() && $pengajuanGedung->user_id !== Auth::id()) {
            Flash::error('Akses ditolak.');
            return redirect(route('pengajuan_gedungs.riwayat'));
        }

        return view('dashboard.pengajuan_gedungs.show')
            ->with('pengajuanGedung', $pengajuanGedung);
    }

    /**
     * Admin: Update status pengajuan (disetujui/ditolak)
     */
    public function updateStatus(Request $request, $id)
    {
        // Hanya admin yang boleh update status
        if (!Auth::user()->isAdmin()) {
            Flash::error('Akses ditolak.');
            return redirect(route('pengajuan_gedungs.riwayat'));
        }

        $request->validate([
            'status' => 'required|in:disetujui,ditolak',
            'catatan_admin' => 'nullable|string|max:1000',
        ]);

        $pengajuan = PengajuanGedung::with('gedung')->findOrFail($id);
        $pengajuan->status = $request->status;
        $pengajuan->catatan_admin = $request->catatan_admin;
        $pengajuan->save();

        // Kirim email notifikasi ke pemohon
        try {
            Mail::to($pengajuan->email_pemohon)->send(new PengajuanStatusMail($pengajuan));
        } catch (\Exception $e) {
            Log::warning('Gagal mengirim email status pengajuan: ' . $e->getMessage());
        }

        $statusLabel = $request->status === 'disetujui' ? 'disetujui' : 'ditolak';
        Flash::success("Pengajuan {$pengajuan->kode_pengajuan} telah {$statusLabel}.");

        return redirect(route('pengajuan_gedungs.index'));
    }

    /**
     * User: Daftar pengajuan milik user yang login
     */
    public function riwayat()
    {
        $pengajuanGedungs = PengajuanGedung::with('gedung')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dashboard.pengajuan_gedungs.riwayat')
            ->with('pengajuanGedungs', $pengajuanGedungs);
    }

    /**
     * Admin: Hapus pengajuan
     */
    public function destroy($id)
    {
        // Hanya admin yang boleh hapus
        if (!Auth::user()->isAdmin()) {
            Flash::error('Akses ditolak.');
            return redirect(route('pengajuan_gedungs.riwayat'));
        }

        $pengajuanGedung = $this->pengajuanGedungRepository->find($id);

        if (empty($pengajuanGedung)) {
            Flash::error('Pengajuan tidak ditemukan.');
            return redirect(route('pengajuan_gedungs.index'));
        }

        $this->pengajuanGedungRepository->delete($id);

        Flash::success('Pengajuan berhasil dihapus.');

        return redirect(route('pengajuan_gedungs.index'));
    }
}
