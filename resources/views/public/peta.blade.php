<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>WebGIS Gedung — Peta Interaktif</title>
<meta name="csrf-token" content="{{ csrf_token() }}">

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800&display=swap" rel="stylesheet">

<link rel="stylesheet" href="{{ asset('css/public-peta.css') }}">
</head>
<body>

<div id="loading">
    <div class="loader-icon"><i class="fas fa-map-marked-alt"></i></div>
    <div class="loader-bar"><div class="loader-bar-fill"></div></div>
    <div class="loader-label">Memuat peta interaktif…</div>
</div>

<div id="map"></div>

<div id="topbar">
    <a class="t-logo" href="{{ url('/') }}">
        <div class="t-logo-icon"><i class="fas fa-map-marked-alt"></i></div>
        <div>
            <div class="t-logo-name">WebGIS Gedung</div>
            <div class="t-logo-sub">Sistem Informasi Geografis</div>
        </div>
    </a>

    <div class="t-search">
        <i class="fas fa-search t-search-ico"></i>
        <input id="searchIn" type="text" class="t-search-in"
               placeholder="Cari gedung atau alamat…" autocomplete="off">
        <button id="searchMic" class="t-search-mic"><i class="fas fa-microphone"></i></button>
        <button id="searchX" class="t-search-x"><i class="fas fa-times"></i></button>
        <div id="searchDrop" class="t-search-drop"></div>
    </div>

    <div class="t-btns">
        <button class="t-btn" id="btnFilter">
            <i class="fas fa-sliders-h"></i>
            <span class="t-btn-tip">Filter</span>
        </button>
        <a class="t-btn" href="{{ route('publik.gedung') }}">
            <i class="fas fa-list"></i>
            <span class="t-btn-tip">Daftar Gedung</span>
        </a>
        <button class="t-btn" id="btnResetRoute" style="display:none; color:var(--danger);">
            <i class="fas fa-trash-alt"></i>
            <span class="t-btn-tip">Reset Rute</span>
        </button>
        {{-- Pengajuan Ruangan: SELALU tampil. Guest akan diminta login dulu via JS handler. --}}
        @auth
            <a class="t-btn" href="{{ route('pengajuan_ruangans.riwayat') }}">
                <i class="fas fa-file-alt"></i>
                <span class="t-btn-tip">Pengajuan Saya</span>
            </a>
            @if(Auth::user()->isAdmin())
                <a class="t-btn" href="{{ route('home') }}">
                    <i class="fas fa-cogs"></i>
                    <span class="t-btn-tip">Dashboard</span>
                </a>
            @endif
            <a class="t-btn" href="#" onclick="event.preventDefault(); document.getElementById('logout-form-peta').submit();" style="color:#ef4444;">
                <i class="fas fa-sign-out-alt"></i>
                <span class="t-btn-tip">Logout</span>
            </a>
            <form id="logout-form-peta" action="{{ route('logout') }}" method="POST" class="d-none" style="display: none;">
                @csrf
            </form>
        @else
            {{-- Guest: tombol Pengajuan tetap muncul, click → konfirmasi login dulu --}}
            <a class="t-btn t-btn-need-login" href="{{ route('login') }}" data-need-login="1">
                <i class="fas fa-file-alt"></i>
                <span class="t-btn-tip">Pengajuan</span>
            </a>
            <a class="t-btn t-btn-text" href="{{ route('login') }}">
                <i class="fas fa-sign-in-alt"></i>
                <span>Login</span>
            </a>
        @endauth

        @if(config('app.debug'))
            {{-- DEV ONLY: Quick login switcher (admin/user/logout) tanpa form. Hanya muncul saat APP_DEBUG=true --}}
            @php
                $devRole = Auth::check() ? (Auth::user()->isAdmin() ? 'admin' : 'user') : 'guest';
            @endphp
            <div class="t-btn-dev-wrap">
                <button class="t-btn t-btn-dev" id="btnDevSwitch" type="button" title="DEV: Quick Login Switcher">
                    <i class="fas fa-code"></i>
                    <span class="t-btn-tip">DEV ({{ $devRole }})</span>
                </button>
                <div class="t-btn-dev-dropdown hide" id="devSwitchDropdown">
                    <div class="dev-dropdown-header">
                        <i class="fas fa-flask"></i>
                        Development Mode
                    </div>
                    <a href="{{ route('dev.login-as', 'admin') }}" class="dev-dropdown-item">
                        <i class="fas fa-user-shield" style="color:#22c55e;"></i>
                        <div class="dev-item-text">
                            <div class="dev-item-title">Login as Admin</div>
                            <div class="dev-item-sub">admin@webgis.com</div>
                        </div>
                        @auth @if(Auth::user()->isAdmin())<i class="fas fa-check dev-item-active"></i>@endif @endauth
                    </a>
                    <a href="{{ route('dev.login-as', 'user') }}" class="dev-dropdown-item">
                        <i class="fas fa-user" style="color:#3b82f6;"></i>
                        <div class="dev-item-text">
                            <div class="dev-item-title">Login as User</div>
                            <div class="dev-item-sub">user@webgis.com</div>
                        </div>
                        @auth @if(!Auth::user()->isAdmin())<i class="fas fa-check dev-item-active"></i>@endif @endauth
                    </a>
                    @auth
                        <div class="dev-dropdown-sep"></div>
                        <a href="{{ route('dev.logout') }}" class="dev-dropdown-item dev-dropdown-danger">
                            <i class="fas fa-sign-out-alt"></i>
                            <div class="dev-item-text">
                                <div class="dev-item-title">Logout</div>
                                <div class="dev-item-sub">Sekarang: {{ Auth::user()->name }}</div>
                            </div>
                        </a>
                    @endauth
                </div>
            </div>
        @endif
    </div>
