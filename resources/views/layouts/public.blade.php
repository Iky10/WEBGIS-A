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

                    {{-- User Dropdown Menu (AdminLTE-style: avatar besar + member since + footer 2 buttons) --}}
                    @php
                        // Generate inisial dari nama (max 2 char). 'User Biasa' → 'UB', 'Riki' → 'RI'
                        $userName = Auth::user()->name ?? 'User';
                        $nameParts = preg_split('/\s+/', trim($userName));
                        if (count($nameParts) >= 2) {
                            $initials = strtoupper(mb_substr($nameParts[0], 0, 1) . mb_substr($nameParts[1], 0, 1));
                        } else {
                            $initials = strtoupper(mb_substr($userName, 0, 2));
                        }
                        // Hash nama untuk pilih warna avatar (consistent per user)
                        $avatarColors = [
                            ['#3498db', '#2980b9'], // blue
                            ['#27ae60', '#1e8449'], // green
                            ['#e67e22', '#d35400'], // orange
                            ['#9b59b6', '#7d3c98'], // purple
                            ['#1abc9c', '#16a085'], // teal
                            ['#e74c3c', '#c0392b'], // red
                        ];
                        $colorIdx = abs(crc32($userName)) % count($avatarColors);
                        $avatarGradient = 'linear-gradient(135deg, ' . $avatarColors[$colorIdx][0] . ', ' . $avatarColors[$colorIdx][1] . ')';
                        $memberSince = Auth::user()->created_at ? Auth::user()->created_at->isoFormat('MMM YYYY') : '-';
                    @endphp
                    <li class="nav-item dropdown user-dropdown">
                        <a class="nav-link dropdown-toggle user-dropdown-toggle" data-toggle="dropdown"
                           href="#" role="button" aria-haspopup="true" aria-expanded="false">
                            <span class="user-avatar-mini" style="background: {{ $avatarGradient }};">{{ $initials }}</span>
                            <span class="user-dropdown-name">{{ Str::limit($userName, 18) }}</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right user-dropdown-menu">
                            {{-- Header: avatar besar di tengah + nama + member since --}}
                            <div class="user-dropdown-header">
                                <div class="user-avatar-big" style="background: {{ $avatarGradient }};">
                                    {{ $initials }}
                                </div>
                                <div class="user-info-name">{{ $userName }}</div>
                                <div class="user-info-email">{{ Auth::user()->email }}</div>
                                <div class="user-info-meta">
                                    @if(Auth::user()->isAdmin())
                                        <span class="user-info-role">Administrator</span>
                                    @else
                                        <span class="user-info-role user-info-role-user">Pengguna</span>
                                    @endif
                                    <span class="user-info-since">
                                        <i class="far fa-clock"></i> Sejak {{ $memberSince }}
                                    </span>
                                </div>
                            </div>

                            {{-- Quick Links (only di mobile / kalau diperlukan) --}}
                            @if(Auth::user()->isAdmin())
                                <a class="dropdown-item d-lg-none user-dropdown-link" href="{{ route('home') }}">
                                    <i class="fas fa-cogs text-primary mr-2"></i>Dashboard Admin
                                </a>
                                <div class="dropdown-divider d-lg-none"></div>
                            @endif

                            {{-- Footer: 2 buttons (Profil + Logout) --}}
                            <div class="user-dropdown-footer">
                                <a href="#" class="user-footer-btn user-footer-btn-profile" title="Fitur profil akan datang">
                                    <i class="fas fa-user-cog mr-1"></i>
                                    <span>Profil Saya</span>
                                    <span class="badge-coming-soon">Segera</span>
                                </a>
                                <a href="#" class="user-footer-btn user-footer-btn-logout"
                                   onclick="event.preventDefault(); document.getElementById('logout-form-public').submit();">
                                    <i class="fas fa-sign-out-alt mr-1"></i>
                                    <span>Logout</span>
                                </a>
                            </div>
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