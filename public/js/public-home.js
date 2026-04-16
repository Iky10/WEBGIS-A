// Mini peta di hero
    var map = L.map('hero-map', { zoomControl: true, scrollWheelZoom: false })
              .setView([-2.5, 118.0], 5);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    fetch(window.WEBGIS_URL)
        .then(r => r.json())
        .then(data => {
            var bounds = [];
            (data.features || []).forEach(f => {
                var lat = f.geometry.coordinates[1];
                var lng = f.geometry.coordinates[0];
                L.circleMarker([lat, lng], {
                    radius: 8, fillColor: '#fff',
                    color: '#1a3c5e', weight: 2,
                    fillOpacity: 0.9
                }).addTo(map).bindPopup('<strong>' + f.properties.nama_gedung + '</strong>');
                bounds.push([lat, lng]);
            });
            if (bounds.length > 0) map.fitBounds(L.latLngBounds(bounds).pad(0.3));
        });