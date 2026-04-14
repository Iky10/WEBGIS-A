@extends('layouts.app')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-map-marked-alt"></i> WebGIS Gedung</h1>
            </div>
            <div class="col-sm-6">
                <div class="float-right">
                    <a href="{{ route('gedungs.index') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-building"></i> Kelola Gedung
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="content px-3">
    <div class="row">

        {{-- Panel Kiri: Filter + Daftar Gedung --}}
        <div class="col-md-3">

            {{-- Filter --}}
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fas fa-filter"></i> Filter</h5>
                </div>
                <div class="card-body p-2">
                    <div class="form-group mb-2">
                        <label class="mb-1">Fungsi Gedung</label>
                        <select id="filter-fungsi" class="form-control form-control-sm">
                            <option value="">-- Semua --</option>
                            <option value="Perkantoran">Perkantoran</option>
                            <option value="Pendidikan">Pendidikan</option>
                            <option value="Kesehatan">Kesehatan</option>
                            <option value="Komersial">Komersial</option>
                            <option value="Publik">Publik</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label class="mb-1">Kondisi</label>
                        <select id="filter-kondisi" class="form-control form-control-sm">
                            <option value="">-- Semua --</option>
                            <option value="Baik">Baik</option>
                            <option value="Sedang">Sedang</option>
                            <option value="Rusak">Rusak</option>
                        </select>
                    </div>
                    <button id="btn-reset-filter" class="btn btn-secondary btn-sm btn-block">
                        <i class="fas fa-undo"></i> Reset Filter
                    </button>
                </div>
            </div>

            {{-- Daftar Gedung --}}
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list"></i> Daftar Gedung
                        <span id="jumlah-gedung" class="badge badge-info ml-1">0</span>
                    </h5>
                </div>
                <div class="card-body p-0" style="max-height: 400px; overflow-y: auto;">
                    <ul class="list-group list-group-flush" id="list-gedung">
                        <li class="list-group-item text-center text-muted py-3">
                            <i class="fas fa-spinner fa-spin"></i> Memuat...
                        </li>
                    </ul>
                </div>
            </div>

        </div>

        {{-- Panel Kanan: Peta --}}
        <div class="col-md-9">
            <div class="card card-outline card-success">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-map"></i> Peta Lokasi Gedung
                    </h5>
                    <div class="card-tools">
                        <button id="btn-fit-bounds" class="btn btn-sm btn-default" title="Tampilkan semua gedung">
                            <i class="fas fa-compress-arrows-alt"></i> Fit All
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div id="map" style="height: 600px; width: 100%;"></div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('third_party_stylesheets')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<style>
    /* Popup custom */
    .leaflet-popup-content {
        min-width: 220px;
        max-width: 280px;
    }
    .popup-foto {
        width: 100%;
        height: 140px;
        object-fit: cover;
        border-radius: 4px;
        margin-bottom: 8px;
    }
    .popup-no-foto {
        width: 100%;
        height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f4f6f9;
        border-radius: 4px;
        margin-bottom: 8px;
        color: #aaa;
        font-size: 13px;
    }
    .popup-title {
        font-weight: bold;
        font-size: 14px;
        margin-bottom: 4px;
        color: #333;
    }
    .popup-badge {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        margin-bottom: 6px;
    }
    .badge-baik    { background: #d4edda; color: #155724; }
    .badge-sedang  { background: #fff3cd; color: #856404; }
    .badge-rusak   { background: #f8d7da; color: #721c24; }
    .badge-fungsi  { background: #cce5ff; color: #004085; }

    .popup-info-row {
        font-size: 12px;
        color: #555;
        margin-bottom: 2px;
    }
    .popup-info-row i {
        width: 14px;
        color: #888;
    }
    .popup-btn {
        display: block;
        margin-top: 8px;
        text-align: center;
        background: #007bff;
        color: #fff !important;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 12px;
        text-decoration: none !important;
    }
    .popup-btn:hover { background: #0056b3; }

    /* List item gedung */
    #list-gedung .list-group-item {
        cursor: pointer;
        padding: 8px 12px;
        border-left: 3px solid transparent;
        transition: all 0.2s;
    }
    #list-gedung .list-group-item:hover,
    #list-gedung .list-group-item.active-item {
        background: #e8f4ff;
        border-left-color: #007bff;
    }
    #list-gedung .item-name {
        font-weight: 600;
        font-size: 13px;
        color: #333;
    }
    #list-gedung .item-sub {
        font-size: 11px;
        color: #888;
    }
</style>
@endpush

@push('page_scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Inisialisasi Peta ────────────────────────────────────────
    var map = L.map('map').setView([-2.5, 118.0], 5);

    // Layer OpenStreetMap
    var osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    });

    // Layer Satelit (Esri)
    var satelitLayer = L.tileLayer(
        'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: '© Esri'
    });

    // Default: OSM
    osmLayer.addTo(map);

    // Layer control
    L.control.layers({
        'Peta Jalan (OSM)': osmLayer,
        'Satelit (Esri)': satelitLayer,
    }).addTo(map);

    // ── Warna Marker berdasarkan Kondisi ─────────────────────────
    function getMarkerColor(kondisi) {
        switch (kondisi) {
            case 'Baik':   return '#28a745';
            case 'Sedang': return '#ffc107';
            case 'Rusak':  return '#dc3545';
            default:       return '#6c757d';
        }
    }

    function createIcon(kondisi) {
        var color = getMarkerColor(kondisi);
        var svg = '<svg xmlns="http://www.w3.org/2000/svg" width="28" height="40" viewBox="0 0 28 40">'
            + '<path d="M14 0C6.27 0 0 6.27 0 14c0 10.5 14 26 14 26S28 24.5 28 14C28 6.27 21.73 0 14 0z" fill="' + color + '" stroke="#fff" stroke-width="1.5"/>'
            + '<circle cx="14" cy="14" r="6" fill="#fff"/>'
            + '</svg>';
        return L.divIcon({
            html: svg,
            className: '',
            iconSize: [28, 40],
            iconAnchor: [14, 40],
            popupAnchor: [0, -40],
        });
    }

    // ── Data & Marker ────────────────────────────────────────────
    var allData     = [];
    var markersMap  = {}; // id => marker
    var markersLayer = L.layerGroup().addTo(map);

    function buildPopup(p) {
        var fotoHtml = p.foto_utama
            ? '<img src="' + p.foto_utama + '" class="popup-foto" alt="' + p.nama_gedung + '">'
            : '<div class="popup-no-foto"><i class="fas fa-image"></i>&nbsp; Tidak ada foto</div>';

        var kondisiBadge = '';
        if (p.kondisi && p.kondisi !== '-') {
            var cls = 'badge-' + p.kondisi.toLowerCase();
            kondisiBadge = '<span class="popup-badge ' + cls + '">' + p.kondisi + '</span> ';
        }
        var fungsiBadge = p.fungsi && p.fungsi !== '-'
            ? '<span class="popup-badge badge-fungsi">' + p.fungsi + '</span>'
            : '';

        return '<div>'
            + fotoHtml
            + '<div class="popup-title">' + p.nama_gedung + '</div>'
            + kondisiBadge + fungsiBadge
            + '<div class="popup-info-row"><i class="fas fa-map-marker-alt"></i> ' + p.alamat + '</div>'
            + (p.jumlah_lantai !== '-' ? '<div class="popup-info-row"><i class="fas fa-layer-group"></i> ' + p.jumlah_lantai + ' lantai</div>' : '')
            + (p.tahun_berdiri !== '-' ? '<div class="popup-info-row"><i class="fas fa-calendar"></i> Berdiri ' + p.tahun_berdiri + '</div>' : '')
            + '<a href="' + p.detail_url + '" class="popup-btn"><i class="fas fa-info-circle"></i> Lihat Detail</a>'
            + '</div>';
    }

    function renderMarkers(data) {
        markersLayer.clearLayers();
        markersMap = {};
        var list   = document.getElementById('list-gedung');
        list.innerHTML = '';

        if (data.length === 0) {
            list.innerHTML = '<li class="list-group-item text-center text-muted">Tidak ada data.</li>';
            document.getElementById('jumlah-gedung').textContent = '0';
            return;
        }

        document.getElementById('jumlah-gedung').textContent = data.length;

        data.forEach(function (feature) {
            var p   = feature.properties;
            var lat = feature.geometry.coordinates[1];
            var lng = feature.geometry.coordinates[0];

            // Marker
            var marker = L.marker([lat, lng], { icon: createIcon(p.kondisi) })
                .bindPopup(buildPopup(p), { maxWidth: 300 });

            markersLayer.addLayer(marker);
            markersMap[p.id] = marker;

            // List item
            var li = document.createElement('li');
            li.className = 'list-group-item';
            li.dataset.id = p.id;
            li.innerHTML = '<div class="item-name"><i class="fas fa-building mr-1"></i>' + p.nama_gedung + '</div>'
                + '<div class="item-sub">' + (p.fungsi !== '-' ? p.fungsi + ' · ' : '') + (p.kondisi !== '-' ? p.kondisi : '') + '</div>';

            li.addEventListener('click', function () {
                document.querySelectorAll('#list-gedung .list-group-item').forEach(function (el) {
                    el.classList.remove('active-item');
                });
                li.classList.add('active-item');
                map.setView([lat, lng], 17);
                markersMap[p.id].openPopup();
            });

            list.appendChild(li);
        });
    }

    // Fit semua marker
    function fitAllMarkers() {
        var bounds = [];
        Object.values(markersMap).forEach(function (m) {
            bounds.push(m.getLatLng());
        });
        if (bounds.length > 0) {
            map.fitBounds(L.latLngBounds(bounds).pad(0.2));
        }
    }

    // ── Fetch GeoJSON dari server ────────────────────────────────
    fetch('{{ route("webgis.geojson") }}')
        .then(function (res) { return res.json(); })
        .then(function (geojson) {
            allData = geojson.features || [];
            renderMarkers(allData);
            if (allData.length > 0) fitAllMarkers();
        })
        .catch(function () {
            document.getElementById('list-gedung').innerHTML =
                '<li class="list-group-item text-danger">Gagal memuat data.</li>';
        });

    // ── Filter ───────────────────────────────────────────────────
    function applyFilter() {
        var fungsi  = document.getElementById('filter-fungsi').value;
        var kondisi = document.getElementById('filter-kondisi').value;

        var filtered = allData.filter(function (f) {
            var p = f.properties;
            return (!fungsi  || p.fungsi  === fungsi)
                && (!kondisi || p.kondisi === kondisi);
        });

        renderMarkers(filtered);
    }

    document.getElementById('filter-fungsi').addEventListener('change', applyFilter);
    document.getElementById('filter-kondisi').addEventListener('change', applyFilter);

    document.getElementById('btn-reset-filter').addEventListener('click', function () {
        document.getElementById('filter-fungsi').value  = '';
        document.getElementById('filter-kondisi').value = '';
        renderMarkers(allData);
        fitAllMarkers();
    });
    // ngoding woy
    // ── Fit All button ───────────────────────────────────────────
    document.getElementById('btn-fit-bounds').addEventListener('click', fitAllMarkers);

});
</script>
@endpush