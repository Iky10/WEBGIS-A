<?php

namespace App\Http\Controllers;

use App\Repositories\PengajuanGedungRepository;
use App\Http\Requests\CreatePengajuanGedungRequest;
use App\Http\Requests\UpdatePengajuanGedungRequest;
use App\Http\Controllers\AppBaseController;
use App\Models\Gedung;
use App\Models\PengajuanGedung;
use App\Mail\PengajuanSubmitted;
use App\Mail\PengajuanStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Flash;
use Response;

class PengajuanGedungController extends AppBaseController
{
    /** @var PengajuanGedungRepository $pengajuanGedungRepository */
    private $pengajuanGedungRepository;

    public function __construct(PengajuanGedungRepository $pengajuanGedungRepo)
    {
        $this->pengajuanGedungRepository = $pengajuanGedungRepo;
    }

    /**
     * Display a listing of the PengajuanGedung.
     */
    public function index(Request $request)
    {
        $pengajuanGedungs = $this->pengajuanGedungRepository->all();

        return view('pengajuan_gedungs.index')
            ->with('pengajuanGedungs', $pengajuanGedungs);
    }

    /**
     * Show the form for creating a new PengajuanGedung.
     * Bisa diakses tanpa login (publik).
     */
    public function create(Request $request)
    {
        $gedungs = Gedung::all()->pluck('nama_gedung', 'id');

        // Pre-fill gedung jika datang dari halaman detail gedung
        $selectedGedung = $request->get('gedung_id', null);

        return view('pengajuan_gedungs.create')
            ->with('gedungs', $gedungs)
            ->with('selectedGedung', $selectedGedung);
    }

    /**
     * Store a newly created PengajuanGedung in storage.
     * Bisa diakses tanpa login (publik).
     */
    public function store(CreatePengajuanGedungRequest $request)
    {
        $input = $request->all();

        // Set user_id jika login, null jika guest
        $input['user_id'] = auth()->id();
        $input['status'] = 'diproses';
        $input['kode_pengajuan'] = PengajuanGedung::generateKode();

        $pengajuanGedung = $this->pengajuanGedungRepository->create($input);

        // Load relasi gedung untuk email
        $pengajuanGedung->load('gedung');

        // Kirim email konfirmasi (try-catch agar tidak blocking jika SMTP gagal)
        try {
            Mail::to($pengajuanGedung->email_pemohon)
                ->send(new PengajuanSubmitted($pengajuanGedung));
        } catch (\Exception $e) {
            // Log error tapi jangan block proses
            \Log::warning('Gagal kirim email pengajuan: ' . $e->getMessage());
        }

        // Redirect ke halaman sukses
        return redirect()->route('pengajuan.sukses', $pengajuanGedung->kode_pengajuan);
    }

    /**
     * Halaman sukses setelah submit pengajuan (publik).
     */
    public function sukses($kode)
    {
        $pengajuan = PengajuanGedung::where('kode_pengajuan', $kode)->firstOrFail();
        $pengajuan->load('gedung');

        return view('pengajuan_gedungs.sukses')->with('pengajuan', $pengajuan);
    }

    /**
     * Form cek status pengajuan (publik).
     */
    public function cekStatus()
    {
        return view('pengajuan_gedungs.cek-status');
    }

    /**
     * Hasil cek status pengajuan (publik).
     * Bisa cek pakai email saja (jika lupa kode) atau email + kode.
     */
    public function cekStatusResult(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = $request->email;
        $kode = $request->kode;

        $query = PengajuanGedung::with('gedung')
            ->where('email_pemohon', $email);

        // Jika kode diisi, filter spesifik
        if ($kode) {
            $query->where('kode_pengajuan', $kode);
        }

        $results = $query->orderBy('created_at', 'desc')->get();

        return view('pengajuan_gedungs.cek-status')
            ->with('results', $results)
            ->with('email', $email)
            ->with('kode', $kode);
    }

