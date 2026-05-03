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

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

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
            <ul class="navbar-nav mr-auto ml-lg-4">
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('/') || Request::is('peta*') ? 'active' : '' }}"
                       href="{{ route('publik.home') }}">
                        <i class="fas fa-home mr-1"></i> Beranda
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('gedung*') ? 'active' : '' }}"
                       href="{{ route('publik.gedung') }}">
                        <i class="fas fa-building mr-1"></i> Daftar Gedung
                    </a>
                </li>
                @auth
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('pengajuan-ruangan*') || Request::is('pengajuan_ruangans*') ? 'active' : '' }}"
                           href="{{ route('pengajuan_ruangans.riwayat') }}">
                            <i class="fas fa-file-alt mr-1"></i> Pengajuan Saya
                        </a>
                    </li>
                @endauth
            </ul>
            <ul class="navbar-nav align-items-center">
                @auth
                    @if(Auth::user()->isAdmin())
                        <li class="nav-item mr-2 d-none d-lg-block">
                            <a class="nav-link btn-admin" href="{{ route('home') }}">
                                <i class="fas fa-cogs mr-1"></i> Dashboard
                            </a>
                        </li>
                    @endif

                    {{-- User Dropdown Menu (desktop: dropdown, mobile: expanded inline via CSS) --}}
                    <li class="nav-item dropdown user-dropdown">
                        <a class="nav-link dropdown-toggle user-dropdown-toggle" data-toggle="dropdown"
                           href="#" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-user-circle"></i>
                            <span class="user-dropdown-name">{{ Str::limit(Auth::user()->name, 18) }}</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right user-dropdown-menu">
                            {{-- User Info Header --}}
                            <div class="user-dropdown-header">
                                <div class="user-info-avatar">
                                    <i class="fas fa-user-circle"></i>
                                </div>
                                <div class="user-info-text">
                                    <div class="user-info-name">{{ Auth::user()->name }}</div>
                                    <div class="user-info-email">{{ Auth::user()->email }}</div>
                                    @if(Auth::user()->isAdmin())
                                        <span class="user-info-role">Administrator</span>
                                    @else
                                        <span class="user-info-role user-info-role-user">Pengguna</span>
                                    @endif
                                </div>
                            </div>

                            <div class="dropdown-divider"></div>

                            {{-- Quick Links --}}
                            @if(Auth::user()->isAdmin())
                                <a class="dropdown-item d-lg-none" href="{{ route('home') }}">
                                    <i class="fas fa-cogs text-primary mr-2"></i>Dashboard Admin
                                </a>
                            @endif
                            <a class="dropdown-item" href="#" title="Fitur akan datang">
                                <i class="fas fa-user-cog text-secondary mr-2"></i>Profil Saya
                                <small class="text-muted ml-1">(Segera)</small>
                            </a>

                            <div class="dropdown-divider"></div>

                            {{-- Logout (subtle styling) --}}
                            <a class="dropdown-item dropdown-item-logout" href="#"
                               onclick="event.preventDefault(); document.getElementById('logout-form-public').submit();">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                            </a>
                        </div>
                        <form id="logout-form-public" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link btn-admin px-4" href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt mr-1"></i> Login
                        </a>
                    </li>
                @endauth
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

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- SweetAlert2: Global Flash Notification --}}
@if(session('flash_notification'))
    @foreach(session('flash_notification', collect())->toArray() as $message)
        <script>
            Swal.fire({
                icon: '{{ $message["level"] === "danger" ? "error" : ($message["level"] === "warning" ? "warning" : "success") }}',
                title: '{!! addslashes($message["message"]) !!}',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3500,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });
        </script>
    @endforeach
    {{ session()->forget('flash_notification') }}
@endif

@stack('scripts')
</body>
</html>