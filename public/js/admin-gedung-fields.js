// ── Peta Koordinat Picker ──────────────────────────────
    var defaultLat = parseFloat(document.getElementById('input_lat').value) || -0.53721103;
    var defaultLng = parseFloat(document.getElementById('input_lng').value) || 117.12494026;
    var initialZoom = document.getElementById('input_lat').value ? 18 : 17;

    var map = L.map('map-picker').setView([defaultLat, defaultLng], initialZoom);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    var marker = null;

    // Kalau sudah ada koordinat (mode edit), tampilkan marker
    if (document.getElementById('input_lat').value && document.getElementById('input_lng').value) {
        marker = L.marker([defaultLat, defaultLng]).addTo(map);
        map.setView([defaultLat, defaultLng], 15);
    }

    map.on('click', function(e) {
        var lat = e.latlng.lat.toFixed(8);
        var lng = e.latlng.lng.toFixed(8);

        document.getElementById('input_lat').value = lat;
        document.getElementById('input_lng').value = lng;

        if (marker) {
            marker.setLatLng(e.latlng);
        } else {
            marker = L.marker(e.latlng).addTo(map);
        }
    });

    // ── Preview Foto Utama ─────────────────────────────────
    document.getElementById('foto_utama').addEventListener('change', function(e) {
        var file = e.target.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(ev) {
                document.getElementById('img-preview-utama').src = ev.target.result;
                document.getElementById('preview-utama').style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });

    // ── Preview Foto Galeri ────────────────────────────────
    document.getElementById('foto_gedung').addEventListener('change', function(e) {
        var container = document.getElementById('preview-galeri');
        container.innerHTML = '';
        Array.from(e.target.files).forEach(function(file) {
            var reader = new FileReader();
            reader.onload = function(ev) {
                var img = document.createElement('img');
                img.src = ev.target.result;
                img.className = 'img-thumbnail';
                img.style.maxHeight = '100px';
                img.style.marginRight = '8px';
                img.style.marginBottom = '8px';
                container.appendChild(img);
            };
            reader.readAsDataURL(file);
        });
    });