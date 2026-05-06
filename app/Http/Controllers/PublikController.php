<?php

namespace App\Http\Controllers;

use App\Models\Gedung;
use App\Models\GambarGedung;
use App\Models\GedungFasilitas;
use App\Models\JadwalRuangan;
use App\Models\JadwalSemester;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PublikController extends Controller
{
    /**
     * Halaman utama — sekarang langsung menampilkan peta layar penuh.
     * Tidak ada lagi halaman beranda tradisional.
     */
    public function home()
    {
        return view('public.peta');
    }

    /**
     * Tampilkan peta interaktif layar penuh (route /peta tetap berfungsi).
     */
    public function peta()
    {
        return view('public.peta');
    }

    /**
     * Daftar semua gedung (grid/list view).
     */
    public function gedung(Request $request)
    {
        $query = Gedung::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_gedung', 'like', '%' . $request->search . '%')
                  ->orWhere('alamat', 'like', '%' . $request->search . '%');
            });
        }

        $gedungs = $query->latest()->paginate(9);

        return view('public.gedung.index', compact('gedungs'));
    }

    /**
     * Detail satu gedung.
     */
    public function detailGedung($id)
    {
        $gedung = Gedung::findOrFail($id);
        $fotos  = GambarGedung::where('gedung_id', $id)->orderBy('urutan')->get();

        return view('public.gedung.show', compact('gedung', 'fotos'));
    }

    /**
     * API: ambil semua jadwal semester untuk gedung tertentu.
     * Dipanggil via AJAX dari popup peta / halaman detail gedung.
     */
    public function apiJadwalSemester($id)
    {
        $gedung = Gedung::with('jadwalSemester')->find($id);

        if (!$gedung) {
            return response()->json(['success' => false, 'message' => 'Gedung tidak ditemukan'], 404);
        }

        $jadwals = $gedung->jadwalSemester
            ->sortBy('semester')
            ->values()
            ->map(function ($j) {
                return [
                    'id'           => $j->id,
                    'semester'     => $j->semester,
                    'tahun_ajaran' => $j->tahun_ajaran,
                    'file_jadwal'  => $j->file_jadwal ? asset($j->file_jadwal) : null,
                    'keterangan'   => $j->keterangan,
                ];
            });

        return response()->json([
            'success' => true,
            'data'    => $jadwals,
        ]);
    }

    /**
     * Detail satu gedung (API).
     */
    public function apiDetail($id)
    {
        $gedung = Gedung::findOrFail($id);
        $fotos  = GambarGedung::where('gedung_id', $id)->orderBy('urutan')->get();

        // Attach foto to response
        $fotosArray = $fotos->map(function($f) {
            return [
                'id' => $f->id,
                'path' => asset($f->path_foto)
            ];
        });

        // Get fasilitas dengan status (mewarisi jam operasional gedung)
        $fasilitas = $gedung->fasilitas()->get()->map(function($f) {
            return [
                'id' => $f->id,
                'nama_fasilitas' => $f->nama_fasilitas,
                'kategori' => $f->kategori,
                'keterangan' => $f->keterangan,
                'latitude' => $f->latitude,
                'longitude' => $f->longitude,
                'foto_ruangan' => $f->foto_ruangan ? asset($f->foto_ruangan) : null,
                'status' => $f->status_dipakai, // 'Sedang Dipakai', 'Kosong', atau 'Tutup'
            ];
        });

        return response()->json([
            'gedung' => $gedung,
            'bisa_diajukan' => (bool) $gedung->bisa_diajukan,
            'foto_utama' => $gedung->foto_utama ? asset($gedung->foto_utama) : null,
            'fotos' => $fotosArray,
            'fasilitas' => $fasilitas
        ]);
    }
}