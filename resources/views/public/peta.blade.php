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

        <span class="fp-label">Kondisi</span>
        <div class="fp-chips" id="chipsKondisi">
            <div class="chip on" data-v="">Semua</div>
            <div class="chip" data-v="Baik">Baik</div>
            <div class="chip" data-v="Sedang">Sedang</div>
            <div class="chip" data-v="Rusak">Rusak</div>
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
    <div class="leg-title">Kondisi Gedung</div>
    <div class="leg-row"><div class="leg-dot" style="background:#22c55e;box-shadow:0 0 5px #22c55e;"></div>Baik</div>
    <div class="leg-row"><div class="leg-dot" style="background:#f59e0b;box-shadow:0 0 5px #f59e0b;"></div>Sedang</div>
    <div class="leg-row"><div class="leg-dot" style="background:#ef4444;box-shadow:0 0 5px #ef4444;"></div>Rusak</div>
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
                    <div class="sb-stat"><span id="sbKondisi" class="sb-stat-v">-</span><span class="sb-stat-k">Kondisi</span></div>
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
(function(){
'use strict';

/* ── MAP INIT ─────────────────────────────── */
var map = L.map('map',{ zoomControl:false, attributionControl:true })
           .setView([-1.2654, 116.8312], 12);

/* ── TILE LAYERS ──────────────────────────── */
var light = L.tileLayer(
    'https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}',
    { attribution:'© Google', subdomains:['mt0','mt1','mt2','mt3'], maxZoom:21 }
);
var sat = L.tileLayer(
    'https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}',
    { attribution:'© Google', subdomains:['mt0','mt1','mt2','mt3'], maxZoom:21 }
);

// MENGUBAH DEFAULT KE SATELIT
sat.addTo(map);
var isSat = true;

/* ── LAYER TOGGLE ─────────────────────────── */
var layerBtn = document.getElementById('layerBtn');
layerBtn.addEventListener('click', function(){
    isSat = !isSat;
    if(isSat){ map.removeLayer(light); sat.addTo(map); this.classList.add('on'); toast('Layer: Satelit'); }
    else     { map.removeLayer(sat);  light.addTo(map); this.classList.remove('on'); toast('Layer: Peta (Google)'); }
});

/* ── ZOOM BUTTONS ─────────────────────────── */
document.getElementById('zIn').addEventListener('click', function(){ map.zoomIn(); });
document.getElementById('zOut').addEventListener('click', function(){ map.zoomOut(); });

/* ── COORDS ───────────────────────────────── */
map.on('mousemove', function(e){
    document.getElementById('coords').textContent =
        e.latlng.lat.toFixed(6) + ', ' + e.latlng.lng.toFixed(6);
});

/* ── HELPERS ──────────────────────────────── */
function getColor(k){
    return k==='Baik'?'#22c55e': k==='Sedang'?'#f59e0b': k==='Rusak'?'#ef4444': '#475569';
}
function getBadgeClass(k){
    return k==='Baik'?'b-baik': k==='Sedang'?'b-sedang': k==='Rusak'?'b-rusak': '';
}

/* ── MARKER ICON ──────────────────────────── */
function makeIcon(kondisi){
    var c = getColor(kondisi);
    var svg = '<svg xmlns="http://www.w3.org/2000/svg" width="30" height="42" viewBox="0 0 30 42">'
        +'<defs><filter id="ds"><feDropShadow dx="0" dy="3" stdDeviation="2.5" flood-color="rgba(0,0,0,.45)"/></filter></defs>'
        +'<path filter="url(#ds)" d="M15 2C8.37 2 3 7.37 3 14c0 8.9 12 26 12 26S27 22.9 27 14C27 7.37 21.63 2 15 2z" fill="'+c+'" stroke="rgba(255,255,255,.55)" stroke-width="1.5"/>'
        +'<circle cx="15" cy="14" r="5.5" fill="rgba(255,255,255,.92)"/>'
        +'<circle cx="15" cy="14" r="2.8" fill="'+c+'"/>'
        +'</svg>';
    return L.divIcon({ html:svg, className:'', iconSize:[30,42], iconAnchor:[15,42], popupAnchor:[0,-44] });
}

/* ── BUILD POPUP ──────────────────────────── */
function buildPopup(p, lat, lng){
    // Popup sederhana - hanya menampilkan nama gedung/prodi
    return '<div class="gis-popup" style="width:auto; padding:0;">'
        +'<div class="pu-body" style="padding:10px 14px;">'
        +'<div class="pu-name" style="font-size:0.95rem; margin:0;">'+p.nama_gedung+'</div>'
        +'</div></div>';
}

/* ── DATA & LAYERS ────────────────────────── */
var allData      = [];
var markerGroup  = L.layerGroup().addTo(map);
var labelGroup   = L.layerGroup();
var filterFungsi = '';
var filterKondisi= '';

function renderMarkers(data){
    markerGroup.clearLayers();
    labelGroup.clearLayers();

    data.forEach(function(f){
        var lat = f.geometry.coordinates[1];
        var lng = f.geometry.coordinates[0];
        var p   = f.properties;

        var m = L.marker([lat,lng],{ icon:makeIcon(p.kondisi||''), title:p.nama_gedung });
        
        // Store data untuk hover preview
        m.gedungData = {
            id: p.id,
            nama: p.nama_gedung,
            alamat: p.alamat || '-',
            foto: p.foto_utama,
            lat: lat,
            lng: lng
        };
        
        // Klik marker langsung buka sidebar detail gedung
        m.on('click', function(){
            map.flyTo([lat,lng], 18, {duration:1});
            openSidebar(p.id);
        });
        
        // Hover marker - show preview
        m.on('mouseover', function(){
            showMarkerPreview(this, lat, lng);
        });
        m.on('mouseout', function(){
            hideMarkerPreview();
        });
        
        markerGroup.addLayer(m);

        var lbl = L.marker([lat,lng],{
            icon: L.divIcon({
                html:'<div class="lbl-inner">'+p.nama_gedung+'</div>',
                className:'lbl-icon',
                iconAnchor:[0,0]
            }),
            interactive:false
        });
        labelGroup.addLayer(lbl);
    });

    document.getElementById('fpCount').textContent = data.length;
    updateLabels();
}

/* ── DYNAMIC LABELS ───────────────────────── */
function updateLabels(){
    if(map.getZoom()>=16){ if(!map.hasLayer(labelGroup)) labelGroup.addTo(map); }
    else                  { if(map.hasLayer(labelGroup))  map.removeLayer(labelGroup); }
}
map.on('zoomend', updateLabels);

/* ── FILTER CHIPS ─────────────────────────── */
function applyFilter(){
    renderMarkers(allData.filter(function(f){
        var p = f.properties;
        return (!filterFungsi  || p.fungsi  === filterFungsi)
            && (!filterKondisi || p.kondisi === filterKondisi);
    }));
}

function setupChips(containerId, onSelect){
    document.getElementById(containerId).addEventListener('click', function(e){
        var chip = e.target.closest('.chip');
        if(!chip) return;
        this.querySelectorAll('.chip').forEach(function(c){ c.classList.remove('on'); });
        chip.classList.add('on');
        onSelect(chip.dataset.v);
        applyFilter();
    });
}
setupChips('chipsFungsi',   function(v){ filterFungsi   = v; });
setupChips('chipsKondisi',  function(v){ filterKondisi  = v; });

document.getElementById('fpReset').addEventListener('click', function(){
    filterFungsi = filterKondisi = '';
    document.querySelectorAll('#chipsFungsi .chip, #chipsKondisi .chip').forEach(function(c){ c.classList.remove('on'); });
    document.querySelector('#chipsFungsi .chip[data-v=""]').classList.add('on');
    document.querySelector('#chipsKondisi .chip[data-v=""]').classList.add('on');
    renderMarkers(allData);
    toast('Filter direset');
});

/* ── FILTER PANEL TOGGLE ──────────────────── */
document.getElementById('btnFilter').addEventListener('click', function(){
    var p = document.getElementById('filterPanel');
    var open = p.classList.toggle('hide');
    this.classList.toggle('on', !open);
});

/* ── SEARCH ───────────────────────────────── */
var sIn   = document.getElementById('searchIn');
var sDrop = document.getElementById('searchDrop');
var sX    = document.getElementById('searchX');
var sMic  = document.getElementById('searchMic');

// Web Speech API initialization
var speechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
var recognition = speechRecognition ? new speechRecognition() : null;
var isListening = false;

if(recognition) {
    recognition.continuous = false;
    recognition.interimResults = true;
    recognition.lang = 'id-ID'; // Indonesian language
    
    recognition.onstart = function() {
        isListening = true;
        sMic.classList.add('listening');
        sIn.placeholder = 'Mendengarkan...';
    };
    
    recognition.onend = function() {
        isListening = false;
        sMic.classList.remove('listening');
        sIn.placeholder = 'Cari gedung atau alamat…';
    };
    
    recognition.onerror = function(event) {
        console.error('Speech recognition error', event.error);
        toast('Gagal mengenali suara');
        sMic.classList.remove('listening');
        sIn.placeholder = 'Cari gedung atau alamat…';
    };
    
    recognition.onresult = function(event) {
        var transcript = '';
        for(var i = event.resultIndex; i < event.results.length; i++) {
            if(event.results[i].isFinal) {
                transcript += event.results[i][0].transcript;
            }
        }
        if(transcript) {
            sIn.value = transcript;
            // Focus input untuk highlight
            sIn.focus();
            // Trigger search dengan delay kecil untuk ensure DOM update
            setTimeout(function(){
                sIn.dispatchEvent(new Event('input'));
            }, 100);
        }
    };
}

// Microphone button click
sMic.addEventListener('click', function(e) {
    e.preventDefault();
    
    if(!recognition) {
        toast('Browser Anda tidak mendukung voice search');
        return;
    }
    
    if(isListening) {
        recognition.stop();
    } else {
        sIn.value = '';
        recognition.start();
    }
});

sIn.addEventListener('keydown', function(e){
    if(e.key === 'Enter') {
        var firstItem = sDrop.querySelector('.t-drop-item[data-id]');
        if(firstItem) {
            firstItem.click();
        }
        e.preventDefault();
    }
    else if(e.key === 'Escape') {
        sDrop.style.display = 'none';
        sIn.value = '';
        sX.style.display = 'none';
        sMic.style.display = 'flex';
    }
});

sIn.addEventListener('input', function(){
    var q = this.value.trim().toLowerCase();
    sX.style.display = q ? 'block' : 'none';
    // Hide mic when typing
    sMic.style.display = q ? 'none' : 'flex';
    
    if(!q){ sDrop.style.display='none'; return; }

    // Improved search: prioritize starts-with matches
    var hits = allData.filter(function(f){
        var p = f.properties;
        var name = p.nama_gedung.toLowerCase();
        var addr = p.alamat ? p.alamat.toLowerCase() : '';
        return name.includes(q) || addr.includes(q);
    });
    
    // Sort: exact matches first, then starts-with, then includes
    hits.sort(function(a, b){
        var nameA = a.properties.nama_gedung.toLowerCase();
        var nameB = b.properties.nama_gedung.toLowerCase();
        
        if(nameA === q) return -1;
        if(nameB === q) return 1;
        if(nameA.startsWith(q) && !nameB.startsWith(q)) return -1;
        if(!nameA.startsWith(q) && nameB.startsWith(q)) return 1;
        return 0;
    });
    
    hits = hits.slice(0, 8);

    sDrop.innerHTML = hits.length
        ? hits.map(function(f, idx){
            var p = f.properties;
            var isFirst = idx === 0 ? ' style="background:rgba(59,130,246,.12); border-top:1px solid var(--accent-dim); border-bottom:1px solid var(--border);"' : '';
            return '<div class="t-drop-item" data-id="'+p.id+'" data-lat="'+f.geometry.coordinates[1]+'" data-lng="'+f.geometry.coordinates[0]+'"' + isFirst + '>'
                +'<div class="t-drop-ico"><i class="fas fa-building"></i></div>'
                +'<div><div class="t-drop-name">'+p.nama_gedung+'</div>'
                +'<div class="t-drop-sub">'+(p.fungsi!=='-'?p.fungsi+' · ':'')+p.alamat+'</div></div>'
                +'</div>';
        }).join('')
        : '<div class="t-drop-item"><div class="t-drop-sub" style="padding:4px 0; text-align:center;">Tidak ada hasil untuk "' + this.value + '"</div></div>';

    sDrop.style.display = 'block';
});

sDrop.addEventListener('click', function(e){
    var item = e.target.closest('.t-drop-item[data-id]');
    if(!item) return;
    var id = parseInt(item.dataset.id);
    var lat = parseFloat(item.dataset.lat);
    var lng = parseFloat(item.dataset.lng);
    
    map.flyTo([lat,lng], 18, {duration:1.3});
    setTimeout(function(){
        openSidebar(id);
    }, 1400);
    
    sDrop.style.display='none'; sIn.value=''; sX.style.display='none';
    sMic.style.display = 'flex';
});

sX.addEventListener('click', function(){
    sIn.value=''; sDrop.style.display='none'; this.style.display='none';
    sMic.style.display = 'flex';
});

document.addEventListener('click', function(e){
    if(!e.target.closest('.t-search')) sDrop.style.display='none';
});

/* ── TOAST ────────────────────────────────── */
var toastEl = document.getElementById('toast');
var toastTimer;
function toast(msg){
    clearTimeout(toastTimer);
    toastEl.textContent = msg;
    toastEl.classList.add('show');
    toastTimer = setTimeout(function(){ toastEl.classList.remove('show'); }, 2400);
}

/* ── MARKER PREVIEW ───────────────────────── */
var previewEl = document.getElementById('markerPreview');
var previewHideTimer;
var isPreviewHovering = false;

function showMarkerPreview(marker, lat, lng) {
    clearTimeout(previewHideTimer);
    var data = marker.gedungData;
    
    var imgHtml = data.foto 
        ? '<img class="mp-img" src="'+data.foto+'" alt="'+data.nama+'">'
        : '<div class="mp-no-img"><i class="fas fa-building"></i></div>';
    
    previewEl.innerHTML = ''
        + imgHtml
        + '<div class="mp-body">'
        + '<div class="mp-name">'+data.nama+'</div>'
        + '<div class="mp-addr"><i class="fas fa-map-marker-alt"></i> '+data.alamat+'</div>'
        + '<div class="mp-btns">'
        + '<button class="mp-btn detail" onclick="openSidebar('+data.id+')"><i class="fas fa-info-circle"></i> Detail</button>'
        + '<button class="mp-btn route" onclick="setRoutingDest('+lat+','+lng+')"><i class="fas fa-directions"></i> Rute</button>'
        + '</div>'
        + '</div>';
    
    // Position preview above marker
    var point = map.latLngToContainerPoint([lat, lng]);
    previewEl.style.left = (point.x - 140) + 'px'; // Center horizontally
    previewEl.style.top = (point.y - 180) + 'px'; // Above marker
    
    isPreviewHovering = true;
    previewEl.classList.add('show');
}

function hideMarkerPreview() {
    if(isPreviewHovering) return; // Jangan hide jika user sedang hover preview card
    
    previewHideTimer = setTimeout(function() {
        previewEl.classList.remove('show');
    }, 200);
}

// Prevent preview dari hilang saat user hover preview card
previewEl.addEventListener('mouseover', function(){
    isPreviewHovering = true;
    clearTimeout(previewHideTimer);
});

previewEl.addEventListener('mouseout', function(){
    isPreviewHovering = false;
    hideMarkerPreview();
});

/* ── SHOW USER LOCATION (INIT) ────────────── */
function showUserLocationOnInit() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(pos) {
            var userLat = pos.coords.latitude;
            var userLng = pos.coords.longitude;
            
            if(userMarker) {
                map.removeLayer(userMarker);
            }
            userMarker = L.circleMarker([userLat, userLng], {
                radius: 8,
                fillColor: '#3b82f6',
                color: '#fff',
                weight: 2,
                opacity: 1,
                fillOpacity: 0.8
            }).addTo(map).bindPopup('📍 Lokasi Anda Saat Ini', {closeButton: false});
        }, function(err) {
            console.log('Geolocation error:', err);
        });
    }
}

