<?php

namespace App\Http\Controllers;

use App\Repositories\JadwalRuanganRepository;
use App\Http\Controllers\AppBaseController;
use App\Models\GedungFasilitas;
use Illuminate\Http\Request;
use Flash;
use Response;

class JadwalRuanganController extends AppBaseController
{
    /** @var JadwalRuanganRepository $jadwalRuanganRepository*/
    private $jadwalRuanganRepository;

    public function __construct(JadwalRuanganRepository $jadwalRuanganRepo)
    {
        $this->jadwalRuanganRepository = $jadwalRuanganRepo;
    }

    /**
     * Display a listing of the JadwalRuangan.
     */
    public function index(Request $request)
    {
        $jadwalRuangans = $this->jadwalRuanganRepository->all();

        return view('jadwal_ruangans.index')
            ->with('jadwalRuangans', $jadwalRuangans);
    }

    /**
     * Show the form for creating a new JadwalRuangan.
     */
    public function create()
    {
        $fasilitas = GedungFasilitas::all()->mapWithKeys(function ($item) {
            return [$item->id => $item->gedung->nama_gedung . ' - ' . $item->nama_fasilitas];
        });

        return view('jadwal_ruangans.create')->with('fasilitas', $fasilitas);
    }

    /**
     * Store a newly created JadwalRuangan in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'gedung_fasilitas_id' => 'required|exists:gedung_fasilitas,id',
            'nama_kegiatan' => 'required|string|max:255',
            'hari' => 'required|string|max:20',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'keterangan' => 'nullable|string'
        ], [
            'gedung_fasilitas_id.required' => 'Fasilitas / Ruangan harus dipilih.',
            'nama_kegiatan.required' => 'Nama kegiatan wajib diisi.',
            'hari.required' => 'Hari wajib dipilih.',
            'jam_mulai.required' => 'Jam mulai wajib diisi.',
            'jam_selesai.required' => 'Jam selesai wajib diisi.',
            'jam_selesai.after' => 'Jam selesai harus lebih besar dari jam mulai.'
        ]);

        $input = $request->all();

        $jadwalRuangan = $this->jadwalRuanganRepository->create($input);

        Flash::success('Jadwal Ruangan berhasil disimpan.');

        return redirect(route('jadwal_ruangans.index'));
    }

    /**
     * Display the specified JadwalRuangan.
     */
    public function show($id)
    {
        $jadwalRuangan = $this->jadwalRuanganRepository->find($id);

        if (empty($jadwalRuangan)) {
            Flash::error('Jadwal Ruangan tidak ditemukan.');

            return redirect(route('jadwal_ruangans.index'));
        }

        return view('jadwal_ruangans.show')->with('jadwalRuangan', $jadwalRuangan);
    }

    /**
     * Show the form for editing the specified JadwalRuangan.
     */
    public function edit($id)
    {
        $jadwalRuangan = $this->jadwalRuanganRepository->find($id);

        if (empty($jadwalRuangan)) {
            Flash::error('Jadwal Ruangan tidak ditemukan.');

            return redirect(route('jadwal_ruangans.index'));
        }

        $fasilitas = GedungFasilitas::all()->mapWithKeys(function ($item) {
            return [$item->id => $item->gedung->nama_gedung . ' - ' . $item->nama_fasilitas];
        });

        return view('jadwal_ruangans.edit')
            ->with('jadwalRuangan', $jadwalRuangan)
            ->with('fasilitas', $fasilitas);
    }

    /**
     * Update the specified JadwalRuangan in storage.
     */
    public function update($id, Request $request)
    {
        $request->validate([
            'gedung_fasilitas_id' => 'required|exists:gedung_fasilitas,id',
            'nama_kegiatan' => 'required|string|max:255',
            'hari' => 'required|string|max:20',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'keterangan' => 'nullable|string'
        ], [
            'gedung_fasilitas_id.required' => 'Fasilitas / Ruangan harus dipilih.',
            'nama_kegiatan.required' => 'Nama kegiatan wajib diisi.',
            'hari.required' => 'Hari wajib dipilih.',
            'jam_mulai.required' => 'Jam mulai wajib diisi.',
            'jam_selesai.required' => 'Jam selesai wajib diisi.',
            'jam_selesai.after' => 'Jam selesai harus lebih besar dari jam mulai.'
        ]);

        $jadwalRuangan = $this->jadwalRuanganRepository->find($id);

        if (empty($jadwalRuangan)) {
            Flash::error('Jadwal Ruangan tidak ditemukan.');

            return redirect(route('jadwal_ruangans.index'));
        }

        $jadwalRuangan = $this->jadwalRuanganRepository->update($request->all(), $id);

        Flash::success('Jadwal Ruangan berhasil diperbarui.');

        return redirect(route('jadwal_ruangans.index'));
    }

    /**
     * Remove the specified JadwalRuangan from storage.
     */
    public function destroy($id)
    {
        $jadwalRuangan = $this->jadwalRuanganRepository->find($id);

        if (empty($jadwalRuangan)) {
            Flash::error('Jadwal Ruangan tidak ditemukan.');

            return redirect(route('jadwal_ruangans.index'));
        }

        $this->jadwalRuanganRepository->delete($id);

        Flash::success('Jadwal Ruangan berhasil dihapus.');

        return redirect(route('jadwal_ruangans.index'));
    }
}