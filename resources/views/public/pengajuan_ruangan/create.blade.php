@extends('layouts.public')

@section('title', 'Ajukan Penggunaan Ruangan — WebGIS Gedung')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/public-gedung.css') }}">
<style>
    /* ══ HEADER ══ */
    .form-header {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        padding: 40px 0 30px;
        color: #fff;
    }
    .form-header h2 { font-weight: 700; margin: 0; }
    .form-header p { opacity: .85; margin: 5px 0 0; }

    /* ══ STEP INDICATOR ══ */
    .step-progress {
        display: flex; justify-content: space-between; align-items: center;
        max-width: 640px; margin: 0 auto 32px; padding: 0;
        position: relative;
    }
    .step-progress::before {
        content: ''; position: absolute; top: 22px; left: 40px; right: 40px;
        height: 2px; background: #dee2e6; z-index: 0;
    }
    .step-item {
        position: relative; z-index: 1; display: flex; flex-direction: column;
        align-items: center; flex: 1;
    }
    .step-circle {
        width: 44px; height: 44px; border-radius: 50%;
        background: #fff; border: 2px solid #dee2e6; color: #adb5bd;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 1.05rem; transition: all .25s;
    }
    .step-item.active .step-circle {
        background: #3498db; border-color: #3498db; color: #fff;
        box-shadow: 0 0 0 4px rgba(52, 152, 219, 0.2);
    }
    .step-item.done .step-circle {
        background: #27ae60; border-color: #27ae60; color: #fff;
    }
    .step-label {
        margin-top: 8px; font-size: .85rem; color: #6c757d; text-align: center;
        font-weight: 500;
    }
    .step-item.active .step-label, .step-item.done .step-label { color: #2c3e50; font-weight: 600; }

    /* ══ SECTION CARD ══ */
    .section-card {
        background: #fff; border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,.06); padding: 28px;
        margin-bottom: 24px;
        transition: opacity .3s, transform .3s;
    }
    .section-card.locked {
        opacity: .4; pointer-events: none; transform: scale(.98);
    }
    .section-card h4 {
        color: #2c3e50; font-weight: 700; margin-bottom: 6px;
        display: flex; align-items: center; gap: 10px;
    }
    .section-card h4 .step-num {
        background: #3498db; color: #fff; width: 28px; height: 28px;
        border-radius: 50%; display: inline-flex; align-items: center;
        justify-content: center; font-size: .9rem;
    }
    .section-card .section-desc {
        color: #7f8c8d; margin-bottom: 20px; font-size: .95rem;
    }

    /* ══ GEDUNG / RUANGAN PICKER CARDS ══ */
    .picker-grid {
        display: grid; gap: 16px;
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    }
    .picker-card {
        border: 2px solid #e9ecef; border-radius: 10px;
        background: #fff; cursor: pointer; overflow: hidden;
        transition: all .2s; position: relative;
    }
    .picker-card:hover {
        border-color: #3498db; transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(52, 152, 219, 0.15);
    }
    .picker-card.selected {
        border-color: #27ae60; background: #eafaf1;
        box-shadow: 0 4px 12px rgba(39, 174, 96, 0.2);
    }
    .picker-card.selected::before {
        content: '\f00c'; font-family: 'Font Awesome 5 Free'; font-weight: 900;
        position: absolute; top: 10px; right: 10px;
        background: #27ae60; color: #fff; width: 26px; height: 26px;
        border-radius: 50%; display: flex; align-items: center; justify-content: center;
        font-size: .8rem; z-index: 2;
    }
    .picker-img {
        width: 100%; height: 120px; object-fit: cover;
        background: linear-gradient(135deg, #e9ecef, #f8f9fa);
    }
    .picker-img-placeholder {
        width: 100%; height: 120px; display: flex; align-items: center;
        justify-content: center; background: linear-gradient(135deg, #e9ecef, #f8f9fa);
        color: #adb5bd; font-size: 2.5rem;
    }
    .picker-body { padding: 14px 16px; }
    .picker-title { font-weight: 600; color: #2c3e50; margin: 0 0 4px; font-size: 1rem; }
    .picker-meta { font-size: .82rem; color: #7f8c8d; margin: 0; }
    .picker-meta i { margin-right: 4px; }

    /* Status badge di ruangan card */
    .ruangan-status {
        position: absolute; top: 10px; left: 10px; z-index: 2;
        padding: 3px 10px; border-radius: 12px; font-size: .72rem;
        font-weight: 600; color: #fff;
    }
    .ruangan-status.kosong { background: #27ae60; }
    .ruangan-status.dipakai { background: #3498db; }
    .ruangan-status.tutup { background: #95a5a6; }

    /* ══ FORM FIELDS ══ */
    .form-group label { font-weight: 500; color: #2c3e50; margin-bottom: 6px; }
    .form-control { border-radius: 8px; border: 1.5px solid #e9ecef; padding: 10px 14px; }
    .form-control:focus {
        border-color: #3498db; box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.15);
    }

    /* ══ LIVE AVAILABILITY ══ */
    .availability-box {
        border-radius: 10px; padding: 14px 18px; margin-top: 12px;
        border-left: 4px solid;
    }
    .availability-box.checking {
        background: #f8f9fa; border-color: #adb5bd; color: #6c757d;
    }
    .availability-box.available {
        background: #eafaf1; border-color: #27ae60; color: #1e8449;
    }
    .availability-box.unavailable {
        background: #fadbd8; border-color: #e74c3c; color: #922b21;
    }
    .availability-box h6 { margin: 0 0 6px; font-weight: 700; }
    .availability-box .conflict-item {
        background: rgba(255,255,255,0.6); border-radius: 6px;
        padding: 8px 12px; margin-top: 8px; font-size: .88rem;
    }

    /* ══ BUTTONS ══ */
    .btn-kirim {
        background: linear-gradient(135deg, #27ae60, #219a52);
        border: none; color: #fff; border-radius: 8px;
        padding: 12px 32px; font-weight: 600; font-size: 1rem;
    }
    .btn-kirim:hover { background: linear-gradient(135deg, #219a52, #1e8449); color: #fff; }
    .btn-kirim:disabled { opacity: .5; cursor: not-allowed; }

    .btn-batal { border-radius: 8px; padding: 12px 28px; }

    /* ══ EMPTY STATE ══ */
    .empty-ruangan {
        padding: 32px; text-align: center; color: #7f8c8d;
        background: #f8f9fa; border-radius: 10px;
    }
    .empty-ruangan i { font-size: 2rem; margin-bottom: 10px; display: block; color: #bdc3c7; }

    /* Validation errors list */
    .alert-danger { border-radius: 10px; border-left: 4px solid #e74c3c; }
</style>
@endpush

@section('content')
<div class="form-header">
    <div class="container">
        <h2><i class="fas fa-door-open mr-2"></i>Ajukan Penggunaan Ruangan</h2>
        <p>Pilih ruangan yang ingin Anda gunakan dan isi detail kegiatan</p>
    </div>
</div>

<div class="container py-4">

    {{-- Step Indicator --}}
    <div class="step-progress">
        <div class="step-item active" id="step-1">
            <div class="step-circle">1</div>
            <div class="step-label">Pilih Gedung</div>
        </div>
        <div class="step-item" id="step-2">
            <div class="step-circle">2</div>
            <div class="step-label">Pilih Ruangan</div>
        </div>
        <div class="step-item" id="step-3">
            <div class="step-circle">3</div>
            <div class="step-label">Detail Kegiatan</div>
        </div>
    </div>

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <strong><i class="fas fa-exclamation-triangle mr-1"></i> Terjadi Kesalahan:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {!! Form::open(['route' => 'pengajuan_ruangans.store', 'id' => 'form-pengajuan']) !!}

    {{-- hidden field untuk ID ruangan terpilih --}}
    <input type="hidden" name="gedung_fasilitas_id" id="gedung_fasilitas_id" value="{{ old('gedung_fasilitas_id', $selectedRuangan) }}">

    {{-- ═══════════════════════════════════════════════════
         STEP 1 — PILIH GEDUNG
    ═══════════════════════════════════════════════════ --}}
    <div class="section-card" id="section-gedung">
        <h4><span class="step-num">1</span> Pilih Gedung</h4>
        <p class="section-desc">Pilih gedung yang ingin Anda gunakan ruangannya.</p>

        @if($gedungs->isEmpty())
            <div class="empty-ruangan">
                <i class="fas fa-building"></i>
                <h6>Belum Ada Gedung Tersedia</h6>
                <p class="mb-0">Tidak ada gedung yang dibuka untuk pengajuan saat ini.</p>
            </div>
        @else
            <div class="picker-grid">
                @foreach($gedungs as $gedung)
                    <div class="picker-card gedung-card
                                {{ $selectedGedung == $gedung->id ? 'selected' : '' }}"
                         data-gedung-id="{{ $gedung->id }}"
                         data-ruangan-count="{{ $gedung->fasilitas->count() }}">
                        @if($gedung->foto_utama)
                            <img src="{{ asset($gedung->foto_utama) }}"
                                 alt="{{ $gedung->nama_gedung }}"
                                 class="picker-img"
                                 onerror="this.outerHTML='<div class=&quot;picker-img-placeholder&quot;><i class=&quot;fas fa-building&quot;></i></div>'">
                        @else
                            <div class="picker-img-placeholder"><i class="fas fa-building"></i></div>
                        @endif
                        <div class="picker-body">
                            <p class="picker-title">{{ $gedung->nama_gedung }}</p>
                            <p class="picker-meta">
                                <i class="fas fa-door-open"></i>{{ $gedung->fasilitas->count() }} ruangan
                            </p>
                            @if($gedung->jam_buka && $gedung->jam_tutup)
                                <p class="picker-meta">
                                    <i class="fas fa-clock"></i>{{ \Carbon\Carbon::parse($gedung->jam_buka)->format('H:i') }}
                                    — {{ \Carbon\Carbon::parse($gedung->jam_tutup)->format('H:i') }}
                                </p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ═══════════════════════════════════════════════════
         STEP 2 — PILIH RUANGAN (hidden until gedung dipilih)
    ═══════════════════════════════════════════════════ --}}
    <div class="section-card locked" id="section-ruangan">
        <h4><span class="step-num">2</span> Pilih Ruangan</h4>
        <p class="section-desc">Pilih ruangan yang ingin Anda gunakan dari gedung yang sudah dipilih.</p>

        {{-- Ruangan akan di-render di sini oleh JS saat gedung dipilih --}}
        <div id="ruangan-container"></div>

        {{-- Data ruangan embedded sebagai JSON (dihidden, dibaca oleh JS) --}}
        <script type="application/json" id="data-ruangan">
            @php
                $ruanganData = [];
                foreach ($gedungs as $g) {
                    foreach ($g->fasilitas as $r) {
                        $ruanganData[] = [
                            'id'             => $r->id,
                            'gedung_id'      => $g->id,
                            'nama_fasilitas' => $r->nama_fasilitas,
                            'kategori'       => $r->kategori,
                            'keterangan'     => $r->keterangan,
                            'foto_ruangan'   => $r->foto_ruangan ? asset($r->foto_ruangan) : null,
                            'status'         => $r->status_dipakai,
                        ];
                    }
                }
            @endphp
            {!! json_encode($ruanganData) !!}
        </script>
    </div>

    {{-- ═══════════════════════════════════════════════════
         STEP 3 — DETAIL KEGIATAN (hidden until ruangan dipilih)
    ═══════════════════════════════════════════════════ --}}
    <div class="section-card locked" id="section-detail">
        <h4><span class="step-num">3</span> Detail Kegiatan</h4>
        <p class="section-desc">Lengkapi informasi pemohon dan detail kegiatan Anda.</p>

        <div class="row">
            {{-- Data Pemohon --}}
            <div class="form-group col-md-6">
                {!! Form::label('nama_pemohon', 'Nama Pemohon') !!}
                {!! Form::text('nama_pemohon', old('nama_pemohon', Auth::user()->name ?? null), ['class' => 'form-control', 'required' => true]) !!}
            </div>
            <div class="form-group col-md-6">
                {!! Form::label('email_pemohon', 'Email') !!}
                {!! Form::email('email_pemohon', old('email_pemohon', Auth::user()->email ?? null), ['class' => 'form-control', 'required' => true]) !!}
            </div>
            <div class="form-group col-md-6">
                {!! Form::label('no_telepon', 'No. Telepon') !!}
                {!! Form::text('no_telepon', old('no_telepon'), ['class' => 'form-control', 'required' => true, 'placeholder' => '08xxxxxxxxxx']) !!}
            </div>
            <div class="form-group col-md-6">
                {!! Form::label('asal_instansi', 'Asal Instansi') !!}
                {!! Form::text('asal_instansi', old('asal_instansi'), ['class' => 'form-control', 'required' => true, 'placeholder' => 'Contoh: Politani Samarinda']) !!}
            </div>

            {{-- Data Kegiatan --}}
            <div class="form-group col-md-6">
                {!! Form::label('jenis_kegiatan', 'Jenis Kegiatan') !!}
                {!! Form::select('jenis_kegiatan', [
                    'Seminar' => 'Seminar',
                    'Workshop' => 'Workshop',
                    'Rapat' => 'Rapat',
                    'Wisuda' => 'Wisuda',
                    'Sidang' => 'Sidang',
                    'Pelatihan' => 'Pelatihan',
                    'Olahraga' => 'Olahraga',
                    'Lainnya' => 'Lainnya',
                ], old('jenis_kegiatan'), ['class' => 'form-control', 'placeholder' => '-- Pilih Jenis --', 'required' => true]) !!}
            </div>
            <div class="form-group col-md-6">
                {!! Form::label('jumlah_peserta', 'Jumlah Peserta (opsional)') !!}
                {!! Form::number('jumlah_peserta', old('jumlah_peserta'), ['class' => 'form-control', 'min' => 1, 'placeholder' => 'Estimasi peserta']) !!}
            </div>
            <div class="form-group col-md-12">
                {!! Form::label('nama_kegiatan', 'Nama Kegiatan') !!}
                {!! Form::text('nama_kegiatan', old('nama_kegiatan'), ['class' => 'form-control', 'required' => true, 'placeholder' => 'Contoh: Seminar Nasional Teknologi 2026']) !!}
            </div>

            {{-- Waktu --}}
            <div class="form-group col-md-3">
                {!! Form::label('tanggal_mulai', 'Tanggal Mulai') !!}
                {!! Form::date('tanggal_mulai', old('tanggal_mulai'), ['class' => 'form-control', 'required' => true, 'min' => now()->toDateString()]) !!}
            </div>
            <div class="form-group col-md-3">
                {!! Form::label('tanggal_selesai', 'Tanggal Selesai') !!}
                {!! Form::date('tanggal_selesai', old('tanggal_selesai'), ['class' => 'form-control', 'required' => true, 'min' => now()->toDateString()]) !!}
            </div>
            <div class="form-group col-md-3">
                {!! Form::label('jam_mulai', 'Jam Mulai') !!}
                {!! Form::time('jam_mulai', old('jam_mulai'), ['class' => 'form-control', 'required' => true]) !!}
            </div>
            <div class="form-group col-md-3">
                {!! Form::label('jam_selesai', 'Jam Selesai') !!}
                {!! Form::time('jam_selesai', old('jam_selesai'), ['class' => 'form-control', 'required' => true]) !!}
            </div>

            {{-- Keperluan --}}
            <div class="form-group col-md-12">
                {!! Form::label('keperluan', 'Keperluan / Keterangan Tambahan (opsional)') !!}
                {!! Form::textarea('keperluan', old('keperluan'), ['class' => 'form-control', 'rows' => 3, 'placeholder' => 'Informasi tambahan tentang kegiatan...']) !!}
            </div>
        </div>

        {{-- Live Availability Check --}}
        <div id="availability-placeholder"></div>

        {{-- Submit Buttons --}}
        <div class="mt-4 d-flex justify-content-between align-items-center flex-wrap">
            <a href="{{ route('pengajuan_ruangans.riwayat') }}" class="btn btn-default btn-batal">
                <i class="fas fa-times mr-1"></i> Batal
            </a>
            <button type="submit" class="btn btn-kirim" id="btn-submit" disabled>
                <i class="fas fa-paper-plane mr-1"></i> Kirim Pengajuan
            </button>
        </div>
    </div>

    {!! Form::close() !!}
</div>
@endsection

@push('scripts')
<script>
(function() {
    'use strict';

    // ── STATE ─────────────────────────────────────────────────
    var allRuangan = JSON.parse(document.getElementById('data-ruangan').textContent.trim() || '[]');
    var selectedGedungId  = {{ $selectedGedung ? (int)$selectedGedung : 'null' }};
    var selectedRuanganId = {{ $selectedRuangan ? (int)$selectedRuangan : 'null' }};
    var availabilityTimer = null;

    // ── DOM ───────────────────────────────────────────────────
    var $ruanganContainer = $('#ruangan-container');
    var $sectionRuangan   = $('#section-ruangan');
    var $sectionDetail    = $('#section-detail');
    var $btnSubmit        = $('#btn-submit');
    var $hiddenRuanganId  = $('#gedung_fasilitas_id');
    var $availability     = $('#availability-placeholder');

    // ── STEP INDICATOR ────────────────────────────────────────
    function updateSteps() {
        $('.step-item').removeClass('active done');
        if (!selectedGedungId) {
            $('#step-1').addClass('active');
        } else if (!selectedRuanganId) {
            $('#step-1').addClass('done');
            $('#step-2').addClass('active');
        } else {
            $('#step-1').addClass('done');
            $('#step-2').addClass('done');
            $('#step-3').addClass('active');
        }
    }

    // ── RENDER RUANGAN CARDS ─────────────────────────────────
    function renderRuangan(gedungId) {
        var ruanganList = allRuangan.filter(function(r) { return r.gedung_id == gedungId; });

        if (ruanganList.length === 0) {
            $ruanganContainer.html(
                '<div class="empty-ruangan">' +
                    '<i class="fas fa-door-closed"></i>' +
                    '<h6>Belum Ada Ruangan</h6>' +
                    '<p class="mb-0">Gedung ini belum memiliki ruangan yang terdaftar.</p>' +
                '</div>'
            );
            return;
        }

        var html = '<div class="picker-grid">';
        ruanganList.forEach(function(r) {
            var statusClass = r.status === 'Sedang Dipakai' ? 'dipakai'
                            : r.status === 'Tutup' ? 'tutup'
                            : 'kosong';
            var statusLabel = r.status === 'Sedang Dipakai' ? 'Sedang Dipakai'
                            : r.status === 'Tutup' ? 'Tutup'
                            : 'Tersedia';
            var isSelected  = r.id == selectedRuanganId ? 'selected' : '';

            var imgHtml = r.foto_ruangan
                ? '<img src="' + r.foto_ruangan + '" alt="' + r.nama_fasilitas + '" class="picker-img" onerror="this.outerHTML=\'<div class=&quot;picker-img-placeholder&quot;><i class=&quot;fas fa-door-open&quot;></i></div>\'">'
                : '<div class="picker-img-placeholder"><i class="fas fa-door-open"></i></div>';

            html += '<div class="picker-card ruangan-card ' + isSelected + '" data-ruangan-id="' + r.id + '">' +
                        '<span class="ruangan-status ' + statusClass + '">' + statusLabel + '</span>' +
                        imgHtml +
                        '<div class="picker-body">' +
                            '<p class="picker-title">' + escapeHtml(r.nama_fasilitas) + '</p>' +
                            (r.kategori ? '<p class="picker-meta"><i class="fas fa-tag"></i>' + escapeHtml(r.kategori) + '</p>' : '') +
                            (r.keterangan ? '<p class="picker-meta text-truncate" title="' + escapeHtml(r.keterangan) + '">' + escapeHtml(r.keterangan) + '</p>' : '') +
                        '</div>' +
                    '</div>';
        });
        html += '</div>';

        $ruanganContainer.html(html);
    }

    function escapeHtml(s) {
        if (!s) return '';
        return String(s).replace(/[&<>"']/g, function(c) {
            return { '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' }[c];
        });
    }

    // ── STATE TRANSITIONS ────────────────────────────────────
    function selectGedung(gedungId) {
        selectedGedungId  = gedungId;
        selectedRuanganId = null;
        $hiddenRuanganId.val('');

        $('.gedung-card').removeClass('selected');
        $('.gedung-card[data-gedung-id="' + gedungId + '"]').addClass('selected');

        $sectionRuangan.removeClass('locked');
        $sectionDetail.addClass('locked');
        $btnSubmit.prop('disabled', true);

        renderRuangan(gedungId);
        updateSteps();

        // Scroll ke section ruangan
        setTimeout(function() {
            $sectionRuangan[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 150);
    }

    function selectRuangan(ruanganId) {
        selectedRuanganId = ruanganId;
        $hiddenRuanganId.val(ruanganId);

        $('.ruangan-card').removeClass('selected');
        $('.ruangan-card[data-ruangan-id="' + ruanganId + '"]').addClass('selected');

        $sectionDetail.removeClass('locked');
        $btnSubmit.prop('disabled', false);

        updateSteps();
        checkAvailabilityDebounced();

        // Scroll ke section detail
        setTimeout(function() {
            $sectionDetail[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 150);
    }

    // ── LIVE AVAILABILITY CHECK ──────────────────────────────
    function checkAvailabilityDebounced() {
        clearTimeout(availabilityTimer);
        availabilityTimer = setTimeout(checkAvailability, 400);
    }

    function checkAvailability() {
        var ruanganId      = $hiddenRuanganId.val();
        var tanggalMulai   = $('input[name="tanggal_mulai"]').val();
        var tanggalSelesai = $('input[name="tanggal_selesai"]').val();
        var jamMulai       = $('input[name="jam_mulai"]').val();
        var jamSelesai     = $('input[name="jam_selesai"]').val();

        if (!ruanganId || !tanggalMulai || !tanggalSelesai || !jamMulai || !jamSelesai) {
            $availability.empty();
            return;
        }

        $availability.html(
            '<div class="availability-box checking">' +
                '<i class="fas fa-spinner fa-spin mr-2"></i>Mengecek ketersediaan ruangan...' +
            '</div>'
        );

        $.ajax({
            url: '{{ route("pengajuan_ruangans.cek-ketersediaan") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                gedung_fasilitas_id: ruanganId,
                tanggal_mulai:   tanggalMulai,
                tanggal_selesai: tanggalSelesai,
                jam_mulai:       jamMulai,
                jam_selesai:     jamSelesai
            },
            success: function(resp) {
                if (resp.available) {
                    $availability.html(
                        '<div class="availability-box available">' +
                            '<h6><i class="fas fa-check-circle mr-2"></i>Ruangan Tersedia!</h6>' +
                            '<p class="mb-0">Tidak ada konflik jadwal. Silakan kirim pengajuan.</p>' +
                        '</div>'
                    );
                    $btnSubmit.prop('disabled', false);
                } else {
                    var conflictHtml = resp.conflicts.map(function(c) {
                        var statusBadge = c.status === 'disetujui'
                            ? '<span class="badge badge-success">Disetujui</span>'
                            : '<span class="badge badge-warning">Diproses</span>';
                        return '<div class="conflict-item">' +
                                    '<strong>' + escapeHtml(c.kode_pengajuan) + '</strong> ' + statusBadge + '<br>' +
                                    '<small>' + escapeHtml(c.nama_kegiatan) + ' — ' +
                                    c.tanggal_mulai + ' ' + c.jam_mulai + '–' + c.jam_selesai + '</small>' +
                               '</div>';
                    }).join('');

                    $availability.html(
                        '<div class="availability-box unavailable">' +
                            '<h6><i class="fas fa-exclamation-circle mr-2"></i>Ruangan Tidak Tersedia</h6>' +
                            '<p class="mb-2">Sudah ada pengajuan lain pada jadwal yang sama. Silakan pilih waktu atau ruangan lain:</p>' +
                            conflictHtml +
                        '</div>'
                    );
                    $btnSubmit.prop('disabled', true);
                }
            },
            error: function() {
                $availability.html(
                    '<div class="availability-box checking">' +
                        '<i class="fas fa-info-circle mr-2"></i>Tidak dapat mengecek ketersediaan saat ini. Silakan coba lagi.' +
                    '</div>'
                );
            }
        });
    }

    // ── EVENT BINDINGS ───────────────────────────────────────
    $(document).on('click', '.gedung-card', function() {
        selectGedung($(this).data('gedung-id'));
    });

    $(document).on('click', '.ruangan-card', function() {
        selectRuangan($(this).data('ruangan-id'));
    });

    $('input[name="tanggal_mulai"], input[name="tanggal_selesai"], input[name="jam_mulai"], input[name="jam_selesai"]')
        .on('change input', checkAvailabilityDebounced);

    // Auto-sync tanggal_selesai >= tanggal_mulai
    $('input[name="tanggal_mulai"]').on('change', function() {
        var v = $(this).val();
        $('input[name="tanggal_selesai"]').attr('min', v);
        if ($('input[name="tanggal_selesai"]').val() < v) {
            $('input[name="tanggal_selesai"]').val(v);
        }
    });

    // Auto-sync jam_selesai > jam_mulai
    $('input[name="jam_mulai"]').on('change', function() {
        var v = $(this).val();
        if ($('input[name="jam_selesai"]').val() <= v) {
            // tambah 1 jam
            var parts = v.split(':');
            var h = (parseInt(parts[0], 10) + 1) % 24;
            $('input[name="jam_selesai"]').val(String(h).padStart(2, '0') + ':' + parts[1]);
        }
    });

    // ── INITIAL STATE ────────────────────────────────────────
    // Kalau ada pre-selected (via ?gedung_id= atau ?ruangan_id= atau dari old())
    if (selectedGedungId) {
        $sectionRuangan.removeClass('locked');
        renderRuangan(selectedGedungId);
        if (selectedRuanganId) {
            $sectionDetail.removeClass('locked');
            $btnSubmit.prop('disabled', false);
        }
    }
    updateSteps();

})();
</script>
@endpush
