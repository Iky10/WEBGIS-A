<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateGambarGedungRequest;
use App\Http\Requests\UpdateGambarGedungRequest;
use App\Repositories\GambarGedungRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Response;

class GambarGedungController extends AppBaseController
{
    /** @var GambarGedungRepository $gambarGedungRepository*/
    private $gambarGedungRepository;

    public function __construct(GambarGedungRepository $gambarGedungRepo)
    {
        $this->gambarGedungRepository = $gambarGedungRepo;
    }

    /**
     * Display a listing of the GambarGedung.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $gambarGedungs = $this->gambarGedungRepository->all();

        return view('gambar_gedungs.index')
            ->with('gambarGedungs', $gambarGedungs);
    }

    /**
     * Show the form for creating a new GambarGedung.
     *
     * @return Response
     */
    public function create()
    {
        return view('gambar_gedungs.create');
    }

    /**
     * Store a newly created GambarGedung in storage.
     *
     * @param CreateGambarGedungRequest $request
     *
     * @return Response
     */
    public function store(CreateGambarGedungRequest $request)
    {
        $input = $request->all();

        $gambarGedung = $this->gambarGedungRepository->create($input);

        Flash::success('Gambar Gedung saved successfully.');

        return redirect(route('gambarGedungs.index'));
    }

    /**
     * Display the specified GambarGedung.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $gambarGedung = $this->gambarGedungRepository->find($id);

        if (empty($gambarGedung)) {
            Flash::error('Gambar Gedung not found');

            return redirect(route('gambarGedungs.index'));
        }

        return view('gambar_gedungs.show')->with('gambarGedung', $gambarGedung);
    }

    /**
     * Show the form for editing the specified GambarGedung.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $gambarGedung = $this->gambarGedungRepository->find($id);

        if (empty($gambarGedung)) {
            Flash::error('Gambar Gedung not found');

            return redirect(route('gambarGedungs.index'));
        }

        return view('gambar_gedungs.edit')->with('gambarGedung', $gambarGedung);
    }

    /**
     * Update the specified GambarGedung in storage.
     *
     * @param int $id
     * @param UpdateGambarGedungRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateGambarGedungRequest $request)
    {
        $gambarGedung = $this->gambarGedungRepository->find($id);

        if (empty($gambarGedung)) {
            Flash::error('Gambar Gedung not found');

            return redirect(route('gambarGedungs.index'));
        }

        $gambarGedung = $this->gambarGedungRepository->update($request->all(), $id);

        Flash::success('Gambar Gedung updated successfully.');

        return redirect(route('gambarGedungs.index'));
    }

    /**
     * Remove the specified GambarGedung from storage.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        $gambarGedung = $this->gambarGedungRepository->find($id);

        if (empty($gambarGedung)) {
            Flash::error('Gambar Gedung not found');

            return redirect(route('gambarGedungs.index'));
        }

        $this->gambarGedungRepository->delete($id);

        Flash::success('Gambar Gedung deleted successfully.');

        return redirect(route('gambarGedungs.index'));
    }
}
