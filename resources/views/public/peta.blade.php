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

<style>
/* ═══════════════════════════════════════════
   RESET & ROOT
═══════════════════════════════════════════ */
*, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }

:root {
    --accent:        #3b82f6;
    --accent-dim:    rgba(59,130,246,.18);
    --accent-glow:   rgba(59,130,246,.35);
    --surface:       rgba(10,15,30,.88);
    --surface-hi:    rgba(18,26,50,.94);
    --border:        rgba(255,255,255,.08);
    --border-hi:     rgba(255,255,255,.15);
    --text:          #e8eeff;
    --muted:         #7c8db0;
    --success:       #22c55e;
    --warning:       #f59e0b;
    --danger:        #ef4444;
    --radius-sm:     8px;
    --radius-md:     14px;
    --radius-lg:     18px;
    --blur:          blur(22px) saturate(160%);
}

html, body {
    width:100%; height:100%;
    overflow:hidden;
    font-family:'Plus Jakarta Sans',sans-serif;
    background:#07101e;
    color:var(--text);
}

::-webkit-scrollbar { width:4px; }
::-webkit-scrollbar-thumb { background:rgba(255,255,255,.12); border-radius:2px; }

/* ═══════════════════════════════════════════
   MAP
═══════════════════════════════════════════ */
#map {
    position:fixed;
    inset:0;
    width:100vw;
    height:100vh;
    z-index:0;
}

/* Hide default leaflet controls */
.leaflet-control-zoom { display:none !important; }
.leaflet-control-layers { display:none !important; }
.leaflet-control-attribution {
    background:rgba(0,0,0,.5) !important;
    color:rgba(255,255,255,.35) !important;
    font-size:9px !important;
    backdrop-filter:blur(6px);
    border-radius:4px 0 0 0 !important;
}
.leaflet-control-attribution a { color:rgba(255,255,255,.45) !important; }

/* ═══════════════════════════════════════════
   LOADING SCREEN
═══════════════════════════════════════════ */
#loading {
    position:fixed;
    inset:0;
    z-index:9999;
    background:#07101e;
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    gap:20px;
    transition:opacity .5s ease;
}
#loading.out { opacity:0; pointer-events:none; }

.loader-icon {
    width:62px; height:62px;
    background:var(--accent);
    border-radius:16px;
    display:flex; align-items:center; justify-content:center;
    font-size:28px; color:#fff;
    animation: pulse 1.6s ease-in-out infinite;
    box-shadow: 0 0 0 0 var(--accent-glow);
}
@keyframes pulse {
    0%,100% { box-shadow:0 0 24px var(--accent-glow); }
    50%      { box-shadow:0 0 48px rgba(59,130,246,.55), 0 0 0 12px rgba(59,130,246,.08); }
}
.loader-bar {
    width:180px; height:3px;
    background:rgba(255,255,255,.07);
    border-radius:2px;
    overflow:hidden;
}
.loader-bar-fill {
    height:100%;
    background:var(--accent);
    border-radius:2px;
    animation:bar 1.8s ease-in-out infinite;
}
@keyframes bar {
    0%   { width:0%;   margin-left:0; }
    50%  { width:70%;  margin-left:0; }
    100% { width:0%;   margin-left:100%; }
}
.loader-label {
    color:var(--muted);
    font-size:.82rem;
    font-weight:600;
    letter-spacing:.4px;
}

/* ═══════════════════════════════════════════
   TOP BAR
═══════════════════════════════════════════ */
#topbar {
    position:fixed;
    top:0; left:0; right:0;
    z-index:1000;
    height:62px;
    display:flex;
    align-items:center;
    gap:10px;
    padding:0 16px;
    background:linear-gradient(180deg, rgba(7,16,30,.96) 0%, rgba(7,16,30,0) 100%);
    pointer-events:none;
}
#topbar > * { pointer-events:all; }

/* Logo */
.t-logo {
    display:flex; align-items:center; gap:10px;
    text-decoration:none; flex-shrink:0;
}
.t-logo-icon {
    width:38px; height:38px;
    background:var(--accent);
    border-radius:10px;
    display:flex; align-items:center; justify-content:center;
    font-size:17px; color:#fff;
    box-shadow:0 0 18px var(--accent-glow);
    flex-shrink:0;
}
.t-logo-name {
    font-size:.88rem; font-weight:800;
    color:var(--text); line-height:1.15;
    white-space:nowrap;
}
.t-logo-sub {
    font-size:.68rem; color:var(--muted);
    font-weight:500;
}

