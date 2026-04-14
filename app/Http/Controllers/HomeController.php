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
        $gedungBaik     = Gedung::where('kondisi', 'Baik')->count();
        $gedungSedang   = Gedung::where('kondisi', 'Sedang')->count();
        $gedungRusak    = Gedung::where('kondisi', 'Rusak')->count();

        // Statistik per fungsi
        $perFungsi = Gedung::selectRaw('fungsi, count(*) as total')
            ->whereNotNull('fungsi')
            ->groupBy('fungsi')
            ->orderByDesc('total')
            ->get();

        // 5 gedung terbaru
        $gedungTerbaru = Gedung::latest()->take(5)->get();

        return view('home', compact(
            'totalGedung',
            'totalFoto',
            'gedungBaik',
            'gedungSedang',
            'gedungRusak',
            'perFungsi',
            'gedungTerbaru'
        ));
    }
}