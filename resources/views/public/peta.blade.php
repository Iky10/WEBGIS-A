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
        <a class="t-btn" href="{{ route('login') }}">
            <i class="fas fa-lock"></i>
            <span class="t-btn-tip">Admin</span>
        </a>
    </div>
</div>

<div id="layerBtn" class="on" title="Ganti Layer">
    <i class="fas fa-satellite"></i>
</div>

<div id="filterPanel" class="hide">
    <div class="fp-head">
        <div class="fp-head-icon"><i class="fas fa-layer-group"></i></div>
        <div class="fp-head-title">Filter Peta</div>
    </div>
    <div class="fp-body">

        <span class="fp-label">Fungsi Gedung</span>
        <div class="fp-chips" id="chipsFungsi">
            <div class="chip on" data-v="">Semua</div>
            @foreach(['Perkantoran','Pendidikan','Kesehatan','Komersial','Publik','Lainnya'] as $f)
            <div class="chip" data-v="{{ $f }}">{{ $f }}</div>
            @endforeach
        </div>

        <span class="fp-label">Status Pemakaian</span>
        <div class="fp-chips" id="chipsKondisi">
            <div class="chip on" data-v="">Semua</div>
            <div class="chip" data-v="Sedang Dipakai">Sedang Dipakai</div>
            <div class="chip" data-v="Kosong">Kosong</div>
        </div>

        <div class="fp-sep"></div>

        <div class="fp-footer">
            <div>
                <div class="fp-count-num" id="fpCount">—</div>
                <div class="fp-count-lbl">gedung terlihat</div>
            </div>
            <button class="fp-reset" id="fpReset">
                <i class="fas fa-undo"></i> Reset
            </button>
        </div>

    </div>
</div>

<div id="zoomBox">
    <div class="z-btn" id="zIn">+</div>
    <div class="z-sep"></div>
    <div class="z-btn" id="zOut">−</div>
</div>

<div class="zoom-indicator">
    <div class="zoom-level-num" id="zoomLevel">18</div>
    <div class="zoom-mode-badge mode-gedung" id="zoomModeBadge">🏢 Gedung</div>
</div>

<div id="legend">
    <div class="leg-title">Status Pemakaian Gedung</div>
    <div class="leg-row"><div class="leg-dot" style="background:#22c55e;box-shadow:0 0 5px #22c55e;"></div>Sedang Dipakai</div>
    <div class="leg-row"><div class="leg-dot" style="background:#6c757d;box-shadow:0 0 5px #6c757d;"></div>Kosong</div>
    <div class="leg-row"><div class="leg-dot" style="background:#475569;"></div>Tidak diketahui</div>
    <div class="leg-sep"></div>
    <div class="leg-title">Kategori Ruangan</div>
    <div class="leg-row"><div class="leg-dot" style="background:#3b82f6;box-shadow:0 0 5px #3b82f6;"></div>Ruang Kelas</div>
    <div class="leg-row"><div class="leg-dot" style="background:#ef4444;box-shadow:0 0 5px #ef4444;"></div>Post Penjagaan</div>
    <div class="leg-row"><div class="leg-dot" style="background:#8b5cf6;box-shadow:0 0 5px #8b5cf6;"></div>Ruang Kuliah Umum</div>
    <div class="leg-row"><div class="leg-dot" style="background:#f59e0b;box-shadow:0 0 5px #f59e0b;"></div>Perpustakaan</div>
    <div class="leg-row"><div class="leg-dot" style="background:#10b981;box-shadow:0 0 5px #10b981;"></div>Kepala Ruangan</div>
    <div class="leg-row"><div class="leg-dot" style="background:#6366f1;box-shadow:0 0 5px #6366f1;"></div>Sekretariatan</div>
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
            <div class="sb-img-wrap" id="sbMainCarouselWrap">
                <div id="sbMainSlides" class="sb-main-slides">
                    <!-- Foto slides inserted here by JS -->
                </div>
                <div class="sb-main-nav" id="sbMainNav" style="display:none;">
                    <button class="sb-main-btn-prev" onclick="sbMainGalleryNav(-1)"><i class="fas fa-chevron-left"></i></button>
                    <button class="sb-main-btn-next" onclick="sbMainGalleryNav(1)"><i class="fas fa-chevron-right"></i></button>
                </div>
                <div class="sb-main-counter" id="sbMainCounter" style="display:none;">1 / 1</div>
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

                <div style="margin: 16px 0;">
                    <button id="sbBtnRoute" class="sb-cta-btn" style="background:var(--success); box-shadow:0 6px 20px rgba(34,197,94,.35);"><i class="fas fa-directions"></i> Rute ke Sini</button>
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
                    
                    <div id="sbJadwalAktifBadge" style="background: rgba(34, 197, 94, 0.1); border: 1px solid var(--success); color: var(--success); padding: 8px 12px; border-radius: 8px; font-size: 0.8rem; font-weight: 700; display: flex; align-items: center; justify-content: center; gap: 8px; margin-bottom: 15px;">
                        <div style="width: 8px; height: 8px; background: var(--success); border-radius: 50%; box-shadow: 0 0 8px var(--success);"></div>
                        <span id="sbJadwalAktifText">Semester Aktif</span>
                    </div>

                    <div id="sbJadwalTabs" class="rp-semester-tabs" style="margin-top:10px;">
                    </div>

                    <div id="sbJadwalViewer" style="margin-top:12px;">
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>


<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>
<script>
    window.WEBGIS_URL = '{{ route("webgis.geojson") }}';
    window.WEBGIS_RUANGAN_URL = '{{ route("webgis.geojson.ruangan") }}';
</script>
<script src="{{ asset('js/public-peta.js') }}?v={{ time() }}"></script>

</body>
</html>