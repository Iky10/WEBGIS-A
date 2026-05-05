@extends('layouts.public')

@section('title', 'Riwayat Pengajuan Saya — WebGIS Gedung')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/public-gedung.css') }}">
<style>
    .pengajuan-header {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        padding: 40px 0 30px;
        color: #fff;
    }
    .pengajuan-header h2 { font-weight: 700; margin: 0; }
    .pengajuan-header p { opacity: .85; margin: 5px 0 0; }

    .pengajuan-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,.08);
        transition: transform .2s, box-shadow .2s;
    }
    .pengajuan-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,.12);
    }

    .badge-status {
        font-size: .85rem;
        padding: 5px 12px;
        border-radius: 20px;
    }
    .badge-diproses   { background: #f39c12; color: #fff; }
    .badge-disetujui  { background: #27ae60; color: #fff; }
    .badge-ditolak    { background: #e74c3c; color: #fff; }
    .badge-dibatalkan { background: #95a5a6; color: #fff; }

    .btn-batal {
        border-color: #e74c3c; color: #e74c3c;
    }
    .btn-batal:hover {
        background: #e74c3c; color: #fff;
    }

    .btn-ajukan {
        background: linear-gradient(135deg, #3498db, #2980b9);
        border: none; color: #fff;
        border-radius: 8px; padding: 10px 24px;
        font-weight: 600;
    }
    .btn-ajukan:hover { background: linear-gradient(135deg, #2980b9, #2471a3); color: #fff; }

    .empty-state {
        text-align: center; padding: 60px 20px;
        color: #7f8c8d;
    }
    .empty-state i { font-size: 48px; margin-bottom: 16px; display: block; color: #bdc3c7; }

    .ruangan-cell {
        min-width: 180px;
    }
    .ruangan-cell .nama-ruangan {
        font-weight: 600; color: #2c3e50;
    }
    .ruangan-cell .nama-gedung {
        font-size: 12px; color: #7f8c8d;
    }

    /* ══ MOBILE LAYOUT (< 768px) ══
     * Table dengan 6 kolom impossible untuk mobile.
     * Pendekatan: hide table, tampilkan card-list mobile-friendly.
     */
    .mobile-card-list { display: none; }

    @media (max-width: 767.98px) {
        .pengajuan-header { padding: 24px 0 20px; }
        .pengajuan-header h2 { font-size: 1.4rem; }
        .pengajuan-header p { font-size: .85rem; }
        .pengajuan-header .container {
            flex-direction: column; align-items: flex-start !important; gap: 12px;
        }
        .pengajuan-header .btn-ajukan {
            width: 100%; padding: 10px; font-size: .9rem;
        }

        /* Hide desktop table, show mobile cards */
        .desktop-table { display: none !important; }
        .mobile-card-list {
            display: flex; flex-direction: column; gap: 12px;
        }
    }

    /* Mobile pengajuan card */
    .pengajuan-mobile-item {
        background: #fff; border-radius: 10px;
        padding: 14px; border: 1px solid #e9ecef;
        box-shadow: 0 2px 6px rgba(0,0,0,.04);
    }
    .pengajuan-mobile-item .pmi-head {
        display: flex; justify-content: space-between; align-items: flex-start;
        gap: 10px; margin-bottom: 10px; flex-wrap: wrap;
    }
    .pengajuan-mobile-item .pmi-kode {
        font-family: monospace; font-size: .8rem; font-weight: 700;
        color: #3498db; word-break: break-all;
    }
    .pengajuan-mobile-item .pmi-kegiatan {
        font-weight: 600; color: #2c3e50; margin: 0 0 8px;
        font-size: 1rem;
    }
    .pengajuan-mobile-item .pmi-info {
        display: flex; flex-direction: column; gap: 5px;
        font-size: .85rem; color: #5d6d7e;
    }
    .pengajuan-mobile-item .pmi-info i {
        width: 16px; color: #95a5a6; margin-right: 4px;
    }
    .pengajuan-mobile-item .pmi-actions {
        display: flex; gap: 8px; margin-top: 12px;
        padding-top: 12px; border-top: 1px solid #f1f3f5;
    }
    .pengajuan-mobile-item .pmi-actions .btn {
        flex: 1; padding: 8px 12px; font-size: .85rem;
    }
</style>
@endpush

@section('content')
    <div class="pengajuan-header">
        <div class="container d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="fas fa-history mr-2"></i>Riwayat Pengajuan Saya</h2>
                <p>Pantau status pengajuan penggunaan ruangan Anda</p>
            </div>
            <a href="{{ route('pengajuan_ruangans.create') }}" class="btn btn-ajukan">
                <i class="fas fa-plus mr-1"></i> Ajukan Baru
            </a>
        </div>
    </div>

    <div class="container py-4">
        @if($pengajuanRuangans->isEmpty())
            <div class="pengajuan-card card">
                <div class="card-body empty-state">
                    <i class="fas fa-inbox"></i>
                    <h5>Belum Ada Pengajuan</h5>
                    <p>Anda belum pernah mengajukan penggunaan ruangan.</p>
                    <a href="{{ route('pengajuan_ruangans.create') }}" class="btn btn-ajukan mt-2">
                        <i class="fas fa-plus mr-1"></i> Buat Pengajuan Pertama
                    </a>
                </div>
            </div>
        @else
            {{-- DESKTOP: Table view (≥768px) --}}
            <div class="pengajuan-card card desktop-table">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Kode</th>
                                    <th>Ruangan</th>
                                    <th>Kegiatan</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($pengajuanRuangans as $pengajuan)
                                <tr>
                                    <td><strong>{{ $pengajuan->kode_pengajuan }}</strong></td>
                                    <td class="ruangan-cell">
                                        <div class="nama-ruangan">
                                            <i class="fas fa-door-open text-primary mr-1"></i>{{ $pengajuan->ruangan->nama_fasilitas ?? '-' }}
                                        </div>
                                        <div class="nama-gedung">
                                            <i class="fas fa-building mr-1"></i>{{ $pengajuan->ruangan->gedung->nama_gedung ?? '-' }}
                                        </div>
                                    </td>
                                    <td>{{ $pengajuan->nama_kegiatan }}</td>
                                    <td>
                                        {{ $pengajuan->tanggal_mulai->format('d/m/Y') }}<br>
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse($pengajuan->jam_mulai)->format('H:i') }} —
                                            {{ \Carbon\Carbon::parse($pengajuan->jam_selesai)->format('H:i') }}
                                        </small>
                                    </td>
                                    <td>
                                        @if($pengajuan->status === 'disetujui')
                                            <span class="badge badge-status badge-disetujui">Disetujui</span>
                                        @elseif($pengajuan->status === 'ditolak')
                                            <span class="badge badge-status badge-ditolak">Ditolak</span>
                                        @elseif($pengajuan->status === 'dibatalkan')
                                            <span class="badge badge-status badge-dibatalkan">Dibatalkan</span>
                                        @else
                                            <span class="badge badge-status badge-diproses">Diproses</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('pengajuan_ruangans.show', $pengajuan->id) }}"
                                           class="btn btn-sm btn-outline-primary mr-1">
                                            <i class="far fa-eye mr-1"></i>Detail
                                        </a>

                                        @if($pengajuan->canBeCanceledBy(auth()->user()))
                                            <form action="{{ route('pengajuan_ruangans.cancel', $pengajuan->id) }}"
                                                  method="POST" class="d-inline form-batal-pengajuan">
                                                @csrf @method('PATCH')
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-danger btn-batal btn-batal-pengajuan"
                                                        data-kode="{{ $pengajuan->kode_pengajuan }}">
                                                    <i class="fas fa-times mr-1"></i>Batalkan
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- MOBILE: Card list (< 768px) --}}
            <div class="mobile-card-list">
                @foreach($pengajuanRuangans as $pengajuan)
                    <div class="pengajuan-mobile-item">
                        <div class="pmi-head">
                            <span class="pmi-kode">{{ $pengajuan->kode_pengajuan }}</span>
                            @if($pengajuan->status === 'disetujui')
                                <span class="badge badge-status badge-disetujui">Disetujui</span>
                            @elseif($pengajuan->status === 'ditolak')
                                <span class="badge badge-status badge-ditolak">Ditolak</span>
                            @elseif($pengajuan->status === 'dibatalkan')
                                <span class="badge badge-status badge-dibatalkan">Dibatalkan</span>
                            @else
                                <span class="badge badge-status badge-diproses">Diproses</span>
                            @endif
                        </div>

                        <h6 class="pmi-kegiatan">{{ $pengajuan->nama_kegiatan }}</h6>

                        <div class="pmi-info">
                            <span>
                                <i class="fas fa-door-open"></i>
                                {{ $pengajuan->ruangan->nama_fasilitas ?? '-' }}
                                @if($pengajuan->ruangan && $pengajuan->ruangan->gedung)
                                    <small class="text-muted">— {{ $pengajuan->ruangan->gedung->nama_gedung }}</small>
                                @endif
                            </span>
                            <span>
                                <i class="far fa-calendar"></i>
                                {{ $pengajuan->tanggal_mulai->format('d/m/Y') }}
                                <small class="text-muted">
                                    {{ \Carbon\Carbon::parse($pengajuan->jam_mulai)->format('H:i') }}–{{ \Carbon\Carbon::parse($pengajuan->jam_selesai)->format('H:i') }}
                                </small>
                            </span>
                        </div>

                        <div class="pmi-actions">
                            <a href="{{ route('pengajuan_ruangans.show', $pengajuan->id) }}"
                               class="btn btn-sm btn-outline-primary">
                                <i class="far fa-eye mr-1"></i>Detail
                            </a>
                            @if($pengajuan->canBeCanceledBy(auth()->user()))
                                <form action="{{ route('pengajuan_ruangans.cancel', $pengajuan->id) }}"
                                      method="POST" class="form-batal-pengajuan d-flex" style="flex:1;">
                                    @csrf @method('PATCH')
                                    <button type="button"
                                            class="btn btn-sm btn-outline-danger btn-batal btn-batal-pengajuan flex-grow-1"
                                            data-kode="{{ $pengajuan->kode_pengajuan }}">
                                        <i class="fas fa-times mr-1"></i>Batalkan
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.querySelectorAll('.btn-batal-pengajuan').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var form = btn.closest('form');
            var kode = btn.dataset.kode || '';
            Swal.fire({
                title: 'Batalkan Pengajuan?',
                html: 'Pengajuan <strong>' + kode + '</strong> akan dibatalkan.<br>Tindakan ini tidak bisa dikembalikan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74c3c',
                cancelButtonColor: '#7f8c8d',
                confirmButtonText: 'Ya, batalkan',
                cancelButtonText: 'Tidak',
                reverseButtons: true
            }).then(function (result) {
                if (result.isConfirmed) form.submit();
            });
        });
    });
</script>
@endpush