</div>

<div id="layerBtn" class="on" title="Ganti Layer">
    <i class="fas fa-satellite"></i>
</div>

<div id="filterPanel" class="hide">
    <div class="fp-head">
        <div class="fp-head-left">
            <div class="fp-head-icon"><i class="fas fa-layer-group"></i></div>
            <div class="fp-head-title">Filter Peta</div>
        </div>
        <button class="fp-close" id="fpClose"><i class="fas fa-times"></i></button>
    </div>
    <div class="fp-body">

        <!-- Section: Kategori Ruangan -->
        <div class="fp-section">
            <div class="fp-section-header" data-toggle="secKategori">
                <span class="fp-label">Kategori Ruangan</span>
                <i class="fas fa-chevron-up fp-chevron"></i>
            </div>
            <div class="fp-section-body open" id="secKategori">
                <label class="fp-check-item">
                    <input type="checkbox" class="fp-checkbox" name="kategori" value="" checked>
                    <span class="fp-check-mark"></span>
                    <span class="fp-check-label">Semua</span>
                    <span class="fp-check-count" id="countKategoriAll"></span>
                </label>
                @foreach(['Ruang Kelas','Post Penjagaan','Ruang Kuliah Umum','Perpustakaan','Kepala Ruangan / Pengurus','Ruangan Sekretariatan / Administrasi'] as $k)
                <label class="fp-check-item">
                    <input type="checkbox" class="fp-checkbox" name="kategori" value="{{ $k }}" checked>
                    <span class="fp-check-mark"></span>
                    <span class="fp-check-label">{{ $k }}</span>
                    <span class="fp-check-count" id="countKategori{{ Str::slug($k,'') }}"></span>
                </label>
                @endforeach
            </div>
        </div>

        <!-- Section: Status Pemakaian (3 status: Sedang Dipakai / Terbuka / Tutup) -->
        <div class="fp-section">
            <div class="fp-section-header" data-toggle="secKondisi">
                <span class="fp-label">Status Pemakaian</span>
                <i class="fas fa-chevron-up fp-chevron"></i>
            </div>
            <div class="fp-section-body open" id="secKondisi">
                <label class="fp-check-item">
                    <input type="checkbox" class="fp-checkbox" name="kondisi" value="" checked>
                    <span class="fp-check-mark"></span>
                    <span class="fp-check-label">Semua</span>
                    <span class="fp-check-count" id="countKondisiAll"></span>
                </label>
                <label class="fp-check-item">
                    <input type="checkbox" class="fp-checkbox" name="kondisi" value="Sedang Dipakai" checked>
                    <span class="fp-check-mark"></span>
                    <span class="fp-check-label">Sedang Dipakai</span>
                    <span class="fp-check-count" id="countKondisiSedangDipakai"></span>
                </label>
                <label class="fp-check-item">
                    <input type="checkbox" class="fp-checkbox" name="kondisi" value="Kosong" checked>
                    <span class="fp-check-mark"></span>
                    <span class="fp-check-label">Terbuka</span>
                    <span class="fp-check-count" id="countKondisiKosong"></span>
                </label>
                <label class="fp-check-item">
                    <input type="checkbox" class="fp-checkbox" name="kondisi" value="Tutup" checked>
                    <span class="fp-check-mark"></span>
                    <span class="fp-check-label">Tutup</span>
                    <span class="fp-check-count" id="countKondisiTutup"></span>
                </label>
            </div>
        </div>

        <!-- Section: Vegetasi -->
        <div class="fp-section">
            <div class="fp-section-header" data-toggle="secVegetasi">
                <span class="fp-label">Vegetasi</span>
                <i class="fas fa-chevron-up fp-chevron"></i>
            </div>
            <div class="fp-section-body open" id="secVegetasi">
                <label class="fp-check-item">
                    <input type="checkbox" class="fp-checkbox" name="vegetasi" value="show" checked>
                    <span class="fp-check-mark"></span>
                    <span class="fp-check-label">Tampilkan Vegetasi</span>
                    <span class="fp-check-count" id="countVegetasi"></span>
                </label>
            </div>
        </div>

        <div class="fp-sep"></div>

        <div class="fp-summary">
            <div class="fp-count-num" id="fpCount">—</div>
            <div class="fp-count-lbl">gedung terlihat</div>
        </div>

    </div>
    <div class="fp-actions">
        <button class="fp-btn fp-btn-cancel" id="fpCancel">
            <i class="fas fa-times"></i> Batal
        </button>
        <button class="fp-btn fp-btn-ok" id="fpOk">
            <i class="fas fa-check"></i> OK
        </button>
    </div>