/* Search */
.t-search {
    flex:1;
    max-width:400px;
    position:relative;
}
.t-search-in {
    width:100%;
    background:var(--surface);
    border:1px solid var(--border);
    border-radius:var(--radius-sm);
    padding:9px 36px 9px 36px;
    color:var(--text);
    font-family:'Plus Jakarta Sans',sans-serif;
    font-size:.84rem; font-weight:500;
    backdrop-filter:var(--blur);
    outline:none;
    transition:border-color .2s, box-shadow .2s;
}
.t-search-in::placeholder { color:var(--muted); }
.t-search-in:focus {
    border-color:var(--accent);
    box-shadow:0 0 0 3px var(--accent-dim);
}
.t-search-ico {
    position:absolute; left:11px; top:50%;
    transform:translateY(-50%);
    color:var(--muted); font-size:.8rem;
    pointer-events:none;
}
.t-search-x {
    position:absolute; right:10px; top:50%;
    transform:translateY(-50%);
    background:none; border:none;
    color:var(--muted); cursor:pointer;
    font-size:.78rem; display:none;
}
.t-search-x:hover { color:var(--text); }

/* Search dropdown */
.t-search-drop {
    position:absolute;
    top:calc(100% + 6px); left:0; right:0;
    background:var(--surface-hi);
    border:1px solid var(--border);
    border-radius:var(--radius-md);
    backdrop-filter:var(--blur);
    box-shadow:0 24px 60px rgba(0,0,0,.55);
    overflow:hidden;
    display:none;
    max-height:260px;
    overflow-y:auto;
}
.t-drop-item {
    display:flex; align-items:center; gap:10px;
    padding:10px 14px;
    cursor:pointer;
    border-bottom:1px solid var(--border);
    transition:background .15s;
}
.t-drop-item:last-child { border-bottom:none; }
.t-drop-item:hover { background:rgba(255,255,255,.04); }
.t-drop-ico {
    width:30px; height:30px; flex-shrink:0;
    background:var(--accent-dim);
    border-radius:7px;
    display:flex; align-items:center; justify-content:center;
    color:var(--accent); font-size:.78rem;
}
.t-drop-name {
    font-size:.8rem; font-weight:700; color:var(--text);
    white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
}
.t-drop-sub { font-size:.7rem; color:var(--muted); }

/* Top icon buttons */
.t-btns {
    display:flex; align-items:center; gap:6px;
    margin-left:auto; flex-shrink:0;
}
.t-btn {
    width:38px; height:38px;
    border-radius:var(--radius-sm);
    background:var(--surface);
    border:1px solid var(--border);
    color:var(--muted);
    display:flex; align-items:center; justify-content:center;
    cursor:pointer; font-size:.9rem;
    backdrop-filter:var(--blur);
    transition:all .2s;
    text-decoration:none;
    position:relative;
}
.t-btn:hover, .t-btn.on {
    background:var(--accent);
    border-color:var(--accent);
    color:#fff;
    box-shadow:0 0 16px var(--accent-glow);
}
.t-btn-tip {
    position:absolute;
    bottom:-28px; left:50%;
    transform:translateX(-50%);
    background:rgba(0,0,0,.8);
    color:#fff; font-size:.68rem;
    white-space:nowrap; padding:3px 7px;
    border-radius:4px; opacity:0;
    transition:opacity .15s; pointer-events:none;
}
.t-btn:hover .t-btn-tip { opacity:1; }

/* ═══════════════════════════════════════════
   FILTER PANEL
═══════════════════════════════════════════ */
#filterPanel {
    position:fixed;
    top:72px; left:16px;
    z-index:900;
    width:252px;
    background:var(--surface);
    border:1px solid var(--border);
    border-radius:var(--radius-lg);
    backdrop-filter:var(--blur);
    box-shadow:0 20px 60px rgba(0,0,0,.45);
    overflow:hidden;
    transition:transform .28s cubic-bezier(.4,0,.2,1), opacity .28s;
    transform-origin:top left;
}
#filterPanel.hide {
    transform:scale(.92) translateY(-8px);
    opacity:0;
    pointer-events:none;
}

