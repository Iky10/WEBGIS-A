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
     *
     * Mendukung bulk insert multi-hari dengan per-day time override.
     * Form mengirim:
     *   - hari[]                  : array hari yang dicentang
     *   - jam_mulai / jam_selesai : jam default (dipakai kalau tidak ada override)
     *   - override_enabled[hari]  : '1' kalau hari tsb pakai jam custom
     *   - override_jam_mulai[hari], override_jam_selesai[hari]
     */
    public function store(Request $request)
    {
        $validDays = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

        $request->validate([
            'gedung_fasilitas_id'            => 'required|exists:gedung_fasilitas,id',
            'nama_kegiatan'                  => 'required|string|max:255',
            'hari'                           => 'required|array|min:1',
            'hari.*'                         => ['required', 'string', \Illuminate\Validation\Rule::in($validDays)],
            'jam_mulai'                      => 'required|date_format:H:i',
            'jam_selesai'                    => 'required|date_format:H:i|after:jam_mulai',
            'override_enabled'               => 'nullable|array',
            'override_enabled.*'             => 'nullable|in:0,1',
            'override_jam_mulai'             => 'nullable|array',
            'override_jam_mulai.*'           => 'nullable|date_format:H:i',
            'override_jam_selesai'           => 'nullable|array',
            'override_jam_selesai.*'         => 'nullable|date_format:H:i',
            'keterangan'                     => 'nullable|string',
        ], [
            'gedung_fasilitas_id.required' => 'Fasilitas / Ruangan harus dipilih.',
            'nama_kegiatan.required'       => 'Nama kegiatan wajib diisi.',
            'hari.required'                => 'Minimal pilih satu hari.',
            'hari.min'                     => 'Minimal pilih satu hari.',
            'hari.*.in'                    => 'Hari yang dipilih tidak valid.',
            'jam_mulai.required'           => 'Jam mulai default wajib diisi.',
            'jam_mulai.date_format'        => 'Format jam mulai harus HH:MM.',
            'jam_selesai.required'         => 'Jam selesai default wajib diisi.',
            'jam_selesai.date_format'      => 'Format jam selesai harus HH:MM.',
            'jam_selesai.after'            => 'Jam selesai default harus lebih besar dari jam mulai.',
        ]);

        $days            = $request->input('hari', []);
        $defaultMulai    = $request->input('jam_mulai');
        $defaultSelesai  = $request->input('jam_selesai');
        $overrideEnabled = $request->input('override_enabled', []);
        $overrideMulai   = $request->input('override_jam_mulai', []);
        $overrideSelesai = $request->input('override_jam_selesai', []);

        $baseInput = [
            'gedung_fasilitas_id' => $request->input('gedung_fasilitas_id'),
            'nama_kegiatan'       => $request->input('nama_kegiatan'),
            'keterangan'          => $request->input('keterangan'),
        ];

        // Validasi per-hari: kalau override aktif, jam_selesai > jam_mulai untuk hari tsb
        $perDayErrors = [];
        $rowsToInsert = [];
        foreach ($days as $day) {
            $useOverride = !empty($overrideEnabled[$day]) && $overrideEnabled[$day] === '1';
            $jamMulai    = $useOverride ? ($overrideMulai[$day] ?? null) : $defaultMulai;
            $jamSelesai  = $useOverride ? ($overrideSelesai[$day] ?? null) : $defaultSelesai;

            if ($useOverride && (empty($jamMulai) || empty($jamSelesai))) {
                $perDayErrors[] = "Override untuk {$day} aktif tapi jam mulai/selesai belum diisi.";
                continue;
            }

            if ($jamSelesai <= $jamMulai) {
                $perDayErrors[] = "Jam selesai {$day} ({$jamSelesai}) harus lebih besar dari jam mulai ({$jamMulai}).";
                continue;
            }

            $rowsToInsert[] = array_merge($baseInput, [
                'hari'        => $day,
                'jam_mulai'   => $jamMulai,
                'jam_selesai' => $jamSelesai,
            ]);
        }

        if (!empty($perDayErrors)) {
            return redirect()->back()->withInput()->withErrors($perDayErrors);
        }

        $created = 0;
        foreach ($rowsToInsert as $row) {
            $this->jadwalRuanganRepository->create($row);
            $created++;
        }

        // Flush cache satu kali setelah semua insert (hemat query)
        $this->flushStatusCacheForFasilitas($baseInput['gedung_fasilitas_id']);

        if ($created === 1) {
            Flash::success('Jadwal Ruangan berhasil disimpan.');
        } else {
            Flash::success($created . ' jadwal ruangan berhasil dibuat sekaligus.');
        }

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