// Tampilkan lokasi user saat halaman dimuat
showUserLocationOnInit();

/* ── LOAD DATA ────────────────────────────── */
fetch('{{ route("webgis.geojson") }}')
    .then(function(r){ return r.json(); })
    .then(function(gj){
        allData = gj.features || [];
        renderMarkers(allData);

        if(allData.length){
            var pts = allData.map(function(f){ return [f.geometry.coordinates[1],f.geometry.coordinates[0]]; });
            map.fitBounds(L.latLngBounds(pts).pad(0.22));
        }

        // Focus on ?id=
        var uid = new URLSearchParams(window.location.search).get('id');
        if(uid){
            var t = allData.find(function(f){ return f.properties.id==uid; });
            if(t) map.flyTo([t.geometry.coordinates[1],t.geometry.coordinates[0]],18,{duration:1.5});
        }

        // Hide loading
        var ldr = document.getElementById('loading');
        ldr.classList.add('out');
        setTimeout(function(){ ldr.style.display='none'; }, 520);

        toast(allData.length + ' gedung dimuat');
    })
    .catch(function(){
        document.getElementById('loading').classList.add('out');
        setTimeout(function(){ document.getElementById('loading').style.display='none'; },400);
    });

/* ── LEAFLET ROUTING MACHINE ───────────────── */
var routeProfile = 'car'; // default profile: car, bike, foot
var routingControl = null;
var currentWaypoints = [];
var currentCoordinates = { start: null, end: null }; // Simpan koordinat numerik
var userMarker = null;