</div>

<div id="zoomBox">
    <div class="z-btn" id="zIn">+</div>
    <div class="z-sep"></div>
    <div class="z-btn" id="zOut">−</div>
</div>

<div id="legend">
    <button class="leg-toggle" id="legToggle" title="Toggle Legend"><i class="fas fa-chevron-down"></i></button>
    <div class="leg-content" id="legContent">
        <div class="leg-group">
            <div class="leg-title">Status Pemakaian Gedung</div>
            <div class="leg-items">
                <div class="leg-row"><div class="leg-dot" style="background:#3b82f6;box-shadow:0 0 5px #3b82f6;"></div>Sedang Dipakai</div>
                <div class="leg-row"><div class="leg-dot" style="background:#22c55e;box-shadow:0 0 5px #22c55e;"></div>Terbuka</div>
                <div class="leg-row"><div class="leg-dot" style="background:#6b7280;"></div>Tutup</div>
            </div>
        </div>
        <div class="leg-divider"></div>
        <div class="leg-group">
            <div class="leg-title">Kategori Ruangan</div>
            <div class="leg-items">
                <div class="leg-row"><div class="leg-dot" style="background:#3b82f6;box-shadow:0 0 5px #3b82f6;"></div>Ruang Kelas</div>
                <div class="leg-row"><div class="leg-dot" style="background:#ef4444;box-shadow:0 0 5px #ef4444;"></div>Post Penjagaan</div>
                <div class="leg-row"><div class="leg-dot" style="background:#8b5cf6;box-shadow:0 0 5px #8b5cf6;"></div>Ruang Kuliah Umum</div>
                <div class="leg-row"><div class="leg-dot" style="background:#f59e0b;box-shadow:0 0 5px #f59e0b;"></div>Perpustakaan</div>
                <div class="leg-row"><div class="leg-dot" style="background:#10b981;box-shadow:0 0 5px #10b981;"></div>Kepala Ruangan</div>
                <div class="leg-row"><div class="leg-dot" style="background:#6366f1;box-shadow:0 0 5px #6366f1;"></div>Sekretariatan</div>
            </div>
        </div>
        <div class="leg-divider"></div>
        <div class="leg-group">
            <div class="leg-title">Vegetasi</div>
            <div class="leg-items">
                <div class="leg-row"><div class="leg-dot" style="background:#064e3b;box-shadow:0 0 5px #064e3b;"></div>Vegetasi</div>
            </div>
        </div>
    </div>
</div>

<div id="coords">Arahkan mouse ke peta</div>

<!-- PANEL NAVIGASI (G-MAPS STYLE) -->
<div id="navPanel" class="hide">
    <div class="nav-header">
        <div class="nav-modes">
            <button class="nav-mode-btn active" data-mode="car" onclick="changeRouteMode('car')">
                <i class="fas fa-car"></i> Mobil
            </button>
            <button class="nav-mode-btn" data-mode="bike" onclick="changeRouteMode('bike')">
                <i class="fas fa-motorcycle"></i> Motor
            </button>
            <button class="nav-mode-btn" data-mode="foot" onclick="changeRouteMode('foot')">
                <i class="fas fa-walking"></i> Jalan
            </button>
        </div>

        <div class="nav-summary">
            <div id="navRouteList" class="nav-route-list">
                <!-- Daftar rute (Tercepat, Alternatif) akan muncul di sini -->
            </div>
            <div class="nav-main-info" id="navMainInfo">
                <div id="navTime" class="nav-time">-- mnt</div>
                <div class="nav-dist-traffic">
                    <span id="navDist">-- km</span> • 
                    <span id="navTraffic" class="nav-traffic-fast">Lalu lintas lancar</span>
                </div>
                <div id="navStreet" class="nav-street">Memuat rute...</div>
            </div>
            <button onclick="toggleNavPanel()" style="background:none; border:none; color:#5f6368; cursor:pointer; font-size:1.2rem; margin-left:10px;">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <div class="nav-actions">
        <button id="btnNavDetail" class="nav-btn nav-btn-secondary">
            <i class="fas fa-list-ul"></i> Detail
        </button>
        <button id="btnNavPreview" class="nav-btn nav-btn-primary">
            <i class="fas fa-eye"></i> Pratinjau
        </button>
    </div>

    <div id="navSteps" class="nav-steps-container">
        <!-- Instruksi langkah demi langkah akan dimasukkan di sini oleh JS -->
    </div>
