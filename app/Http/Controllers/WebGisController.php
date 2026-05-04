<?php

namespace App\Http\Controllers;

use App\Models\Gedung;
use App\Models\GedungFasilitas;
use App\Models\Vegetasi;
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
                    'fungsi'       => $gedung->fungsi ?? '-',
                    'jumlah_lantai'=> $gedung->jumlah_lantai ?? '-',
                    'tahun_berdiri'=> $gedung->tahun_berdiri ?? '-',
                    'kondisi'      => $gedung->status_dipakai,
                    'foto_utama'   => $gedung->foto_utama
                                        ? asset($gedung->foto_utama)
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

    /**
     * API: Ambil semua ruangan/fasilitas dalam format GeoJSON untuk Leaflet
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
                    'coordinates' => [(float) $ruangan->longitude, (float) $ruangan->latitude], // [lng, lat]
                ],
                'properties' => [
                    'id'              => $ruangan->id,
                    'nama_fasilitas'  => $ruangan->nama_fasilitas,
                    'kategori'        => $ruangan->kategori,
                    'keterangan'      => $ruangan->keterangan,
                    'nama_gedung'     => optional($ruangan->gedung)->nama_gedung ?? '-',
                    'gedung_id'       => $ruangan->gedung_id,
                    'is_aktif'        => $ruangan->is_aktif,
                    'status_dipakai'  => $ruangan->status_dipakai,
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

    /**
     * API: Ambil semua vegetasi dalam format GeoJSON untuk Leaflet
     */
    public function geojsonVegetasi()
    {
        $vegetasis = Vegetasi::with(['gedung', 'gambarVegetasis'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        $features = $vegetasis->map(function ($vegetasi) {
            $fotos = $vegetasi->gambarVegetasis->map(function ($g) {
                return asset($g->path_foto);
            })->toArray();

            return [
                'type'     => 'Feature',
                'geometry' => [
                    'type'        => 'Point',
                    'coordinates' => [(float) $vegetasi->longitude, (float) $vegetasi->latitude], // [lng, lat]
                ],
                'properties' => [
                    'id'              => $vegetasi->id,
                    'nama_vegetasi'   => $vegetasi->nama_vegetasi,
                    'kategori'        => $vegetasi->kategori,
                    'keterangan'      => $vegetasi->keterangan,
                    'nama_gedung'     => optional($vegetasi->gedung)->nama_gedung ?? '-',
                    'gedung_id'       => $vegetasi->gedung_id,
                    'foto_utama'      => $vegetasi->foto_utama ? asset($vegetasi->foto_utama) : null,
                    'foto_tambahan'   => $fotos,
                ],
            ];
        });

        return response()->json([
            'type'     => 'FeatureCollection',
            'features' => $features,
        ]);
    }
}