// Map user-friendly names to OSRM profiles
var profileMap = {
    'car': 'driving',
    'bike': 'cycling',
    'foot': 'walking',
    'driving': 'driving',
    'cycling': 'cycling',
    'walking': 'walking'
};

function createRoutingControl(profile, waypoints) {
    var osrmProfile = profileMap[profile || routeProfile] || 'driving';
    
    // Hapus control lama jika ada
    if(routingControl) {
        try {
            map.removeControl(routingControl);
        } catch(e) {
            console.log('Old control removal error:', e);
        }
        routingControl = null;
    }
    
    console.log('Creating routing with profile:', osrmProfile, 'waypoints:', waypoints);
    
    var control = L.Routing.control({
        waypoints: (waypoints && waypoints.length >= 2) ? waypoints : [],
        routeWhileDragging: false,
        addWaypoints: false,
        fitSelectedRoutes: true,
        router: L.Routing.osrmv1({
            serviceUrl: 'https://router.project-osrm.org/route/v1',
            profile: osrmProfile,
            timeout: 10000,
            stepInterpolation: true,
            exclude: [],
            // Add timestamp to prevent caching
            urlParameters: {
                '_t': Date.now()
            }
        }),
        lineOptions: {
            styles: [{ 
                color: '#3b82f6',
                opacity: 0.8,
                weight: 6
            }]
        },
        createMarker: function() { 
            return null; 
        }
    }).addTo(map);
    
    // Attach event listeners
    attachRoutingEvents(control);
    
    return control;
}

