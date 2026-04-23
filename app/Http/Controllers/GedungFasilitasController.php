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

        return view('gedung_fasilitas.index')
            ->with('gedungFasilitas', $gedungFasilitas);
    }

    /**
     * Show the form for creating a new GedungFasilitas.
     */
    public function create()
    {
        $gedungs = Gedung::pluck('nama_gedung', 'id');
        return view('gedung_fasilitas.create')->with('gedungs', $gedungs);
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
            'keterangan' => 'nullable|string'
        ], [
            'gedung_id.required' => 'Gedung harus dipilih.',
            'nama_fasilitas.required' => 'Nama fasilitas / ruangan wajib diisi.',
            'kategori.required' => 'Kategori wajib dipilih.'
        ]);

        $input = $request->all();
        // Handle checkbox for is_aktif
        $input['is_aktif'] = $request->has('is_aktif') ? true : false;

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

        return view('gedung_fasilitas.show')->with('gedungFasilitas', $gedungFasilitas);
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
        return view('gedung_fasilitas.edit')
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
            'keterangan' => 'nullable|string'
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

        $input = $request->all();
        $input['is_aktif'] = $request->has('is_aktif') ? true : false;

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

        $this->gedungFasilitasRepository->delete($id);

        Flash::success('Fasilitas / Ruangan berhasil dihapus.');

        return redirect(route('gedung_fasilitas.index'));
    }
}