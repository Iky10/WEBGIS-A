<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name') }}</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"
          integrity="sha512-iBBXm8fW90+nuLcSKlbmrPcLa0OT92xO1BIsZ+ywDWZCvqsWgccV3gFoRBv0z+8dLJgyAHIhR35VZc2oM/gI1w=="
          crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap4-toggle/3.6.1/bootstrap4-toggle.min.css"
          integrity="sha512-EzrsULyNzUc4xnMaqTrB4EpGvudqpetxG/WNjCpG6ZyyAGxeB6OBF9o246+mwx3l/9Cn838iLIcrxpPHTiygAA=="
          crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- AdminLTE -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.1.0/css/adminlte.min.css"
          integrity="sha512-mxrUXSjrxl8vm5GwafxcqTrEwO1/oBNU25l20GODsysHReZo4uhVISzAKzaABH6/tTfAxZrY2FprmeAP5UZY8A=="
          crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- iCheck -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/icheck-bootstrap/3.0.1/icheck-bootstrap.min.css"
          integrity="sha512-8vq2g5nHE062j3xor4XxPeZiPjmRDh6wlufQlfC6pdQ/9urJkU07NM0tEREeymP++NczacJ/Q59ul+/K2eYvcg=="
          crossorigin="anonymous"/>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css"
          integrity="sha512-nMNlpuaDPrqlEls3IX/Q56H36qvBASwb3ipuo3MxeWbsQB1881ox0cRv7UPTgBlriqoynt35KjEwgGUeUXIPnw=="
          crossorigin="anonymous"/>

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"
          integrity="sha512-aEe/ZxePawj0+G2R+AaIxgrQuKT68I28qh+wgLrcAJOz3rxCP+TwrK5SPN+E5I+1IQjNtcfvb96HDagwrKRdBw=="
          crossorigin="anonymous"/>

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Toastr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <!-- DataTables -->
    @include('layouts.datatables_css')

    <!-- Admin Custom -->
    <link rel="stylesheet" href="{{ asset('css/admin-custom.css') }}?v={{ filemtime(public_path('css/admin-custom.css')) }}">

    {{-- Notif Bell Styling --}}
    <style>
        .notif-bell-wrapper .nav-link {
            position: relative; padding: .5rem .75rem;
        }
        .notif-bell-wrapper .nav-link i {
            font-size: 1.2rem; color: #495057;
        }
        .notif-bell-wrapper .nav-link:hover i { color: #007bff; }
        .notif-bell-wrapper .navbar-badge {
            position: absolute; top: 4px; right: 2px;
            font-size: .65rem; padding: 2px 5px;
            border-radius: 10px; line-height: 1;
        }
        .notif-bell-wrapper .nav-link.has-pending i {
            color: #f39c12;
            animation: bellShake 2.5s ease-in-out infinite;
        }
        @keyframes bellShake {
            0%, 88%, 100% { transform: rotate(0deg); }
            90%, 94% { transform: rotate(-12deg); }
            92%, 96% { transform: rotate(12deg); }
        }
        .notif-dropdown {
            min-width: 360px; max-width: 380px; padding: 0;
            box-shadow: 0 8px 24px rgba(0,0,0,.12);
            border: 1px solid #e9ecef; border-radius: 8px;
            overflow: hidden;
            max-height: 80vh; overflow-y: auto;
        }
        /* Mobile: notif dropdown fit viewport (constrain width + position right edge) */
        @media (max-width: 575.98px) {
            .notif-bell-wrapper .notif-dropdown {
                position: absolute !important;
                top: 100% !important;
                right: 0 !important;
                left: auto !important;
                min-width: 0 !important;
                width: calc(100vw - 16px) !important;
                max-width: calc(100vw - 16px) !important;
                margin-top: 6px !important;
                margin-right: 8px;
                transform: none !important;
                max-height: 75vh;
            }
            .notif-bell-wrapper {
                position: relative !important;
            }
            /* Tighter padding di item supaya muat */
            .notif-item {
                padding: 10px 12px !important;
            }
            .notif-item .notif-meta {
                gap: 6px !important;
                font-size: .72rem !important;
            }
            .notif-item .notif-meta span {
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                max-width: 100%;
            }
            /* Admin user-menu dropdown: same fix supaya fit viewport */
            .user-menu .admin-user-menu {
                position: absolute !important;
                top: 100% !important;
                right: 0 !important;
                left: auto !important;
                min-width: 0 !important;
                width: calc(100vw - 16px) !important;
                max-width: 320px !important;
                margin-top: 6px !important;
                margin-right: 8px;
                transform: none !important;
            }
            .user-menu {
                position: relative !important;
            }
        }
        .notif-dropdown .dropdown-header {
            background: #f8f9fa; font-weight: 600; color: #2c3e50;
            padding: 10px 14px; font-size: .9rem;
        }
        .notif-item {
            display: block; padding: 10px 14px; border-bottom: 1px solid #f1f3f5;
            transition: background .15s; text-decoration: none; color: #2c3e50;
        }
        .notif-item:last-child { border-bottom: none; }
        .notif-item:hover {
            background: #f8f9fa; text-decoration: none;
            color: #2c3e50;
        }
        .notif-item .notif-kode {
            font-weight: 600; font-size: .85rem; color: #007bff;
        }
        .notif-item .notif-urgent-badge {
            display: inline-block; background: #e74c3c; color: #fff;
            font-size: .65rem; padding: 1px 6px; border-radius: 8px;
            margin-left: 6px; font-weight: 600;
        }
        .notif-item .notif-meta {
            font-size: .78rem; color: #7f8c8d; margin-top: 2px;
            display: flex; flex-wrap: wrap; gap: 8px;
        }
        .notif-item .notif-meta i { margin-right: 3px; }
        .notif-item .notif-time {
            font-size: .72rem; color: #95a5a6; margin-top: 4px;
        }
        .notif-empty {
            padding: 24px 14px; text-align: center; color: #95a5a6;
        }
        .notif-empty i { font-size: 1.8rem; margin-bottom: 8px; display: block; }
        .dropdown-footer {
            background: #f8f9fa; font-size: .85rem; color: #007bff !important;
            font-weight: 500; padding: 10px;
        }
        .dropdown-footer:hover { background: #e9ecef; }
    </style>

    @stack('third_party_stylesheets')

    @stack('page_css')
</head>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <!-- Main Header -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
        </ul>

        <ul class="navbar-nav ml-auto">
            {{-- Notifikasi Bell (admin only) --}}
            @if(Auth::check() && Auth::user()->isAdmin())
                <li class="nav-item dropdown notif-bell-wrapper">
                    <a href="#" class="nav-link" data-toggle="dropdown" data-display="static" id="notif-bell-toggle"
                       title="Notifikasi Pengajuan Pending">
                        <i class="far fa-bell"></i>
                        <span class="badge badge-danger navbar-badge notif-badge" id="notif-bell-count" style="display:none;">0</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right notif-dropdown" id="notif-dropdown">
                        <span class="dropdown-item dropdown-header" id="notif-header">
                            <i class="fas fa-clock mr-1"></i> Pengajuan Menunggu
                        </span>
                        <div class="dropdown-divider"></div>
                        <div id="notif-list-container">
                            <div class="dropdown-item text-center text-muted py-3">
                                <i class="fas fa-spinner fa-spin"></i> Memuat...
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('pengajuan_ruangans.index') }}" class="dropdown-item dropdown-footer text-center">
                            <i class="fas fa-list mr-1"></i> Lihat Semua Pengajuan
                        </a>
                    </div>
                </li>
            @endif

            <li class="nav-item dropdown user-menu">
                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" data-display="static">
                    <img src="https://assets.infyom.com/logo/blue_logo_150x150.png"
                         class="user-image img-circle elevation-2" alt="User Image">
                    <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right admin-user-menu">
                    <!-- User image -->
                    <li class="user-header bg-primary">
                        <img src="https://assets.infyom.com/logo/blue_logo_150x150.png"
                             class="img-circle elevation-2"
                             alt="User Image">
                        <p>
                            {{ Auth::user()->name }}
                            <small>Member since {{ Auth::user()->created_at->format('M. Y') }}</small>
                        </p>
                    </li>
                    <!-- Menu Footer-->
                    <li class="user-footer">
                        <a href="#" class="btn btn-default btn-flat">Profile</a>
                        <a href="#" class="btn btn-default btn-flat float-right"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            Sign out
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>

    <!-- Left side column. contains the logo and sidebar -->
