<?php

namespace App\Http\Controllers;

use App\Models\Gedung;
use App\Models\GedungFasilitas;
use Illuminate\Http\Request;

class WebGisController extends Controller
{
    /**
     * Halaman utama WebGIS
     */
    public function index()
    {
        return view('dashboard.webgis.index');
    }

    /**
     * API: Ambil semua gedung dalam format GeoJSON untuk Leaflet
     */
    public function geojson()
    {
        $gedungs = Gedung::whereNotNull('x')
            ->whereNotNull('y')
            ->get();

        $features = $gedungs->map(function ($gedung) {
            return [
                'type'     => 'Feature',
                'geometry' => [
                    'type'        => 'Point',
                    'coordinates' => [(float) $gedung->y, (float) $gedung->x], // [lng, lat]
                ],
                'properties' => [
                    'id'           => $gedung->id,
                    'nama_gedung'  => $gedung->nama_gedung,
                    'alamat'       => $gedung->alamat,
                    'kondisi'      => $gedung->status_dipakai,
                    'foto_utama'   => $gedung->foto_utama
                                        ? asset($gedung->foto_utama)
                                        : null,
                    'detail_url'   => route('publik.gedung.detail', $gedung->id),
                ],
            ];
        });

        return response()->json([
            'type'     => 'FeatureCollection',
            'features' => $features,
        ]);
    }

    /**
     * API: Ambil semua ruangan/fasilitas dalam format GeoJSON untuk Leaflet.
     */
    public function geojsonRuangan()
    {
        $ruangans = GedungFasilitas::with('gedung')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        $features = $ruangans->map(function ($ruangan) {
            return [
                'type'     => 'Feature',
                'geometry' => [
                    'type'        => 'Point',
                    'coordinates' => [(float) $ruangan->longitude, (float) $ruangan->latitude],
                ],
                'properties' => [
                    'id'              => $ruangan->id,
                    'nama_fasilitas'  => $ruangan->nama_fasilitas,
                    'kategori'        => $ruangan->kategori,
                    'keterangan'      => $ruangan->keterangan,
                    'nama_gedung'     => optional($ruangan->gedung)->nama_gedung ?? '-',
                    'gedung_id'       => $ruangan->gedung_id,
                    'status_dipakai'  => $ruangan->status_dipakai,
                    'is_aktif'        => $ruangan->status_dipakai === 'Sedang Dipakai',
                    'foto_ruangan'    => $ruangan->foto_ruangan
                                            ? asset($ruangan->foto_ruangan)
                                            : null,
                ],
            ];
        });

        return response()->json([
            'type'     => 'FeatureCollection',
            'features' => $features,
        ]);
    }
}