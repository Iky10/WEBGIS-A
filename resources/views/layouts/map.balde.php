<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title', 'WebGIS Gedung')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @stack('styles')

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --glass-bg: rgba(15, 20, 35, 0.82);
            --glass-border: rgba(255, 255, 255, 0.08);
            --glass-hover: rgba(255, 255, 255, 0.06);
            --accent: #3b82f6;
            --accent-light: #60a5fa;
            --accent-glow: rgba(59, 130, 246, 0.35);
            --text-primary: #f0f4ff;
            --text-muted: #94a3b8;
            --success: #22c55e;
            --warning: #f59e0b;
            --danger: #ef4444;
            --surface: rgba(20, 27, 48, 0.92);
        }

        html, body {
            width: 100%; height: 100%;
            overflow: hidden;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #0f172a;
        }

        #map-container {
            position: fixed;
            inset: 0;
            width: 100vw;
            height: 100vh;
        }

        /* Leaflet z-index fixes */
        .leaflet-control-zoom { display: none; }
        .leaflet-control-layers-toggle { display: none !important; }
        .leaflet-control-attribution {
            background: rgba(0,0,0,0.45) !important;
            color: rgba(255,255,255,0.45) !important;
            font-size: 10px !important;
            backdrop-filter: blur(4px);
        }
        .leaflet-control-attribution a { color: rgba(255,255,255,0.5) !important; }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 2px; }
    </style>
</head>
<body>

<div id="map-container">
    @yield('content')
</div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<!-- jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

@stack('scripts')
</body>
</html>