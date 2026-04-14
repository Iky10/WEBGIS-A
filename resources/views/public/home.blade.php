@extends('layouts.public')

@section('title', 'Beranda - WebGIS Gedung')

@push('styles')
<style>
    /* Hero */
    .hero-section {
        background: linear-gradient(135deg, #1a3c5e 0%, #2d6a9f 60%, #3d8bcd 100%);
        color: #fff;
        padding: 80px 0 60px;
        position: relative;
        overflow: hidden;
    }
    .hero-section::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 600px;
        height: 600px;
        background: rgba(255,255,255,0.05);
        border-radius: 50%;
    }
    .hero-title {
        font-size: 2.5rem;
        font-weight: 800;
        line-height: 1.2;
    }
    .hero-subtitle {
        font-size: 1.1rem;
        opacity: 0.85;
        margin: 16px 0 30px;
    }
    .btn-hero-primary {
        background: #fff;
        color: #1a3c5e;
        font-weight: 700;
        padding: 12px 30px;
        border-radius: 25px;
        font-size: 1rem;
        border: none;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-block;
    }
    .btn-hero-primary:hover {
        background: #e8f0fe;
        color: #1a3c5e;
        text-decoration: none;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    .btn-hero-outline {
        border: 2px solid rgba(255,255,255,0.7);
        color: #fff;
        font-weight: 600;
        padding: 11px 28px;
        border-radius: 25px;
        font-size: 1rem;
        background: transparent;
        margin-left: 12px;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-block;
    }
    .btn-hero-outline:hover {
        background: rgba(255,255,255,0.15);
        color: #fff;
        text-decoration: none;
    }
    .hero-stats {
        margin-top: 50px;
        display: flex;
        gap: 40px;
    }
    .hero-stat-item h3 {
        font-size: 2rem;
        font-weight: 800;
        margin: 0;
    }
    .hero-stat-item p {
        margin: 0;
        opacity: 0.8;
        font-size: 0.9rem;
    }

    /* Map Preview */
    #hero-map {
        height: 350px;
        border-radius: 12px;
        box-shadow: 0 20px 50px rgba(0,0,0,0.3);
        border: 3px solid rgba(255,255,255,0.2);
    }

    /* Stats Cards */
    .stat-card {
        border-radius: 12px;
        padding: 30px 20px;
        text-align: center;
        border: none;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        transition: transform 0.2s;
    }
    .stat-card:hover { transform: translateY(-4px); }
    .stat-card .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
        font-size: 1.5rem;
    }
    .stat-card h3 { font-size: 2.2rem; font-weight: 800; margin: 0; }
    .stat-card p  { color: #888; margin: 0; font-size: 0.9rem; }

    /* Gedung Cards */
    .gedung-card {
        border-radius: 12px;
        overflow: hidden;
        border: none;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        transition: transform 0.2s, box-shadow 0.2s;
        height: 100%;
    }
    .gedung-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }
    .gedung-card img {
        height: 180px;
        object-fit: cover;
        width: 100%;
    }
    .gedung-card .no-foto {
        height: 180px;
        background: linear-gradient(135deg, #e8f0fe, #c5d8f5);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #90a4ae;
        font-size: 2.5rem;
    }
    .badge-kondisi-baik   { background: #d4edda; color: #155724; }
    .badge-kondisi-sedang { background: #fff3cd; color: #856404; }
    .badge-kondisi-rusak  { background: #f8d7da; color: #721c24; }

    /* Section */
    .section-title {
        font-weight: 800;
        font-size: 1.8rem;
        color: #1a3c5e;
    }
    .section-subtitle { color: #888; margin-bottom: 30px; }
</style>
@endpush

@section('content')

{{-- Hero Section --}}
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <h1 class="hero-title">
                    Sistem Informasi<br>
                    Geografis Gedung
                </h1>
                <p class="hero-subtitle">
                    Temukan informasi lengkap tentang gedung-gedung di wilayah kami.
                    Lihat lokasi, kondisi, foto, dan detail setiap gedung secara interaktif.
                </p>
                <a href="{{ route('publik.peta') }}" class="btn-hero-primary">
                    <i class="fas fa-map mr-2"></i> Buka Peta
                </a>
                <a href="{{ route('publik.gedung') }}" class="btn-hero-outline">
                    <i class="fas fa-building mr-2"></i> Daftar Gedung
                </a>

                <div class="hero-stats">
                    <div class="hero-stat-item">
                        <h3>{{ $totalGedung }}</h3>
                        <p>Total Gedung</p>
                    </div>
                    <div class="hero-stat-item">
                        <h3>{{ $gedungBaik }}</h3>
                        <p>Kondisi Baik</p>
                    </div>
                    <div class="hero-stat-item">
                        <h3>{{ $totalFoto }}</h3>
                        <p>Foto Tersedia</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div id="hero-map"></div>
            </div>
        </div>
    </div>
</section>

{{-- Stats Section --}}
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-md-3 mb-3">
                <div class="stat-card card">
                    <div class="stat-icon bg-primary text-white">
                        <i class="fas fa-building"></i>
                    </div>
                    <h3 class="text-primary">{{ $totalGedung }}</h3>
                    <p>Total Gedung</p>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card card">
                    <div class="stat-icon bg-success text-white">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h3 class="text-success">{{ $gedungBaik }}</h3>
                    <p>Kondisi Baik</p>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card card">
                    <div class="stat-icon bg-warning text-white">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <h3 class="text-warning">{{ $gedungSedang }}</h3>
                    <p>Kondisi Sedang</p>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card card">
                    <div class="stat-icon bg-danger text-white">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <h3 class="text-danger">{{ $gedungRusak }}</h3>
                    <p>Kondisi Rusak</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Gedung Terbaru --}}
<section class="py-5">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="section-title">Gedung Terbaru</h2>
            <p class="section-subtitle">Gedung yang baru ditambahkan ke sistem</p>
        </div>
        <div class="row">
            @forelse($gedungTerbaru as $gedung)
            <div class="col-md-4 mb-4">
                <div class="gedung-card card">
                    @if($gedung->foto_utama)
                        <img src="{{ asset('storage/' . $gedung->foto_utama) }}"
                             alt="{{ $gedung->nama_gedung }}">
                    @else
                        <div class="no-foto">
                            <i class="fas fa-building"></i>
                        </div>
                    @endif
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="card-title mb-0 font-weight-bold">{{ $gedung->nama_gedung }}</h6>
                            @if($gedung->kondisi)
                                <span class="badge badge-kondisi-{{ strtolower($gedung->kondisi) }} ml-2"
                                      style="white-space:nowrap; padding: 4px 8px; border-radius: 10px; font-size:11px;">
                                    {{ $gedung->kondisi }}
                                </span>
                            @endif
                        </div>
                        @if($gedung->fungsi)
                            <span class="badge badge-info mb-2">{{ $gedung->fungsi }}</span>
                        @endif
                        <p class="text-muted small mb-3">
                            <i class="fas fa-map-marker-alt mr-1"></i>
                            {{ Str::limit($gedung->alamat, 60) }}
                        </p>
                        <a href="{{ route('publik.gedung.detail', $gedung->id) }}"
                           class="btn btn-outline-primary btn-sm btn-block">
                            <i class="fas fa-info-circle mr-1"></i> Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center text-muted py-5">
                <i class="fas fa-building fa-3x mb-3"></i>
                <p>Belum ada data gedung.</p>
            </div>
            @endforelse
        </div>
        @if($totalGedung > 3)
        <div class="text-center mt-2">
            <a href="{{ route('publik.gedung') }}" class="btn btn-primary px-4">
                Lihat Semua Gedung <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        @endif
    </div>
</section>

@endsection

@push('scripts')
<script>
    // Mini peta di hero
    var map = L.map('hero-map', { zoomControl: true, scrollWheelZoom: false })
              .setView([-2.5, 118.0], 5);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    fetch('{{ route("webgis.geojson") }}')
        .then(r => r.json())
        .then(data => {
            var bounds = [];
            (data.features || []).forEach(f => {
                var lat = f.geometry.coordinates[1];
                var lng = f.geometry.coordinates[0];
                L.circleMarker([lat, lng], {
                    radius: 8, fillColor: '#fff',
                    color: '#1a3c5e', weight: 2,
                    fillOpacity: 0.9
                }).addTo(map).bindPopup('<strong>' + f.properties.nama_gedung + '</strong>');
                bounds.push([lat, lng]);
            });
            if (bounds.length > 0) map.fitBounds(L.latLngBounds(bounds).pad(0.3));
        });
</script>
@endpush