.fp-head {
    padding:12px 14px;
    display:flex; align-items:center; gap:8px;
    border-bottom:1px solid var(--border);
}
.fp-head-icon {
    width:28px; height:28px;
    background:var(--accent-dim);
    border-radius:7px;
    display:flex; align-items:center; justify-content:center;
    color:var(--accent); font-size:.78rem;
}
.fp-head-title {
    font-size:.78rem; font-weight:800;
    text-transform:uppercase; letter-spacing:.7px;
    color:var(--text);
}

.fp-body { padding:14px; }

.fp-label {
    display:block;
    font-size:.68rem; font-weight:700;
    text-transform:uppercase; letter-spacing:.6px;
    color:var(--muted); margin-bottom:7px;
}

.fp-chips {
    display:flex; flex-wrap:wrap; gap:5px;
    margin-bottom:14px;
}
.chip {
    padding:4px 11px;
    border-radius:20px;
    background:rgba(207, 0, 0, 0.04);
    border:1px solid var(--border);
    color:var(--muted);
    font-size:.72rem; font-weight:700;
    cursor:pointer;
    transition:all .15s;
    white-space:nowrap;
    font-family:'Plus Jakarta Sans',sans-serif;
}
.chip:hover { border-color:var(--accent); color:var(--accent); }
.chip.on {
    background:var(--accent);
    border-color:var(--accent);
    color:#fff;
}

.fp-sep { height:1px; background:var(--border); margin:4px 0 14px; }

.fp-footer {
    display:flex; align-items:center; justify-content:space-between;
}
.fp-count-num {
    font-size:1.7rem; font-weight:800; color:var(--text); line-height:1;
}
.fp-count-lbl {
    font-size:.68rem; color:var(--muted); font-weight:600;
}
.fp-reset {
    background:rgba(239,68,68,.1);
    border:1px solid rgba(239,68,68,.25);
    color:#f87171;
    border-radius:var(--radius-sm);
    padding:6px 12px;
    font-size:.75rem; font-weight:700;
    font-family:'Plus Jakarta Sans',sans-serif;
    cursor:pointer;
    transition:all .15s;
}
.fp-reset:hover { background:rgba(239,68,68,.2); border-color:rgba(239,68,68,.5); }

/* ═══════════════════════════════════════════
   ZOOM CONTROLS
═══════════════════════════════════════════ */
#zoomBox {
    position:fixed;
    bottom:36px; left:16px;
    z-index:900;
    display:flex; flex-direction:column; gap:4px;
}
.z-btn {
    width:36px; height:36px;
    background:var(--surface);
    border:1px solid var(--border);
    border-radius:var(--radius-sm);
    color:var(--text);
    display:flex; align-items:center; justify-content:center;
    cursor:pointer; font-size:1.1rem; font-weight:700;
    backdrop-filter:var(--blur);
    transition:all .15s;
    user-select:none;
}
.z-btn:hover { background:var(--accent); border-color:var(--accent); box-shadow:0 0 12px var(--accent-glow); }
.z-sep { height:1px; background:var(--border); margin:2px 5px; }

/* ═══════════════════════════════════════════
   LEGEND
═══════════════════════════════════════════ */
#legend {
    position:fixed;
    bottom:36px; right:16px;
    z-index:900;
    background:var(--surface);
    border:1px solid var(--border);
    border-radius:var(--radius-md);
    backdrop-filter:var(--blur);
    padding:12px 14px;
    box-shadow:0 8px 32px rgba(0,0,0,.35);
}
.leg-title {
    font-size:.68rem; font-weight:800;
    text-transform:uppercase; letter-spacing:.7px;
    color:var(--muted); margin-bottom:10px;
}
.leg-row {
    display:flex; align-items:center; gap:8px;
    margin-bottom:7px; font-size:.76rem; font-weight:600; color:var(--text);
}
.leg-row:last-child { margin-bottom:0; }
.leg-dot {
    width:10px; height:10px; border-radius:50%; flex-shrink:0;
}

/* ═══════════════════════════════════════════
   LAYER TOGGLE
═══════════════════════════════════════════ */
#layerBtn {
    position:fixed;
    top:72px; right:16px;
    z-index:900;
    width:38px; height:38px;
    background:var(--surface);
    border:1px solid var(--border);
    border-radius:var(--radius-sm);
    color:var(--muted);
    display:flex; align-items:center; justify-content:center;
    cursor:pointer; font-size:.9rem;
    backdrop-filter:var(--blur);
    transition:all .2s;
}
#layerBtn:hover, #layerBtn.on {
    background:var(--accent); border-color:var(--accent); color:#fff;
    box-shadow:0 0 14px var(--accent-glow);
}