@include('layouts.sidebar')

<!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <section class="content">
            @yield('content')
        </section>
    </div>

    <!-- Main Footer -->
    <footer class="main-footer">
        <div class="float-right d-none d-sm-block">
            <b>WebGIS</b> v2.0
        </div>
        <strong>
           &copy; {{ date('Y') }} <a href="{{ url('/') }}">WebGIS Politeknik Negeri Samarinda</a>.
        </strong>
        Sistem Informasi Geografis Gedung.
    </footer>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"
        integrity="sha512-bLT0Qm9VnAYZDflyKcBaQ2gg0hSYNQrJ8RilYldYQ1FxQYoCLtUjuuRuZo+fjqhx/qtq/1itJ0C2ejDxltZVFg=="
        crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"
        integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN"
        crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js"
        integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s"
        crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script>

<!-- AdminLTE App -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.1.0/js/adminlte.min.js"
        integrity="sha512-AJUWwfMxFuQLv1iPZOTZX0N/jTCIrLxyZjTRKQostNU71MzZTEPHjajSK20Kj1TwJELpP7gl+ShXw5brpnKwEg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"
        integrity="sha512-qTXRIMyZIFb8iQcfjXWCO8+M5Tbc38Qi5WzdPOYZHIlZpzBHG3L3by84BBBOiRGiEb7KKtAOAs5qYdUiZiQNNQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"
        integrity="sha512-GDey37RZAxFkpFeJorEUwNoIbkTwsyC736KNSYucu1WJWFK9qTdzYub8ATxktr6Dwke7nbFaioypzbDOQykoRg=="
        crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"
        integrity="sha512-2ImtlRlf2VVmiGZsjm9bEyhjGW4dU7B6TNwh/hx/iSByxNENtj3WVE6o/9Lj4TJeVXPi4bnOIMXFIJJAeufa0A=="
        crossorigin="anonymous"></script>
        
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-switch/1.3/bootstrapSwitch.min.js"
        integrity="sha512-DAc/LqVY2liDbikmJwUS1MSE3pIH0DFprKHZKPcJC7e3TtAOzT55gEMTleegwyuIWgCfOPOM8eLbbvFaG9F/cA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Toastr -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script src="{{ asset('js/layout-app.js') }}"></script>