function attachRoutingEvents(control) {
    control.on('routesfound', onRouteFound);
    control.on('routingerror', onRoutingError);
}

// Fungsi untuk fetch durasi dan jarak dari OSRM secara langsung
function calculateRouteDirect(startLat, startLng, endLat, endLng, profile) {
    var osrmProfile = profileMap[profile] || 'driving';
    
    // Pastikan koordinat adalah numerik
    startLat = parseFloat(startLat);
    startLng = parseFloat(startLng);
    endLat = parseFloat(endLat);
    endLng = parseFloat(endLng);
    
    // Validasi koordinat
    if(isNaN(startLat) || isNaN(startLng) || isNaN(endLat) || isNaN(endLng)) {
        console.error('Invalid coordinates:', {startLat, startLng, endLat, endLng});
        return Promise.reject(new Error('Invalid coordinates'));
    }
    
    // OSRM API: /route/v1/{profile}/{lon1},{lat1};{lon2},{lat2}
    var url = 'https://router.project-osrm.org/route/v1/' + osrmProfile + '/' + 
              startLng + ',' + startLat + ';' + endLng + ',' + endLat + 
              '?overview=false&_t=' + Date.now(); // Cache buster
    
    console.log('Fetching route from OSRM:');
    console.log('  URL:', url);
    console.log('  Profile:', osrmProfile);
    console.log('  Start:', startLat, startLng);
    console.log('  End:', endLat, endLng);
    
    return fetch(url)
        .then(function(response) {
            console.log('OSRM Response status:', response.status);
            if(!response.ok) throw new Error('OSRM response not ok: ' + response.status);
            return response.json();
        })
        .then(function(data) {
            console.log('OSRM Direct Response:', data);
            
            if(data.routes && data.routes[0]) {
                var route = data.routes[0];
                var duration = route.duration; // seconds
                var distance = route.distance; // meters
                
                console.log('Direct API - Duration: ' + duration + 's, Distance: ' + distance + 'm, Profile: ' + osrmProfile);
                
                return {
                    duration: duration,
                    distance: distance,
                    profile: osrmProfile
                };
            } else {
                throw new Error('No routes found in response');
            }
        })
        .catch(function(err) {
            console.error('OSRM Direct API error:', err);
            throw err;
        });
}

