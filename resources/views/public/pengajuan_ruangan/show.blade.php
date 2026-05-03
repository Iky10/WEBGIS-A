@extends('layouts.public')

@section('title', 'Detail Pengajuan Ruangan — WebGIS Gedung')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/public-gedung.css') }}">
<style>
    .detail-header {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        padding: 40px 0 30px;
        color: #fff;
    }
    .detail-header h2 { font-weight: 700; margin: 0; }

    .detail-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,.08);
    }
    .detail-card .card-header {
        background: #f8f9fa;
        border-radius: 12px 12px 0 0;
        border-bottom: 1px solid #eee;
        padding: 16px 24px;
    }

    .info-table th {
        width: 40%;
        color: #6c757d;
        font-weight: 500;
        border-top: none;
        padding: 8px 12px;
    }
    .info-table td {
        border-top: none;
        padding: 8px 12px;
    }

    .section-title {
        color: #2c3e50;
        font-weight: 600;
        font-size: 1.05rem;
        margin-bottom: 12px;
        padding-bottom: 8px;
        border-bottom: 2px solid #3498db;
        display: inline-block;
    }

    .badge-status {
        font-size: .9rem;
        padding: 6px 16px;
        border-radius: 20px;
    }
    .badge-diproses   { background: #f39c12; color: #fff; }
    .badge-disetujui  { background: #27ae60; color: #fff; }
    .badge-ditolak    { background: #e74c3c; color: #fff; }
    .badge-dibatalkan { background: #95a5a6; color: #fff; }

    .btn-batal-detail {
        background: #e74c3c; color: #fff;
        border: none; border-radius: 8px;
        padding: 10px 24px; font-weight: 600;
        transition: background .2s;
    }
    .btn-batal-detail:hover {
        background: #c0392b; color: #fff;
    }

    .alert-catatan {
        border-radius: 8px;
        border-left: 4px solid #3498db;
        background: #eaf2f8;
    }

    .btn-kembali { border-radius: 8px; padding: 8px 20px; }

    .ruangan-highlight {
        background: linear-gradient(135deg, #e8f4fd, #d4e9fb);
        border-radius: 10px;
        padding: 16px 20px;
        margin-bottom: 24px;
        border-left: 4px solid #3498db;
    }
    .ruangan-highlight .ruangan-name {
        font-size: 1.2rem;
        font-weight: 700;
        color: #2c3e50;
        margin: 0;
    }
    .ruangan-highlight .gedung-name {
        font-size: .95rem;
        color: #7f8c8d;
        margin-top: 4px;
    }
    .ruangan-highlight .badge-kategori {
        background: #3498db;
        color: #fff;
        font-weight: 500;
        padding: 3px 10px;
        border-radius: 12px;
        font-size: .75rem;
    }
</style>
@endpush

@section('content')
    <div class="detail-header">
        <div class="container d-flex justify-content-between align-items-center">
            <h2><i class="fas fa-file-alt mr-2"></i>Detail Pengajuan Ruangan</h2>
            <a href="{{ route('pengajuan_ruangans.riwayat') }}" class="btn btn-outline-light btn-kembali">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="container py-4">
        <div class="detail-card card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <strong>{{ $pengajuanRuangan->kode_pengajuan }}</strong>
                </h5>
                @if($pengajuanRuangan->status === 'disetujui')
                    <span class="badge badge-status badge-disetujui"><i class="fas fa-check mr-1"></i>Disetujui</span>
                @elseif($pengajuanRuangan->status === 'ditolak')
                    <span class="badge badge-status badge-ditolak"><i class="fas fa-times mr-1"></i>Ditolak</span>
                @elseif($pengajuanRuangan->status === 'dibatalkan')
                    <span class="badge badge-status badge-dibatalkan"><i class="fas fa-ban mr-1"></i>Dibatalkan</span>
                @else
                    <span class="badge badge-status badge-diproses"><i class="fas fa-clock mr-1"></i>Diproses</span>
                @endif
            </div>
            <div class="card-body" style="padding: 24px;">

                {{-- Highlight Ruangan --}}
                <div class="ruangan-highlight">
                    <p class="ruangan-name">
                        <i class="fas fa-door-open text-primary mr-2"></i>{{ $pengajuanRuangan->ruangan->nama_fasilitas ?? '-' }}
                        @if($pengajuanRuangan->ruangan && $pengajuanRuangan->ruangan->kategori)
                            <span class="badge-kategori ml-2">{{ $pengajuanRuangan->ruangan->kategori }}</span>
                        @endif
                    </p>
                    <p class="gedung-name mb-0">
                        <i class="fas fa-building mr-1"></i>{{ $pengajuanRuangan->ruangan->gedung->nama_gedung ?? '-' }}
                    </p>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <h6 class="section-title"><i class="fas fa-user mr-1"></i> Informasi Pemohon</h6>
                        <table class="table table-sm info-table">
                            <tr><th>Nama</th><td>{{ $pengajuanRuangan->nama_pemohon }}</td></tr>
                            <tr><th>Email</th><td>{{ $pengajuanRuangan->email_pemohon }}</td></tr>
                            <tr><th>Telepon</th><td>{{ $pengajuanRuangan->no_telepon }}</td></tr>
                            <tr><th>Instansi</th><td>{{ $pengajuanRuangan->asal_instansi }}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6 mb-4">
                        <h6 class="section-title"><i class="fas fa-calendar-alt mr-1"></i> Informasi Kegiatan</h6>
                        <table class="table table-sm info-table">
                            <tr><th>Jenis</th><td>{{ $pengajuanRuangan->jenis_kegiatan }}</td></tr>
                            <tr><th>Nama Kegiatan</th><td>{{ $pengajuanRuangan->nama_kegiatan }}</td></tr>
                            <tr>
                                <th>Tanggal</th>
                                <td>
                                    {{ $pengajuanRuangan->tanggal_mulai->format('d M Y') }}
                                    @if($pengajuanRuangan->tanggal_mulai != $pengajuanRuangan->tanggal_selesai)
                                        — {{ $pengajuanRuangan->tanggal_selesai->format('d M Y') }}
                                    @endif
                                </td>
                            </tr>
                            <tr><th>Jam</th><td>{{ $pengajuanRuangan->jam_mulai }} — {{ $pengajuanRuangan->jam_selesai }}</td></tr>
                            <tr><th>Peserta</th><td>{{ $pengajuanRuangan->jumlah_peserta ?? '-' }} orang</td></tr>
                        </table>
                    </div>
                </div>

                @if($pengajuanRuangan->keperluan)
                    <div class="mt-2">
                        <h6 class="section-title"><i class="fas fa-info-circle mr-1"></i> Keperluan</h6>
                        <p>{{ $pengajuanRuangan->keperluan }}</p>
                    </div>
                @endif

                @if($pengajuanRuangan->catatan_admin)
                    <div class="mt-3">
                        <h6 class="section-title"><i class="fas fa-comment-dots mr-1"></i> Catatan Admin</h6>
                        <div class="alert alert-catatan">
                            <i class="fas fa-quote-left text-muted mr-1"></i>
                            {{ $pengajuanRuangan->catatan_admin }}
                        </div>
                    </div>
                @endif

                {{-- Audit trail --}}
                @if($pengajuanRuangan->approved_at)
                    <div class="mt-3 pt-3 border-top">
                        <small class="text-muted">
                            <i class="fas fa-user-check mr-1"></i>
                            Diputuskan pada
                            <strong>{{ $pengajuanRuangan->approved_at->format('d M Y, H:i') }}</strong>
                        </small>
                    </div>
                @endif

                {{-- Tombol batalkan: hanya untuk pemilik & status 'diproses' --}}
                @if($pengajuanRuangan->canBeCanceledBy(auth()->user()))
                    <div class="mt-4 pt-3 border-top text-right">
                        <form action="{{ route('pengajuan_ruangans.cancel', $pengajuanRuangan->id) }}"
                              method="POST" id="form-batal-detail" class="d-inline">
                            @csrf @method('PATCH')
                            <button type="button" id="btn-batal-detail" class="btn-batal-detail">
                                <i class="fas fa-times mr-1"></i> Batalkan Pengajuan
                            </button>
                        </form>
                        <p class="text-muted mt-2 mb-0" style="font-size:.85rem;">
                            <i class="fas fa-info-circle mr-1"></i>
                            Anda masih bisa membatalkan pengajuan ini selama admin belum memutuskan.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
@if($pengajuanRuangan->canBeCanceledBy(auth()->user()))
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.getElementById('btn-batal-detail').addEventListener('click', function () {
        Swal.fire({
            title: 'Batalkan Pengajuan?',
            html: 'Pengajuan <strong>{{ $pengajuanRuangan->kode_pengajuan }}</strong> akan dibatalkan.<br>Tindakan ini tidak bisa dikembalikan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74c3c',
            cancelButtonColor: '#7f8c8d',
            confirmButtonText: 'Ya, batalkan',
            cancelButtonText: 'Tidak',
            reverseButtons: true
        }).then(function (result) {
            if (result.isConfirmed) document.getElementById('form-batal-detail').submit();
        });
    });
</script>
@endif
@endpush
