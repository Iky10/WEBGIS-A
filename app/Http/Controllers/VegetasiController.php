<?php

namespace App\Http\Controllers;

use App\Repositories\VegetasiRepository;
use App\Http\Controllers\AppBaseController;
use App\Models\Gedung;
use App\Models\GambarVegetasi;
use Illuminate\Http\Request;
use Flash;
use Response;

class VegetasiController extends AppBaseController
{
    /** @var VegetasiRepository $vegetasiRepository*/
    private $vegetasiRepository;

    public function __construct(VegetasiRepository $vegetasiRepo)
    {
        $this->vegetasiRepository = $vegetasiRepo;
    }

    /**
     * Display a listing of the Vegetasi.
     */
    public function index(Request $request)
    {
        $vegetasis = $this->vegetasiRepository->all();

        return view('dashboard.vegetasis.index')
            ->with('vegetasis', $vegetasis);
    }

    /**
     * Show the form for creating a new Vegetasi.
     */
    public function create()
    {
        $gedungs = Gedung::pluck('nama_gedung', 'id');
        return view('dashboard.vegetasis.create')->with('gedungs', $gedungs);
    }

    /**
     * Store a newly created Vegetasi in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'gedung_id' => 'required|exists:gedungs,id',
            'nama_vegetasi' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'foto_utama' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'foto_tambahan.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'
        ], [
            'gedung_id.required' => 'Gedung harus dipilih.',
            'nama_vegetasi.required' => 'Nama vegetasi wajib diisi.',
            'kategori.required' => 'Kategori wajib dipilih.'
        ]);

        $input = $request->except(['foto_utama', 'foto_tambahan']);

        // Upload foto utama jika ada
        if ($request->hasFile('foto_utama')) {
            $file = $request->file('foto_utama');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/vegetasi'), $filename);
            $input['foto_utama'] = 'images/vegetasi/' . $filename;
        }

        $vegetasi = $this->vegetasiRepository->create($input);

        // Upload foto tambahan jika ada
        if ($request->hasFile('foto_tambahan')) {
            foreach ($request->file('foto_tambahan') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('images/vegetasi/galeri'), $filename);
                
                GambarVegetasi::create([
                    'vegetasi_id' => $vegetasi->id,
                    'nama_file' => $file->getClientOriginalName(),
                    'path_foto' => 'images/vegetasi/galeri/' . $filename,
                    'urutan' => 0
                ]);
            }
        }

        Flash::success('Data Vegetasi berhasil disimpan.');

        return redirect(route('vegetasis.index'));
    }

    /**
     * Display the specified Vegetasi.
     */
    public function show($id)
    {
        $vegetasi = $this->vegetasiRepository->find($id);

        if (empty($vegetasi)) {
            Flash::error('Data Vegetasi tidak ditemukan.');
            return redirect(route('vegetasis.index'));
        }

        return view('dashboard.vegetasis.show')->with('vegetasi', $vegetasi);
    }

    /**
     * Show the form for editing the specified Vegetasi.
     */
    public function edit($id)
    {
        $vegetasi = $this->vegetasiRepository->find($id);

        if (empty($vegetasi)) {
            Flash::error('Data Vegetasi tidak ditemukan.');
            return redirect(route('vegetasis.index'));
        }

        $gedungs = Gedung::pluck('nama_gedung', 'id');
        return view('dashboard.vegetasis.edit')
            ->with('vegetasi', $vegetasi)
            ->with('gedungs', $gedungs);
    }

    /**
     * Update the specified Vegetasi in storage.
     */
    public function update($id, Request $request)
    {
        $request->validate([
            'gedung_id' => 'required|exists:gedungs,id',
            'nama_vegetasi' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'foto_utama' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'foto_tambahan.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'
        ]);

        $vegetasi = $this->vegetasiRepository->find($id);

        if (empty($vegetasi)) {
            Flash::error('Data Vegetasi tidak ditemukan.');
            return redirect(route('vegetasis.index'));
        }

        $input = $request->except(['foto_utama', 'foto_tambahan']);

        // Upload foto utama jika ada file baru
        if ($request->hasFile('foto_utama')) {
            if ($vegetasi->foto_utama && file_exists(public_path($vegetasi->foto_utama))) {
                unlink(public_path($vegetasi->foto_utama));
            }
            $file = $request->file('foto_utama');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/vegetasi'), $filename);
            $input['foto_utama'] = 'images/vegetasi/' . $filename;
        }

        $vegetasi = $this->vegetasiRepository->update($input, $id);

        // Upload foto tambahan baru jika ada
        if ($request->hasFile('foto_tambahan')) {
            foreach ($request->file('foto_tambahan') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('images/vegetasi/galeri'), $filename);
                
                GambarVegetasi::create([
                    'vegetasi_id' => $vegetasi->id,
                    'nama_file' => $file->getClientOriginalName(),
                    'path_foto' => 'images/vegetasi/galeri/' . $filename,
                    'urutan' => 0
                ]);
            }
        }

        Flash::success('Data Vegetasi berhasil diperbarui.');

        return redirect(route('vegetasis.index'));
    }

    /**
     * Remove the specified Vegetasi from storage.
     */
    public function destroy($id)
    {
        $vegetasi = $this->vegetasiRepository->find($id);

        if (empty($vegetasi)) {
            Flash::error('Data Vegetasi tidak ditemukan.');
            return redirect(route('vegetasis.index'));
        }

        // Hapus foto utama
        if ($vegetasi->foto_utama && file_exists(public_path($vegetasi->foto_utama))) {
            unlink(public_path($vegetasi->foto_utama));
        }

        // Hapus foto tambahan
        foreach ($vegetasi->gambarVegetasis as $gambar) {
            if (file_exists(public_path($gambar->path_foto))) {
                unlink(public_path($gambar->path_foto));
            }
            $gambar->delete();
        }

        $this->vegetasiRepository->delete($id);

        Flash::success('Data Vegetasi berhasil dihapus.');

        return redirect(route('vegetasis.index'));
    }

    /**
     * Delete additional image
     */
    public function deleteImage($id)
    {
        $gambar = GambarVegetasi::find($id);
        if ($gambar) {
            if (file_exists(public_path($gambar->path_foto))) {
                unlink(public_path($gambar->path_foto));
            }
            $gambar->delete();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 404);
    }
}