function onRouteFound(e) {
    console.log('Route found! Event triggered:', e);
    document.getElementById('btnResetRoute').style.display = 'flex';
    
    if(e.routes && e.routes[0]) {
        var route = e.routes[0];
        console.log('Route summary full:', route.summary);
        console.log('Route summary totalTime:', route.summary.totalTime);
        console.log('Route summary totalDistance:', route.summary.totalDistance);
        
        var duration = formatDuration(route.summary.totalTime);
        var distance = formatDistance(route.summary.totalDistance);
        
        console.log('Distance:', distance, 'Duration:', duration, 'Mode:', routeProfile);
        
        document.getElementById('routeInfoDuration').textContent = duration;
        document.getElementById('routeInfoDistance').textContent = distance;
        
        var modeLabel = {
            'car': 'Mobil',
            'bike': 'Motor/Sepeda',
            'foot': 'Jalan Kaki'
        };
        document.getElementById('routeInfoMode').textContent = modeLabel[routeProfile] || routeProfile;
        document.getElementById('routeInfoPanel').style.display = 'block';
    }
}

function onRoutingError(e) {
    console.error('Routing error:', e);
    toast('Tidak ada rute ditemukan untuk mode ini');
}

routingControl = createRoutingControl(routeProfile);

// Format durasi (detik -> jam:menit:detik atau jam atau menit)
function formatDuration(seconds) {
    if(!seconds) return '-';
    
    var hours = Math.floor(seconds / 3600);
    var minutes = Math.floor((seconds % 3600) / 60);
    var secs = Math.floor(seconds % 60);
    
    if(hours > 0) {
        return hours + ' jam ' + minutes + ' menit ' + secs + ' detik';
    } else if(minutes > 0) {
        return minutes + ' menit ' + secs + ' detik';
    } else {
        return secs + ' detik';
    }
}

// Format jarak (meter -> km atau meter)
function formatDistance(meters) {
    if(!meters) return '-';
    if(meters >= 1000) {
        return (meters / 1000).toFixed(2) + ' km';
    }
    return Math.floor(meters) + ' m';
}


