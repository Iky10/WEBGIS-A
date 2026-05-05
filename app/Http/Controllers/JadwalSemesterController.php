<?php

namespace App\Http\Controllers;

use App\Repositories\JadwalSemesterRepository;
use App\Http\Controllers\AppBaseController;
use App\Models\JadwalSemester;
use App\Models\Gedung;
use Illuminate\Http\Request;
use Flash;
use Response;

class JadwalSemesterController extends AppBaseController
{
    /** @var JadwalSemesterRepository $jadwalSemesterRepository */
    private $jadwalSemesterRepository;

    public function __construct(JadwalSemesterRepository $jadwalSemesterRepo)
    {
        $this->jadwalSemesterRepository = $jadwalSemesterRepo;
    }

    /**
     * Display a listing of the JadwalSemester.
     */
    public function index(Request $request)
    {
        $jadwalSemesters = JadwalSemester::with('gedung')->get();

        return view('dashboard.jadwal_semester.index')
            ->with('jadwalSemesters', $jadwalSemesters);
    }

    /**
     * Show the form for creating a new JadwalSemester.
     */
    public function create()
    {
        $gedungs = Gedung::pluck('nama_gedung', 'id');
        
        $tahunSekarang = date('Y');
        $tahunAjarans = [];
        for ($i = -2; $i <= 5; $i++) {
            $tahunStart = $tahunSekarang + $i;
            $tahunEnd = $tahunStart + 1;
            $format = $tahunStart . '/' . $tahunEnd;
            $tahunAjarans[$format] = $format;
        }

        return view('dashboard.jadwal_semester.create', compact('gedungs', 'tahunAjarans'));
    }

    /**
     * Store a newly created JadwalSemester in storage.
     * Includes smart duplicate detection with upsert option.
     */
    public function store(Request $request)
    {
        $request->validate([
            'gedung_id' => 'required|exists:gedungs,id',
            'semester' => 'required|integer|min:1|max:8',
            'tahun_ajaran' => 'nullable|string|max:20',
            'file_jadwal' => 'required|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
            'keterangan' => 'nullable|string'
        ], [
            'gedung_id.required' => 'Gedung harus dipilih.',
            'semester.required' => 'Semester wajib dipilih.',
            'file_jadwal.required' => 'File jadwal wajib diupload.',
            'file_jadwal.mimes' => 'File jadwal harus berformat JPG, PNG, WebP, atau PDF.',
            'file_jadwal.max' => 'Ukuran file maksimal 5MB.'
        ]);

        // Check for existing duplicate
        $existing = JadwalSemester::where('gedung_id', $request->gedung_id)
            ->where('semester', $request->semester)
            ->where('tahun_ajaran', $request->tahun_ajaran)
            ->first();

        // Upload file jadwal
        $file = $request->file('file_jadwal');
        $filename = time() . '_semester' . $request->semester . '_' . $file->getClientOriginalName();
        $file->move(public_path('images/jadwal_semester'), $filename);
        $filePath = 'images/jadwal_semester/' . $filename;

        if ($existing) {
            // Upsert: update existing record
            // Delete old file
            if ($existing->file_jadwal && file_exists(public_path($existing->file_jadwal))) {
                unlink(public_path($existing->file_jadwal));
            }
            $existing->update([
                'file_jadwal' => $filePath,
                'keterangan' => $request->keterangan,
            ]);

            Flash::success('Jadwal Semester berhasil diperbarui (data lama otomatis diganti).');
        } else {
            // Create new
            $input = $request->except(['file_jadwal', 'confirm_update']);
            $input['file_jadwal'] = $filePath;
            $this->jadwalSemesterRepository->create($input);

            Flash::success('Jadwal Semester berhasil disimpan.');
        }

        return redirect(route('jadwal_semester.index'));
    }

    /**
     * Show the form for editing the specified JadwalSemester.
     */
    public function edit($id)
    {
        $jadwalSemester = $this->jadwalSemesterRepository->find($id);

        if (empty($jadwalSemester)) {
            Flash::error('Jadwal Semester tidak ditemukan.');

            return redirect(route('jadwal_semester.index'));
        }

        $gedungs = Gedung::pluck('nama_gedung', 'id');

        $tahunSekarang = date('Y');
        $tahunAjarans = [];
        for ($i = -2; $i <= 5; $i++) {
            $tahunStart = $tahunSekarang + $i;
            $tahunEnd = $tahunStart + 1;
            $format = $tahunStart . '/' . $tahunEnd;
            $tahunAjarans[$format] = $format;
        }

        return view('dashboard.jadwal_semester.edit')
            ->with('jadwalSemester', $jadwalSemester)
            ->with('gedungs', $gedungs)
            ->with('tahunAjarans', $tahunAjarans);
    }

    /**
     * Update the specified JadwalSemester in storage.
     */
    public function update($id, Request $request)
    {
        $request->validate([
            'gedung_id' => 'required|exists:gedungs,id',
            'semester' => 'required|integer|min:1|max:8',
            'tahun_ajaran' => 'nullable|string|max:20',
            'file_jadwal' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
            'keterangan' => 'nullable|string'
        ], [
            'gedung_id.required' => 'Gedung harus dipilih.',
            'semester.required' => 'Semester wajib dipilih.',
            'file_jadwal.mimes' => 'File jadwal harus berformat JPG, PNG, WebP, atau PDF.',
            'file_jadwal.max' => 'Ukuran file maksimal 5MB.'
        ]);

        $jadwalSemester = $this->jadwalSemesterRepository->find($id);

        if (empty($jadwalSemester)) {
            Flash::error('Jadwal Semester tidak ditemukan.');

            return redirect(route('jadwal_semester.index'));
        }

        $input = $request->except(['file_jadwal']);

        // Upload file jadwal jika ada file baru
        if ($request->hasFile('file_jadwal')) {
            // Hapus file lama jika ada
            if ($jadwalSemester->file_jadwal && file_exists(public_path($jadwalSemester->file_jadwal))) {
                unlink(public_path($jadwalSemester->file_jadwal));
            }
            $file = $request->file('file_jadwal');
            $filename = time() . '_semester' . $request->semester . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/jadwal_semester'), $filename);
            $input['file_jadwal'] = 'images/jadwal_semester/' . $filename;
        }

        $jadwalSemester = $this->jadwalSemesterRepository->update($input, $id);

        Flash::success('Jadwal Semester berhasil diperbarui.');

        return redirect(route('jadwal_semester.index'));
    }

    /**
     * Remove the specified JadwalSemester from storage.
     */
    public function destroy($id)
    {
        $jadwalSemester = $this->jadwalSemesterRepository->find($id);

        if (empty($jadwalSemester)) {
            Flash::error('Jadwal Semester tidak ditemukan.');

            return redirect(route('jadwal_semester.index'));
        }

        // Hapus file jadwal dari storage
        if ($jadwalSemester->file_jadwal && file_exists(public_path($jadwalSemester->file_jadwal))) {
            unlink(public_path($jadwalSemester->file_jadwal));
        }

        $this->jadwalSemesterRepository->delete($id);

        Flash::success('Jadwal Semester berhasil dihapus.');

        return redirect(route('jadwal_semester.index'));
    }
}