</div>

<div id="toast"></div>

<div id="markerPreview" class="marker-preview"></div>

<!-- SIDEBAR HTML -->
<div id="sidebar" class="hide">
    {{-- Drag area untuk bottom sheet di mobile (klik untuk toggle expand) --}}
    <div class="sb-drag-area" id="sbDragArea" title="Drag untuk expand/collapse"></div>
    <div class="sb-head">
        <button id="sbClose" class="sb-close"><i class="fas fa-times"></i></button>
        <div class="sb-title">Detail Gedung</div>
    </div>
    <div class="sb-body">
        <div id="sbLoading" style="display:none; text-align:center; padding:30px;">
            <i class="fas fa-spinner fa-spin" style="font-size:30px; color:var(--accent);"></i>
            <p style="margin-top:10px; color:var(--muted); font-size:0.85rem;">Memuat data...</p>
        </div>
        <div id="sbContent" style="display:none;">
            <div class="sb-img-wrap">
                <img id="sbImg" src="" alt="Foto Utama">
            </div>
            
            <div class="sb-info">
                <div id="sbName" class="sb-name">Nama Gedung</div>
                <div class="sb-addr"><i class="fas fa-map-marker-alt"></i> <span id="sbAddr">Alamat Gedung</span></div>
                
                <div class="sb-stats">
                    <div class="sb-stat"><span id="sbFungsi" class="sb-stat-v">-</span><span class="sb-stat-k">Fungsi</span></div>
                    <div class="sb-stat"><span id="sbKondisi" class="sb-stat-v">-</span><span class="sb-stat-k">Status</span></div>
                    <div class="sb-stat"><span id="sbLantai" class="sb-stat-v">-</span><span class="sb-stat-k">Lantai</span></div>
                    <div class="sb-stat"><span id="sbTahun" class="sb-stat-v">-</span><span class="sb-stat-k">Tahun</span></div>
                </div>

                <div id="sbJamOps" class="sb-jam-ops" style="display:none;">
                    <i class="fas fa-clock"></i> <span id="sbJamOpsText">-</span>
                </div>

                <div class="sb-section">
                    <div class="sb-sec-title">Deskripsi</div>
                    <div id="sbDesc" class="sb-sec-text">-</div>
                </div>

                <div class="sb-section">
                    <div class="sb-sec-title">Fasilitas & Kelas <span>(Opsional)</span></div>
                    <div id="sbFasilitas" class="sb-sec-text">Informasi fasilitas & kelas pada gedung ini belum tersedia saat ini.</div>
                </div>

                <!-- SECTION JADWAL SEMESTER -->
                <div id="sbJadwalSemester" class="sb-section" style="display:none;">
                    <div class="sb-sec-title" style="border-bottom:1px solid var(--border); padding-bottom:8px; margin-bottom:12px;">
                        <i class="fas fa-calendar-alt" style="color:var(--accent);"></i> Jadwal Semester
                    </div>

                    <!-- Badge Semester Aktif (info dari AppSetting global) -->
                    <div id="sbJadwalAktifBadge" style="background: rgba(34, 197, 94, 0.1); border: 1px solid var(--success); color: var(--success); padding: 8px 12px; border-radius: 8px; font-size: 0.8rem; font-weight: 700; display: none; align-items: center; justify-content: center; gap: 8px; margin-bottom: 15px;">
                        <div style="width: 8px; height: 8px; background: var(--success); border-radius: 50%; box-shadow: 0 0 8px var(--success);"></div>
                        <span id="sbJadwalAktifText">Semester Aktif</span>
                    </div>

                    {{-- Toggle Semester Ganjil/Genap dihilangkan: badge di atas sudah informasikan
                         semester aktif, dan filter jadwal otomatis ikut setting global. --}}

                    <!-- Tabs per Semester -->
                    <div id="sbJadwalTabs" class="rp-semester-tabs" style="margin-top:10px;"></div>

                    <!-- Dropdown Tahun Ajaran -->
                    <div id="sbJadwalDropdownWrap" style="margin-top:10px; display:none;">
                        <label style="font-size:0.75rem; color:var(--muted); font-weight:600; margin-bottom:4px; display:block;">Tahun Ajaran:</label>
                        <select id="sbJadwalDropdown" class="sb-jadwal-dropdown" onchange="onJadwalDropdownChange()"></select>
                    </div>

                    <!-- Viewer (single preview) -->
                    <div id="sbJadwalViewer" style="margin-top:12px;">
                    </div>
                </div>

                <div class="sb-action-bar">
                    <button id="sbBtnRoute" class="sb-action-btn sb-action-rute">
                        <i class="fas fa-diamond-turn-right"></i> Rute
                    </button>
                    <button id="sbBtnPhotos" class="sb-action-btn sb-action-foto">
                        <i class="fas fa-images"></i> Foto
                    </button>
                    <a id="sbBtnPengajuan" href="{{ route('pengajuan_ruangans.create') }}"
                       class="sb-action-btn sb-action-ajukan"
                       data-tooltip="Ajukan Penggunaan Ruangan">
                        <i class="fas fa-file-pen"></i> Ajukan
                    </a>
                </div>

                <div id="sbGallery" class="sb-gallery" style="display:none;">
                    <div class="sb-sec-title">Galeri Foto</div>
                    <div class="sb-gallery-carousel">
                        <div id="sbGallerySlides" class="sb-gallery-slides">
                            <!-- Foto slides inserted here by JS -->
                        </div>
                        <div class="sb-gallery-nav" id="sbGalleryNav" style="display:none;">
                            <button class="rp-carousel-btn" onclick="sbGalleryNav(-1)"><i class="fas fa-chevron-left"></i></button>
                            <span class="rp-carousel-counter" id="sbGalleryCounter">1 / 1</span>
                            <button class="rp-carousel-btn" onclick="sbGalleryNav(1)"><i class="fas fa-chevron-right"></i></button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<main>
        @yield('content')
    </main>

    <div id="chat-widget-button" onclick="toggleChat()" style="position: fixed !important; bottom: 20px !important; right: 20px !important; z-index: 999999 !important; background: #2563eb; color: white; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 21 1.9-5.7a8.5 8.5 0 1 1 3.8 3.8z"/></svg>
    </div>

    <div id="chat-window" style="position: fixed !important; bottom: 90px !important; right: 20px !important; z-index: 999999 !important; width: 320px; height: 450px; background: white; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); display: none; flex-direction: column; border: 1px solid #ddd; overflow: hidden;">
        <div style="background: #2563eb; padding: 15px; color: white; font-weight: bold; display: flex; justify-content: space-between;">
            <span>Chatbot Politani</span>
            <span onclick="toggleChat()" style="cursor:pointer;">&times;</span>
        </div>
        <div id="chat-messages" style="flex: 1; padding: 15px; overflow-y: auto; display: flex; flex-direction: column; gap: 10px; background: #f9f9f9;">
             <div style="background: #0d0d0d; padding: 10px; border-radius: 10px; align-self: flex-start; max-width: 80%; font-size: 13px;">Halo! Ada yang bisa saya bantu terkait lokasi gedung?</div>
        </div>
        <div style="padding: 10px; border-top: 1px solid #0b0a0a; display: flex; gap: 5px;">
            <input type="text" id="userInput" style="flex: 1; padding: 8px; border: 1px solid #ddd; border-radius: 5px; outline: none;">
            <button onclick="handleSendMessage()" style="background: #2563eb; color: white; border: none; padding: 8px 12px; border-radius: 5px; cursor: pointer;">Kirim</button>
        </div>
    </div>

    <script>
    // Inisialisasi peta dan variabel global

    // Fungsi untuk buka tutup jendela chat
    function toggleChat() {
        const chatWin = document.getElementById('chat-window');
        if (chatWin.style.display === 'none' || chatWin.style.display === '') {
            chatWin.style.display = 'flex';
        } else {
            chatWin.style.display = 'none';
        }
    }

    // Fungsi utama untuk mengirim pesan ke Laravel (API Chatbot)
    async function handleSendMessage() {
        const input = document.getElementById('userInput');
        const messageContainer = document.getElementById('chat-messages');
        const message = input.value.trim();

        if (!message) return;

        // 1. Tampilkan pesan user di chat box (Gunakan insertAdjacentHTML)
        messageContainer.insertAdjacentHTML('beforeend', `
    <div style="background: #2563eb; color: #ffffff; padding: 10px 15px; border-radius: 15px 15px 0 15px; align-self: flex-end; max-width: 80%; font-size: 13px; margin-bottom: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
        ${message}
    </div>
`);
        
        input.value = ''; // Kosongkan input
        messageContainer.scrollTop = messageContainer.scrollHeight;

        // 2. Tampilkan status loading
        const loadingId = 'loading-' + Date.now();
       messageContainer.insertAdjacentHTML('beforeend', `
    <div id="${loadingId}" style="background: #e5e7eb; color: #374151; padding: 10px 15px; border-radius: 15px 15px 15px 0; align-self: flex-start; max-width: 80%; font-size: 13px; font-style: italic; margin-bottom: 8px;">
        Sedang mencari...
    </div>
`);
        messageContainer.scrollTop = messageContainer.scrollHeight;

        try {
            const response = await fetch("{{ url('/api/chat') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ message: message })
            });

            // Cek apakah server error (404, 500, 419)
            if (!response.ok) {
                const errText = await response.text();
                console.error("Error dari Server Laravel:", errText);
                throw new Error("Server mengembalikan status " + response.status);
            }

            const data = await response.json();

            // Hapus elemen loading
            const loadingElem = document.getElementById(loadingId);
            if (loadingElem) loadingElem.remove();

            // Jika API mengirim error
            if (data.error) {
                messageContainer.insertAdjacentHTML('beforeend', `
                    <div style="background-color: #fee2e2 !important; color: #991b1b !important; padding: 10px 15px; border-radius: 15px 15px 15px 0; align-self: flex-start; max-width: 80%; font-size: 13px; margin-bottom: 8px;">
                        Error API: ${data.error.message || data.error}
                    </div>
                `);
                return;
            }

            const aiContent = data.choices[0].message.content;

            try {
                // Langsung coba parse sebagai JSON. Jika berhasil, berarti AI merespons permintaan lokasi.
                const parsed = JSON.parse(aiContent);
                const btnId = "btn-map-" + Date.now();

                messageContainer.insertAdjacentHTML('beforeend', `
                    <div style="background: #1f2937; color: #ffffff; padding: 12px 15px; border-radius: 15px 15px 15px 0; align-self: flex-start; max-width: 80%; font-size: 13px; margin-bottom: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                        ${parsed.message}
                        ${(parsed.lat && parsed.lng) ? `
                            <hr style="border: 0.5px solid #4b5563; margin: 10px 0;">
                            <button id="${btnId}" style="background: #3b82f6; color: #ffffff; border: none; padding: 8px 12px; border-radius: 8px; cursor: pointer; width: 100%; font-weight: bold; font-family: inherit;">
                                📍 Lihat di Peta
                            </button>
                        ` : ''}
                    </div>
                `);

                if (parsed.lat && parsed.lng) {
                    setTimeout(() => {
                        const mapBtn = document.getElementById(btnId);
                        if (mapBtn) {
                            mapBtn.onclick = function() {
                                const latitude = parseFloat(parsed.lat);
                                const longitude = parseFloat(parsed.lng);
                                const myMap = window.peta || peta; 

                                if (myMap) {
                                    myMap.flyTo([latitude, longitude], 18, { animate: true, duration: 1.5 });
                                    L.popup()
                                        .setLatLng([latitude, longitude])
                                        .setContent(`<div style="color:black; font-weight:bold;">${parsed.message}</div>`)
                                        .openOn(myMap);
                                } else {
                                    alert("Peta tidak merespon. Pastikan variabel peta sudah dimuat.");
                                }
                            };
                        }
                    }, 50);
                }

            } catch (e) {
                // Jika JSON.parse gagal, berarti respons AI adalah teks biasa.
                messageContainer.insertAdjacentHTML('beforeend', `
                    <div style="background: #ffffff; color: #1f2937; padding: 10px 15px; border-radius: 15px 15px 15px 0; align-self: flex-start; max-width: 80%; font-size: 13px; border: 1px solid #e5e7eb; margin-bottom: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                        ${aiContent}
                    </div>
                `);
            }
            
        } catch (error) {
            console.error(error);
            const loadingElem = document.getElementById(loadingId);
            if (loadingElem) {
                loadingElem.innerHTML = "Gagal menghubungi server. Silakan coba lagi.";
                loadingElem.style.backgroundColor = "#fee2e2";
                loadingElem.style.color = "#991b1b";
            }
        }   

        messageContainer.scrollTop = messageContainer.scrollHeight;
    }

    // Tambahan: Tekan 'Enter' untuk mengirim
    document.getElementById('userInput').addEventListener("keypress", function(event) {
        if (event.key === "Enter") {
            handleSendMessage();
        }
    });
