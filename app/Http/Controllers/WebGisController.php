<?php

namespace App\Http\Controllers;

use App\Models\Gedung;
use Illuminate\Http\Request;

class WebGisController extends Controller
{
    /**
     * Halaman utama WebGIS
     */
    public function index()
    {
        return view('webgis.index');
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
                    'fungsi'       => $gedung->fungsi ?? '-',
                    'jumlah_lantai'=> $gedung->jumlah_lantai ?? '-',
                    'tahun_berdiri'=> $gedung->tahun_berdiri ?? '-',
                    'kondisi'      => $gedung->status_dipakai,
                    'foto_utama'   => $gedung->foto_utama
                                        ? asset('storage/' . $gedung->foto_utama)
                                        : null,
                    'detail_url'   => route('gedungs.show', $gedung->id),
                ],
            ];
        });

        return response()->json([
            'type'     => 'FeatureCollection',
            'features' => $features,
        ]);
    }
}