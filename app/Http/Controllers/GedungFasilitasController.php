<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateGedungFasilitasRequest; // I need to create this later or use Request
use App\Http\Requests\UpdateGedungFasilitasRequest; // I need to create this later or use Request
use App\Repositories\GedungFasilitasRepository;
use App\Http\Controllers\AppBaseController;
use App\Models\Gedung;
use Illuminate\Http\Request;
use Flash;
use Response;

class GedungFasilitasController extends AppBaseController
{
    /** @var GedungFasilitasRepository $gedungFasilitasRepository*/
    private $gedungFasilitasRepository;

    public function __construct(GedungFasilitasRepository $gedungFasilitasRepo)
    {
        $this->gedungFasilitasRepository = $gedungFasilitasRepo;
    }

    /**
     * Display a listing of the GedungFasilitas.
     */
    public function index(Request $request)
    {
        $gedungFasilitas = $this->gedungFasilitasRepository->all();

        return view('dashboard.gedung_fasilitas.index')
            ->with('gedungFasilitas', $gedungFasilitas);
    }

    /**
     * Show the form for creating a new GedungFasilitas.
     */
    public function create()
    {
        $gedungs = Gedung::pluck('nama_gedung', 'id');
        return view('dashboard.gedung_fasilitas.create')->with('gedungs', $gedungs);
    }

    /**
     * Store a newly created GedungFasilitas in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'gedung_id' => 'required|exists:gedungs,id',
            'nama_fasilitas' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'foto_ruangan' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'
        ], [
            'gedung_id.required' => 'Gedung harus dipilih.',
            'nama_fasilitas.required' => 'Nama fasilitas / ruangan wajib diisi.',
            'kategori.required' => 'Kategori wajib dipilih.'
        ]);

        $input = $request->except(['foto_ruangan']);
        // Handle checkbox fields (hidden field trick: value=0 hidden + value=1 checkbox)
        $input['is_aktif'] = $request->boolean('is_aktif');
        $input['bisa_diajukan'] = $request->boolean('bisa_diajukan');

        // Upload foto ruangan jika ada
        if ($request->hasFile('foto_ruangan')) {
            $file = $request->file('foto_ruangan');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/ruangan'), $filename);
            $input['foto_ruangan'] = 'images/ruangan/' . $filename;
        }

        $gedungFasilitas = $this->gedungFasilitasRepository->create($input);

        Flash::success('Fasilitas / Ruangan berhasil disimpan.');

        return redirect(route('gedung_fasilitas.index'));
    }

    /**
     * Display the specified GedungFasilitas.
     */
    public function show($id)
    {
        $gedungFasilitas = $this->gedungFasilitasRepository->find($id);

        if (empty($gedungFasilitas)) {
            Flash::error('Fasilitas / Ruangan tidak ditemukan.');

            return redirect(route('gedung_fasilitas.index'));
        }

        return view('dashboard.gedung_fasilitas.show')->with('gedungFasilitas', $gedungFasilitas);
    }

    /**
     * Show the form for editing the specified GedungFasilitas.
     */
    public function edit($id)
    {
        $gedungFasilitas = $this->gedungFasilitasRepository->find($id);

        if (empty($gedungFasilitas)) {
            Flash::error('Fasilitas / Ruangan tidak ditemukan.');

            return redirect(route('gedung_fasilitas.index'));
        }

        $gedungs = Gedung::pluck('nama_gedung', 'id');
        return view('dashboard.gedung_fasilitas.edit')
            ->with('gedungFasilitas', $gedungFasilitas)
            ->with('gedungs', $gedungs);
    }

