<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gedung;
use App\Models\GambarGedung;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
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

        // 5 gedung terbaru
        $gedungTerbaru = Gedung::latest()->take(5)->get();

        return view('home', compact(
            'totalGedung',
            'totalFoto',
            'gedungKosong',
            'gedungDipakai',
            'gedungTerbaru'
        ));
    }
}