/* ═══════════════════════════════════════════
   COORDS BAR
═══════════════════════════════════════════ */
#coords {
    position:fixed;
    bottom:8px; left:50%; transform:translateX(-50%);
    z-index:900;
    background:rgba(0,0,0,.48);
    border:1px solid rgba(255,255,255,.06);
    border-radius:6px;
    padding:3px 12px;
    color:rgba(255,255,255,.35);
    font-size:.65rem;
    font-family:'Courier New',monospace;
    backdrop-filter:blur(8px);
    pointer-events:none;
    white-space:nowrap;
}

/* ═══════════════════════════════════════════
   TOAST
═══════════════════════════════════════════ */
#toast {
    position:fixed;
    bottom:80px; left:50%;
    transform:translateX(-50%) translateY(12px);
    z-index:2000;
    background:var(--surface-hi);
    border:1px solid var(--border-hi);
    border-radius:var(--radius-sm);
    padding:9px 18px;
    font-size:.78rem; font-weight:700;
    color:var(--text);
    backdrop-filter:var(--blur);
    white-space:nowrap;
    opacity:0;
    transition:all .28s ease;
    pointer-events:none;
}
#toast.show { opacity:1; transform:translateX(-50%) translateY(0); }

/* ═══════════════════════════════════════════
   CUSTOM POPUP
═══════════════════════════════════════════ */
.leaflet-popup-content-wrapper {
    background:transparent !important;
    padding:0 !important;
    border-radius:0 !important;
    box-shadow:none !important;
}
.leaflet-popup-tip-container { display:none !important; }
.leaflet-popup-content { margin:0 !important; width:auto !important; }
.leaflet-popup { margin-bottom:6px !important; }
.leaflet-popup-close-button {
    top:8px !important; right:8px !important;
    width:26px !important; height:26px !important;
    background:rgba(0,0,0,.55) !important;
    color:#fff !important;
    border-radius:50% !important;
    font-size:16px !important; line-height:26px !important;
    text-align:center !important;
    border:1px solid rgba(255,255,255,.12) !important;
    z-index:10 !important;
    backdrop-filter:blur(6px) !important;
}

