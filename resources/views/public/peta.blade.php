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

<div id="legend">
    <div class="leg-title">Status Pemakaian</div>
    <div class="leg-row"><div class="leg-dot" style="background:#22c55e;box-shadow:0 0 5px #22c55e;"></div>Sedang Dipakai</div>
    <div class="leg-row"><div class="leg-dot" style="background:#6c757d;box-shadow:0 0 5px #6c757d;"></div>Kosong</div>
    <div class="leg-row"><div class="leg-dot" style="background:#475569;"></div>Tidak diketahui</div>
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

                <div class="sb-section">
                    <div class="sb-sec-title">Deskripsi</div>
                    <div id="sbDesc" class="sb-sec-text">-</div>
                </div>

                <div class="sb-section">
                    <div class="sb-sec-title">Fasilitas & Kelas <span>(Opsional)</span></div>
                    <div id="sbFasilitas" class="sb-sec-text">Informasi fasilitas & kelas pada gedung ini belum tersedia saat ini.</div>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:16px;">
                    <button id="sbBtnRoute" class="sb-cta-btn" style="background:var(--success); box-shadow:0 6px 20px rgba(34,197,94,.35);"><i class="fas fa-directions"></i> Rute ke Sini</button>
                    <button id="sbBtnPhotos" class="sb-cta-btn"><i class="fas fa-images"></i> Lihat Foto</button>
                </div>

                <div style="margin-bottom:16px;">
                    <a id="sbBtnPengajuan" href="{{ route('pengajuan_gedungs.create') }}"
                       class="sb-cta-btn" style="display:block; text-align:center; text-decoration:none; padding:10px; background:var(--accent); color:#fff; border-radius:8px; font-weight:700; font-size:0.82rem;">
                        <i class="fas fa-file-alt"></i> Ajukan Penggunaan Gedung
                    </a>
                </div>

                <div id="sbGallery" class="sb-gallery" style="display:none;">
                    <div class="sb-sec-title">Galeri Foto</div>
                    <div id="sbGalleryGrid" class="sb-gallery-grid">
                        <!-- Fotos inserted here -->
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
</script>
<script src="{{ asset('js/public-peta.js') }}"></script>

</body>
</html>