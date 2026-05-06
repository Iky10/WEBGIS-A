document.addEventListener('DOMContentLoaded', function () {

    // ── Inisialisasi Peta ────────────────────────────────────────
    var map = L.map('map').setView([-0.53597801, 117.12345243], 18);

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
    fetch(window.WEBGIS_URL)
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

    // ── Fit All button ───────────────────────────────────────────
    document.getElementById('btn-fit-bounds').addEventListener('click', fitAllMarkers);

});