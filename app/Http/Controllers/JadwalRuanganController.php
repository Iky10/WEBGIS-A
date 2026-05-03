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

        return view('dashboard.jadwal_ruangans.index')
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

        return view('dashboard.jadwal_ruangans.create')->with('fasilitas', $fasilitas);
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

        // Flush cache status realtime — supaya status di peta langsung update
        $this->flushStatusCacheForFasilitas($input['gedung_fasilitas_id']);

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

        return view('dashboard.jadwal_ruangans.show')->with('jadwalRuangan', $jadwalRuangan);
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

        return view('dashboard.jadwal_ruangans.edit')
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

        // Simpan ID ruangan lama (kalau berubah, perlu flush kedua-duanya)
        $oldFasilitasId = $jadwalRuangan->gedung_fasilitas_id;
        $newFasilitasId = $request->input('gedung_fasilitas_id');

        $jadwalRuangan = $this->jadwalRuanganRepository->update($request->all(), $id);

        // Flush cache untuk ruangan lama dan baru (kalau berbeda)
        $this->flushStatusCacheForFasilitas($oldFasilitasId);
        if ($newFasilitasId && $newFasilitasId != $oldFasilitasId) {
            $this->flushStatusCacheForFasilitas($newFasilitasId);
        }

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

        // Simpan ID ruangan SEBELUM delete (model akan jadi soft-deleted)
        $fasilitasId = $jadwalRuangan->gedung_fasilitas_id;

        $this->jadwalRuanganRepository->delete($id);

        // Flush cache supaya status realtime langsung refresh
        $this->flushStatusCacheForFasilitas($fasilitasId);

        Flash::success('Jadwal Ruangan berhasil dihapus.');

        return redirect(route('jadwal_ruangans.index'));
    }

    /**
     * Helper: flush cache status_dipakai untuk ruangan + gedung induknya.
     * Dipanggil setelah JadwalRuangan create/update/delete supaya status
     * realtime di peta dan dashboard langsung tercermin (tanpa tunggu TTL 60s).
     */
    protected function flushStatusCacheForFasilitas($fasilitasId): void
    {
        if (!$fasilitasId) {
            return;
        }

        $fasilitas = GedungFasilitas::with('gedung')->find($fasilitasId);
        if (!$fasilitas) {
            return;
        }

        $fasilitas->flushStatusCache();

        if ($fasilitas->gedung) {
            $fasilitas->gedung->flushStatusCache();
        }
    }
}