// Fungsi untuk menentukan tujuan rute
window.setRoutingDest = function(lat, lng) {

    // Cek apakah browser mendukung GPS
    if (navigator.geolocation) {

        // Ambil lokasi user (titik awal)
        navigator.geolocation.getCurrentPosition(function(pos) {
            
            var userLat = pos.coords.latitude;
            var userLng = pos.coords.longitude;
            
            // Tambahkan marker untuk lokasi user
            if(userMarker) {
                map.removeLayer(userMarker);
            }
            userMarker = L.circleMarker([userLat, userLng], {
                radius: 8,
                fillColor: '#3b82f6',
                color: '#fff',
                weight: 2,
                opacity: 1,
                fillOpacity: 0.8
            }).addTo(map).bindPopup('📍 Lokasi Anda Saat Ini', {closeButton: false});

            // Set titik awal (user) dan tujuan (gedung)
            currentWaypoints = [
                L.latLng(userLat, userLng),
                L.latLng(lat, lng)
            ];
            
            // Simpan koordinat numerik
            currentCoordinates.start = {lat: userLat, lng: userLng};
            currentCoordinates.end = {lat: lat, lng: lng};
            
            console.log('Setting initial waypoints:', currentWaypoints);
            console.log('Saved coordinates:', currentCoordinates);
            console.log('Waypoint 0:', currentWaypoints[0].lat, currentWaypoints[0].lng);
            console.log('Waypoint 1:', currentWaypoints[1].lat, currentWaypoints[1].lng);
            
            routingControl.setWaypoints(currentWaypoints);
            
            // Gunakan direct API call untuk mendapat durasi dan jarak yang akurat
            calculateRouteDirect(userLat, userLng, lat, lng, routeProfile)
                .then(function(routeData) {
                    console.log('Initial route data received:', routeData);
                    
                    var duration = formatDuration(routeData.duration);
                    var distance = formatDistance(routeData.distance);
                    
                    document.getElementById('routeInfoDuration').textContent = duration;
                    document.getElementById('routeInfoDistance').textContent = distance;
                    
                    var modeLabel = {
                        'car': 'Mobil',
                        'bike': 'Motor/Sepeda',
                        'foot': 'Jalan Kaki'
                    };
                    document.getElementById('routeInfoMode').textContent = modeLabel[routeProfile] || routeProfile;
                    document.getElementById('routeInfoPanel').style.display = 'block';
                    document.getElementById('btnResetRoute').style.display = 'flex';
                })
                .catch(function(err) {
                    console.error('Failed to get initial route data:', err);
                });

            // Notifikasi ke user
            toast('Menghitung rute ke lokasi…');

        }, function() {

            // Jika gagal ambil lokasi GPS
            toast('Gagal mendapatkan lokasi. Menggunakan titik tengah peta.');

            // Gunakan titik tengah peta sebagai titik awal
            var centerLat = map.getCenter().lat;
            var centerLng = map.getCenter().lng;
            
            if(userMarker) {
                map.removeLayer(userMarker);
            }
            userMarker = L.circleMarker([centerLat, centerLng], {
                radius: 8,
                fillColor: '#f59e0b',
                color: '#fff',
                weight: 2,
                opacity: 1,
                fillOpacity: 0.8
            }).addTo(map).bindPopup('📍 Lokasi Default (Pusat Peta)', {closeButton: false});
            
            currentWaypoints = [
                L.latLng(centerLat, centerLng),
                L.latLng(lat, lng)
            ];
            
            // Simpan koordinat numerik
            currentCoordinates.start = {lat: centerLat, lng: centerLng};
            currentCoordinates.end = {lat: lat, lng: lng};
            
            routingControl.setWaypoints(currentWaypoints);
            
            // Gunakan direct API call untuk mendapat durasi dan jarak yang akurat
            calculateRouteDirect(centerLat, centerLng, lat, lng, routeProfile)
                .then(function(routeData) {
                    console.log('Fallback route data received:', routeData);
                    
                    var duration = formatDuration(routeData.duration);
                    var distance = formatDistance(routeData.distance);
                    
                    document.getElementById('routeInfoDuration').textContent = duration;
                    document.getElementById('routeInfoDistance').textContent = distance;
                    
                    var modeLabel = {
                        'car': 'Mobil',
                        'bike': 'Motor/Sepeda',
                        'foot': 'Jalan Kaki'
                    };
                    document.getElementById('routeInfoMode').textContent = modeLabel[routeProfile] || routeProfile;
                    document.getElementById('routeInfoPanel').style.display = 'block';
                    document.getElementById('btnResetRoute').style.display = 'flex';
                })
                .catch(function(err) {
                    console.error('Failed to get fallback route data:', err);
                });
        });

    } else {
        // Jika browser tidak support GPS

        var centerLat = map.getCenter().lat;
        var centerLng = map.getCenter().lng;
        
        if(userMarker) {
            map.removeLayer(userMarker);
        }
        userMarker = L.circleMarker([centerLat, centerLng], {
            radius: 8,
            fillColor: '#f59e0b',
            color: '#fff',
            weight: 2,
            opacity: 1,
            fillOpacity: 0.8
        }).addTo(map).bindPopup('📍 Lokasi Default (Pusat Peta)', {closeButton: false});
        
        currentWaypoints = [
            L.latLng(centerLat, centerLng),
            L.latLng(lat, lng)
        ];
        
        // Simpan koordinat numerik
        currentCoordinates.start = {lat: centerLat, lng: centerLng};
        currentCoordinates.end = {lat: lat, lng: lng};
        
        routingControl.setWaypoints(currentWaypoints);
        
        // Gunakan direct API call untuk mendapat durasi dan jarak yang akurat
        calculateRouteDirect(centerLat, centerLng, lat, lng, routeProfile)
            .then(function(routeData) {
                console.log('No-GPS route data received:', routeData);
                
                var duration = formatDuration(routeData.duration);
                var distance = formatDistance(routeData.distance);
                
                document.getElementById('routeInfoDuration').textContent = duration;
                document.getElementById('routeInfoDistance').textContent = distance;
                
                var modeLabel = {
                    'car': 'Mobil',
                    'bike': 'Motor/Sepeda',
                    'foot': 'Jalan Kaki'
                };
                document.getElementById('routeInfoMode').textContent = modeLabel[routeProfile] || routeProfile;
                document.getElementById('routeInfoPanel').style.display = 'block';
                document.getElementById('btnResetRoute').style.display = 'flex';
            })
            .catch(function(err) {
                console.error('Failed to get no-GPS route data:', err);
            });
    }

    // Tutup popup setelah klik tombol rute
    map.closePopup();
};

