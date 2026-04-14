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

    <style>
        body { font-family: 'Segoe UI', sans-serif; }

        /* Navbar */
        .navbar-public {
            background: linear-gradient(135deg, #1a3c5e 0%, #2d6a9f 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            padding: 10px 0;
        }
        .navbar-public .navbar-brand {
            color: #fff !important;
            font-weight: 700;
            font-size: 1.3rem;
            letter-spacing: 0.5px;
        }
        .navbar-public .nav-link {
            color: rgba(255,255,255,0.85) !important;
            font-weight: 500;
            transition: color 0.2s;
            padding: 6px 14px !important;
            border-radius: 6px;
        }
        .navbar-public .nav-link:hover,
        .navbar-public .nav-link.active {
            color: #fff !important;
            background: rgba(255,255,255,0.15);
        }
        .navbar-public .btn-admin {
            background: #fff;
            color: #1a3c5e !important;
            font-weight: 600;
            border-radius: 20px;
            padding: 5px 18px !important;
            font-size: 0.85rem;
        }
        .navbar-public .btn-admin:hover {
            background: #e8f0fe;
        }
        .navbar-toggler { border-color: rgba(255,255,255,0.5); }
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='30' height='30' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(255,255,255,0.8)' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        /* Footer */
        .footer-public {
            background: #1a3c5e;
            color: rgba(255,255,255,0.7);
            padding: 20px 0;
            font-size: 0.85rem;
            margin-top: 40px;
        }
        .footer-public a { color: rgba(255,255,255,0.7); }
        .footer-public a:hover { color: #fff; text-decoration: none; }

        /* Content */
        .page-content { min-height: calc(100vh - 130px); padding-top: 20px; }
    </style>
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