</script>
</body>

<!-- LIGHTBOX -->
<div id="lightbox" class="rk-lightbox" onclick="if(event.target === this) closeLightbox()">
    <div class="rk-lb-content">
        <button class="rk-lb-close" onclick="closeLightbox()"><i class="fas fa-times"></i></button>
        <img id="lightboxImg" class="rk-lb-img" src="" alt="Lightbox">
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>
<script>
    window.WEBGIS_URL = '{{ route("webgis.geojson") }}';
    window.WEBGIS_RUANGAN_URL = '{{ route("webgis.geojson.ruangan") }}';
    window.API_JADWAL_SEMESTER_URL = '{{ url('/api/gedung') }}';
</script>
<script src="{{ asset('js/public-peta.js') }}?v={{ time() }}"></script>

{{-- Bottom Sheet 3-State Gesture (mobile only)
     Pattern Google Maps: peek (14vh) ↔ half (52vh) ↔ full (92vh)
     - Tap drag handle: cycle state ke atas (peek → half → full → peek)
     - Drag up: ke state lebih besar
     - Drag down: ke state lebih kecil
     - Drag down dari peek: close sidebar
--}}
<script>
(function() {
    'use strict';

    var sidebar = document.getElementById('sidebar');
    var dragArea = document.getElementById('sbDragArea');
    if (!sidebar || !dragArea) return;

    // States dalam urutan dari kecil ke besar
    var STATES = ['peek', 'half', 'full'];
    var DEFAULT_STATE = 'half';

    function isMobile() {
        return window.matchMedia('(max-width: 768px)').matches;
    }

    function getCurrentState() {
        for (var i = 0; i < STATES.length; i++) {
            if (sidebar.classList.contains(STATES[i])) return STATES[i];
        }
        return DEFAULT_STATE; // fallback
    }

    function setState(newState) {
        STATES.forEach(function(s) { sidebar.classList.remove(s); });
        sidebar.classList.remove('expanded'); // legacy class cleanup
        sidebar.classList.add(newState);
    }

    // Tap drag handle: cycle state (peek → half → full → peek)
    dragArea.addEventListener('click', function(e) {
        if (!isMobile()) return;
        var current = getCurrentState();
        var idx = STATES.indexOf(current);
        var next = STATES[(idx + 1) % STATES.length];
        setState(next);
    });

    // Touch drag gesture
    var touchStartY = 0;
    var touchCurrentY = 0;
    var isDragging = false;
    var initialHeight = 0;

    dragArea.addEventListener('touchstart', function(e) {
        if (!isMobile()) return;
        touchStartY = e.touches[0].clientY;
        touchCurrentY = touchStartY;
        isDragging = true;
        initialHeight = sidebar.offsetHeight;
        sidebar.style.transition = 'none';
    }, { passive: true });

    dragArea.addEventListener('touchmove', function(e) {
        if (!isDragging || !isMobile()) return;
        touchCurrentY = e.touches[0].clientY;
        var deltaY = touchCurrentY - touchStartY;
        // Live drag: ubah height inline (positive deltaY = drag down = shrink)
        var newHeight = initialHeight - deltaY;
        // Clamp ke 0–100vh
        var minH = 0;
        var maxH = window.innerHeight * 0.92;
        newHeight = Math.max(minH, Math.min(maxH, newHeight));
        sidebar.style.height = newHeight + 'px';
    }, { passive: true });

    dragArea.addEventListener('touchend', function(e) {
        if (!isDragging || !isMobile()) return;
        isDragging = false;
        sidebar.style.transition = ''; // restore transisi
        sidebar.style.height = ''; // clear inline, biar class CSS yg control

        var deltaY = touchCurrentY - touchStartY;
        var current = getCurrentState();
        var idx = STATES.indexOf(current);

        // Threshold: 60px untuk trigger state change
        if (deltaY < -60) {
            // Drag UP: state berikutnya yang lebih besar
            if (idx < STATES.length - 1) {
                setState(STATES[idx + 1]);
            }
        } else if (deltaY > 60) {
            // Drag DOWN: state sebelumnya yang lebih kecil, atau close kalau sudah peek
            if (idx > 0) {
                setState(STATES[idx - 1]);
            } else {
                // Sudah di peek state, drag down = close
                sidebar.classList.remove('show');
                STATES.forEach(function(s) { sidebar.classList.remove(s); });
            }
        }
        // Kalau delta < threshold, tetap di state sebelumnya (snap back via class)
    });

    // Klik X close: reset state ke default untuk next open
    var sbClose = document.getElementById('sbClose');
    if (sbClose) {
        sbClose.addEventListener('click', function() {
            STATES.forEach(function(s) { sidebar.classList.remove(s); });
        });
    }

    // Saat sidebar baru open (via openSidebar), set ke default state (half)
    // Observer untuk detect class 'show' added
    var observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(m) {
            if (m.attributeName === 'class' && isMobile()) {
                if (sidebar.classList.contains('show')) {
                    // Pastikan ada 1 state class
                    var hasState = STATES.some(function(s) { return sidebar.classList.contains(s); });
                    if (!hasState) {
                        sidebar.classList.add(DEFAULT_STATE);
                    }
                }
            }
        });
    });
    observer.observe(sidebar, { attributes: true });

    // ── Hide empty stat cards (FUNGSI/STATUS/LANTAI/TAHUN dengan value '-') ──
    // Observer untuk detect content changes, lalu cek tiap .sb-stat
    function hideEmptyStats() {
        var stats = document.querySelectorAll('.sb-stat');
        stats.forEach(function(stat) {
            var valueEl = stat.querySelector('.sb-stat-v');
            if (!valueEl) return;
            var v = (valueEl.textContent || '').trim();
            // Hide kalau empty, '-', '0', atau placeholder
            if (v === '' || v === '-' || v === 'null') {
                stat.classList.add('sb-stat-empty');
            } else {
                stat.classList.remove('sb-stat-empty');
            }
        });
    }

    // Listen for content updates di sb-content (after AJAX populated data)
    var sbContent = document.getElementById('sbContent');
    if (sbContent) {
        var contentObserver = new MutationObserver(function() {
            // Debounce sedikit supaya semua field ke-update dulu
            setTimeout(hideEmptyStats, 50);
        });
        contentObserver.observe(sbContent, {
            childList: true, subtree: true, characterData: true
        });
    }
})();
</script>

