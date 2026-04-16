@extends('layouts.public')

@section('title', 'Beranda - WebGIS Gedung')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/public-home.css') }}">
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
    window.WEBGIS_URL = '{{ route("webgis.geojson") }}';
</script>
<script src="{{ asset('js/public-home.js') }}"></script>
@endpush