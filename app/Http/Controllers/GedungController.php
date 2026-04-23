<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateGedungRequest;
use App\Http\Requests\UpdateGedungRequest;
use App\Repositories\GedungRepository;
use App\Http\Controllers\AppBaseController;
use App\Models\GambarGedung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Flash;
use Response;

class GedungController extends AppBaseController
{
    /** @var GedungRepository $gedungRepository*/
    private $gedungRepository;

    public function __construct(GedungRepository $gedungRepo)
    {
        $this->gedungRepository = $gedungRepo;
    }

    /**
     * Display a listing of the Gedung.
     */
    public function index(Request $request)
    {
        $gedungs = $this->gedungRepository->all();

        return view('gedungs.index')->with('gedungs', $gedungs);
    }

    /**
     * Show the form for creating a new Gedung.
     */
    public function create()
    {
        return view('gedungs.create');
    }

    /**
     * Store a newly created Gedung in storage.
     */
    public function store(CreateGedungRequest $request)
    {
        $input = $request->except(['foto_gedung']);

        // Upload foto utama jika ada
        if ($request->hasFile('foto_utama')) {
            $file = $request->file('foto_utama');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/gedung/utama'), $filename);
            $input['foto_utama'] = 'images/gedung/utama/' . $filename;
        }

        $gedung = $this->gedungRepository->create($input);

        // Upload multiple foto galeri jika ada
        if ($request->hasFile('foto_gedung')) {
            foreach ($request->file('foto_gedung') as $index => $foto) {
                $filename = time() . '_' . $index . '_' . $foto->getClientOriginalName();
                $foto->move(public_path('images/gedung/galeri'), $filename);

                GambarGedung::create([
                    'gedung_id'  => $gedung->id,
                    'nama_file'  => $foto->getClientOriginalName(),
                    'path_foto'  => 'images/gedung/galeri/' . $filename,
                    'keterangan' => '',
                    'is_utama'   => $index === 0 ? 1 : 0,
                    'urutan'     => $index,
                ]);
            }
        }

        Flash::success('Gedung berhasil disimpan.');

        return redirect(route('gedungs.index'));
    }

    /**
     * Display the specified Gedung.
     */
    public function show($id)
    {
        $gedung = $this->gedungRepository->find($id);

        if (empty($gedung)) {
            Flash::error('Gedung tidak ditemukan.');
            return redirect(route('gedungs.index'));
        }

        $fotos = GambarGedung::where('gedung_id', $id)->orderBy('urutan')->get();

        return view('gedungs.show')
            ->with('gedung', $gedung)
            ->with('fotos', $fotos);
    }

    /**
     * Show the form for editing the specified Gedung.
     */
    public function edit($id)
    {
        $gedung = $this->gedungRepository->find($id);

        if (empty($gedung)) {
            Flash::error('Gedung tidak ditemukan.');
            return redirect(route('gedungs.index'));
        }

        $fotos = GambarGedung::where('gedung_id', $id)->orderBy('urutan')->get();

        return view('gedungs.edit')
            ->with('gedung', $gedung)
            ->with('fotos', $fotos);
    }

    /**
     * Update the specified Gedung in storage.
     */
    public function update($id, UpdateGedungRequest $request)
    {
        $gedung = $this->gedungRepository->find($id);

        if (empty($gedung)) {
            Flash::error('Gedung tidak ditemukan.');
            return redirect(route('gedungs.index'));
        }

        $input = $request->except(['foto_gedung']);

        // Update foto utama jika ada file baru
        if ($request->hasFile('foto_utama')) {
            // Hapus foto lama jika ada
            if ($gedung->foto_utama && file_exists(public_path($gedung->foto_utama))) {
                unlink(public_path($gedung->foto_utama));
            }
            $file = $request->file('foto_utama');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/gedung/utama'), $filename);
            $input['foto_utama'] = 'images/gedung/utama/' . $filename;
        }

        $gedung = $this->gedungRepository->update($input, $id);

        // Tambah foto galeri baru jika ada
        if ($request->hasFile('foto_gedung')) {
            $lastUrutan = GambarGedung::where('gedung_id', $id)->max('urutan') ?? -1;

            foreach ($request->file('foto_gedung') as $index => $foto) {
                $filename = time() . '_' . $index . '_' . $foto->getClientOriginalName();
                $foto->move(public_path('images/gedung/galeri'), $filename);

                GambarGedung::create([
                    'gedung_id'  => $id,
                    'nama_file'  => $foto->getClientOriginalName(),
                    'path_foto'  => 'images/gedung/galeri/' . $filename,
                    'keterangan' => '',
                    'is_utama'   => 0,
                    'urutan'     => $lastUrutan + $index + 1,
                ]);
            }
        }

        Flash::success('Gedung berhasil diperbarui.');

        return redirect(route('gedungs.index'));
    }

    /**
     * Remove the specified Gedung from storage.
     */
    public function destroy($id)
    {
        $gedung = $this->gedungRepository->find($id);

        if (empty($gedung)) {
            Flash::error('Gedung tidak ditemukan.');
            return redirect(route('gedungs.index'));
        }

        // Hapus semua foto galeri dari storage
        $fotos = GambarGedung::where('gedung_id', $id)->get();
        foreach ($fotos as $foto) {
            if ($foto->path_foto && file_exists(public_path($foto->path_foto))) {
                unlink(public_path($foto->path_foto));
            }
        }

        // Hapus foto utama dari storage
        if ($gedung->foto_utama && file_exists(public_path($gedung->foto_utama))) {
            unlink(public_path($gedung->foto_utama));
        }

        $this->gedungRepository->delete($id);

        Flash::success('Gedung berhasil dihapus.');

        return redirect(route('gedungs.index'));
    }

    /**
     * Hapus satu foto galeri
     */
    public function destroyFoto($id)
    {
        $foto = GambarGedung::findOrFail($id);
        if ($foto->path_foto && file_exists(public_path($foto->path_foto))) {
            unlink(public_path($foto->path_foto));
        }
        $foto->delete();

        Flash::success('Foto berhasil dihapus.');

        return back();
    }
}