@if(config('app.debug'))
{{-- DEV SWITCHER toggle: hanya di-load saat APP_DEBUG=true --}}
<script>
(function () {
    'use strict';
    var btn = document.getElementById('btnDevSwitch');
    var dropdown = document.getElementById('devSwitchDropdown');
    if (!btn || !dropdown) return;

    btn.addEventListener('click', function (e) {
        e.stopPropagation();
        dropdown.classList.toggle('hide');
    });

    // Tap di luar dropdown untuk close
    document.addEventListener('click', function (e) {
        if (dropdown.classList.contains('hide')) return;
        if (btn.contains(e.target) || dropdown.contains(e.target)) return;
        dropdown.classList.add('hide');
    });

    // Escape key untuk close
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !dropdown.classList.contains('hide')) {
            dropdown.classList.add('hide');
        }
    });

    // Auto-close dropdown saat klik item login/logout (sebelum navigation ke server)
    // dan tampilkan loading toast biar UX terasa responsive.
    dropdown.querySelectorAll('.dev-dropdown-item').forEach(function (item) {
        item.addEventListener('click', function () {
            dropdown.classList.add('hide');
            if (typeof Swal !== 'undefined') {
                var title = item.querySelector('.dev-item-title');
                Swal.fire({
                    title: title ? title.textContent.trim() + '...' : 'Memproses...',
                    text: 'Mengalihkan halaman',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: function () { Swal.showLoading(); },
                });
            }
        });
    });
})();
</script>
@endif

@guest
{{-- Need-login handler: tampil konfirmasi sebelum redirect ke /login. --}}
{{-- Guard di @guest supaya tidak load script ini saat user sudah authenticated. --}}
<script>
(function () {
    'use strict';
    var loginUrl = @json(route('login'));
    var msg = 'Anda harus login terlebih dahulu untuk mengakses fitur Pengajuan Ruangan.';

    document.querySelectorAll('[data-need-login="1"]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            e.preventDefault();
            // Pakai SweetAlert kalau tersedia (di public.blade.php sudah load),
            // fallback ke confirm() native untuk halaman peta yg belum load Swal.
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'info',
                    title: 'Login Diperlukan',
                    text: msg,
                    showCancelButton: true,
                    confirmButtonText: 'Login Sekarang',
                    cancelButtonText: 'Nanti Saja',
                    confirmButtonColor: '#3b82f6',
                    cancelButtonColor: '#6b7280',
                    reverseButtons: true,
                }).then(function (r) {
                    if (r.isConfirmed) window.location.href = loginUrl;
                });
            } else {
                if (confirm(msg + '\n\nLogin sekarang?')) {
                    window.location.href = loginUrl;
                }
            }
        });
    });
})();
</script>
@endguest

</body>
</html>