    /**
     * Update the specified GedungFasilitas in storage.
     */
    public function update($id, Request $request)
    {
        $request->validate([
            'gedung_id' => 'required|exists:gedungs,id',
            'nama_fasilitas' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'foto_ruangan' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'
        ], [
            'gedung_id.required' => 'Gedung harus dipilih.',
            'nama_fasilitas.required' => 'Nama fasilitas / ruangan wajib diisi.',
            'kategori.required' => 'Kategori wajib dipilih.'
        ]);

        $gedungFasilitas = $this->gedungFasilitasRepository->find($id);

        if (empty($gedungFasilitas)) {
            Flash::error('Fasilitas / Ruangan tidak ditemukan.');

            return redirect(route('gedung_fasilitas.index'));
        }

        $input = $request->except(['foto_ruangan']);
        $input['is_aktif'] = $request->boolean('is_aktif');
        $input['bisa_diajukan'] = $request->boolean('bisa_diajukan');

        // Upload foto ruangan jika ada file baru
        if ($request->hasFile('foto_ruangan')) {
            // Hapus foto lama jika ada
            if ($gedungFasilitas->foto_ruangan && file_exists(public_path($gedungFasilitas->foto_ruangan))) {
                unlink(public_path($gedungFasilitas->foto_ruangan));
            }
            $file = $request->file('foto_ruangan');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/ruangan'), $filename);
            $input['foto_ruangan'] = 'images/ruangan/' . $filename;
        }

        $gedungFasilitas = $this->gedungFasilitasRepository->update($input, $id);

        Flash::success('Fasilitas / Ruangan berhasil diperbarui.');

        return redirect(route('gedung_fasilitas.index'));
    }

    /**
     * Remove the specified GedungFasilitas from storage.
     */
    public function destroy($id)
    {
        $gedungFasilitas = $this->gedungFasilitasRepository->find($id);

        if (empty($gedungFasilitas)) {
            Flash::error('Fasilitas / Ruangan tidak ditemukan.');

            return redirect(route('gedung_fasilitas.index'));
        }

        // Hapus foto ruangan dari storage
        if ($gedungFasilitas->foto_ruangan && file_exists(public_path($gedungFasilitas->foto_ruangan))) {
            unlink(public_path($gedungFasilitas->foto_ruangan));
        }

        $this->gedungFasilitasRepository->delete($id);

        Flash::success('Fasilitas / Ruangan berhasil dihapus.');

        return redirect(route('gedung_fasilitas.index'));
    }

    /**
     * Toggle the status of the specified GedungFasilitas (is_aktif).
     * Flag 'is_aktif' = ruangan operasional / tidak (misal sedang perbaikan).
     */
    public function toggleStatus($id)
    {
        $gedungFasilitas = $this->gedungFasilitasRepository->find($id);

        if (empty($gedungFasilitas)) {
            return response()->json(['success' => false, 'message' => 'Fasilitas tidak ditemukan'], 404);
        }

        $gedungFasilitas->is_aktif = !$gedungFasilitas->is_aktif;
        $gedungFasilitas->save();

        $statusText = $gedungFasilitas->is_aktif ? 'Aktif' : 'Tidak Aktif';
        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diubah menjadi ' . $statusText,
            'is_aktif' => $gedungFasilitas->is_aktif
        ]);
    }

    /**
     * Toggle the bisa_diajukan flag of the specified GedungFasilitas.
     * Flag 'bisa_diajukan' = ruangan boleh diajukan user untuk penggunaan ad-hoc.
     */
    public function toggleBisaDiajukan($id)
    {
        $gedungFasilitas = $this->gedungFasilitasRepository->find($id);

        if (empty($gedungFasilitas)) {
            return response()->json(['success' => false, 'message' => 'Fasilitas tidak ditemukan'], 404);
        }

        $gedungFasilitas->bisa_diajukan = !$gedungFasilitas->bisa_diajukan;
        $gedungFasilitas->save();

        $label = $gedungFasilitas->bisa_diajukan ? 'Ya' : 'Tidak';
        return response()->json([
            'success' => true,
            'message' => 'Status "Bisa Diajukan" berhasil diubah menjadi ' . $label,
            'bisa_diajukan' => $gedungFasilitas->bisa_diajukan
        ]);
    }

    /**
     * Remove multiple GedungFasilitas from storage.
     */
    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;

        if (empty($ids) || !is_array($ids)) {
            return response()->json(['success' => false, 'message' => 'Tidak ada data yang dipilih'], 400);
        }

        $deletedCount = 0;
        foreach ($ids as $id) {
            $gedungFasilitas = $this->gedungFasilitasRepository->find($id);
            if ($gedungFasilitas) {
                // Hapus foto ruangan dari storage
                if ($gedungFasilitas->foto_ruangan && file_exists(public_path($gedungFasilitas->foto_ruangan))) {
                    unlink(public_path($gedungFasilitas->foto_ruangan));
                }
                $this->gedungFasilitasRepository->delete($id);
                $deletedCount++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => $deletedCount . ' data fasilitas berhasil dihapus.'
        ]);
    }
}