    /**
     * Display the specified PengajuanGedung.
     */
    public function show($id)
    {
        $pengajuanGedung = $this->pengajuanGedungRepository->find($id);

        if (empty($pengajuanGedung)) {
            Flash::error('Pengajuan tidak ditemukan.');

            return redirect(route('pengajuan_gedungs.index'));
        }

        return view('pengajuan_gedungs.show')->with('pengajuanGedung', $pengajuanGedung);
    }

    /**
     * Show the form for editing the specified PengajuanGedung.
     */
    public function edit($id)
    {
        $pengajuanGedung = $this->pengajuanGedungRepository->find($id);

        if (empty($pengajuanGedung)) {
            Flash::error('Pengajuan tidak ditemukan.');

            return redirect(route('pengajuan_gedungs.index'));
        }

        $gedungs = Gedung::all()->pluck('nama_gedung', 'id');

        return view('pengajuan_gedungs.edit')
            ->with('pengajuanGedung', $pengajuanGedung)
            ->with('gedungs', $gedungs);
    }

    /**
     * Update the specified PengajuanGedung in storage.
     */
    public function update($id, UpdatePengajuanGedungRequest $request)
    {
        $pengajuanGedung = $this->pengajuanGedungRepository->find($id);

        if (empty($pengajuanGedung)) {
            Flash::error('Pengajuan tidak ditemukan.');

            return redirect(route('pengajuan_gedungs.index'));
        }

        $pengajuanGedung = $this->pengajuanGedungRepository->update($request->all(), $id);

        Flash::success('Pengajuan berhasil diperbarui.');

        return redirect(route('pengajuan_gedungs.index'));
    }

    /**
     * Remove the specified PengajuanGedung from storage.
     */
    public function destroy($id)
    {
        $pengajuanGedung = $this->pengajuanGedungRepository->find($id);

        if (empty($pengajuanGedung)) {
            Flash::error('Pengajuan tidak ditemukan.');

            return redirect(route('pengajuan_gedungs.index'));
        }

        $this->pengajuanGedungRepository->delete($id);

        Flash::success('Pengajuan berhasil dihapus.');

        return redirect(route('pengajuan_gedungs.index'));
    }

    /**
     * Update status pengajuan (admin only).
     * Otomatis kirim email notifikasi ke pemohon.
     */
    public function updateStatus($id, Request $request)
    {
        $pengajuanGedung = $this->pengajuanGedungRepository->find($id);

        if (empty($pengajuanGedung)) {
            Flash::error('Pengajuan tidak ditemukan.');

            return redirect(route('pengajuan_gedungs.index'));
        }

        $request->validate([
            'status' => 'required|in:diproses,disetujui,ditolak',
        ]);

        $pengajuanGedung->status = $request->status;
        $pengajuanGedung->catatan_admin = $request->catatan_admin;
        $pengajuanGedung->save();

        // Kirim email notifikasi ke pemohon
        try {
            $pengajuanGedung->load('gedung');
            Mail::to($pengajuanGedung->email_pemohon)
                ->send(new PengajuanStatusUpdated($pengajuanGedung));
        } catch (\Exception $e) {
            \Log::warning('Gagal kirim email status update: ' . $e->getMessage());
        }

        Flash::success('Status pengajuan berhasil diperbarui.');

        return redirect(route('pengajuan_gedungs.show', $id));
    }

    /**
     * Ajukan ulang dari pengajuan yang ditolak (admin).
     * Menggunakan layout admin (create-admin).
     */
    public function ajukanUlang($id)
    {
        $pengajuanGedung = $this->pengajuanGedungRepository->find($id);

        if (empty($pengajuanGedung)) {
            Flash::error('Pengajuan tidak ditemukan.');

            return redirect(route('pengajuan_gedungs.index'));
        }

        $gedungs = Gedung::all()->pluck('nama_gedung', 'id');

        // Kirim data lama ke form create-admin untuk di-copy
        return view('pengajuan_gedungs.create-admin')
            ->with('gedungs', $gedungs)
            ->with('selectedGedung', $pengajuanGedung->gedung_id)
            ->with('pengajuanLama', $pengajuanGedung);
    }
}
