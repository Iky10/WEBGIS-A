<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gedung;
use App\Models\GambarGedung;
use App\Models\PengajuanGedung;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // User biasa tidak boleh akses dashboard admin
        if (!auth()->user()->isAdmin()) {
            return redirect('/');
        }

        // Statistik utama
        $totalGedung    = Gedung::count();
        $totalFoto      = GambarGedung::count();
        
        $gedungs = Gedung::with('fasilitas')->get();
        $gedungKosong = 0;
        $gedungDipakai = 0;
        foreach ($gedungs as $g) {
            if ($g->status_dipakai == 'Sedang Dipakai') {
                $gedungDipakai++;
            } else {
                $gedungKosong++;
            }
        }

        // Statistik pengajuan gedung
        $totalPengajuan = PengajuanGedung::count();
        $pengajuanMenunggu = PengajuanGedung::where('status', 'diproses')->count();
        $pengajuanDisetujui = PengajuanGedung::where('status', 'disetujui')
            ->whereDate('updated_at', today())->count();

        // 5 gedung terbaru
        $gedungTerbaru = Gedung::latest()->take(5)->get();

        // 5 pengajuan terbaru yang menunggu persetujuan
        $pengajuanTerbaru = PengajuanGedung::with(['user', 'gedung'])
            ->where('status', 'diproses')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.home', compact(
            'totalGedung',
            'totalFoto',
            'gedungKosong',
            'gedungDipakai',
            'gedungTerbaru',
            'totalPengajuan',
            'pengajuanMenunggu',
            'pengajuanDisetujui',
            'pengajuanTerbaru'
        ));
    }
}