// Fungsi untuk mengganti mode transportasi
window.changeRouteMode = function(mode) {
    console.log('Changing route mode to:', mode, 'mapped to profile:', profileMap[mode]);
    
    // Update current profile
    routeProfile = mode;
    
    // Gunakan simpan koordinat numerik, bukan getWaypoints()
    if(currentCoordinates.start && currentCoordinates.end) {
        console.log('Changing mode with saved coordinates:', currentCoordinates);
        
        // Gunakan direct API call untuk mendapat durasi dan jarak yang akurat
        var startLat = currentCoordinates.start.lat;
        var startLng = currentCoordinates.start.lng;
        var endLat = currentCoordinates.end.lat;
        var endLng = currentCoordinates.end.lng;
        
        console.log('Calling calculateRouteDirect with:', {
            startLat: startLat, startLng: startLng,
            endLat: endLat, endLng: endLng,
            profile: mode
        });
        
        calculateRouteDirect(startLat, startLng, endLat, endLng, mode)
            .then(function(routeData) {
                console.log('Direct API returned:', routeData);
                
                var duration = formatDuration(routeData.duration);
                var distance = formatDistance(routeData.distance);
                
                console.log('Formatted - Distance:', distance, 'Duration:', duration, 'Mode:', mode);
                
                document.getElementById('routeInfoDuration').textContent = duration;
                document.getElementById('routeInfoDistance').textContent = distance;
                
                var modeLabel = {
                    'car': 'Mobil',
                    'bike': 'Motor/Sepeda',
                    'foot': 'Jalan Kaki'
                };
                document.getElementById('routeInfoMode').textContent = modeLabel[mode] || mode;
                document.getElementById('routeInfoPanel').style.display = 'block';
                document.getElementById('btnResetRoute').style.display = 'flex';
            })
            .catch(function(err) {
                console.error('Failed to calculate route:', err);
                toast('Gagal menghitung rute untuk mode ' + mode);
            });
        
        var modeMsg = mode === 'car' ? 'Mobil' : mode === 'bike' ? 'Motor/Sepeda' : 'Jalan Kaki';
        toast('Menghitung rute dengan mode ' + modeMsg + '...');
    } else {
        console.warn('No coordinates saved for route calculation');
        toast('Silakan klik "Rute ke Sini" terlebih dahulu');
    }
    
    // Update button styles
    document.querySelectorAll('.route-mode-btn').forEach(function(btn) {
        btn.classList.remove('active');
        btn.style.background = 'rgba(255,255,255,.05)';
        btn.style.color = 'var(--muted)';
        btn.style.borderColor = 'var(--border)';
    });
    
    var activeBtn = document.querySelector('[data-mode="' + mode + '"]');
    if(activeBtn) {
        activeBtn.classList.add('active');
        activeBtn.style.background = 'var(--accent)';
        activeBtn.style.color = '#fff';
        activeBtn.style.borderColor = 'var(--accent)';
    }
};


// Event tombol reset rute
document.getElementById('btnResetRoute').addEventListener('click', function() {

    routingControl.setWaypoints([]); 
    // Menghapus rute dari peta
    
    currentWaypoints = [];
    currentCoordinates = { start: null, end: null }; // Clear coordinates

    this.style.display = 'none'; 
    // Sembunyikan tombol reset
    
    document.getElementById('routeInfoPanel').style.display = 'none';
    // Sembunyikan info panel

    if(userMarker) {
        map.removeLayer(userMarker);
        userMarker = null;
    }

    toast('Rute dihapus'); 
    // Tampilkan notifikasi
});

/* ── SIDEBAR ──────────────────────────────── */
var sidebar = document.getElementById('sidebar');
var sbClose = document.getElementById('sbClose');
var sbGallery = document.getElementById('sbGallery');
var sbLoading = document.getElementById('sbLoading');
var sbContent = document.getElementById('sbContent');
var currentGedungLat = null;
var currentGedungLng = null;

sbClose.addEventListener('click', function(){
    sidebar.classList.remove('show');
});

document.getElementById('sbBtnRoute').addEventListener('click', function(){
    if(currentGedungLat && currentGedungLng) {
        setRoutingDest(currentGedungLat, currentGedungLng);
        sidebar.classList.remove('show');
        toast('Rute sedang dihitung...');
    }
});

document.getElementById('sbBtnPhotos').addEventListener('click', function(){
    if(sbGallery.style.display === 'none') {
        sbGallery.style.display = 'block';
        this.innerHTML = '<i class="fas fa-chevron-up"></i> Sembunyikan Foto';
    } else {
        sbGallery.style.display = 'none';
        this.innerHTML = '<i class="fas fa-images"></i> Lihat Foto';
    }
});