{{-- SweetAlert2: Global Confirm Functions --}}
<script>
    /**
     * Konfirmasi hapus data dengan SweetAlert2
     * @param {HTMLElement} formEl - Form element yang akan di-submit
     * @param {string} message - Pesan konfirmasi (opsional)
     */
    function confirmDelete(formEl, message) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: message || 'Data yang dihapus tidak dapat dikembalikan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-trash-alt mr-1"></i> Ya, hapus!',
            cancelButtonText: '<i class="fas fa-times mr-1"></i> Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                formEl.submit();
            }
        });
    }

    /**
     * Konfirmasi aksi umum dengan SweetAlert2
     * @param {HTMLElement} formEl - Form element yang akan di-submit
     * @param {string} title - Judul dialog
     * @param {string} message - Pesan konfirmasi
     * @param {string} icon - Icon SweetAlert2 (warning, question, info)
     * @param {string} confirmText - Teks tombol konfirmasi
     */
    function confirmAction(formEl, title, message, icon, confirmText) {
        Swal.fire({
            title: title || 'Apakah Anda yakin?',
            text: message || '',
            icon: icon || 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#6c757d',
            confirmButtonText: confirmText || 'Ya, lanjutkan!',
            cancelButtonText: '<i class="fas fa-times mr-1"></i> Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                formEl.submit();
            }
        });
    }
</script>

<!-- DataTables -->
@include('layouts.datatables_js')