.gis-popup {
    width:290px;
    background:var(--surface-hi);
    border:1px solid var(--border-hi);
    border-radius:var(--radius-lg);
    overflow:hidden;
    box-shadow:0 28px 70px rgba(0,0,0,.6), 0 0 0 1px rgba(255,255,255,.04);
    font-family:'Plus Jakarta Sans',sans-serif;
}
.pu-img-wrap {
    height:160px;
    overflow:hidden;
    position:relative;
    background:#0b1425;
}
.pu-img {
    width:100%; height:100%;
    object-fit:cover;
    transition:transform .4s ease;
    display:block;
}
.pu-img-wrap:hover .pu-img { transform:scale(1.04); }
.pu-no-img {
    width:100%; height:100%;
    display:flex; align-items:center; justify-content:center;
    background:linear-gradient(135deg,#0f1e3a,#071020);
    font-size:3rem; color:rgba(255,255,255,.06);
}
.pu-badges {
    position:absolute; bottom:10px; left:10px;
    display:flex; gap:5px; flex-wrap:wrap;
}
.pu-badge {
    padding:3px 9px;
    border-radius:20px;
    font-size:.66rem; font-weight:800;
    letter-spacing:.3px;
    backdrop-filter:blur(10px);
    border:1px solid rgba(255,255,255,.15);
}
.b-fungsi  { background:rgba(59,130,246,.55);  color:#bfdbfe; }
.b-baik    { background:rgba(34,197,94,.55);   color:#bbf7d0; }
.b-sedang  { background:rgba(245,158,11,.55);  color:#fde68a; }
.b-rusak   { background:rgba(239,68,68,.55);   color:#fecaca; }

.pu-body { padding:14px; }
.pu-name {
    font-size:.92rem; font-weight:800;
    color:var(--text); margin-bottom:5px; line-height:1.3;
}
.pu-addr {
    display:flex; align-items:flex-start; gap:5px;
    font-size:.74rem; color:var(--muted);
    margin-bottom:12px; line-height:1.45;
}
.pu-addr i { color:var(--accent); margin-top:2px; flex-shrink:0; font-size:.7rem; }

.pu-stats {
    display:grid; grid-template-columns:1fr 1fr;
    gap:1px;
    background:var(--border);
    border-radius:var(--radius-sm);
    overflow:hidden;
    margin-bottom:12px;
}
.pu-stat {
    background:rgba(255,255,255,.025);
    padding:8px;
    text-align:center;
}
.pu-stat-v {
    display:block; font-size:.9rem; font-weight:800;
    color:var(--text); line-height:1;
}
.pu-stat-k { font-size:.65rem; color:var(--muted); font-weight:600; }

.pu-cta {
    display:block;
    width:100%;
    background:var(--accent);
    color:#fff;
    border:none;
    border-radius:var(--radius-sm);
    padding:10px;
    font-family:'Plus Jakarta Sans',sans-serif;
    font-size:.8rem; font-weight:800;
    text-align:center; text-decoration:none;
    cursor:pointer;
    transition:all .2s;
    box-shadow:0 4px 16px var(--accent-glow);
    letter-spacing:.2px;
}
.pu-cta:hover {
    background:#2563eb; color:#fff;
    transform:translateY(-1px);
    box-shadow:0 8px 24px var(--accent-glow);
}
.pu-cta i { margin-right:6px; }

/* ═══════════════════════════════════════════
   BUILDING LABEL (zoom ≥ 16)
═══════════════════════════════════════════ */
.lbl-icon { background:none !important; border:none !important; }
.lbl-inner {
    background:rgba(7,16,30,.85);
    color:var(--text);
    font-family:'Plus Jakarta Sans',sans-serif;
    font-size:10.5px; font-weight:800;
    padding:3px 8px;
    border-radius:5px;
    border:1px solid rgba(255,255,255,.1);
    white-space:nowrap;
    backdrop-filter:blur(8px);
    pointer-events:none;
    margin-top:2px;
    box-shadow:0 2px 8px rgba(0,0,0,.4);
}

/* ═══════════════════════════════════════════
   ROUTING PANEL (LIEDMAN)
   ═══════════════════════════════════════════ */
.leaflet-routing-container {
    background: var(--surface-hi) !important;
    border: 1px solid var(--border-hi) !important;
    border-radius: var(--radius-md) !important;
    color: var(--text) !important;
    backdrop-filter: var(--blur) !important;
    font-family: 'Plus Jakarta Sans', sans-serif !important;
    box-shadow: 0 12px 40px rgba(0,0,0,0.5) !important;
    max-width: 280px !important;
    margin-right: 16px !important;
    margin-top: 72px !important;
}

.leaflet-routing-container h2, 
.leaflet-routing-container h3 {
    color: var(--accent) !important;
    font-weight: 800 !important;
    text-transform: uppercase !important;
    font-size: 0.75rem !important;
}

.leaflet-routing-alt {
    max-height: 300px !important;
    background: transparent !important;
    border-bottom: 1px solid var(--border) !important;
}

.leaflet-routing-alt::-webkit-scrollbar { width: 4px; }
.leaflet-routing-alt::-webkit-scrollbar-thumb { background: var(--border-hi); border-radius: 2px; }

.leaflet-routing-alt table tr:hover {
    background: rgba(255,255,255,0.05) !important;
}

.leaflet-routing-icon {
    filter: invert(1) hue-rotate(180deg);
}

.leaflet-routing-instruction-dist {
    color: var(--muted) !important;
    font-size: 0.7rem !important;
}

.leaflet-routing-geocoders input {
    background: var(--surface) !important;
    color: var(--text) !important;
    border: 1px solid var(--border) !important;
    border-radius: 4px !important;
    padding: 4px 8px !important;
}

/* ═══════════════════════════════════════════
   SIDEBAR
═══════════════════════════════════════════ */
#sidebar {
    position:fixed;
    top:0; right:0; bottom:0;
    width:380px;
    z-index:1100;
    background:var(--surface-hi);
    border-left:1px solid var(--border-hi);
    backdrop-filter:var(--blur);
    box-shadow:-10px 0 40px rgba(0,0,0,.6);
    display:flex; flex-direction:column;
    transform:translateX(100%);
    transition:transform .35s cubic-bezier(.4,0,.2,1);
}
#sidebar.show { transform:translateX(0); }

.sb-head {
    display:flex; align-items:center; gap:12px;
    padding:16px 20px;
    border-bottom:1px solid var(--border);
    background:rgba(0,0,0,.15);
}
.sb-close {
    width:32px; height:32px;
    background:rgba(255,255,255,.05); border:none; border-radius:8px;
    color:var(--text); display:flex; align-items:center; justify-content:center;
    cursor:pointer; transition:all .2s; font-size:16px;
}
.sb-close:hover { background:var(--danger); color:#fff; }
.sb-title { font-size:1rem; font-weight:800; color:var(--text); letter-spacing:.5px;}

.sb-body {
    flex:1; overflow-y:auto; overflow-x:hidden;
}

.sb-img-wrap {
    width:100%; height:220px;
    background:#07101e;
}
.sb-img-wrap img {
    width:100%; height:100%; object-fit:cover; display:block;
}

.sb-info { padding:20px; }
.sb-name { font-size:1.4rem; font-weight:800; color:var(--text); line-height:1.2; margin-bottom:8px; }
.sb-addr { font-size:.85rem; color:var(--muted); margin-bottom:20px; display:flex; gap:6px; line-height:1.4; }
.sb-addr i { color:var(--accent); margin-top:3px; }

.sb-stats {
    display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:22px;
}
.sb-stat {
    background:rgba(255,255,255,.03); border:1px solid var(--border);
    border-radius:var(--radius-sm); padding:10px; text-align:center;
}
.sb-stat-v { display:block; font-size:.95rem; font-weight:800; color:var(--text); }
.sb-stat-k { font-size:.7rem; color:var(--muted); font-weight:600; text-transform:uppercase; letter-spacing:.5px; }

.sb-section { margin-bottom:22px; }
.sb-sec-title { 
    font-size:.85rem; font-weight:800; color:var(--text); margin-bottom:8px; 
    display:flex; justify-content:space-between; align-items:flex-end;
}
.sb-sec-title span { font-size:.7rem; color:var(--muted); font-weight:600;}
.sb-sec-text { font-size:.85rem; color:var(--muted); line-height:1.6; }

.sb-cta-btn {
    width:100%; background:var(--accent); color:#fff; border:none; border-radius:var(--radius-sm);
    padding:12px; font-family:'Plus Jakarta Sans',sans-serif; font-size:.9rem; font-weight:800;
    cursor:pointer; transition:all .2s; box-shadow:0 6px 20px var(--accent-glow);
    display:flex; align-items:center; justify-content:center; gap:8px;
}
.sb-cta-btn:hover { background:#2563eb; transform:translateY(-2px); box-shadow:0 8px 25px var(--accent-glow); }

.sb-gallery { margin-top:24px; border-top:1px dashed var(--border); padding-top:20px;}
.sb-gallery-grid { display:grid; grid-template-columns:1fr 1fr; gap:8px; }
.sb-gallery-grid img { width:100%; height:90px; object-fit:cover; border-radius:6px; border:1px solid var(--border); transition:transform .3s;}
.sb-gallery-grid img:hover { transform:scale(1.05); cursor:pointer;}

/* ═══════════════════════════════════════════
   RESPONSIVE
═══════════════════════════════════════════ */
@media (max-width:600px) {
    .t-logo-name, .t-logo-sub { display:none; }
    .t-search { max-width:190px; }
    #filterPanel { width:220px; }
    .gis-popup { width:265px; }
    #sidebar { width:100%; }
}

</style>
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

<div id="toast"></div>

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

                <button id="sbBtnPhotos" class="sb-cta-btn"><i class="fas fa-images"></i> Lihat Banyak Foto</button>

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
    'https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png',
    { attribution:'© <a href="https://carto.com">CARTO</a> © <a href="https://www.openstreetmap.org/copyright">OSM</a>',
      subdomains:'abcd', maxZoom:22, maxNativeZoom:20 }
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
    if(isSat){ map.removeLayer(light); sat.addTo(map); this.classList.add('on'); toast('Layer: Citra Satelit'); }
    else     { map.removeLayer(sat);  light.addTo(map); this.classList.remove('on'); toast('Layer: Peta Terang'); }
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
    var foto = p.foto_utama
        ? '<img class="pu-img" src="'+p.foto_utama+'" alt="'+p.nama_gedung+'" loading="lazy">'
        : '<div class="pu-no-img"><i class="fas fa-building"></i></div>';

    var bFungsi = (p.fungsi && p.fungsi!=='-')
        ? '<span class="pu-badge b-fungsi">'+p.fungsi+'</span>' : '';
    var bKond   = (p.kondisi && p.kondisi!=='-')
        ? '<span class="pu-badge '+getBadgeClass(p.kondisi)+'">'+p.kondisi+'</span>' : '';

    var lantai = (p.jumlah_lantai && p.jumlah_lantai!=='-') ? p.jumlah_lantai : '—';
    var tahun  = (p.tahun_berdiri && p.tahun_berdiri!=='-') ? p.tahun_berdiri : '—';

    return '<div class="gis-popup">'
        +'<div class="pu-img-wrap">'+foto
        +'<div class="pu-badges">'+bFungsi+bKond+'</div></div>'
        +'<div class="pu-body">'
        +'<div class="pu-name">'+p.nama_gedung+'</div>'
        +'<div class="pu-addr"><i class="fas fa-map-marker-alt"></i>'+(p.alamat||'Alamat belum tersedia')+'</div>'
        +'<div class="pu-stats">'
        +'<div class="pu-stat"><span class="pu-stat-v">'+lantai+'</span><span class="pu-stat-k">Lantai</span></div>'
        +'<div class="pu-stat"><span class="pu-stat-v">'+tahun+'</span><span class="pu-stat-k">Tahun Berdiri</span></div>'
        +'</div>'
        +'<div style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">'
        +'<button onclick="openSidebar('+p.id+')" class="pu-cta" style="border:none;"><i class="fas fa-info-circle"></i>Detail</button>'
        +'<button onclick="setRoutingDest('+lat+','+lng+')" class="pu-cta" style="background:var(--success); border:none; box-shadow:0 4px 16px rgba(34,197,94,.35);"><i class="fas fa-directions"></i>Rute</button>'
        +'</div>'
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
        m.bindPopup(buildPopup(p, lat, lng),{ maxWidth:310, closeButton:true });
        m.on('click', function(){ map.panTo([lat,lng]); });
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

sIn.addEventListener('input', function(){
    var q = this.value.trim().toLowerCase();
    sX.style.display = q ? 'block' : 'none';
    if(!q){ sDrop.style.display='none'; return; }

    var hits = allData.filter(function(f){
        var p = f.properties;
        return p.nama_gedung.toLowerCase().includes(q)
            || (p.alamat && p.alamat.toLowerCase().includes(q));
    }).slice(0,8);

    sDrop.innerHTML = hits.length
        ? hits.map(function(f){
            var p = f.properties;
            return '<div class="t-drop-item" data-lat="'+f.geometry.coordinates[1]+'" data-lng="'+f.geometry.coordinates[0]+'">'
                +'<div class="t-drop-ico"><i class="fas fa-building"></i></div>'
                +'<div><div class="t-drop-name">'+p.nama_gedung+'</div>'
                +'<div class="t-drop-sub">'+(p.fungsi!=='-'?p.fungsi+' · ':'')+p.alamat+'</div></div>'
                +'</div>';
        }).join('')
        : '<div class="t-drop-item"><div class="t-drop-sub" style="padding:4px 0">Tidak ada hasil</div></div>';

    sDrop.style.display = 'block';
});

sDrop.addEventListener('click', function(e){
    var item = e.target.closest('.t-drop-item[data-lat]');
    if(!item) return;
    var lat = parseFloat(item.dataset.lat);
    var lng = parseFloat(item.dataset.lng);
    map.flyTo([lat,lng],18,{ duration:1.3 });
    setTimeout(function(){
        markerGroup.eachLayer(function(lyr){
            if(!lyr.getLatLng) return;
            var ll = lyr.getLatLng();
            if(Math.abs(ll.lat-lat)<0.0001 && Math.abs(ll.lng-lng)<0.0001) lyr.openPopup();
        });
    },1400);
    sDrop.style.display='none'; sIn.value=''; sX.style.display='none';
});

sX.addEventListener('click', function(){
    sIn.value=''; sDrop.style.display='none'; this.style.display='none';
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
// Inisialisasi fitur routing (membuat kontrol rute di peta)
var routingControl = L.Routing.control({
    
    waypoints: [], // Titik awal & tujuan (kosong dulu)

    routeWhileDragging: true, 
    // Jika titik digeser → rute otomatis dihitung ulang

    lineOptions: {
        styles: [{ 
            color: '#3b82f6', // Warna garis rute (biru)
            opacity: 0.8,     // Transparansi
            weight: 6         // Ketebalan garis
        }]
    },

    createMarker: function() { 
        return null; 
    } 
    // Menghilangkan marker default (biar pakai marker custom)

}).addTo(map); 
// Menambahkan routing ke peta


// Fungsi untuk menentukan tujuan rute
window.setRoutingDest = function(lat, lng) {

    // Cek apakah browser mendukung GPS
    if (navigator.geolocation) {

        // Ambil lokasi user (titik awal)
        navigator.geolocation.getCurrentPosition(function(pos) {

            // Set titik awal (user) dan tujuan (gedung)
            routingControl.setWaypoints([
                L.latLng(pos.coords.latitude, pos.coords.longitude), // START (lokasi user)
                L.latLng(lat, lng) // DESTINATION (gedung yang diklik)
            ]);

            // Notifikasi ke user
            toast('Menghitung rute ke lokasi…');

        }, function() {

            // Jika gagal ambil lokasi GPS
            toast('Gagal mendapatkan lokasi. Menggunakan titik tengah peta.');

            // Gunakan titik tengah peta sebagai titik awal
            routingControl.setWaypoints([
                L.latLng(map.getCenter()), // START fallback
                L.latLng(lat, lng)         // DESTINATION
            ]);
        });

    } else {
        // Jika browser tidak support GPS

        routingControl.setWaypoints([
            L.latLng(map.getCenter()), // START fallback
            L.latLng(lat, lng)         // DESTINATION
        ]);
    }

    // Tutup popup setelah klik tombol rute
    map.closePopup();
};


// Event ketika rute berhasil ditemukan
routingControl.on('routesfound', function() {

    // Tampilkan tombol reset rute
    document.getElementById('btnResetRoute').style.display = 'flex';
});


// Event tombol reset rute
document.getElementById('btnResetRoute').addEventListener('click', function() {

    routingControl.setWaypoints([]); 
    // Menghapus rute dari peta

    this.style.display = 'none'; 
    // Sembunyikan tombol reset

    toast('Rute dihapus'); 
    // Tampilkan notifikasi
});

/* ── SIDEBAR ──────────────────────────────── */
var sidebar = document.getElementById('sidebar');
var sbClose = document.getElementById('sbClose');
var sbGallery = document.getElementById('sbGallery');
var sbLoading = document.getElementById('sbLoading');
var sbContent = document.getElementById('sbContent');

sbClose.addEventListener('click', function(){
    sidebar.classList.remove('show');
});

document.getElementById('sbBtnPhotos').addEventListener('click', function(){
    if(sbGallery.style.display === 'none') {
        sbGallery.style.display = 'block';
        this.innerHTML = '<i class="fas fa-chevron-up"></i> Sembunyikan Foto';
    } else {
        sbGallery.style.display = 'none';
        this.innerHTML = '<i class="fas fa-images"></i> Lihat Banyak Foto';
    }
});

window.openSidebar = function(id) {
    sidebar.classList.add('show');
    sbLoading.style.display = 'block';
    sbContent.style.display = 'none';
    sbGallery.style.display = 'none';
    document.getElementById('sbBtnPhotos').innerHTML = '<i class="fas fa-images"></i> Lihat Banyak Foto';

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
                var fasHtml = '<ul style="padding-left:18px; margin:0;">';
                data.fasilitas.forEach(function(f) {
                    fasHtml += '<li style="margin-bottom:6px;">' 
                            + '<strong>' + f.nama_fasilitas + '</strong>'
                            + (f.kategori ? ' <span style="font-size:0.7rem; background:rgba(255,255,255,0.05); padding:2px 6px; border-radius:4px; border:1px solid var(--border); ml-1">' + f.kategori + '</span>' : '')
                            + (f.keterangan ? '<div style="font-size:0.75rem; color:var(--muted);">' + f.keterangan + '</div>' : '')
                            + '</li>';
                });
                fasHtml += '</ul>';
                fasEl.innerHTML = fasHtml;
            } else {
                fasEl.innerHTML = 'Informasi fasilitas & kelas pada gedung ini belum tersedia saat ini.';
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