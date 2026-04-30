/**
 * admin-gedung-fields.js
 * Logic for map picker and photo previews in Gedung Create/Edit forms.
 */

document.addEventListener('DOMContentLoaded', function() {
    // ── Elements ───────────────────────────────────────────
    const inputLat = document.getElementById('input_lat');
    const inputLng = document.getElementById('input_lng');
    const fotoUtama = document.getElementById('foto_utama');
    const fotoGedung = document.getElementById('foto_gedung');

    // ── Peta Koordinat Picker ──────────────────────────────
    // Default location (requested: -0.53597801, 117.12345243)
    let latVal = parseFloat(inputLat.value);
    let lngVal = parseFloat(inputLng.value);

    const hasCoords = !isNaN(latVal) && !isNaN(lngVal);
    const defaultLat = hasCoords ? latVal : -0.53604774;
    const defaultLng = hasCoords ? lngVal : 117.12357581;
    const initialZoom = hasCoords ? 18 : 19;

    const map = L.map('map-picker').setView([defaultLat, defaultLng], initialZoom);

    // Layer Satelit (Google)
    const sat = L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
        attribution: '© Google', subdomains: ['mt0', 'mt1', 'mt2', 'mt3'], maxZoom: 21
    }).addTo(map);

    const light = L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
        attribution: '© Google', subdomains: ['mt0', 'mt1', 'mt2', 'mt3'], maxZoom: 21
    });

    L.control.layers({
        "Satelit (Google)": sat,
        "Peta (Google)": light
    }).addTo(map);

    let marker = null;

    // Jika sudah ada koordinat, pasang marker
    if (hasCoords) {
        marker = L.marker([latVal, lngVal], { draggable: true }).addTo(map);
        marker.on('dragend', function(e) {
            updateInputs(marker.getLatLng());
        });
    }

    // Fungsi update input teks
    function updateInputs(latlng) {
        inputLat.value = latlng.lat.toFixed(8);
        inputLng.value = latlng.lng.toFixed(8);
    }

    // Fungsi update marker dari input (sync manual)
    function updateMarkerFromInputs() {
        const lat = parseFloat(inputLat.value);
        const lng = parseFloat(inputLng.value);

        if (!isNaN(lat) && !isNaN(lng)) {
            const newLatLng = [lat, lng];
            if (marker) {
                marker.setLatLng(newLatLng);
            } else {
                marker = L.marker(newLatLng, { draggable: true }).addTo(map);
                marker.on('dragend', function(e) {
                    updateInputs(marker.getLatLng());
                });
            }
            map.setView(newLatLng, 18);
        }
    }

    // Listener klik pada peta
    map.on('click', function(e) {
        updateInputs(e.latlng);

        if (marker) {
            marker.setLatLng(e.latlng);
        } else {
            marker = L.marker(e.latlng, { draggable: true }).addTo(map);
            marker.on('dragend', function(e) {
                updateInputs(marker.getLatLng());
            });
        }
    });

    // Listener input manual
    inputLat.addEventListener('input', updateMarkerFromInputs);
    inputLng.addEventListener('input', updateMarkerFromInputs);

    // Fix map gray tiles when in tabs or hidden containers
    setTimeout(() => {
        map.invalidateSize();
    }, 500);

    // ── Preview Foto Utama ─────────────────────────────────
    if (fotoUtama) {
        fotoUtama.addEventListener('change', function(e) {
            const file = e.target.files[0];
            const previewDiv = document.getElementById('preview-utama');
            const previewImg = document.getElementById('img-preview-utama');
            const label = e.target.nextElementSibling;

            if (file) {
                if (label) label.textContent = file.name;
                const reader = new FileReader();
                reader.onload = function(ev) {
                    if (previewImg) previewImg.src = ev.target.result;
                    if (previewDiv) previewDiv.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // ── Preview Foto Galeri ────────────────────────────────
    if (fotoGedung) {
        fotoGedung.addEventListener('change', function(e) {
            const container = document.getElementById('preview-galeri');
            const label = e.target.nextElementSibling;

            if (container) container.innerHTML = '';
            if (label) label.textContent = e.target.files.length + " foto dipilih";

            Array.from(e.target.files).forEach(function(file) {
                const reader = new FileReader();
                reader.onload = function(ev) {
                    const img = document.createElement('img');
                    img.src = ev.target.result;
                    img.className = 'img-thumbnail';
                    img.style.maxHeight = '100px';
                    img.style.marginRight = '8px';
                    img.style.marginBottom = '8px';
                    img.style.objectFit = 'cover';
                    img.style.width = '100px';
                    if (container) container.appendChild(img);
                };
                reader.readAsDataURL(file);
            });
        });
    }
});