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
            <a class="t-btn t-btn-text" href="{{ route('login') }}">
                <i class="fas fa-sign-in-alt"></i>
                <span>Login</span>
            </a>
        @endauth
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


        <span class="fp-label">Status Pemakaian</span>
        <div class="fp-chips" id="chipsKondisi">
            <div class="chip on" data-v="">Semua</div>
            <div class="chip" data-v="Sedang Dipakai">Sedang Dipakai</div>
            <div class="chip" data-v="Kosong">Terbuka</div>
            <div class="chip" data-v="Tutup">Tutup</div>
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

<div id="legend">
    <div class="leg-title">Status Pemakaian Gedung</div>
    <div class="leg-row"><div class="leg-dot" style="background:#3b82f6;box-shadow:0 0 5px #3b82f6;"></div>Sedang Dipakai</div>
    <div class="leg-row"><div class="leg-dot" style="background:#22c55e;box-shadow:0 0 5px #22c55e;"></div>Terbuka</div>
    <div class="leg-row"><div class="leg-dot" style="background:#6b7280;"></div>Tutup</div>
    {{-- Kategori Ruangan disembunyikan sementara --}}
</div>

<div id="coords">Arahkan mouse ke peta</div>

<div id="routeInfoPanel" style="display:none; position:fixed; bottom:80px; right:16px; z-index:900; background:var(--surface-hi); border:1px solid var(--border-hi); border-radius:var(--radius-md); backdrop-filter:var(--blur); padding:14px; max-width:280px; box-shadow:0 12px 40px rgba(0,0,0,.5);">
    <div style="font-size:0.78rem; font-weight:800; color:var(--text); margin-bottom:8px; text-transform:uppercase; letter-spacing:.5px;">Informasi Rute</div>
    
    <div style="margin-bottom:12px;">
        <div style="font-size:0.7rem; color:var(--muted); font-weight:600; margin-bottom:3px;">Durasi Perjalanan</div>
        <div id="routeInfoDuration" style="font-size:0.9rem; font-weight:800; color:var(--accent);">-</div>
    </div>
    
    <div style="margin-bottom:12px;">
        <div style="font-size:0.7rem; color:var(--muted); font-weight:600; margin-bottom:3px;">Jarak</div>
        <div id="routeInfoDistance" style="font-size:0.9rem; font-weight:800; color:var(--success);">-</div>
    </div>
    
    <div style="margin-bottom:12px;">
        <div style="font-size:0.7rem; color:var(--muted); font-weight:600; margin-bottom:3px;">Moda Transportasi</div>
        <div id="routeInfoMode" style="font-size:0.9rem; font-weight:800; color:var(--text);">-</div>
    </div>
    
    <div style="border-top:1px solid var(--border); padding-top:10px; display:flex; gap:5px; flex-wrap:wrap;">
        <button class="route-mode-btn active" data-mode="car" onclick="changeRouteMode('car')" style="flex:1; min-width:60px; padding:6px; border-radius:6px; background:var(--accent); color:#fff; border:none; font-size:0.7rem; font-weight:700; cursor:pointer; font-family:'Plus Jakarta Sans',sans-serif; transition:all .2s;">🚗 Mobil</button>
        <button class="route-mode-btn" data-mode="bike" onclick="changeRouteMode('bike')" style="flex:1; min-width:60px; padding:6px; border-radius:6px; background:rgba(255,255,255,.05); color:var(--muted); border:1px solid var(--border); font-size:0.7rem; font-weight:700; cursor:pointer; font-family:'Plus Jakarta Sans',sans-serif; transition:all .2s;">🏍️ Motor</button>
        <button class="route-mode-btn" data-mode="foot" onclick="changeRouteMode('foot')" style="flex:1; min-width:60px; padding:6px; border-radius:6px; background:rgba(255,255,255,.05); color:var(--muted); border:1px solid var(--border); font-size:0.7rem; font-weight:700; cursor:pointer; font-family:'Plus Jakarta Sans',sans-serif; transition:all .2s;">🚶 Jalan</button>
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

                    <!-- Toggle Ganjil / Genap -->
                    <div class="js-toggle-container">
                        <button class="js-toggle-btn active" id="btnJadwalGanjil" onclick="toggleJadwalSemester('ganjil')">Semester Ganjil</button>
                        <button class="js-toggle-btn" id="btnJadwalGenap" onclick="toggleJadwalSemester('genap')">Semester Genap</button>
                    </div>

                    <!-- Tabs per Semester -->
                    <div id="sbJadwalTabs" class="rp-semester-tabs" style="margin-top:10px;"></div>

                    <!-- Dropdown Tahun Ajaran -->
                    <div id="sbJadwalDropdownWrap" style="margin-top:10px; display:none;">
                        <label style="font-size:0.75rem; color:var(--muted); font-weight:600; margin-bottom:4px; display:block;">Tahun Ajaran:</label>
                        <select id="sbJadwalDropdown" class="sb-jadwal-dropdown" onchange="onJadwalDropdownChange()"></select>
                    </div>

                    <!-- Viewer (single preview) -->
                    <div id="sbJadwalViewer" style="margin-top:12px;">
                        <!-- Rendered by JS: image preview + buttons -->
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


<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>
<script>
    window.WEBGIS_URL = '{{ route("webgis.geojson") }}';
    window.WEBGIS_RUANGAN_URL = '{{ route("webgis.geojson.ruangan") }}';
    window.API_JADWAL_SEMESTER_URL = '{{ url('/api/gedung') }}';
</script>
<script src="{{ asset('js/public-peta.js') }}"></script>

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

</body>
</html>