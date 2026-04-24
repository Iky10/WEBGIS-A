<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'WebGIS Gedung')</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

    @stack('styles')

<link rel="stylesheet" href="{{ asset('css/layout-public.css') }}">
</head>
<body>

{{-- Navbar Publik --}}
<nav class="navbar navbar-expand-lg navbar-public">
    <div class="container">
        <a class="navbar-brand" href="{{ route('publik.home') }}">
            <i class="fas fa-map-marked-alt mr-2"></i>WebGIS Gedung
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navPublik">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navPublik">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('/') ? 'active' : '' }}"
                       href="{{ route('publik.home') }}">
                        <i class="fas fa-home mr-1"></i> Beranda
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('peta*') ? 'active' : '' }}"
                       href="{{ route('publik.peta') }}">
                        <i class="fas fa-map mr-1"></i> Peta
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('gedung*') ? 'active' : '' }}"
                       href="{{ route('publik.gedung') }}">
                        <i class="fas fa-building mr-1"></i> Daftar Gedung
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('pengajuan/cek-status*') ? 'active' : '' }}"
                       href="{{ route('pengajuan.cek_status') }}">
                        <i class="fas fa-search mr-1"></i> Cek Status
                    </a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link btn-admin" href="{{ route('login') }}">
                        <i class="fas fa-lock mr-1"></i> Admin
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

{{-- Konten Halaman --}}
<div class="page-content">
    @yield('content')
</div>

{{-- Footer --}}
<footer class="footer-public">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <strong class="text-white">
                    <i class="fas fa-map-marked-alt mr-1"></i> WebGIS Gedung
                </strong>
                <p class="mb-0 mt-1">Sistem Informasi Geografis Data Gedung</p>
            </div>
            <div class="col-md-6 text-md-right mt-3 mt-md-0">
                <p class="mb-0">© {{ date('Y') }} WebGIS Gedung. All rights reserved.</p>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

@stack('scripts')
</body>
</html>