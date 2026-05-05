<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use Illuminate\Http\Request;
use Flash;

class SemesterAktifController extends Controller
{
    public function index()
    {
        $semesterAktif = AppSetting::get('semester_aktif', 'genap');
        $tahunAjaranAktif = AppSetting::get('tahun_ajaran_aktif', '2025/2026');

        return view('dashboard.semester_aktif.index', compact('semesterAktif', 'tahunAjaranAktif'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'semester_aktif' => 'required|in:ganjil,genap',
            'tahun_ajaran_aktif' => 'required|string|max:20',
        ]);

        AppSetting::set('semester_aktif', $request->semester_aktif);
        AppSetting::set('tahun_ajaran_aktif', $request->tahun_ajaran_aktif);

        Flash::success('Pengaturan Global berhasil diperbarui.');

        return redirect()->route('semester_aktif.index');
    }

    public function apiGetSemesterAktif()
    {
        return response()->json([
            'semester_aktif' => AppSetting::get('semester_aktif', 'genap'),
            'tahun_ajaran_aktif' => AppSetting::get('tahun_ajaran_aktif', '2025/2026'),
        ]);
    }
}
