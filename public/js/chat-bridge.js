/**
 * chat-bridge.js  — v2
 * Jembatan dua arah: Chatbot AI ↔ Peta Leaflet ↔ Sidebar ↔ Routing
 * Letakkan di: public/js/chat-bridge.js
 * Include SETELAH public-peta.js di blade view.
 */

(function () {
    'use strict';

    /* ════════════════════════════════════════════
       AMBIL INSTANCE LEAFLET MAP
       Coba semua cara — tidak bergantung satu nama variabel saja.
    ════════════════════════════════════════════ */
    function getMap() {
        // 1. Coba window.map (jika sudah di-expose dari public-peta.js)
        if (window.map && typeof window.map.flyTo === 'function') return window.map;

        // 2. Coba nama lain yang umum dipakai
        var candidates = ['peta', 'leafletMap', 'myMap', 'mapInstance'];
        for (var i = 0; i < candidates.length; i++) {
            var m = window[candidates[i]];
            if (m && typeof m.flyTo === 'function') return m;
        }

        // 3. Cari semua instance Leaflet yang terdaftar di DOM
        //    Leaflet menyimpan instance di elemen._leaflet_map
        var mapEl = document.getElementById('map');
        if (mapEl && mapEl._leaflet_map) return mapEl._leaflet_map;
        if (mapEl && mapEl._leaflet_id) {
            // Leaflet >= 1.x: cari di L.Map._instances
            if (window.L && L.Map && L.Map._instances) {
                var instances = L.Map._instances;
                for (var key in instances) {
                    if (instances.hasOwnProperty(key)) return instances[key];
                }
            }
        }

        // 4. Fallback: cari elemen dengan class leaflet-container
        var containers = document.querySelectorAll('.leaflet-container');
        for (var j = 0; j < containers.length; j++) {
            if (containers[j]._leaflet_map) return containers[j]._leaflet_map;
        }

        return null;
    }

    /* ════════════════════════════════════════════
       AMBIL FUNGSI GLOBAL DARI public-peta.js
       Fungsi-fungsi ini di-expose via window.xxx
       di akhir IIFE public-peta.js
    ════════════════════════════════════════════ */
    function getAllData()      { return window.allData      || []; }
    function getRenderFn()    { return window.renderMarkers || null; }
    function getOpenSidebar() { return window.openSidebar  || null; }
    function getRoutingFn()   { return window.setRoutingDest || null; }
    
    function showToast(message) {
    // 1. Hapus toast lama jika masih ada (mencegah bentrok)
    var oldToast = document.getElementById('chatCustomToast');
    if (oldToast) oldToast.remove();

    // 2. Buat elemen toast baru
    var toast = document.createElement('div');
    toast.id = 'chatCustomToast';
    toast.innerHTML = message;
    
    // 3. Styling otomatis via JS
    Object.assign(toast.style, {
        position: 'fixed',
        bottom: '30px',
        left: '50%',
        transform: 'translateX(-50%)',
        backgroundColor: 'rgba(15, 23, 42, 0.95)',
        color: '#f8fafc',
        padding: '10px 22px',
        borderRadius: '50px',
        zIndex: '10001', // Pastikan di atas peta
        fontSize: '0.8rem',
        fontWeight: '600',
        boxShadow: '0 4px 15px rgba(0,0,0,0.3)',
        border: '1px solid rgba(59,130,246,0.3)',
        opacity: '0',
        transition: 'opacity 0.3s ease-in-out',
        pointerEvents: 'none'
    });

    document.body.appendChild(toast);

    // 4. Efek Fade In
    setTimeout(function() { toast.style.opacity = '1'; }, 10);

    // 5. Efek Fade Out lalu hapus setelah 3 detik
    setTimeout(function() {
        toast.style.opacity = '0';
        setTimeout(function() { 
            if (toast.parentNode) toast.remove(); 
        }, 300);
    }, 3000);
    }

    /* ════════════════════════════════════════════
       STATE
    ════════════════════════════════════════════ */
    var userLat        = null;
    var userLng        = null;
    var isOpen         = false;
    var highlightLayers = [];
    var isTyping       = false;

    /* ════════════════════════════════════════════
       GPS
    ════════════════════════════════════════════ */
    function initGPS() {
        if (!navigator.geolocation) return;
        navigator.geolocation.getCurrentPosition(function (pos) {
            userLat = pos.coords.latitude;
            userLng = pos.coords.longitude;
        }, function () { /* GPS ditolak */ });
    }
    initGPS();

    /* ════════════════════════════════════════════
       INJECT HTML WIDGET
    ════════════════════════════════════════════ */
    function injectWidget() {
        var html = [
            /* FAB */
            '<button id="chatFAB" onclick="ChatBridge.toggle()" aria-label="Buka Chatbot AI">',
            '  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">',
            '    <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>',
            '  </svg>',
            '  <span class="chat-fab-badge">AI</span>',
            '</button>',

            /* Window */
            '<div id="chatWindow" class="chat-window" style="display:none;">',

            /* Header */
            '  <div class="chat-header">',
            '    <div class="chat-header-left">',
            '      <div class="chat-avatar"><span>🤖</span></div>',
            '      <div>',
            '        <div class="chat-header-title">Asisten Kampus AI</div>',
            '        <div class="chat-header-sub" id="chatStatusText">Online · Siap membantu</div>',
            '      </div>',
            '    </div>',
            '    <div class="chat-header-actions">',
            '      <button class="chat-icon-btn" onclick="ChatBridge.clearHistory()" title="Hapus riwayat">',
            '        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">',
            '          <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/>',
            '          <path d="M10 11v6M14 11v6"/>',
            '        </svg>',
            '      </button>',
            '      <button class="chat-icon-btn" onclick="ChatBridge.toggle()" title="Tutup">',
            '        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">',
            '          <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>',
            '        </svg>',
            '      </button>',
            '    </div>',
            '  </div>',

            /* Quick Actions */
            '  <div class="chat-quick-wrap" id="chatQuickWrap">',
            '    <button class="chat-quick-btn" onclick="ChatBridge.quickSend(\'Gedung mana yang kosong sekarang?\')">🏛️ Gedung kosong</button>',
            '    <button class="chat-quick-btn" onclick="ChatBridge.quickSend(\'Gedung terdekat dari lokasi saya?\')">📍 Terdekat</button>',
            '    <button class="chat-quick-btn" onclick="ChatBridge.quickSend(\'Tampilkan semua gedung\')">🗺️ Semua gedung</button>',
            '    <button class="chat-quick-btn" onclick="ChatBridge.quickSend(\'Gedung yang sedang dipakai?\')">🔴 Sedang dipakai</button>',
            '  </div>',

            /* Messages */
            '  <div class="chat-messages" id="chatMessages">',
            '    <div class="chat-bubble bot">',
            '      <div class="chat-bubble-avatar">🤖</div>',
            '      <div class="chat-bubble-content">',
            '        <div class="chat-bubble-text">Halo! Saya asisten AI WebGIS Kampus. Saya bisa:<br>',
            '          <ul style="margin:6px 0 0 14px;padding:0;font-size:0.8rem;">',
            '            <li>🗺️ Arahkan rute ke gedung</li>',
            '            <li>🔍 Filter gedung berdasarkan status</li>',
            '            <li>📍 Rekomendasikan gedung terdekat</li>',
            '            <li>ℹ️ Berikan info detail gedung</li>',
            '          </ul>',
            '        </div>',
            '      </div>',
            '    </div>',
            '  </div>',

            /* Input */
            '  <div class="chat-input-wrap">',
            '    <div class="chat-input-box">',
            '      <input type="text" id="chatInput" placeholder="Ketik pesan... (Enter untuk kirim)" autocomplete="off" />',
            '      <button id="chatMicBtn" class="chat-mic-btn" onclick="ChatBridge.toggleVoice()" title="Voice input">',
            '        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">',
            '          <path d="M12 1a3 3 0 00-3 3v8a3 3 0 006 0V4a3 3 0 00-3-3z"/>',
            '          <path d="M19 10v2a7 7 0 01-14 0v-2M12 19v4M8 23h8"/>',
            '        </svg>',
            '      </button>',
            '      <button class="chat-send-btn" onclick="ChatBridge.send()">',
            '        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">',
            '          <line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/>',
            '        </svg>',
            '      </button>',
            '    </div>',
            '    <div style="text-align:center;font-size:0.6rem;color:rgba(255,255,255,0.35);margin-top:4px;">Powered by Gemini AI · Data realtime kampus</div>',
            '  </div>',

            '</div>',
        ].join('\n');

        var container  = document.createElement('div');
        container.id   = 'chatBridgeRoot';
        container.innerHTML = html;
        document.body.appendChild(container);

        injectStyles();
        bindEvents();
    }

    /* ════════════════════════════════════════════
       STYLES
    ════════════════════════════════════════════ */
    function injectStyles() {
        var css = [
            '#chatFAB{position:fixed;bottom:24px;right:24px;z-index:10000;width:52px;height:52px;border-radius:50%;background:linear-gradient(135deg,#3b82f6,#1d4ed8);border:none;cursor:pointer;box-shadow:0 4px 20px rgba(59,130,246,.55);color:#fff;display:flex;align-items:center;justify-content:center;transition:transform .2s,box-shadow .2s;}',
            '#chatFAB:hover{transform:scale(1.1);box-shadow:0 6px 28px rgba(59,130,246,.7);}',
            '#chatFAB svg{width:22px;height:22px;}',
            '.chat-fab-badge{position:absolute;top:-4px;right:-4px;background:#22c55e;color:#fff;font-size:0.55rem;font-weight:800;padding:2px 5px;border-radius:8px;letter-spacing:.5px;border:1.5px solid #0f172a;}',
            '.chat-window{position:fixed;bottom:88px;right:24px;z-index:9999;width:360px;max-width:calc(100vw - 32px);height:520px;max-height:calc(100vh - 120px);background:#0f172a;border:1px solid rgba(59,130,246,.25);border-radius:18px;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.6);animation:chatSlideUp .25s ease;}',
            '@keyframes chatSlideUp{from{opacity:0;transform:translateY(16px) scale(.97)}to{opacity:1;transform:translateY(0) scale(1)}}',
            '.chat-header{display:flex;align-items:center;justify-content:space-between;padding:14px 16px;background:rgba(59,130,246,.12);border-bottom:1px solid rgba(59,130,246,.18);}',
            '.chat-header-left{display:flex;align-items:center;gap:10px;}',
            '.chat-avatar{width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#3b82f6,#8b5cf6);display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0;}',
            '.chat-header-title{font-size:.85rem;font-weight:700;color:#f1f5f9;letter-spacing:.2px;}',
            '.chat-header-sub{font-size:.65rem;color:#22c55e;font-weight:500;}',
            '.chat-header-actions{display:flex;gap:6px;}',
            '.chat-icon-btn{background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.1);color:rgba(255,255,255,.7);padding:5px;border-radius:8px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background .15s;}',
            '.chat-icon-btn:hover{background:rgba(255,255,255,.14);}',
            '.chat-quick-wrap{display:flex;gap:6px;padding:8px 12px;overflow-x:auto;scrollbar-width:none;border-bottom:1px solid rgba(255,255,255,.05);}',
            '.chat-quick-wrap::-webkit-scrollbar{display:none;}',
            '.chat-quick-btn{flex-shrink:0;padding:5px 10px;background:rgba(59,130,246,.12);border:1px solid rgba(59,130,246,.25);border-radius:100px;color:#93c5fd;font-size:.65rem;font-weight:600;cursor:pointer;white-space:nowrap;transition:all .15s;font-family:inherit;}',
            '.chat-quick-btn:hover{background:rgba(59,130,246,.25);color:#fff;}',
            '.chat-messages{flex:1;overflow-y:auto;padding:12px;display:flex;flex-direction:column;gap:10px;scrollbar-width:thin;scrollbar-color:rgba(255,255,255,.1) transparent;}',
            '.chat-messages::-webkit-scrollbar{width:4px;}',
            '.chat-messages::-webkit-scrollbar-thumb{background:rgba(255,255,255,.12);border-radius:4px;}',
            '.chat-bubble{display:flex;gap:8px;max-width:100%;align-items:flex-end;}',
            '.chat-bubble.user{flex-direction:row-reverse;}',
            '.chat-bubble-avatar{width:28px;height:28px;border-radius:50%;background:rgba(59,130,246,.2);display:flex;align-items:center;justify-content:center;font-size:.8rem;flex-shrink:0;}',
            '.chat-bubble-content{max-width:82%;}',
            '.chat-bubble-text{padding:9px 12px;border-radius:14px;font-size:.78rem;line-height:1.5;word-wrap:break-word;}',
            '.chat-bubble.bot .chat-bubble-text{background:rgba(255,255,255,.07);color:#e2e8f0;border-radius:4px 14px 14px 14px;}',
            '.chat-bubble.user .chat-bubble-text{background:linear-gradient(135deg,#3b82f6,#2563eb);color:#fff;border-radius:14px 14px 4px 14px;}',
            '.chat-bubble-time{font-size:.58rem;color:rgba(255,255,255,.3);margin-top:3px;}',
            '.chat-bubble.user .chat-bubble-time{text-align:right;}',
            '.chat-action-card{margin-top:8px;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.1);border-radius:10px;padding:8px 10px;font-size:.72rem;}',
            '.chat-action-btn{display:block;width:100%;margin-top:7px;padding:7px 12px;background:linear-gradient(135deg,#3b82f6,#2563eb);color:#fff;border:none;border-radius:8px;font-size:.72rem;font-weight:700;cursor:pointer;font-family:inherit;transition:opacity .15s;}',
            '.chat-action-btn:hover{opacity:.88;}',
            '.chat-nearest-item{display:flex;align-items:center;justify-content:space-between;padding:5px 0;border-bottom:1px solid rgba(255,255,255,.06);}',
            '.chat-nearest-item:last-child{border:none;}',
            '.chat-nearest-name{font-weight:600;color:#f1f5f9;font-size:.75rem;}',
            '.chat-nearest-dist{font-size:.65rem;color:#94a3b8;}',
            '.chat-nearest-badge{font-size:.6rem;padding:2px 7px;border-radius:100px;font-weight:700;}',
            '.badge-kosong{background:rgba(34,197,94,.15);color:#22c55e;}',
            '.badge-dipakai{background:rgba(59,130,246,.15);color:#3b82f6;}',
            '.badge-tutup{background:rgba(107,114,128,.15);color:#9ca3af;}',
            '.chat-typing{display:flex;gap:4px;align-items:center;padding:9px 12px;background:rgba(255,255,255,.07);border-radius:4px 14px 14px 14px;width:fit-content;}',
            '.chat-typing span{width:6px;height:6px;border-radius:50%;background:#94a3b8;animation:chatTyping 1.2s infinite;}',
            '.chat-typing span:nth-child(2){animation-delay:.2s;}',
            '.chat-typing span:nth-child(3){animation-delay:.4s;}',
            '@keyframes chatTyping{0%,80%,100%{transform:scale(.8);opacity:.5}40%{transform:scale(1);opacity:1}}',
            '.chat-input-wrap{padding:10px 12px 12px;border-top:1px solid rgba(255,255,255,.06);}',
            '.chat-input-box{display:flex;align-items:center;gap:6px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:12px;padding:6px 6px 6px 12px;}',
            '.chat-input-box:focus-within{border-color:rgba(59,130,246,.5);background:rgba(59,130,246,.05);}',
            '#chatInput{flex:1;background:transparent;border:none;outline:none;color:#f1f5f9;font-size:.8rem;font-family:inherit;}',
            '#chatInput::placeholder{color:rgba(255,255,255,.3);}',
            '.chat-send-btn,.chat-mic-btn{width:30px;height:30px;border-radius:8px;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .15s;flex-shrink:0;}',
            '.chat-send-btn{background:#3b82f6;color:#fff;}',
            '.chat-send-btn:hover{background:#2563eb;}',
            '.chat-mic-btn{background:rgba(255,255,255,.07);color:rgba(255,255,255,.6);}',
            '.chat-mic-btn:hover{background:rgba(255,255,255,.14);}',
            '.chat-mic-btn.listening{background:rgba(239,68,68,.2);color:#ef4444;animation:chatPulse 1s infinite;}',
            '@keyframes chatPulse{0%,100%{box-shadow:0 0 0 0 rgba(239,68,68,.4)}70%{box-shadow:0 0 0 8px rgba(239,68,68,0)}}',
            '@media(max-width:480px){.chat-window{width:calc(100vw - 16px);right:8px;bottom:80px;height:420px;}}',
        ].join('');

        var style = document.createElement('style');
        style.textContent = css;
        document.head.appendChild(style);
    }

    /* ════════════════════════════════════════════
       EVENTS
    ════════════════════════════════════════════ */
    function bindEvents() {
        setTimeout(function () {
            var input = document.getElementById('chatInput');
            if (input) {
                input.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter' && !e.shiftKey) {
                        e.preventDefault();
                        ChatBridge.send();
                    }
                });
            }
        }, 100);
    }

    /* ════════════════════════════════════════════
       VOICE INPUT
    ════════════════════════════════════════════ */
    var recognition = null;
    var isListening = false;

    function initVoice() {
        var SR = window.SpeechRecognition || window.webkitSpeechRecognition;
        if (!SR) return;
        recognition = new SR();
        recognition.lang = 'id-ID';
        recognition.continuous = false;
        recognition.interimResults = false;
        recognition.onresult = function (e) {
            var transcript = e.results[0][0].transcript;
            var input = document.getElementById('chatInput');
            if (input) { input.value = transcript; ChatBridge.send(); }
        };
        recognition.onend = function () {
            isListening = false;
            var btn = document.getElementById('chatMicBtn');
            if (btn) btn.classList.remove('listening');
        };
    }
    initVoice();

    /* ════════════════════════════════════════════
       HELPERS
    ════════════════════════════════════════════ */
    function timeNow() {
        return new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
    }

    function appendBubble(role, htmlContent) {
        var container = document.getElementById('chatMessages');
        if (!container) return;

        var avatarHtml = role === 'bot'
            ? '<div class="chat-bubble-avatar">🤖</div>'
            : '<div class="chat-bubble-avatar" style="background:rgba(59,130,246,.3);">👤</div>';

        var div = document.createElement('div');
        div.className = 'chat-bubble ' + role;
        div.innerHTML = avatarHtml
            + '<div class="chat-bubble-content">'
            + '<div class="chat-bubble-text">' + htmlContent + '</div>'
            + '<div class="chat-bubble-time">' + timeNow() + '</div>'
            + '</div>';

        if (role === 'user') {
            var qw = document.getElementById('chatQuickWrap');
            if (qw) qw.style.display = 'none';
        }

        container.appendChild(div);
        container.scrollTop = container.scrollHeight;
        return div;
    }

    function showTyping() {
        var container = document.getElementById('chatMessages');
        if (!container) return null;
        var div = document.createElement('div');
        div.className = 'chat-bubble bot';
        div.id = 'chatTypingBubble';
        div.innerHTML = '<div class="chat-bubble-avatar">🤖</div>'
            + '<div class="chat-bubble-content">'
            + '<div class="chat-typing"><span></span><span></span><span></span></div>'
            + '</div>';
        container.appendChild(div);
        container.scrollTop = container.scrollHeight;
        return div;
    }

    function removeTyping() {
        var el = document.getElementById('chatTypingBubble');
        if (el) el.remove();
    }

    function clearHighlights() {
        var m = getMap();
        highlightLayers.forEach(function (l) { if (l && m) m.removeLayer(l); });
        highlightLayers = [];
    }

    function statusText(kondisi) {
        if (kondisi === 'Sedang Dipakai') return '<span class="chat-nearest-badge badge-dipakai">Dipakai</span>';
        if (kondisi === 'Tutup')          return '<span class="chat-nearest-badge badge-tutup">Tutup</span>';
        return '<span class="chat-nearest-badge badge-kosong">Kosong</span>';
    }

    function formatJarak(m) {
        return m >= 1000 ? (m / 1000).toFixed(1) + ' km' : Math.round(m) + ' m';
    }

    /* ════════════════════════════════════════════
       FLY TO — wrapper aman dengan fallback
    ════════════════════════════════════════════ */
    function safeFlyTo(lat, lng, zoom) {
        zoom = zoom || 18;
        var m = getMap();
        if (!m) {
            console.warn('[ChatBridge] getMap() returned null — peta belum ready');
            return;
        }
        try {
            m.flyTo([lat, lng], zoom, { duration: 1.5 });
        } catch (e) {
            console.error('[ChatBridge] flyTo error:', e);
            try { m.setView([lat, lng], zoom); } catch(e2) { /* silent */ }
        }
    }

    /* ════════════════════════════════════════════
       EXECUTE ACTION
    ════════════════════════════════════════════ */
    function executeAction(parsed) {
        var action = parsed.action;

        /* ── 1. navigate ─────────────────────────── */
        if (action === 'navigate') {
            var dest = parsed.destination;
            var html = parsed.message || '';
            if (dest) {
                html += '<div class="chat-action-card">'
                    + '<div style="font-weight:700;color:#f1f5f9;margin-bottom:4px;">📍 ' + dest.nama + '</div>'
                    + '<button class="chat-action-btn" id="chatNavBtn">🧭 Mulai Navigasi</button>'
                    + '</div>';
            }
            appendBubble('bot', html);

            if (dest) {
                setTimeout(function () {
                    var btn = document.getElementById('chatNavBtn');
                    if (btn) {
                        btn.onclick = function () {
                            ChatBridge.toggle();
                            var routeFn = getRoutingFn();
                            if (routeFn) {
                                routeFn(dest.lat, dest.lng);
                                showToast('🧭 Menghitung rute ke ' + dest.nama);
                            } else {
                                safeFlyTo(dest.lat, dest.lng, 18);
                                showToast('📍 ' + dest.nama + ' ditampilkan di peta');
                            }
                        };
                    }
                }, 50);
            }
            return;
        }

        /* ── 2. fly_to ───────────────────────────── */
        if (action === 'fly_to') {
            appendBubble('bot', parsed.message || '');
            safeFlyTo(parsed.lat, parsed.lng, parsed.zoom || 18);
            if (parsed.open_sidebar && parsed.gedung_id) {
                var sidebarFn = getOpenSidebar();
                if (sidebarFn) setTimeout(function () { sidebarFn(parsed.gedung_id); }, 1600);
            }
            return;
        }

        /* ── 3. filter_map ───────────────────────── */
        if (action === 'filter_map') {
            var ids         = parsed.highlight_ids || [];
            var filterValue = parsed.filter_value  || 'all';
            var allData     = getAllData();
            var renderFn    = getRenderFn();

            var html = parsed.message || '';
            if (parsed.count !== undefined) {
                html += ' <span style="color:#22c55e;font-weight:700;">(' + parsed.count + ' gedung)</span>';
            }
            appendBubble('bot', html);

            if (renderFn && allData.length) {
                clearHighlights();
                var filtered = filterValue === 'all'
                    ? allData
                    : allData.filter(function (f) { return f.properties.kondisi === filterValue; });

                renderFn(filtered);

                var m = getMap();
                if (m && filtered.length) {
                    filtered.forEach(function (f) {
                        if (!ids.length || ids.indexOf(f.properties.id) !== -1) {
                            var lat = f.geometry.coordinates[1];
                            var lng = f.geometry.coordinates[0];
                            var ring = L.circleMarker([lat, lng], {
                                radius: 18, color: '#22c55e', weight: 2.5, fill: false, opacity: 0.8
                            }).addTo(m);
                            highlightLayers.push(ring);
                        }
                    });

                    var pts = filtered.map(function (f) {
                        return [f.geometry.coordinates[1], f.geometry.coordinates[0]];
                    });
                    try { m.fitBounds(L.latLngBounds(pts).pad(0.2)); } catch(e) {}
                }
            } else {
                appendBubble('bot', '⚠️ Data peta belum siap. Coba beberapa saat lagi.');
            }
            return;
        }

        /* ── 4. nearest ──────────────────────────── */
        if (action === 'nearest') {
            var results = parsed.results || [];
            var html = '<div style="font-weight:700;color:#f1f5f9;margin-bottom:8px;">📍 ' + (parsed.message || '') + '</div>';
            html += '<div class="chat-action-card">';
            results.forEach(function (r, i) {
                html += '<div class="chat-nearest-item">'
                    + '<div><div class="chat-nearest-name">' + (i + 1) + '. ' + r.nama + '</div>'
                    + '<div class="chat-nearest-dist">' + formatJarak(r.jarak_meter) + '</div></div>'
                    + statusText(r.kondisi) + '</div>';
            });
            if (parsed.top_id) {
                html += '<button class="chat-action-btn" id="chatNearestBtn">📍 Tampilkan di Peta</button>';
            }
            html += '</div>';
            appendBubble('bot', html);

            if (results[0]) safeFlyTo(results[0].lat, results[0].lng, 18);

            clearHighlights();
            var mapInst = getMap();
            if (mapInst) {
                results.forEach(function (r, i) {
                    var ring = L.circleMarker([r.lat, r.lng], {
                        radius: 16 - i * 3,
                        color: i === 0 ? '#22c55e' : '#3b82f6',
                        weight: 2, fill: false, opacity: 0.85
                    }).addTo(mapInst);
                    highlightLayers.push(ring);
                });
            }

            if (parsed.top_id) {
                setTimeout(function () {
                    var btn = document.getElementById('chatNearestBtn');
                    if (btn) {
                        btn.onclick = function () {
                            ChatBridge.toggle();
                            var sidebarFn = getOpenSidebar();
                            if (sidebarFn) sidebarFn(parsed.top_id);
                        };
                    }
                }, 50);
            }
            return;
        }

        /* ── 5. open_sidebar ─────────────────────── */
        if (action === 'open_sidebar') {
            appendBubble('bot', parsed.message || '');
            if (parsed.lat && parsed.lng) safeFlyTo(parsed.lat, parsed.lng, 18);
            if (parsed.gedung_id) {
                var sidebarFn = getOpenSidebar();
                if (sidebarFn) setTimeout(function () { sidebarFn(parsed.gedung_id); }, 1600);
            }
            return;
        }

        /* ── 6. list_info ────────────────────────── */
        if (action === 'list_info') {
            var html = parsed.message || '';
            if (parsed.items && parsed.items.length) {
                html += '<ul style="margin:8px 0 0 14px;padding:0;font-size:0.76rem;line-height:1.7;">';
                parsed.items.forEach(function (item) { html += '<li>' + item + '</li>'; });
                html += '</ul>';
            }
            appendBubble('bot', html);
            return;
        }

        /* ── 7. multi ────────────────────────────── */
        if (action === 'multi') {
            appendBubble('bot', parsed.message || '');
            if (parsed.steps && parsed.steps.length) {
                parsed.steps.forEach(function (step, i) {
                    setTimeout(function () { executeAction(step); }, i * 700);
                });
            }
            return;
        }

        /* ── 8. reject ───────────────────────────── */
        if (action === 'reject') {
            appendBubble('bot', '⚠️ ' + (parsed.message || 'Pertanyaan di luar konteks kampus.'));
            return;
        }

        /* ── 9. list_buildings ───────────────────────── */
        if (action === 'list_buildings') {
            var html = '<div style="margin-bottom:8px; font-weight:600;">' + (parsed.message || 'Daftar Gedung:') + '</div>';
            var bldgs = parsed.buildings || [];
            
            bldgs.forEach(function(b) {
                // Membuat tombol untuk setiap gedung. 
                // Jika diklik, otomatis mengirim pesan "arahkan saya ke [Nama Gedung]"
                html += '<button class="chat-action-btn" style="margin-top:6px; text-align:left; padding:8px 12px; background:rgba(59,130,246,0.15); border:1px solid rgba(59,130,246,0.4); color:#e2e8f0; display:flex; justify-content:space-between; align-items:center;" onclick="ChatBridge.quickSend(\'arahkan saya ke ' + b.nama + '\')">'
                      + '<span>🏢 ' + b.nama + '</span>'
                      + '<span style="font-size:0.6rem; color:#93c5fd; background:#1e3a8a; padding:2px 6px; border-radius:4px;">📍 Navigasi</span>'
                      + '</button>';
            });
            
            appendBubble('bot', html);
            return;
        }

        /* ── Fallback ────────────────────────────── */
        appendBubble('bot', parsed.message || 'Maaf, saya tidak mengerti permintaan Anda.');
    }

    /* ════════════════════════════════════════════
       PUBLIC API
    ════════════════════════════════════════════ */
    window.ChatBridge = {

        toggle: function () {
            var win = document.getElementById('chatWindow');
            if (!win) return;
            isOpen = !isOpen;
            win.style.display = isOpen ? 'flex' : 'none';
            if (isOpen) {
                setTimeout(function () {
                    var input = document.getElementById('chatInput');
                    if (input) input.focus();
                }, 100);
            }
        },

        send: function () {
            var input = document.getElementById('chatInput');
            if (!input) return;
            var msg = input.value.trim();
            if (!msg || isTyping) return;
            input.value = '';
            this._sendMessage(msg);
        },

        quickSend: function (msg) {
            var input = document.getElementById('chatInput');
            if (input) input.value = msg;
            this.send();
        },

        _sendMessage: function (msg) {
            isTyping = true;
            appendBubble('user', msg);
            showTyping();

            var statusEl = document.getElementById('chatStatusText');
            if (statusEl) { statusEl.textContent = 'Sedang berpikir...'; statusEl.style.color = '#f59e0b'; }

            var payload = { message: msg };
            if (userLat && userLng) { payload.user_lat = userLat; payload.user_lng = userLng; }

            var csrfMeta = document.querySelector('meta[name="csrf-token"]');
            var csrfToken = csrfMeta ? csrfMeta.content : '';

            fetch('/api/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(payload)
            })
            .then(function (r) {
                if (!r.ok) throw new Error('HTTP ' + r.status);
                return r.json();
            })
            .then(function (data) {
                removeTyping();
                isTyping = false;
                if (statusEl) { statusEl.textContent = 'Online · Siap membantu'; statusEl.style.color = '#22c55e'; }

                if (data.error) {
                    appendBubble('bot', '❌ Error: ' + (data.error.message || data.error));
                    return;
                }

                var content = data.choices && data.choices[0] && data.choices[0].message
                    ? data.choices[0].message.content : '{}';

                var parsed;
                try { parsed = JSON.parse(content); }
                catch (e) { parsed = { action: 'list_info', message: content, items: [] }; }

                executeAction(parsed);
            })
            .catch(function (err) {
                removeTyping();
                isTyping = false;
                if (statusEl) { statusEl.textContent = 'Online · Siap membantu'; statusEl.style.color = '#22c55e'; }
                appendBubble('bot', '❌ Gagal menghubungi server. Coba lagi.');
                console.error('[ChatBridge] fetch error:', err);
            });
        },

        clearHistory: function () {
            var container = document.getElementById('chatMessages');
            if (!container) return;
            container.innerHTML = '<div class="chat-bubble bot"><div class="chat-bubble-avatar">🤖</div><div class="chat-bubble-content"><div class="chat-bubble-text">Riwayat dibersihkan. Ada yang bisa saya bantu?</div></div></div>';
            clearHighlights();
            var qw = document.getElementById('chatQuickWrap');
            if (qw) qw.style.display = 'flex';
        },

        toggleVoice: function () {
            if (!recognition) {
                showToast('Browser tidak mendukung voice input');
                return;
            }
            var btn = document.getElementById('chatMicBtn');
            if (isListening) {
                recognition.stop();
                isListening = false;
                if (btn) btn.classList.remove('listening');
            } else {
                recognition.start();
                isListening = true;
                if (btn) btn.classList.add('listening');
                showToast('🎤 Mendengarkan...');
            }
        },

        sendFromMap: function (msg) {
            var win = document.getElementById('chatWindow');
            if (win && !isOpen) { isOpen = true; win.style.display = 'flex'; }
            this._sendMessage(msg);
        }
    };

    /* ════════════════════════════════════════════
       INIT
    ════════════════════════════════════════════ */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', injectWidget);
    } else {
        injectWidget();
    }

})();
