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
            $path = $file->storeAs('gedung/utama', $filename, 'public');
            $input['foto_utama'] = $path;
        }

        $gedung = $this->gedungRepository->create($input);

        // Upload multiple foto galeri jika ada
        if ($request->hasFile('foto_gedung')) {
            foreach ($request->file('foto_gedung') as $index => $foto) {
                $filename = time() . '_' . $index . '_' . $foto->getClientOriginalName();
                $path = $foto->storeAs('gedung/galeri', $filename, 'public');

                GambarGedung::create([
                    'gedung_id'  => $gedung->id,
                    'nama_file'  => $foto->getClientOriginalName(),
                    'path_foto'  => $path,
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
            if ($gedung->foto_utama) {
                Storage::disk('public')->delete($gedung->foto_utama);
            }
            $file = $request->file('foto_utama');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('gedung/utama', $filename, 'public');
            $input['foto_utama'] = $path;
        }

        $gedung = $this->gedungRepository->update($input, $id);

        // Tambah foto galeri baru jika ada
        if ($request->hasFile('foto_gedung')) {
            $lastUrutan = GambarGedung::where('gedung_id', $id)->max('urutan') ?? -1;

            foreach ($request->file('foto_gedung') as $index => $foto) {
                $filename = time() . '_' . $index . '_' . $foto->getClientOriginalName();
                $path = $foto->storeAs('gedung/galeri', $filename, 'public');

                GambarGedung::create([
                    'gedung_id'  => $id,
                    'nama_file'  => $foto->getClientOriginalName(),
                    'path_foto'  => $path,
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
            Storage::disk('public')->delete($foto->path_foto);
        }

        // Hapus foto utama dari storage
        if ($gedung->foto_utama) {
            Storage::disk('public')->delete($gedung->foto_utama);
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
        Storage::disk('public')->delete($foto->path_foto);
        $foto->delete();

        Flash::success('Foto berhasil dihapus.');

        return back();
    }
}