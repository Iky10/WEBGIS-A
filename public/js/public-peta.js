(function () {
    'use strict';

    /* ── MAP INIT ─────────────────────────────── */
    var map = L.map('map', { zoomControl: false, attributionControl: true })
        .setView([-0.53604774, 117.12357581], 19);

    /* ── TILE LAYERS ──────────────────────────── */
    var light = L.tileLayer(
        'https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}',
        { attribution: '© Google', subdomains: ['mt0', 'mt1', 'mt2', 'mt3'], maxZoom: 21 }
    );
    var sat = L.tileLayer(
        'https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}',
        { attribution: '© Google', subdomains: ['mt0', 'mt1', 'mt2', 'mt3'], maxZoom: 21 }
    );

    // MENGUBAH DEFAULT KE SATELIT
    sat.addTo(map);
    var isSat = true;

    /* ── LAYER TOGGLE ─────────────────────────── */
    var layerBtn = document.getElementById('layerBtn');
    layerBtn.addEventListener('click', function () {
        isSat = !isSat;
        if (isSat) { map.removeLayer(light); sat.addTo(map); this.classList.add('on'); toast('Layer: Satelit'); }
        else { map.removeLayer(sat); light.addTo(map); this.classList.remove('on'); toast('Layer: Peta (Google)'); }
    });

    /* ── ZOOM BUTTONS ─────────────────────────── */
    document.getElementById('zIn').addEventListener('click', function () { map.zoomIn(); });
    document.getElementById('zOut').addEventListener('click', function () { map.zoomOut(); });

    /* ── COORDS ───────────────────────────────── */
    map.on('mousemove', function (e) {
        document.getElementById('coords').textContent =
            e.latlng.lat.toFixed(6) + ', ' + e.latlng.lng.toFixed(6);
    });

    /* ── HELPERS ──────────────────────────────── */
    function getColor(k) {
        return k === 'Sedang Dipakai' ? '#22c55e' : k === 'Kosong' ? '#6c757d' : '#475569';
    }
    function getBadgeClass(k) {
        return k === 'Sedang Dipakai' ? 'badge-success' : k === 'Kosong' ? 'badge-secondary' : '';
    }

    /* ── MARKER ICON ──────────────────────────── */
    function makeIcon(kondisi) {
        var c = getColor(kondisi);
        var svg = '<svg xmlns="http://www.w3.org/2000/svg" width="30" height="42" viewBox="0 0 30 42">'
            + '<defs><filter id="ds"><feDropShadow dx="0" dy="3" stdDeviation="2.5" flood-color="rgba(0,0,0,.45)"/></filter></defs>'
            + '<path filter="url(#ds)" d="M15 2C8.37 2 3 7.37 3 14c0 8.9 12 26 12 26S27 22.9 27 14C27 7.37 21.63 2 15 2z" fill="' + c + '" stroke="rgba(255,255,255,.55)" stroke-width="1.5"/>'
            + '<circle cx="15" cy="14" r="5.5" fill="rgba(255,255,255,.92)"/>'
            + '<circle cx="15" cy="14" r="2.8" fill="' + c + '"/>'
            + '</svg>';
        return L.divIcon({ html: svg, className: '', iconSize: [30, 42], iconAnchor: [15, 42], popupAnchor: [0, -44] });
    }

    /* ── BUILD POPUP ──────────────────────────── */
    function buildPopup(p, lat, lng) {
        // Popup sederhana - hanya menampilkan nama gedung/prodi
        return '<div class="gis-popup" style="width:auto; padding:0;">'
            + '<div class="pu-body" style="padding:10px 14px;">'
            + '<div class="pu-name" style="font-size:0.95rem; margin:0;">' + p.nama_gedung + '</div>'
            + '</div></div>';
    }

    /* ── DATA & LAYERS ────────────────────────── */
    var allData = [];
    var markerGroup = L.layerGroup().addTo(map);
    var labelGroup = L.layerGroup();
    var filterFungsi = '';
    var filterKondisi = '';

    function renderMarkers(data) {
        markerGroup.clearLayers();
        labelGroup.clearLayers();

        data.forEach(function (f) {
            var lat = f.geometry.coordinates[1];
            var lng = f.geometry.coordinates[0];
            var p = f.properties;

            var m = L.marker([lat, lng], { icon: makeIcon(p.kondisi || ''), title: p.nama_gedung });

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
            m.on('click', function () {
                map.flyTo([lat, lng], 18, { duration: 1 });
                openSidebar(p.id);
            });

            // Hover marker - show preview
            m.on('mouseover', function () {
                showMarkerPreview(this, lat, lng);
            });
            m.on('mouseout', function () {
                hideMarkerPreview();
            });

            markerGroup.addLayer(m);

            var lbl = L.marker([lat, lng], {
                icon: L.divIcon({
                    html: '<div class="lbl-inner">' + p.nama_gedung + '</div>',
                    className: 'lbl-icon',
                    iconAnchor: [0, 0]
                }),
                interactive: false
            });
            labelGroup.addLayer(lbl);
        });

        document.getElementById('fpCount').textContent = data.length;
        updateZoomLayers();
    }

    /* ── ZOOM-BASED LAYER SWITCHING (Google Maps Style) ── */
    var ZOOM_THRESHOLD = 20;
    var currentMapMode = 'gedung';

    function updateZoomLayers() {
        var zoom = map.getZoom();

        // Update zoom level indicator
        var zoomIndicator = document.getElementById('zoomLevel');
        if (zoomIndicator) zoomIndicator.textContent = zoom;

        // Update mode badge
        var modeBadge = document.getElementById('zoomModeBadge');

        if (zoom >= ZOOM_THRESHOLD) {
            // ZOOM IN: Tampilkan ruangan, sembunyikan gedung
            if (!map.hasLayer(ruanganMarkerGroup)) ruanganMarkerGroup.addTo(map);
            if (!map.hasLayer(vegetasiMarkerGroup)) vegetasiMarkerGroup.addTo(map);

            if (modeBadge) {
                modeBadge.textContent = '\uD83C\uDFE0 Ruangan & Vegetasi';
                modeBadge.className = 'zoom-mode-badge mode-ruangan';
            }
            if (currentMapMode !== 'ruangan') {
                currentMapMode = 'ruangan';
                toast('\uD83C\uDFE0 Mode Detail (Ruangan & Vegetasi)');
            }
        } else {
            // ZOOM OUT: Tampilkan gedung, sembunyikan ruangan & vegetasi
            if (!map.hasLayer(markerGroup)) markerGroup.addTo(map);
            if (map.hasLayer(ruanganMarkerGroup)) map.removeLayer(ruanganMarkerGroup);
            if (map.hasLayer(vegetasiMarkerGroup)) map.removeLayer(vegetasiMarkerGroup);

            // Labels muncul di zoom >= 16
            if (zoom >= 16) {
                if (!map.hasLayer(labelGroup)) labelGroup.addTo(map);
            } else {
                if (map.hasLayer(labelGroup)) map.removeLayer(labelGroup);
            }

            if (modeBadge) {
                modeBadge.textContent = '\uD83C\uDFE2 Gedung';
                modeBadge.className = 'zoom-mode-badge mode-gedung';
            }
            if (currentMapMode !== 'gedung') {
                currentMapMode = 'gedung';
                toast('\uD83C\uDFE2 Mode Gedung');
            }
        }
    }
    map.on('zoomend', updateZoomLayers);

    /* ── FILTER CHIPS ─────────────────────────── */
    function applyFilter() {
        renderMarkers(allData.filter(function (f) {
            var p = f.properties;
            return (!filterFungsi || p.fungsi === filterFungsi)
                && (!filterKondisi || p.kondisi === filterKondisi);
        }));
    }

    function setupChips(containerId, onSelect) {
        document.getElementById(containerId).addEventListener('click', function (e) {
            var chip = e.target.closest('.chip');
            if (!chip) return;
            this.querySelectorAll('.chip').forEach(function (c) { c.classList.remove('on'); });
            chip.classList.add('on');
            onSelect(chip.dataset.v);
            applyFilter();
        });
    }
    setupChips('chipsFungsi', function (v) { filterFungsi = v; });
    setupChips('chipsKondisi', function (v) { filterKondisi = v; });

    document.getElementById('fpReset').addEventListener('click', function () {
        filterFungsi = filterKondisi = '';
        document.querySelectorAll('#chipsFungsi .chip, #chipsKondisi .chip').forEach(function (c) { c.classList.remove('on'); });
        document.querySelector('#chipsFungsi .chip[data-v=""]').classList.add('on');
        document.querySelector('#chipsKondisi .chip[data-v=""]').classList.add('on');
        renderMarkers(allData);
        toast('Filter direset');
    });

    /* ── FILTER PANEL TOGGLE ──────────────────── */
    document.getElementById('btnFilter').addEventListener('click', function () {
        var p = document.getElementById('filterPanel');
        var open = p.classList.toggle('hide');
        this.classList.toggle('on', !open);
    });

    /* ── SEARCH ───────────────────────────────── */
    var sIn = document.getElementById('searchIn');
    var sDrop = document.getElementById('searchDrop');
    var sX = document.getElementById('searchX');
    var sMic = document.getElementById('searchMic');

    // Web Speech API initialization
    var speechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    var recognition = speechRecognition ? new speechRecognition() : null;
    var isListening = false;

    if (recognition) {
        recognition.continuous = false;
        recognition.interimResults = true;
        recognition.lang = 'id-ID'; // Indonesian language

        recognition.onstart = function () {
            isListening = true;
            sMic.classList.add('listening');
            sIn.placeholder = 'Mendengarkan...';
        };

        recognition.onend = function () {
            isListening = false;
            sMic.classList.remove('listening');
            sIn.placeholder = 'Cari gedung atau alamat…';
        };

        recognition.onerror = function (event) {
            console.error('Speech recognition error', event.error);
            toast('Gagal mengenali suara');
            sMic.classList.remove('listening');
            sIn.placeholder = 'Cari gedung atau alamat…';
        };

        recognition.onresult = function (event) {
            var transcript = '';
            for (var i = event.resultIndex; i < event.results.length; i++) {
                if (event.results[i].isFinal) {
                    transcript += event.results[i][0].transcript;
                }
            }
            if (transcript) {
                sIn.value = transcript;
                // Focus input untuk highlight
                sIn.focus();
                // Trigger search dengan delay kecil untuk ensure DOM update
                setTimeout(function () {
                    sIn.dispatchEvent(new Event('input'));
                }, 100);
            }
        };
    }

    // Microphone button click
    sMic.addEventListener('click', function (e) {
        e.preventDefault();

        if (!recognition) {
            toast('Browser Anda tidak mendukung voice search');
            return;
        }

        if (isListening) {
            recognition.stop();
        } else {
            sIn.value = '';
            recognition.start();
        }
    });

    sIn.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            var firstItem = sDrop.querySelector('.t-drop-item[data-id]');
            if (firstItem) {
                firstItem.click();
            }
            e.preventDefault();
        }
        else if (e.key === 'Escape') {
            sDrop.style.display = 'none';
            sIn.value = '';
            sX.style.display = 'none';
            sMic.style.display = 'flex';
        }
    });

    sIn.addEventListener('input', function () {
        var q = this.value.trim().toLowerCase();
        sX.style.display = q ? 'block' : 'none';
        // Hide mic when typing
        sMic.style.display = q ? 'none' : 'flex';

        if (!q) { sDrop.style.display = 'none'; return; }

        // Improved search: prioritize starts-with matches
        var hits = allData.filter(function (f) {
            var p = f.properties;
            var name = p.nama_gedung.toLowerCase();
            var addr = p.alamat ? p.alamat.toLowerCase() : '';
            return name.includes(q) || addr.includes(q);
        });

        // Sort: exact matches first, then starts-with, then includes
        hits.sort(function (a, b) {
            var nameA = a.properties.nama_gedung.toLowerCase();
            var nameB = b.properties.nama_gedung.toLowerCase();

            if (nameA === q) return -1;
            if (nameB === q) return 1;
            if (nameA.startsWith(q) && !nameB.startsWith(q)) return -1;
            if (!nameA.startsWith(q) && nameB.startsWith(q)) return 1;
            return 0;
        });

        hits = hits.slice(0, 8);

        sDrop.innerHTML = hits.length
            ? hits.map(function (f, idx) {
                var p = f.properties;
                var isFirst = idx === 0 ? ' style="background:rgba(59,130,246,.12); border-top:1px solid var(--accent-dim); border-bottom:1px solid var(--border);"' : '';
                return '<div class="t-drop-item" data-id="' + p.id + '" data-lat="' + f.geometry.coordinates[1] + '" data-lng="' + f.geometry.coordinates[0] + '"' + isFirst + '>'
                    + '<div class="t-drop-ico"><i class="fas fa-building"></i></div>'
                    + '<div><div class="t-drop-name">' + p.nama_gedung + '</div>'
                    + '<div class="t-drop-sub">' + (p.fungsi !== '-' ? p.fungsi + ' · ' : '') + p.alamat + '</div></div>'
                    + '</div>';
            }).join('')
            : '<div class="t-drop-item"><div class="t-drop-sub" style="padding:4px 0; text-align:center;">Tidak ada hasil untuk "' + this.value + '"</div></div>';

        sDrop.style.display = 'block';
    });

    sDrop.addEventListener('click', function (e) {
        var item = e.target.closest('.t-drop-item[data-id]');
        if (!item) return;
        var id = parseInt(item.dataset.id);
        var lat = parseFloat(item.dataset.lat);
        var lng = parseFloat(item.dataset.lng);

        map.flyTo([lat, lng], 18, { duration: 1.3 });
        setTimeout(function () {
            openSidebar(id);
        }, 1400);

        sDrop.style.display = 'none'; sIn.value = ''; sX.style.display = 'none';
        sMic.style.display = 'flex';
    });

    sX.addEventListener('click', function () {
        sIn.value = ''; sDrop.style.display = 'none'; this.style.display = 'none';
        sMic.style.display = 'flex';
    });

    document.addEventListener('click', function (e) {
        if (!e.target.closest('.t-search')) sDrop.style.display = 'none';
    });

    /* ── TOAST ────────────────────────────────── */
    var toastEl = document.getElementById('toast');
    var toastTimer;
    function toast(msg) {
        clearTimeout(toastTimer);
        toastEl.textContent = msg;
        toastEl.classList.add('show');
        toastTimer = setTimeout(function () { toastEl.classList.remove('show'); }, 2400);
    }

    /* ── MARKER PREVIEW ───────────────────────── */
    var previewEl = document.getElementById('markerPreview');
    var previewHideTimer;
    var isPreviewHovering = false;

    function showMarkerPreview(marker, lat, lng) {
        clearTimeout(previewHideTimer);
        var data = marker.gedungData;

        var imgHtml = data.foto
            ? '<img class="mp-img" src="' + data.foto + '" alt="' + data.nama + '">'
            : '<div class="mp-no-img"><i class="fas fa-building"></i></div>';

        previewEl.innerHTML = ''
            + imgHtml
            + '<div class="mp-body">'
            + '<div class="mp-name">' + data.nama + '</div>'
            + '<div class="mp-addr"><i class="fas fa-map-marker-alt"></i> ' + data.alamat + '</div>'
            + '<div class="mp-btns">'
            + '<button class="mp-btn detail" onclick="openSidebar(' + data.id + ')"><i class="fas fa-info-circle"></i> Detail</button>'
            + '<button class="mp-btn route" onclick="setRoutingDest(' + lat + ',' + lng + ')"><i class="fas fa-directions"></i> Rute</button>'
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
        if (isPreviewHovering) return; // Jangan hide jika user sedang hover preview card

        previewHideTimer = setTimeout(function () {
            previewEl.classList.remove('show');
        }, 200);
    }

    // Prevent preview dari hilang saat user hover preview card
    previewEl.addEventListener('mouseover', function () {
        isPreviewHovering = true;
        clearTimeout(previewHideTimer);
    });

    previewEl.addEventListener('mouseout', function () {
        isPreviewHovering = false;
        hideMarkerPreview();
    });

    /* ── SHOW USER LOCATION (INIT) ────────────── */
    function showUserLocationOnInit() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (pos) {
                var userLat = pos.coords.latitude;
                var userLng = pos.coords.longitude;

                if (userMarker) {
                    map.removeLayer(userMarker);
                }
                userMarker = L.circleMarker([userLat, userLng], {
                    radius: 8,
                    fillColor: '#3b82f6',
                    color: '#fff',
                    weight: 2,
                    opacity: 1,
                    fillOpacity: 0.8
                }).addTo(map).bindPopup('📍 Lokasi Anda Saat Ini', { closeButton: false });
            }, function (err) {
                console.log('Geolocation error:', err);
            });
        }
    }

    // Tampilkan lokasi user saat halaman dimuat
    showUserLocationOnInit();

    /* ── LOAD DATA ────────────────────────────── */
    fetch(window.WEBGIS_URL)
        .then(function (r) { return r.json(); })
        .then(function (gj) {
            allData = gj.features || [];
            renderMarkers(allData);

            if (allData.length) {
                var pts = allData.map(function (f) { return [f.geometry.coordinates[1], f.geometry.coordinates[0]]; });
                // map.fitBounds(L.latLngBounds(pts).pad(0.22));
            }

            // Focus on ?id=
            var uid = new URLSearchParams(window.location.search).get('id');
            if (uid) {
                var t = allData.find(function (f) { return f.properties.id == uid; });
                if (t) map.flyTo([t.geometry.coordinates[1], t.geometry.coordinates[0]], 18, { duration: 1.5 });
            }

            // Hide loading
            var ldr = document.getElementById('loading');
            ldr.classList.add('out');
            setTimeout(function () { ldr.style.display = 'none'; }, 520);

            toast(allData.length + ' gedung dimuat');

            // Load markers after gedung
            loadRuanganMarkers();
            loadVegetasiMarkers();
        })
        .catch(function () {
            document.getElementById('loading').classList.add('out');
            setTimeout(function () { document.getElementById('loading').style.display = 'none'; }, 400);
        });

    /* ══════════════════════════════════════════════════
       RUANGAN MARKERS (Opsi B — sub-marker saat diklik)
    ══════════════════════════════════════════════════ */
    var allRuanganData = [];
    var ruanganMarkerGroup = L.layerGroup().addTo(map);

    var allVegetasiData = [];
    var vegetasiMarkerGroup = L.layerGroup().addTo(map);

    // Warna per kategori ruangan
    var kategoriColors = {
        'Ruang Kelas': '#3b82f6',
        'Post Penjagaan': '#ef4444',
        'Ruang Kuliah Umum': '#8b5cf6',
        'Perpustakaan': '#f59e0b',
        'Kepala Ruangan / Pengurus': '#10b981',
        'Ruangan Sekretariatan / Administrasi': '#6366f1'
    };

    // Emoji/icon per kategori
    var kategoriEmoji = {
        'Ruang Kelas': '🏫',
        'Post Penjagaan': '🛡️',
        'Ruang Kuliah Umum': '🎓',
        'Perpustakaan': '📚',
        'Kepala Ruangan / Pengurus': '👤',
        'Ruangan Sekretariatan / Administrasi': '📋'
    };

    // Buat icon marker ruangan (lebih kecil dari gedung)
    function makeRuanganIcon(kategori) {
        var c = kategoriColors[kategori] || '#94a3b8';
        var svg = '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="30" viewBox="0 0 22 30">'
            + '<defs><filter id="rds"><feDropShadow dx="0" dy="2" stdDeviation="1.5" flood-color="rgba(0,0,0,.4)"/></filter></defs>'
            + '<path filter="url(#rds)" d="M11 1C6.03 1 2 5.03 2 10c0 6.5 9 19 9 19s9-12.5 9-19C20 5.03 15.97 1 11 1z" fill="' + c + '" stroke="rgba(255,255,255,.7)" stroke-width="1.2"/>'
            + '<circle cx="11" cy="10" r="4" fill="rgba(255,255,255,.92)"/>'
            + '<circle cx="11" cy="10" r="2" fill="' + c + '"/>'
            + '</svg>';
        return L.divIcon({ html: svg, className: '', iconSize: [22, 30], iconAnchor: [11, 30], popupAnchor: [0, -32] });
    }

    // Build popup HTML untuk ruangan
    function buildRuanganPopup(p, lat, lng) {
        var c = kategoriColors[p.kategori] || '#94a3b8';
        var emoji = kategoriEmoji[p.kategori] || '📍';
        var statusColor = p.status_dipakai === 'Sedang Dipakai' ? '#22c55e' : '#6c757d';
        var statusText = p.status_dipakai || 'Kosong';

        var imgHtml = p.foto_ruangan
            ? '<img class="rp-img" src="' + p.foto_ruangan + '" alt="' + p.nama_fasilitas + '">'
            : '';

        var keteranganHtml = p.keterangan
            ? '<div class="rp-keterangan">' + p.keterangan + '</div>'
            : '';

        return '<div class="ruangan-popup" id="rpPopup_' + p.id + '">'
            + '<div class="rp-slider step-1" id="rpSlider_' + p.id + '">'

            // PANEL 1: Info Singkat (Overview)
            + '<div class="rp-panel rp-panel-1">'
            + '<div class="rp-header">'
            + '<div class="rp-icon" style="background:' + c + '20; color:' + c + ';">' + emoji + '</div>'
            + '<div class="rp-header-text">'
            + '<div class="rp-name">' + p.nama_fasilitas + '</div>'
            + '<div class="rp-kategori" style="color:' + c + ';">' + p.kategori + '</div>'
            + '</div>'
            + '</div>'
            + '<div class="rp-body">'
            + '<div class="rp-gedung"><i class="fas fa-building"></i> ' + p.nama_gedung + '</div>'
            + imgHtml
            + '<div class="rp-status">'
            + '<div class="rp-status-dot" style="background:' + statusColor + '; box-shadow:0 0 6px ' + statusColor + ';"></div>'
            + '<span class="rp-status-text" style="color:' + statusColor + ';">' + statusText + '</span>'
            + '</div>'
            + '<button class="rp-btn-detail" onclick="goToStep(' + p.id + ', 2)"><i class="fas fa-info-circle"></i> Lihat Fasilitas</button>'
            + '</div>' // rp-body
            + '</div>' // rp-panel-1

            // PANEL 2: Detail Lengkap
            + '<div class="rp-panel rp-panel-2">'
            + '<div class="rp-detail-header">'
            + '<button class="rp-btn-back" onclick="goToStep(' + p.id + ', 1)"><i class="fas fa-arrow-left"></i></button>'
            + '<div class="rp-detail-title">Lihat Fasilitas</div>'
            + '</div>'
            + '<div class="rp-detail-body">'
            + '<div class="rp-detail-card">'
            + '<div class="rp-detail-label">Keterangan</div>'
            + (p.keterangan ? '<div class="rp-detail-value">' + p.keterangan + '</div>' : '<div class="rp-detail-value text-muted">-</div>')
            + '</div>'
            + '<div class="rp-detail-card">'
            + '<div class="rp-detail-label">Status Saat Ini</div>'
            + '<div class="rp-detail-value" style="color:' + statusColor + '; font-weight:700;">' + statusText + '</div>'
            + '</div>'
            + '<button class="rp-btn-jadwal mt-3" onclick="goToStep(' + p.id + ', 3)"><i class="fas fa-images"></i> Lihat Galeri Foto</button>'
            + '</div>' // rp-detail-body
            + '</div>' // rp-panel-2

            // PANEL 3: Galeri Foto
            + '<div class="rp-panel rp-panel-3">'
            + '<div class="rp-detail-header">'
            + '<button class="rp-btn-back" onclick="goToStep(' + p.id + ', 2)"><i class="fas fa-arrow-left"></i></button>'
            + '<div class="rp-detail-title">Galeri Foto</div>'
            + '</div>'
            + '<div class="rp-detail-body" id="rpDetailBody_' + p.id + '">'
            + '<div class="rp-carousel" id="rpCarousel_' + p.id + '">'
            + (p.foto_ruangan
                ? '<img class="rp-carousel-slide active" src="' + p.foto_ruangan + '" onclick="openLightbox(\'' + p.foto_ruangan + '\')">'
                : '')
            + '</div>'
            + '<div class="rp-carousel-nav">'
            + '<button class="rp-carousel-btn" onclick="carouselNav(' + p.id + ', -1)"><i class="fas fa-chevron-left"></i></button>'
            + '<span class="rp-carousel-counter" id="rpCounter_' + p.id + '">1 / ' + (p.foto_ruangan ? '1' : '0') + '</span>'
            + '<button class="rp-carousel-btn" onclick="carouselNav(' + p.id + ', 1)"><i class="fas fa-chevron-right"></i></button>'
            + '</div>'
            + '<div style="text-align:center; font-size:0.7rem; color:var(--muted); margin-top:6px;"><i class="fas fa-hand-pointer"></i> Klik foto untuk memperbesar</div>'
            + '</div>'
            + '</div>' // rp-detail-body
            + '</div>' // rp-panel-3

            + '</div>' // rp-slider
            + '</div>'; // ruangan-popup
    }

    // Navigasi Slider Popup (3 Steps)
    window.goToStep = function (id, step) {
        var slider = document.getElementById('rpSlider_' + id);
        if (!slider) return;

        // Update class for slide animation
        slider.className = 'rp-slider step-' + step;
    };

    // Carousel navigation for gallery photos
    window.carouselNav = function (id, direction) {
        var carousel = document.getElementById('rpCarousel_' + id);
        if (!carousel) return;
        var slides = carousel.querySelectorAll('.rp-carousel-slide');
        if (slides.length === 0) return;

        var currentIdx = -1;
        slides.forEach(function (s, i) { if (s.classList.contains('active')) currentIdx = i; });
        if (currentIdx === -1) currentIdx = 0;

        // Remove active from current
        slides[currentIdx].classList.remove('active');

        // Calculate new index (wrap around)
        var newIdx = (currentIdx + direction + slides.length) % slides.length;
        slides[newIdx].classList.add('active');

        // Update counter
        var counter = document.getElementById('rpCounter_' + id);
        if (counter) counter.textContent = (newIdx + 1) + ' / ' + slides.length;
    };

    // Sidebar main gallery carousel navigation
    window.sbMainGalleryNav = function (direction) {
        var container = document.getElementById('sbMainSlides');
        if (!container) return;
        var slides = container.querySelectorAll('.sb-main-slide');
        if (slides.length === 0) return;

        var currentIdx = -1;
        slides.forEach(function (s, i) { if (s.classList.contains('active')) currentIdx = i; });
        if (currentIdx === -1) currentIdx = 0;

        slides[currentIdx].classList.remove('active');
        var newIdx = (currentIdx + direction + slides.length) % slides.length;
        slides[newIdx].classList.add('active');

        var counter = document.getElementById('sbMainCounter');
        if (counter) counter.textContent = (newIdx + 1) + ' / ' + slides.length;
    };

    // Expand/Collapse fasilitas list in sidebar
    window.toggleFasilitasExpand = function () {
        var list = document.getElementById('sbFasilitasList');
        var btn = document.getElementById('sbFasExpandBtn');
        if (!list || !btn) return;

        var items = list.querySelectorAll('.sb-fas-item');
        var isExpanded = btn.dataset.expanded === 'true';

        items.forEach(function (item, idx) {
            if (idx >= 3) {
                item.style.display = isExpanded ? 'none' : 'block';
            }
        });

        if (isExpanded) {
            btn.innerHTML = '<i class="fas fa-chevron-down"></i> Lihat Semua (' + items.length + ')';
            btn.dataset.expanded = 'false';
        } else {
            btn.innerHTML = '<i class="fas fa-chevron-up"></i> Sembunyikan';
            btn.dataset.expanded = 'true';
        }
    };

    // Global variable to store current fetched jadwal
    var currentJadwalGedung = [];

    // Tampilkan data ke sidebar
    function populateSidebar(data) {
        var sbDesc = document.getElementById('sbDesc');
        sbDesc.innerHTML = data.gedung.deskripsi
            ? data.gedung.deskripsi.replace(/\n/g, '<br>')
            : 'Belum ada deskripsi untuk gedung ini.';

        var gId = data.gedung.id;

        // Fetch jadwal semester untuk gedung dan setting aktif
        Promise.all([
            fetch('/api/gedung/' + gId + '/jadwal-semester').then(res => res.json()),
            fetch('/api/semester-aktif').then(res => res.json())
        ])
            .then(([resJadwal, resSetting]) => {
                var sbJadwal = document.getElementById('sbJadwalSemester');
                var sbJadwalAktifText = document.getElementById('sbJadwalAktifText');
                
                if (resJadwal.success && resJadwal.data.length > 0) {
                    currentJadwalGedung = resJadwal.data;
                    sbJadwal.style.display = 'block';

                    var activeType = resSetting.semester_aktif || 'genap'; // ganjil / genap
                    var taAktif = resSetting.tahun_ajaran_aktif || '-';
                    
                    // Set badge text
                    var labelSemester = activeType === 'ganjil' ? 'Ganjil' : 'Genap';
                    if (sbJadwalAktifText) {
                        sbJadwalAktifText.textContent = 'Semester ' + labelSemester + ' (' + taAktif + ') Aktif';
                    }

                    toggleJadwalSemester(activeType);
                } else {
                    currentJadwalGedung = [];
                    sbJadwal.style.display = 'none';
                }
            })
            .catch(err => {
                console.error(err);
                document.getElementById('sbJadwalSemester').style.display = 'none';
            });
    }

    // Toggle Jadwal Ganjil / Genap
    window.toggleJadwalSemester = function (type) {
        var ganjilSemesters = [1, 3, 5, 7];
        var genapSemesters = [2, 4, 6, 8];

        var activeSemesters = type === 'ganjil' ? ganjilSemesters : genapSemesters;

        // Filter jadwal yang sesuai tipe ganjil/genap
        var filteredJadwal = currentJadwalGedung.filter(function (j) {
            return activeSemesters.includes(j.semester);
        });

        renderSidebarJadwal(filteredJadwal, type);
    };

    function renderSidebarJadwal(jadwals, type) {
        var tabsContainer = document.getElementById('sbJadwalTabs');
        var viewer = document.getElementById('sbJadwalViewer');

        if (!jadwals || jadwals.length === 0) {
            tabsContainer.innerHTML = '';
            viewer.innerHTML = '<div style="text-align:center; padding:30px 15px; color:#64748b; background:rgba(255,255,255,.02); border:1px dashed var(--border); border-radius:8px;">'
                + '<i class="fas fa-calendar-times" style="font-size:24px; margin-bottom:10px; display:block;"></i>'
                + 'Belum ada jadwal semester ' + type + '.'
                + '<div style="font-size:0.7rem; margin-top:8px;">Coba klik toggle lainnya</div>'
                + '</div>';
            return;
        }

        // Group jadwals by semester number
        var semesterMap = {};
        jadwals.forEach(function (j) {
            if (!semesterMap[j.semester]) semesterMap[j.semester] = [];
            semesterMap[j.semester].push(j);
        });

        // Sort each group by tahun_ajaran descending (newest first)
        Object.keys(semesterMap).forEach(function (sem) {
            semesterMap[sem].sort(function (a, b) {
                return (b.tahun_ajaran || '').localeCompare(a.tahun_ajaran || '');
            });
        });

        var semesterKeys = Object.keys(semesterMap).sort(function (a, b) { return a - b; });

        // Render Tabs (one per unique semester)
        var htmlTabs = '';
        semesterKeys.forEach(function (sem, idx) {
            var active = idx === 0 ? ' active' : '';
            htmlTabs += '<button class="rp-sem-tab' + active + '" data-sem="' + sem + '" onclick="switchJadwalSemTab(' + sem + ', this)">'
                + 'Sem ' + sem
                + '</button>';
        });
        tabsContainer.innerHTML = htmlTabs;

        // Store semesterMap globally for dropdown interaction
        window._currentSemMap = semesterMap;

        // Auto-select first semester tab
        if (semesterKeys.length > 0) {
            populateJadwalDropdown(semesterKeys[0]);
        }
    }

    // Populate dropdown for a specific semester
    function populateJadwalDropdown(semNum) {
        var jadwals = window._currentSemMap[semNum] || [];

        if (jadwals.length === 0) {
            document.getElementById('sbJadwalViewer').innerHTML = '<div style="text-align:center; padding:20px; color:var(--muted); font-size:0.8rem;">Tidak ada jadwal untuk semester ini.</div>';
            return;
        }

        // Auto-render the first (newest) jadwal
        renderJadwalViewer(jadwals[0]);
    }

    // Render single jadwal preview
    function renderJadwalViewer(jadwal) {
        var viewer = document.getElementById('sbJadwalViewer');
        if (!jadwal) {
            viewer.innerHTML = '';
            return;
        }

        var ext = jadwal.file_jadwal.split('.').pop().toLowerCase();
        var isPdf = ext === 'pdf';
        var fileUrl = '/' + jadwal.file_jadwal;

        var html = '';
        if (isPdf) {
            html += '<div style="padding:30px; background:rgba(0,0,0,.15); border-radius:8px; text-align:center; border:1px solid var(--border);">'
                + '<i class="fas fa-file-pdf" style="font-size:36px; color:#ef4444; margin-bottom:10px;"></i>'
                + '<div style="font-size:0.8rem; color:var(--muted);">Format PDF</div>'
                + '</div>';
        } else {
            html += '<img src="' + fileUrl + '" onclick="openLightbox(\'' + fileUrl + '\')" style="width:100%; border-radius:8px; border:1px solid var(--border); cursor:zoom-in;">';
        }

        html += '<div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-top:10px;">'
            + '<button onclick="openLightbox(\'' + fileUrl + '\')" style="padding:8px; background:rgba(255,255,255,.05); border:1px solid var(--border); border-radius:6px; color:var(--text); font-size:0.78rem; font-weight:600; cursor:pointer; font-family:inherit;"><i class="fas fa-search-plus"></i> Perbesar</button>'
            + '<a href="' + fileUrl + '" target="_blank" style="display:block; text-align:center; padding:8px; background:var(--accent); color:#fff; border-radius:6px; font-size:0.78rem; font-weight:700; text-decoration:none;"><i class="fas fa-download"></i> Download</a>'
            + '</div>';

        viewer.innerHTML = html;
    }

    // Tab switching
    window.switchJadwalSemTab = function (semNum, btnEl) {
        var tabs = document.getElementById('sbJadwalTabs').querySelectorAll('.rp-sem-tab');
        tabs.forEach(function (t) { t.classList.remove('active'); });
        btnEl.classList.add('active');
        populateJadwalDropdown(semNum);
    };

    // Dropdown change dihapus karena sudah tidak digunakan

    // Lightbox global
    window.openLightbox = function (src) {
        var lb = document.getElementById('rkLightbox');
        if (!lb) {
            var div = document.createElement('div');
            div.id = 'rkLightbox';
            div.className = 'rk-lightbox';
            div.innerHTML = '<button class="rk-lightbox-close" onclick="document.getElementById(\'rkLightbox\').classList.remove(\'show\')"><i class="fas fa-times"></i></button>'
                + '<img id="rkLightboxImg" src="">';
            document.body.appendChild(div);
            lb = div;
        }
        document.getElementById('rkLightboxImg').src = src;
        lb.classList.add('show');
    };

    // Render marker ruangan
    function renderRuanganMarkers(data) {
        ruanganMarkerGroup.clearLayers();

        data.forEach(function (f) {
            var lat = f.geometry.coordinates[1];
            var lng = f.geometry.coordinates[0];
            var p = f.properties;

            var m = L.marker([lat, lng], { icon: makeRuanganIcon(p.kategori), title: p.nama_fasilitas });
            m.ruanganId = p.id;

            m.bindPopup(buildRuanganPopup(p, lat, lng), { maxWidth: 280, closeButton: true });

            // Tampilkan tooltip hanya saat hover
            m.bindTooltip(p.nama_fasilitas, {
                direction: 'top',
                offset: [0, -30],
                className: 'ruangan-tooltip'
            });

            ruanganMarkerGroup.addLayer(m);
        });
    }

    // Load data ruangan
    function loadRuanganMarkers() {
        if (!window.WEBGIS_RUANGAN_URL) return;

        fetch(window.WEBGIS_RUANGAN_URL)
            .then(function (r) { return r.json(); })
            .then(function (gj) {
                allRuanganData = gj.features || [];
                renderRuanganMarkers(allRuanganData);
                updateZoomLayers(); // Apply zoom visibility
            })
            .catch(function (err) {
                console.error('Gagal memuat data ruangan:', err);
            });
    }

    // Fungsi tambahan untuk merender ruangan milik 1 gedung tertentu
    window.showRuanganForGedung = function (gedungId) {
        var filteredData = allRuanganData.filter(function (f) {
            return f.properties.gedung_id == gedungId;
        });
        renderRuanganMarkers(filteredData);
    };

    /* ── LEAFLET ROUTING MACHINE ───────────────── */
    var routeProfile = 'car';
    var routingControl = null;
    var currentWaypoints = [];
    var currentCoordinates = { start: null, end: null };
    var userMarker = null; // Ini untuk marker Start (titik biru)
    var routePoints = [];      // Untuk tracking klik user
    var routesData = [];         // Semua data route dari OSRM
    var routeLayers = [];        // Semua polyline layer yang digambar
    var routeLabels = [];        // Tooltip label pada rute (misal: +5 mnt)
    var selectedRouteIndex = 0;  // Index route yang sedang aktif
    var animationFrameId = null; // ID untuk requestAnimationFrame
    var animatingMarker = null;  // Marker yang bergerak

    // Profile mapping
    var profileMap = {
        'car': 'driving',
        'bike': 'driving', // Motor di Indonesia menggunakan rute mobil (OSRM driving)
        'foot': 'walking'
    };

    // Kecepatan rata-rata (km/jam) untuk estimasi waktu ala Google Maps
    var averageSpeeds = {
        'car': 30,    // Rata-rata mobil di perkotaan (incl. traffic ringan)
        'bike': 40,   // Rata-rata motor di perkotaan (lebih lincah)
        'foot': 5     // Rata-rata jalan kaki (standard Google Maps)
    };

    function calculateEstimatedTime(distanceInMeters, mode) {
        var speedKmh = averageSpeeds[mode] || 30;
        var timeInHours = (distanceInMeters / 1000) / speedKmh;
        var timeInMinutes = Math.round(timeInHours * 60);
        
        // Minimal 1 menit jika jarak sangat dekat
        return Math.max(1, timeInMinutes);
    }

    function formatDuration(minutes) {
        if (minutes < 60) return minutes + ' mnt';
        
        var d = Math.floor(minutes / 1440);
        var h = Math.floor((minutes % 1440) / 60);
        var m = minutes % 60;
        
        if (d > 0) {
            var res = d + ' hari';
            if (h > 0) res += ' ' + h + ' jam';
            return res;
        }
        
        var res = h + ' jam';
        if (m > 0) res += ' ' + m + ' mnt';
        return res;
    }

    // Icons
    var startIcon = L.divIcon({
        className: 'custom-div-icon',
        html: '<div class="marker-start-dot"></div>',
        iconSize: [20, 20],
        iconAnchor: [10, 10]
    });

    var endIcon = L.divIcon({
        className: 'custom-div-icon',
        html: '<div class="marker-end-pin"><i class="fas fa-map-marker-alt" style="font-size: 26px;"></i></div>',
        iconSize: [30, 30],
        iconAnchor: [15, 30]
    });

    function createRoutingControl(profile, waypoints) {
        var osrmProfile = profileMap[profile || routeProfile] || 'driving';

        if (routingControl) {
            map.removeControl(routingControl);
            routingControl = null;
        }

        var control = L.Routing.control({
            waypoints: waypoints || [],
            show: false,
            showAlternatives: true,
            addWaypoints: false,
            draggableWaypoints: false,
            routeWhileDragging: false,
            fitSelectedRoutes: true,
            router: L.Routing.osrmv1({
                serviceUrl: 'https://router.project-osrm.org/route/v1',
                profile: (osrmProfile === 'foot' ? 'walking' : 'driving'),
                timeout: 15000,
                alternatives: true, // Beritahu LRM bahwa kita mau alternatif
                urlParameters: {
                    alternatives: '3', // Minta hingga 3 rute ke server OSRM
                    steps: 'true',
                    overview: 'full',
                    continue_straight: 'false'
                },
                useHints: false
            }),
            // Matikan renderer bawaan LRM – kita gambar polyline sendiri
            lineOptions: { addWaypoints: false, styles: [] },
            createMarker: function (i, wp) {
                if (i === 0) return L.marker(wp.latLng, { icon: startIcon });
                return L.marker(wp.latLng, { icon: endIcon });
            },
            // Minta semua alternatif route dari OSRM
            plan: L.Routing.plan(waypoints || [], { createMarker: function(i, wp) {
                if (i === 0) return L.marker(wp.latLng, { icon: startIcon });
                return L.marker(wp.latLng, { icon: endIcon });
            }, routeWhileDragging: false })
        }).addTo(map);

        control.on('routesfound', onRouteFound);
        control.on('routingerror', function() {
            toast('Rute tidak ditemukan');
            toggleNavPanel();
        });

        return control;
    }

    function onRouteFound(e) {
        console.log("Jumlah rute ditemukan:", e.routes.length);
        // Hapus polyline & label lama
        routeLayers.forEach(function(layer) { if(layer) map.removeLayer(layer); });
        routeLabels.forEach(function(label) { if(label) map.removeLayer(label); });
        routeLayers = [];
        routeLabels = [];

        // Simpan maksimal 2 rute (Tercepat & Alternatif)
        routesData = e.routes.slice(0, 2);
        selectedRouteIndex = 0;
        
        if (routesData.length < 2) {
            toast('Rute terbaik ditemukan. Tidak ada jalur alternatif yang signifikan.');
        } else {
            toast('Ditemukan ' + routesData.length + ' opsi rute untuk perbandingan.');
        }
        
        var mainDuration = calculateEstimatedTime(routesData[0].summary.totalDistance, routeProfile);

        // ── Gambar rute alternatif dulu agar rute utama di atas ──
        var drawOrder = routesData.length > 1 ? [1, 0] : [0];

        var routeListHtml = '';
        if (routesData.length < 2) {
            routeListHtml = '<div class="nav-route-item active" data-idx="0">'
                + '<span class="nav-route-item-title">Rute Tunggal</span>'
                + '<span class="nav-route-item-time">' + formatDuration(calculateEstimatedTime(routesData[0].summary.totalDistance, routeProfile)) + '</span>'
                + '</div>';
        } else {
            drawOrder.forEach(function(idx) {
                var route = routesData[idx];
                var duration = calculateEstimatedTime(route.summary.totalDistance, routeProfile);
                var isActive = (idx === selectedRouteIndex);
                var title = idx === 0 ? 'Tercepat' : 'Alternatif';
                
                routeListHtml += '<div class="nav-route-item ' + (isActive ? 'active' : '') + '" data-idx="' + idx + '" onclick="selectRoute(' + idx + ')">'
                    + '<span class="nav-route-item-title">' + title + '</span>'
                    + '<span class="nav-route-item-time">' + formatDuration(duration) + '</span>'
                    + '</div>';
            });
        }
        document.getElementById('navRouteList').innerHTML = routeListHtml;

        drawOrder.forEach(function(idx) {
            var route = routesData[idx];
            var isActive = (idx === selectedRouteIndex);

            var polyline = L.polyline(route.coordinates, {
                color:   isActive ? '#22c55e' : '#3b82f6', // Gunakan Biru untuk rute alternatif agar kontras
                weight:  isActive ? 12 : 9,               // Lebih tebal lagi
                opacity: isActive ? 1  : 0.5,
                lineCap:  'round',
                lineJoin: 'round',
                interactive: true,
                bubblingMouseEvents: false
            }).addTo(map);

            polyline._routeIndex = idx;

            // Tambahkan Label untuk rute
            var duration = calculateEstimatedTime(route.summary.totalDistance, routeProfile);
            var labelText = "";
            
            if (idx === 0) {
                labelText = '<b>' + formatDuration(duration) + '</b> (Tercepat)';
            } else {
                var diff = duration - mainDuration;
                var diffText = diff > 0 ? "+" + formatDuration(diff) : (diff < 0 ? formatDuration(diff) : "Sama");
                labelText = '<span style="color:#1a73e8"><b>' + formatDuration(duration) + '</b> (' + diffText + ')</span>';
            }
            
            // Titik tengah rute untuk label (geser sedikit jika rute tumpang tindih)
            var coordIdx = Math.floor(route.coordinates.length * (0.3 + (idx * 0.2)));
            var labelPoint = route.coordinates[coordIdx];
            var label = L.tooltip({
                permanent: true,
                direction: 'center',
                className: 'route-label'
            })
            .setContent(labelText)
            .setLatLng(labelPoint)
            .addTo(map);
            
            // Buat label bisa diklik
            label.on('add', function() {
                var el = this.getElement();
                if (el) {
                    el.addEventListener('click', function(ev) {
                        ev.stopPropagation();
                        selectRoute(idx);
                    });
                }
            });
            
            routeLabels[idx] = label;

            if (isActive) {
                requestAnimationFrame(function() {
                    var el = polyline.getElement();
                    if (el) el.classList.add('route-active');
                });
            }

            polyline.on('click', function(ev) { 
                L.DomEvent.stopPropagation(ev);
                selectRoute(idx); 
            });

            polyline.on('mouseover', function() {
                if (idx !== selectedRouteIndex) {
                    this.setStyle({ color: '#6b7280', weight: 7, opacity: 0.85 });
                }
            });
            polyline.on('mouseout', function() {
                if (idx !== selectedRouteIndex) {
                    this.setStyle({ color: '#9ca3af', weight: 5, opacity: 0.6 });
                }
            });

            routeLayers[idx] = polyline;
        });

        // Update panel dengan route utama
        updateRoutePanel(selectedRouteIndex);
        showNavPanel();
        animateRoute(selectedRouteIndex);
        document.getElementById('btnResetRoute').style.display = 'flex';

        // Bounding box gabungan semua rute
        var bounds = routeLayers[0].getBounds();
        routeLayers.forEach(function(l) { if (l) bounds.extend(l.getBounds()); });
        map.fitBounds(bounds.pad(0.12));
    }

    function animateRoute(index) {
        var route = routesData[index];
        if (!route || !route.coordinates) return;

        // Bersihkan animasi sebelumnya
        if (animatingMarker) {
            map.removeLayer(animatingMarker);
            animatingMarker = null;
        }
        if (animationFrameId) {
            cancelAnimationFrame(animationFrameId);
            animationFrameId = null;
        }

        var coords = route.coordinates;
        var mode = routeProfile; // car, bike, foot

        // Tentukan icon berdasarkan mode
        var iconHtml = '';
        if (mode === 'foot') iconHtml = '<div class="route-anim-marker"><i class="fas fa-walking"></i></div>';
        else if (mode === 'bike') iconHtml = '<div class="route-anim-marker"><i class="fas fa-motorcycle"></i></div>';
        else iconHtml = '<div class="route-anim-marker"><i class="fas fa-car"></i></div>';

        var animatedIcon = L.divIcon({
            className: 'route-anim-icon-wrap',
            html: iconHtml,
            iconSize: [40, 40],
            iconAnchor: [20, 20]
        });

        animatingMarker = L.marker(coords[0], { 
            icon: animatedIcon, 
            zIndexOffset: 2000,
            interactive: false 
        }).addTo(map);

        var startTime = null;
        var duration = 3000; // Durasi total animasi (3 detik)

        // Hitung jarak total untuk pergerakan konstan
        var totalDist = 0;
        var segments = [];
        for (var i = 0; i < coords.length - 1; i++) {
            var d = map.distance(coords[i], coords[i+1]);
            totalDist += d;
            segments.push(d);
        }

        function step(timestamp) {
            if (!startTime) startTime = timestamp;
            var elapsed = timestamp - startTime;
            var progress = Math.min(elapsed / duration, 1);

            // Easing function (easeInOutQuad) untuk kesan lebih premium
            var easedProgress = progress < 0.5 ? 2 * progress * progress : 1 - Math.pow(-2 * progress + 2, 2) / 2;
            
            var targetDist = totalDist * easedProgress;
            var currentDist = 0;
            var pos = coords[0];

            for (var i = 0; i < segments.length; i++) {
                if (currentDist + segments[i] >= targetDist) {
                    var segProgress = (targetDist - currentDist) / segments[i];
                    var p1 = coords[i];
                    var p2 = coords[i+1];
                    pos = L.latLng(
                        p1.lat + (p2.lat - p1.lat) * segProgress,
                        p1.lng + (p2.lng - p1.lng) * segProgress
                    );
                    
                    break;
                }
                currentDist += segments[i];
                if (i === segments.length - 1) pos = coords[coords.length - 1];
            }

            animatingMarker.setLatLng(pos);

            if (progress < 1) {
                animationFrameId = requestAnimationFrame(step);
            } else {
                // Selesai, hapus marker dengan fade out
                var el = animatingMarker.getElement();
                if (el) el.style.opacity = '0';
                setTimeout(function() {
                    if (animatingMarker) {
                        map.removeLayer(animatingMarker);
                        animatingMarker = null;
                    }
                }, 500);
            }
        }

        animationFrameId = requestAnimationFrame(step);
    }

    window.selectRoute = function(index) {
        if (index < 0 || index >= routeLayers.length) return;
        selectedRouteIndex = index;
        var currentDuration = calculateEstimatedTime(routesData[index].summary.totalDistance, routeProfile);
        var mainDuration = calculateEstimatedTime(routesData[0].summary.totalDistance, routeProfile);

        // Update UI List di Panel
        document.querySelectorAll('.nav-route-item').forEach((item) => {
            var itemIdx = parseInt(item.getAttribute('data-idx'));
            item.classList.toggle('active', itemIdx === index);
        });

        routeLayers.forEach(function(layer, i) {
            if (!layer) return;

            var el = layer.getElement ? layer.getElement() : null;
            if (el) el.classList.remove('route-active');

            if (i === index) {
                layer.setStyle({ color: '#22c55e', weight: 12, opacity: 1 });
                layer.bringToFront();
                requestAnimationFrame(function() {
                    var elNow = layer.getElement();
                    if (elNow) elNow.classList.add('route-active');
                });
            } else {
                layer.setStyle({ color: '#3b82f6', weight: 9, opacity: 0.5 });
            }

            // Update Label Konten
            if (routeLabels[i]) {
                var duration = calculateEstimatedTime(routesData[i].summary.totalDistance, routeProfile);
                var labelText = "";
                
                if (i === 0) {
                    labelText = '<b>' + formatDuration(duration) + '</b> (Tercepat)';
                } else {
                    var diff = duration - currentDuration;
                    var diffText = diff > 0 ? "+" + formatDuration(diff) : (diff < 0 ? formatDuration(diff) : "Sama");
                    labelText = '<b>' + formatDuration(duration) + '</b> (' + (i === index ? 'Terpilih' : diffText) + ')';
                }
                routeLabels[i].setContent(labelText);
            }
        });

        updateRoutePanel(index);
        
        // Jalankan animasi marker bergerak
        animateRoute(index);
    };

    function updateRoutePanel(index) {
        var route = routesData[index];
        if (!route) return;

        var summary = route.summary;
        var instructions = route.instructions;

        // Update Summary UI
        var durationMinutes = calculateEstimatedTime(summary.totalDistance, routeProfile);
        document.getElementById('navTime').textContent = formatDuration(durationMinutes);
        document.getElementById('navDist').textContent = formatDistance(summary.totalDistance);

        // Ambil nama jalan utama dari instruksi
        var mainStreet = 'Jalan Utama';
        for (var i = 0; i < instructions.length; i++) {
            if (instructions[i].road) { mainStreet = instructions[i].road; break; }
        }
        document.getElementById('navStreet').textContent = 'Lewat ' + mainStreet;

        // Traffic simulation (Simulasi lalu lintas ala Google Maps)
        var trafficEl = document.getElementById('navTraffic');
        var trafficChance = 0;
        if (routeProfile === 'car') trafficChance = 0.4; // 40% kemungkinan padat
        else if (routeProfile === 'bike') trafficChance = 0.15; // 15% kemungkinan padat untuk motor

        if (routeProfile !== 'foot' && Math.random() < trafficChance && durationMinutes > 2) {
            trafficEl.textContent = 'Lalu lintas padat';
            trafficEl.className = 'nav-traffic-slow';
            // Tambahkan sedikit waktu ekstra pada UI jika macet
            var extra = Math.ceil(durationMinutes * 0.2);
            document.getElementById('navTime').textContent = formatDuration(durationMinutes + extra);
        } else {
            trafficEl.textContent = 'Lalu lintas lancar';
            trafficEl.className = 'nav-traffic-fast';
        }

        // Parse Instructions
        var stepsHtml = '';
        instructions.forEach(function(instr) {
            var icon = getStepIcon(instr.type);
            stepsHtml += '<div class="nav-step">'
                + '<div class="nav-step-icon"><i class="' + icon + '"></i></div>'
                + '<div class="nav-step-text">'
                + '<div>' + instr.text + '</div>'
                + '<div class="nav-step-dist">' + formatDistance(instr.distance) + '</div>'
                + '</div></div>';
        });
        document.getElementById('navSteps').innerHTML = stepsHtml;
    }

    function getStepIcon(type) {
        var icons = {
            'Straight': 'fas fa-arrow-up',
            'SlightRight': 'fas fa-location-arrow fa-rotate-45',
            'Right': 'fas fa-reply fa-rotate-180',
            'SharpRight': 'fas fa-redo',
            'TurnAround': 'fas fa-sync-alt',
            'SharpLeft': 'fas fa-undo',
            'Left': 'fas fa-share',
            'SlightLeft': 'fas fa-location-arrow fa-rotate-315',
            'WaypointReached': 'fas fa-map-marker-alt',
            'Roundabout': 'fas fa-not-equal', // atau ikon bundaran lain
            'StartAt': 'fas fa-play',
            'DestinationReached': 'fas fa-flag-checkered',
            'EnterAgainstAllowedDirection': 'fas fa-exclamation-triangle',
            'LeaveAgainstAllowedDirection': 'fas fa-exclamation-triangle'
        };
        return icons[type] || 'fas fa-arrow-up';
    }

    function formatDistance(m) {
        return m >= 1000 ? (m / 1000).toFixed(1) + ' km' : Math.round(m) + ' m';
    }

    function showNavPanel() {
        var p = document.getElementById('navPanel');
        p.classList.remove('hide');
    }

    window.toggleNavPanel = function() {
        document.getElementById('navPanel').classList.add('hide');
        
        // Reset Routing
        if (routingControl) routingControl.setWaypoints([]);
        
        // Hapus semua polyline & label route yang digambar manual
        routeLayers.forEach(function(layer) { if(layer) map.removeLayer(layer); });
        routeLabels.forEach(function(label) { if(label) map.removeLayer(label); });
        
        routeLayers = [];
        routeLabels = [];
        routesData = [];
        
        if (userMarker) map.removeLayer(userMarker);
        document.getElementById('btnResetRoute').style.display = 'none';
        currentCoordinates = { start: null, end: null };
        routePoints = [];
        
        toast('Rute ditutup dan direset');
    };

    // Tombol Detail Toggle
    document.getElementById('btnNavDetail').addEventListener('click', function() {
        var container = document.getElementById('navSteps');
        var isVisible = container.style.display === 'block';
        container.style.display = isVisible ? 'none' : 'block';
        this.innerHTML = isVisible ? '<i class="fas fa-list-ul"></i> Detail' : '<i class="fas fa-chevron-up"></i> Sembunyikan';
    });

    // Tombol Pratinjau
    document.getElementById('btnNavPreview').addEventListener('click', function() {
        if (routeLayers[selectedRouteIndex]) {
            map.fitBounds(routeLayers[selectedRouteIndex].getBounds(), { padding: [50, 50] });
        }
    });

    // Tombol Reset Rute (Topbar)
    document.getElementById('btnResetRoute').addEventListener('click', function () {
        toggleNavPanel();
    });

    routingControl = createRoutingControl(routeProfile);

    // KLIK 2 TITIK UNTUK RUTE
    map.on('click', function (e) {
        // Abaikan jika sedang klik marker atau UI
        if (e.originalEvent.target.closest('.leaflet-marker-icon') || 
            e.originalEvent.target.closest('#navPanel') ||
            e.originalEvent.target.closest('#topbar')) return;

        routePoints.push(e.latlng);

        if (routePoints.length === 1) {
            toast('Titik awal ditentukan. Pilih titik tujuan...');
            // Tampilkan marker sementara
            if (userMarker) map.removeLayer(userMarker);
            userMarker = L.marker(e.latlng, { icon: startIcon }).addTo(map);
        } 
        else if (routePoints.length === 2) {
            if (userMarker) map.removeLayer(userMarker);
            
            currentWaypoints = [
                L.latLng(routePoints[0].lat, routePoints[0].lng),
                L.latLng(routePoints[1].lat, routePoints[1].lng)
            ];
            
            currentCoordinates.start = routePoints[0];
            currentCoordinates.end = routePoints[1];

            routingControl.setWaypoints(currentWaypoints);
            routePoints = []; // Reset for next pair
        }
    });

    // Fungsi Rute ke Sini (dari Sidebar)
    window.setRoutingDest = function (lat, lng) {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (pos) {
                var start = L.latLng(pos.coords.latitude, pos.coords.longitude);
                var end = L.latLng(lat, lng);
                
                currentCoordinates.start = start;
                currentCoordinates.end = end;
                
                routingControl.setWaypoints([start, end]);
                toast('Menghitung rute dari lokasi Anda...');
            }, function() {
                toast('Gagal akses lokasi. Klik 2 titik di peta untuk rute.');
            });
        }
    };

    window.changeRouteMode = function (mode) {
        routeProfile = mode;
        
        // Update UI Active State
        document.querySelectorAll('.nav-mode-btn').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.mode === mode);
        });

        if (currentCoordinates.start && currentCoordinates.end) {
            // Hapus control lama agar marker tidak menumpuk
            if (routingControl) map.removeControl(routingControl);

            routingControl = createRoutingControl(mode, [
                L.latLng(currentCoordinates.start.lat, currentCoordinates.start.lng),
                L.latLng(currentCoordinates.end.lat, currentCoordinates.end.lng)
            ]);
        }
    };

    /* ── SIDEBAR ──────────────────────────────── */
    var sidebar = document.getElementById('sidebar');
    var sbClose = document.getElementById('sbClose');
    var sbLoading = document.getElementById('sbLoading');
    var sbContent = document.getElementById('sbContent');
    var currentGedungLat = null;
    var currentGedungLng = null;

    sbClose.addEventListener('click', function () {
        sidebar.classList.remove('show');
    });

    document.getElementById('sbBtnRoute').addEventListener('click', function () {
        if (currentGedungLat && currentGedungLng) {
            setRoutingDest(currentGedungLat, currentGedungLng);
            sidebar.classList.remove('show');
            toast('Rute sedang dihitung...');
        }
    });

    window.openSidebar = function (id) {
        sidebar.classList.add('show');
        sbLoading.style.display = 'block';
        sbContent.style.display = 'none';
        sbMainIdx = 0; // Reset index carousel

        // Ruangan markers dikelola otomatis oleh zoom level

        // Cari data gedung dari allData untuk mendapat koordinat
        var gedungData = allData.find(function (f) { return f.properties.id == id; });
        if (gedungData) {
            currentGedungLat = gedungData.geometry.coordinates[1];
            currentGedungLng = gedungData.geometry.coordinates[0];
        }

        fetch('/api/gedung/' + id)
            .then(function (r) { return r.json(); })
            .then(function (data) {
                sbLoading.style.display = 'none';
                sbContent.style.display = 'block';

                var p = data.gedung;

                // Kumpulkan semua foto (utama + galeri)
                var allPhotos = [];
                if (data.foto_utama) {
                    allPhotos.push(data.foto_utama);
                }
                if (data.fotos && data.fotos.length > 0) {
                    data.fotos.forEach(function (f) {
                        allPhotos.push(f.path);
                    });
                }

                // Render Carousel Foto Utama
                var slidesContainer = document.getElementById('sbMainSlides');
                var mainNav = document.getElementById('sbMainNav');
                var mainCounter = document.getElementById('sbMainCounter');
                var carouselWrap = document.getElementById('sbMainCarouselWrap');

                if (allPhotos.length > 0) {
                    carouselWrap.style.display = 'block';
                    slidesContainer.innerHTML = allPhotos.map(function (src, idx) {
                        var activeClass = idx === 0 ? ' active' : '';
                        return '<img class="sb-main-slide' + activeClass + '" src="' + src + '" alt="Foto Gedung" onclick="openLightbox(\'' + src + '\')">';
                    }).join('');

                    if (allPhotos.length > 1) {
                        mainNav.style.display = 'flex';
                        mainCounter.style.display = 'block';
                        mainCounter.textContent = '1 / ' + allPhotos.length;
                    } else {
                        mainNav.style.display = 'none';
                        mainCounter.style.display = 'none';
                    }
                } else {
                    carouselWrap.style.display = 'none';
                }

                document.getElementById('sbName').textContent = p.nama_gedung;
                document.getElementById('sbAddr').textContent = p.alamat || '-';

                // Fasilitas
                var fasEl = document.getElementById('sbFasilitas');
                if (data.fasilitas && data.fasilitas.length > 0) {
                    var maxVisible = 3;
                    var totalFas = data.fasilitas.length;
                    var fasHtml = '<div id="sbFasilitasList" style="display:flex; flex-direction:column; gap:10px;">';
                    data.fasilitas.forEach(function (f, idx) {
                        var statusColor = f.is_aktif ? 'var(--success)' : 'var(--muted)';
                        var statusText = f.is_aktif ? 'Sedang Dipakai' : 'Kosong';
                        var pulseClass = f.is_aktif ? 'pulse-mini' : '';
                        var hiddenStyle = idx >= maxVisible ? ' style="display:none;"' : '';

                        var btnLokasi = (f.latitude && f.longitude) 
                            ? '<button onclick="zoomToRuangan(' + f.id + ', ' + f.latitude + ', ' + f.longitude + ')" style="background:#3b82f6; border:1px solid #3b82f6; color:#ffffff; padding:4px 12px; border-radius:100px; font-size:0.65rem; font-weight:700; cursor:pointer; transition:all 0.2s; text-transform:uppercase; letter-spacing:0.5px; box-shadow:0 2px 4px rgba(0,0,0,0.2);"><i class="fas fa-search-location" style="margin-right:4px;"></i> Lihat Detail</button>'
                            : '';

                        fasHtml += '<div class="sb-fas-item"' + hiddenStyle + '>'
                            + '<div style="display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center; gap:8px; margin-bottom:8px;">'
                            + '<strong style="font-size:0.95rem; color:var(--text); flex:1; min-width:140px;">' + f.nama_fasilitas + '</strong>'
                            + (f.kategori ? '<span style="font-size:0.6rem; color:var(--accent); font-weight:800; text-transform:uppercase; letter-spacing:0.8px; border:1px solid var(--accent-dim); padding:3px 10px; border-radius:100px; background:var(--accent-dim); white-space:normal; text-align:center; max-width:180px;">' + f.kategori + '</span>' : '')
                            + '</div>'
                            + (f.keterangan ? '<div style="font-size:0.75rem; color:var(--muted); margin-bottom:8px; line-height:1.4;">' + f.keterangan + '</div>' : '')
                            + '<div style="display:flex; justify-content:space-between; align-items:center; margin-top:8px;">'
                            + '<div style="display:flex; align-items:center; gap:8px;">'
                            + '<div class="status-dot ' + pulseClass + '" style="width:7px; height:7px; border-radius:50%; background:' + statusColor + '; box-shadow:0 0 10px ' + (f.is_aktif ? 'rgba(34,197,94,0.4)' : 'transparent') + '"></div>'
                            + '<span style="font-size:0.7rem; font-weight:600; color:' + statusColor + '; text-transform:uppercase; letter-spacing:0.5px;">' + statusText + '</span>'
                            + '</div>'
                            + btnLokasi
                            + '</div>'
                            + '</div>';
                    });
                    fasHtml += '</div>';

                    // Add expand button if more than maxVisible
                    if (totalFas > maxVisible) {
                        fasHtml += '<button class="sb-fas-expand-btn" id="sbFasExpandBtn" onclick="toggleFasilitasExpand()">'
                            + '<i class="fas fa-chevron-down"></i> Lihat Semua (' + totalFas + ')'
                            + '</button>';
                    }

                    fasEl.innerHTML = fasHtml;
                } else {
                    fasEl.innerHTML = '<div style="background:rgba(255,255,255,0.03); border:1px dashed var(--border); border-radius:12px; padding:16px; text-align:center; color:var(--muted); font-size:0.8rem;">Informasi fasilitas & kelas pada gedung ini belum tersedia saat ini.</div>';
                }

                // Call populateSidebar to fetch jadwal semester
                populateSidebar(data);

            })
            .catch(function (err) {
                console.error(err);
                toast('Gagal mengambil data gedung');
            });
    }
    window.zoomToRuangan = function (id, lat, lng) {
        if (!lat || !lng) {
            toast('Koordinat ruangan tidak tersedia');
            return;
        }
        
        map.flyTo([lat, lng], 21, { duration: 1.5 });
        
        if (window.innerWidth <= 768) {
            document.getElementById('sidebar').classList.remove('show');
        }
        
        setTimeout(function() {
            var found = false;
            ruanganMarkerGroup.eachLayer(function(m) {
                if (m.ruanganId === id) {
                    m.openPopup();
                    found = true;
                }
            });
            
            if(!found) {
                setTimeout(function() {
                    ruanganMarkerGroup.eachLayer(function(m) {
                        if (m.ruanganId === id) m.openPopup();
                    });
                }, 500);
            }
        }, 1600);
    };

    /* ── LIGHTBOX LOGIC ── */
    window.openLightbox = function (src) {
        var lb = document.getElementById('lightbox');
        var img = document.getElementById('lightboxImg');
        if (!lb || !img) return;

        img.src = src;
        lb.style.display = 'flex';
        setTimeout(function () {
            lb.classList.add('show');
        }, 10);
    };

    window.closeLightbox = function () {
        var lb = document.getElementById('lightbox');
        if (!lb) return;

        lb.classList.remove('show');
        setTimeout(function () {
            lb.style.display = 'none';
        }, 300);
    };

    /* ── SIDEBAR CAROUSEL LOGIC ── */
    var sbMainIdx = 0;
    window.sbMainGalleryNav = function (dir) {
        var slides = document.querySelectorAll('.sb-main-slide');
        if (slides.length <= 1) return;

        slides[sbMainIdx].classList.remove('active');
        sbMainIdx = (sbMainIdx + dir + slides.length) % slides.length;
        slides[sbMainIdx].classList.add('active');

        var counter = document.getElementById('sbMainCounter');
        if (counter) {
            counter.textContent = (sbMainIdx + 1) + ' / ' + slides.length;
        }
    };

    /* ══════════════════════════════════════════════════
       VEGETASI MARKERS
    ══════════════════════════════════════════════════ */
    function makeVegetasiIcon(kategori) {
        var c = '#064e3b'; // Hijau Tua
        var svg = '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="30" viewBox="0 0 22 30">'
            + '<defs><filter id="vds"><feDropShadow dx="0" dy="2" stdDeviation="1.5" flood-color="rgba(0,0,0,.4)"/></filter></defs>'
            + '<path filter="url(#vds)" d="M11 1C6.03 1 2 5.03 2 10c0 6.5 9 19 9 19s9-12.5 9-19C20 5.03 15.97 1 11 1z" fill="' + c + '" stroke="rgba(255,255,255,.7)" stroke-width="1.2"/>'
            + '<circle cx="11" cy="10" r="4" fill="rgba(255,255,255,.92)"/>'
            + '<circle cx="11" cy="10" r="2" fill="' + c + '"/>'
            + '</svg>';
        return L.divIcon({ html: svg, className: '', iconSize: [22, 30], iconAnchor: [11, 30], popupAnchor: [0, -32] });
    }

    function buildVegetasiPopup(p) {
        var id = 'veg_' + p.id;
        var imgHtml = p.foto_utama
            ? '<img class="rp-img" src="' + p.foto_utama + '" alt="' + p.nama_vegetasi + '" style="width:100%; height:140px; object-fit:cover; border-radius:10px; margin-bottom:12px; border:1px solid var(--border);">'
            : '<div style="width:100%; height:140px; background:rgba(255,255,255,0.03); border-radius:10px; margin-bottom:12px; border:1px dashed var(--border); display:flex; align-items:center; justify-content:center; color:var(--muted); font-size:0.75rem;">Tidak ada foto utama</div>';

        // Persiapkan data foto gabungan untuk galeri
        var allPhotos = [];
        if (p.foto_utama) allPhotos.push(p.foto_utama);
        if (p.foto_tambahan) allPhotos = allPhotos.concat(p.foto_tambahan);

        var slidesHtml = allPhotos.map(function (src, idx) {
            var activeClass = idx === 0 ? ' active' : '';
            return '<img class="rp-carousel-slide' + activeClass + '" src="' + src + '" onclick="openLightbox(\'' + src + '\')">';
        }).join('');

        return '<div class="ruangan-popup" id="rpPopup_' + id + '">'
            + '<div class="rp-slider step-1" id="rpSlider_' + id + '">'

            // PANEL 1: Info Singkat (Overview)
            + '<div class="rp-panel rp-panel-1">'
            + '<div class="rp-header" style="background:rgba(34,197,94,0.05);">'
            + '<div class="rp-icon" style="background:rgba(34,197,94,0.15); color:#4ade80;">🌳</div>'
            + '<div class="rp-header-text">'
            + '<div class="rp-name">' + p.nama_vegetasi + '</div>'
            + '<div class="rp-kategori" style="color:#4ade80;">' + p.kategori + '</div>'
            + '</div>'
            + '</div>'
            + '<div class="rp-body">'
            + '<div class="rp-gedung"><i class="fas fa-building" style="color:var(--accent);"></i> ' + p.nama_gedung + '</div>'
            + imgHtml
            + '<button class="rp-btn-detail" onclick="goToStep(\'' + id + '\', 2)"><i class="fas fa-info-circle"></i> Lihat Detail</button>'
            + '</div>'
            + '</div>'

            // PANEL 2: Detail Lengkap
            + '<div class="rp-panel rp-panel-2">'
            + '<div class="rp-detail-header">'
            + '<button class="rp-btn-back" onclick="goToStep(\'' + id + '\', 1)"><i class="fas fa-arrow-left"></i></button>'
            + '<div class="rp-detail-title">Detail Vegetasi</div>'
            + '</div>'
            + '<div class="rp-detail-body">'
            + '<div class="rp-detail-card">'
            + '<div class="rp-detail-label">Keterangan</div>'
            + (p.keterangan ? '<div class="rp-detail-value">' + p.keterangan + '</div>' : '<div class="rp-detail-value text-muted">-</div>')
            + '</div>'
            + '<button class="rp-btn-jadwal mt-3" onclick="goToStep(\'' + id + '\', 3)"><i class="fas fa-images"></i> Galeri Foto (' + allPhotos.length + ')</button>'
            + '</div>'
            + '</div>'

            // PANEL 3: Galeri Foto
            + '<div class="rp-panel rp-panel-3">'
            + '<div class="rp-detail-header">'
            + '<button class="rp-btn-back" onclick="goToStep(\'' + id + '\', 2)"><i class="fas fa-arrow-left"></i></button>'
            + '<div class="rp-detail-title">Galeri Foto</div>'
            + '</div>'
            + '<div class="rp-detail-body">'
            + '<div class="rp-carousel" id="rpCarousel_' + id + '">'
            + (slidesHtml || '<div style="color:var(--muted); font-size:0.8rem; text-align:center; padding:40px 0;">Tidak ada foto</div>')
            + '</div>'
            + (allPhotos.length > 0 ?
                '<div class="rp-carousel-nav">'
                + '<button class="rp-carousel-btn" onclick="carouselNav(\'' + id + '\', -1)"><i class="fas fa-chevron-left"></i></button>'
                + '<span class="rp-carousel-counter" id="rpCounter_' + id + '">1 / ' + allPhotos.length + '</span>'
                + '<button class="rp-carousel-btn" onclick="carouselNav(\'' + id + '\', 1)"><i class="fas fa-chevron-right"></i></button>'
                + '</div>'
                + '<div style="text-align:center; font-size:0.7rem; color:var(--muted); margin-top:6px;"><i class="fas fa-hand-pointer"></i> Klik foto untuk memperbesar</div>'
                : '')
            + '</div>'
            + '</div>'

            + '</div>' // rp-slider
            + '</div>'; // ruangan-popup
    }

    function renderVegetasiMarkers(data) {
        vegetasiMarkerGroup.clearLayers();
        data.forEach(function (f) {
            var lat = f.geometry.coordinates[1];
            var lng = f.geometry.coordinates[0];
            var p = f.properties;

            var m = L.marker([lat, lng], { icon: makeVegetasiIcon(p.kategori), title: p.nama_vegetasi });
            m.bindPopup(buildVegetasiPopup(p), { maxWidth: 250 });
            
            m.bindTooltip(p.nama_vegetasi, {
                direction: 'top',
                offset: [0, -30],
                className: 'ruangan-tooltip'
            });

            vegetasiMarkerGroup.addLayer(m);
        });
    }

    function loadVegetasiMarkers() {
        fetch('/webgis/geojson-vegetasi')
            .then(function (r) { return r.json(); })
            .then(function (gj) {
                allVegetasiData = gj.features || [];
                renderVegetasiMarkers(allVegetasiData);
                updateZoomLayers();
            })
            .catch(function (err) {
                console.error('Gagal memuat data vegetasi:', err);
            });
    }
})();