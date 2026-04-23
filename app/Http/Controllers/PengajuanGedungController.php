<?php

namespace App\Http\Controllers;

use App\Repositories\PengajuanGedungRepository;
use App\Http\Requests\CreatePengajuanGedungRequest;
use App\Http\Requests\UpdatePengajuanGedungRequest;
use App\Http\Controllers\AppBaseController;
use App\Models\Gedung;
use Illuminate\Http\Request;
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
     */
    public function store(CreatePengajuanGedungRequest $request)
    {
        $input = $request->all();

        // Auto-set user_id dan status
        $input['user_id'] = auth()->id();
        $input['status'] = 'diproses';

        $pengajuanGedung = $this->pengajuanGedungRepository->create($input);

        Flash::success('Pengajuan penggunaan gedung berhasil dikirim.');

        return redirect(route('pengajuan_gedungs.index'));
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

        Flash::success('Status pengajuan berhasil diperbarui.');

        return redirect(route('pengajuan_gedungs.show', $id));
    }

    /**
     * Ajukan ulang dari pengajuan yang ditolak.
     */
    public function ajukanUlang($id)
    {
        $pengajuanGedung = $this->pengajuanGedungRepository->find($id);

        if (empty($pengajuanGedung)) {
            Flash::error('Pengajuan tidak ditemukan.');

            return redirect(route('pengajuan_gedungs.index'));
        }

        $gedungs = Gedung::all()->pluck('nama_gedung', 'id');

        // Kirim data lama ke form create untuk di-copy
        return view('pengajuan_gedungs.create')
            ->with('gedungs', $gedungs)
            ->with('selectedGedung', $pengajuanGedung->gedung_id)
            ->with('pengajuanLama', $pengajuanGedung);
    }
}