{{-- Toastr: Konversi Flash Message → Toast --}}
<script>
    $(function () {
        // Konfigurasi Toastr
        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: 'toast-top-right',
            timeOut: 4000,
            extendedTimeOut: 2000,
            showEasing: 'swing',
            hideEasing: 'linear',
            showMethod: 'fadeIn',
            hideMethod: 'fadeOut'
        };

        // Konversi flash message ke toast dan sembunyikan alert aslinya
        var $flash = $('.alert:not(.alert-danger):not(.alert-important)');
        if ($flash.length) {
            $flash.each(function() {
                var msg = $(this).text().trim();
                if (msg && msg !== '×') {
                    if ($(this).hasClass('alert-success')) {
                        toastr.success(msg);
                    } else if ($(this).hasClass('alert-warning')) {
                        toastr.warning(msg);
                    } else if ($(this).hasClass('alert-info')) {
                        toastr.info(msg);
                    } else {
                        toastr.info(msg);
                    }
                    $(this).hide();
                }
            });
        }
    });
</script>

@if(Auth::check() && Auth::user()->isAdmin())
{{-- Notif Bell: Polling Pengajuan Pending setiap 60 detik --}}
<script>
(function() {
    'use strict';
    var POLL_INTERVAL_MS = 60000; // 60 detik
    var ENDPOINT = '{{ route("pengajuan_ruangans.notifikasi-pending") }}';

    var $bellLink   = $('#notif-bell-toggle');
    var $bellCount  = $('#notif-bell-count');
    var $listContainer = $('#notif-list-container');
    var $header     = $('#notif-header');

    function escapeHtml(str) {
        if (str === null || str === undefined) return '';
        return String(str).replace(/[&<>"']/g, function(m) {
            return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m];
        });
    }

    function renderEmpty() {
        $listContainer.html(
            '<div class="notif-empty">' +
                '<i class="far fa-check-circle text-success"></i>' +
                '<div>Tidak ada pengajuan menunggu.</div>' +
                '<small>Semua sudah ditangani!</small>' +
            '</div>'
        );
    }

    function renderItems(items) {
        if (!items || items.length === 0) {
            renderEmpty();
            return;
        }

        var html = items.map(function(item) {
            var urgentBadge = item.is_urgent
                ? '<span class="notif-urgent-badge">URGEN</span>'
                : '';

            return '<a href="' + escapeHtml(item.url) + '" class="notif-item">' +
                       '<div>' +
                           '<span class="notif-kode">' + escapeHtml(item.kode) + '</span>' +
                           urgentBadge +
                       '</div>' +
                       '<div class="font-weight-bold mt-1">' + escapeHtml(item.nama_kegiatan) + '</div>' +
                       '<div class="notif-meta">' +
                           '<span><i class="fas fa-user"></i>' + escapeHtml(item.nama_pemohon) + '</span>' +
                           '<span><i class="fas fa-door-open"></i>' + escapeHtml(item.ruangan) + '</span>' +
                       '</div>' +
                       '<div class="notif-meta">' +
                           '<span><i class="fas fa-calendar"></i>' + escapeHtml(item.tanggal_mulai) + '</span>' +
                       '</div>' +
                       '<div class="notif-time">' +
                           '<i class="fas fa-clock"></i> ' + escapeHtml(item.created_human) +
                       '</div>' +
                   '</a>';
        }).join('');

        $listContainer.html(html);
    }

    function fetchNotifications() {
        $.get(ENDPOINT)
            .done(function(resp) {
                var count = parseInt(resp.count, 10) || 0;

                // Update badge
                if (count > 0) {
                    $bellCount.text(count > 99 ? '99+' : count).show();
                    $bellLink.addClass('has-pending');
                    $header.html('<i class="fas fa-clock mr-1"></i> ' + count + ' Pengajuan Menunggu');
                } else {
                    $bellCount.hide();
                    $bellLink.removeClass('has-pending');
                    $header.html('<i class="fas fa-clock mr-1"></i> Pengajuan Menunggu');
                }

                renderItems(resp.items);
            })
            .fail(function() {
                $listContainer.html(
                    '<div class="notif-empty">' +
                        '<i class="fas fa-exclamation-triangle text-warning"></i>' +
                        '<div>Gagal memuat notifikasi.</div>' +
                    '</div>'
                );
            });
    }

    // Initial fetch + polling
    $(function() {
        fetchNotifications();
        setInterval(fetchNotifications, POLL_INTERVAL_MS);

        // Refresh saat dropdown di-buka manual (force refresh untuk data fresh)
        $bellLink.on('click', function() {
            fetchNotifications();
        });
    });
})();
</script>
@endif

@stack('third_party_scripts')

@stack('page_scripts')
</body>
</html>