window.openSidebar = function(id) {
    sidebar.classList.add('show');
    sbLoading.style.display = 'block';
    sbContent.style.display = 'none';
    sbGallery.style.display = 'none';
    document.getElementById('sbBtnPhotos').innerHTML = '<i class="fas fa-images"></i> Lihat Foto';

    // Cari data gedung dari allData untuk mendapat koordinat
    var gedungData = allData.find(function(f){ return f.properties.id == id; });
    if(gedungData) {
        currentGedungLat = gedungData.geometry.coordinates[1];
        currentGedungLng = gedungData.geometry.coordinates[0];
    }

    fetch('/api/gedung/' + id)
        .then(function(r) { return r.json(); })
        .then(function(data) {
            sbLoading.style.display = 'none';
            sbContent.style.display = 'block';
            
            var p = data.gedung;
            
            var imgEl = document.getElementById('sbImg');
            if (data.foto_utama) {
                imgEl.src = data.foto_utama;
                imgEl.closest('.sb-img-wrap').style.display = 'block';
            } else {
                imgEl.closest('.sb-img-wrap').style.display = 'none';
            }

            document.getElementById('sbName').textContent = p.nama_gedung;
            document.getElementById('sbAddr').textContent = p.alamat || '-';
            document.getElementById('sbFungsi').textContent = p.fungsi && p.fungsi!=='-' ? p.fungsi : '-';
            document.getElementById('sbKondisi').textContent = p.kondisi && p.kondisi!=='-' ? p.kondisi : '-';
            document.getElementById('sbLantai').textContent = p.jumlah_lantai && p.jumlah_lantai!=='-' ? p.jumlah_lantai : '-';
            document.getElementById('sbTahun').textContent = p.tahun_berdiri && p.tahun_berdiri!=='-' ? p.tahun_berdiri : '-';
            
            // Deskripsi
            document.getElementById('sbDesc').innerHTML = p.deskripsi || '-';

            // Photos
            var grid = document.getElementById('sbGalleryGrid');
            if(data.fotos && data.fotos.length > 0) {
                document.getElementById('sbBtnPhotos').style.display = 'flex';
                grid.innerHTML = data.fotos.map(function(f){
                    return '<a href="'+f.path+'" target="_blank"><img src="'+f.path+'" alt="Foto"></a>';
                }).join('');
            } else {
                document.getElementById('sbBtnPhotos').style.display = 'none';
                grid.innerHTML = '<div style="grid-column:1/3; color:var(--muted); font-size:0.8rem; text-align:center;">Belum ada foto galeri</div>';
            }

            // Fasilitas
            var fasEl = document.getElementById('sbFasilitas');
            if(data.fasilitas && data.fasilitas.length > 0) {
                var fasHtml = '<div style="display:flex; flex-direction:column; gap:10px;">';
                data.fasilitas.forEach(function(f) {
                    var statusColor = f.is_aktif ? 'var(--success)' : 'var(--muted)';
                    var statusText = f.is_aktif ? 'Sedang Dipakai' : 'Kosong';
                    var pulseClass = f.is_aktif ? 'pulse-mini' : '';
                    
                    fasHtml += '<div style="background:rgba(255,255,255,0.03); border:1px solid var(--border); border-radius:12px; padding:12px; transition:all 0.3s ease;">' 
                            + '<div style="display:flex; justify-content:space-between; align-items:start; margin-bottom:4px;">'
                            + '<strong style="font-size:0.9rem; color:var(--text);">' + f.nama_fasilitas + '</strong>'
                            + (f.kategori ? '<span style="font-size:0.65rem; color:var(--accent); font-weight:700; text-transform:uppercase; letter-spacing:0.5px; border:1px solid var(--accent-dim); padding:2px 8px; border-radius:100px; background:var(--accent-dim);">' + f.kategori + '</span>' : '')
                            + '</div>'
                            + (f.keterangan ? '<div style="font-size:0.75rem; color:var(--muted); margin-bottom:8px; line-height:1.4;">' + f.keterangan + '</div>' : '')
                            + '<div style="display:flex; align-items:center; gap:8px;">'
                            + '<div class="status-dot ' + pulseClass + '" style="width:7px; height:7px; border-radius:50%; background:' + statusColor + '; box-shadow:0 0 10px ' + (f.is_aktif ? 'rgba(34,197,94,0.4)' : 'transparent') + '"></div>'
                            + '<span style="font-size:0.7rem; font-weight:600; color:' + statusColor + '; text-transform:uppercase; letter-spacing:0.5px;">' + statusText + '</span>'
                            + '</div>'
                            + '</div>';
                });
                fasHtml += '</div>';
                fasEl.innerHTML = fasHtml;
            } else {
                fasEl.innerHTML = '<div style="background:rgba(255,255,255,0.03); border:1px dashed var(--border); border-radius:12px; padding:16px; text-align:center; color:var(--muted); font-size:0.8rem;">Informasi fasilitas & kelas pada gedung ini belum tersedia saat ini.</div>';
            }

        })
        .catch(function(err) {
            console.error(err);
            toast('Gagal mengambil data gedung');
        });
}
})();
</script>

</body>
</html>