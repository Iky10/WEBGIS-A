document.addEventListener('DOMContentLoaded', function() {
    if (typeof window.GEDUNG_X !== 'undefined' && typeof window.GEDUNG_Y !== 'undefined') {
        var map = L.map('mini-map', { zoomControl: true, scrollWheelZoom: false })
                  .setView([window.GEDUNG_X, window.GEDUNG_Y], 16);

        L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: '© Esri'
        }).addTo(map);

        L.marker([window.GEDUNG_X, window.GEDUNG_Y])
         .addTo(map)
         .bindPopup('<strong>' + window.GEDUNG_NAMA + '</strong>').openPopup();
    }
});
