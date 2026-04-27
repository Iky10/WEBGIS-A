@extends('layouts.public')

@section('title', 'Detail Pengajuan — WebGIS Gedung')

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
    .badge-diproses  { background: #f39c12; color: #fff; }
    .badge-disetujui { background: #27ae60; color: #fff; }
    .badge-ditolak   { background: #e74c3c; color: #fff; }

    .alert-catatan {
        border-radius: 8px;
        border-left: 4px solid #3498db;
        background: #eaf2f8;
    }

    .btn-kembali {
        border-radius: 8px; padding: 8px 20px;
    }
</style>
@endpush

@section('content')
    <div class="detail-header">
        <div class="container d-flex justify-content-between align-items-center">
            <h2><i class="fas fa-file-alt mr-2"></i>Detail Pengajuan</h2>
            <a href="{{ route('pengajuan_gedungs.riwayat') }}" class="btn btn-outline-light btn-kembali">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="container py-4">
        @if(session('flash_notification'))
            @foreach(session('flash_notification', collect())->toArray() as $msg)
                <div class="alert alert-{{ $msg['level'] ?? 'info' }} alert-dismissible fade show">
                    {!! $msg['message'] !!}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endforeach
        @endif

        <div class="detail-card card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <strong>{{ $pengajuanGedung->kode_pengajuan }}</strong>
                </h5>
                @if($pengajuanGedung->status === 'disetujui')
                    <span class="badge badge-status badge-disetujui"><i class="fas fa-check mr-1"></i>Disetujui</span>
                @elseif($pengajuanGedung->status === 'ditolak')
                    <span class="badge badge-status badge-ditolak"><i class="fas fa-times mr-1"></i>Ditolak</span>
                @else
                    <span class="badge badge-status badge-diproses"><i class="fas fa-clock mr-1"></i>Diproses</span>
                @endif
            </div>
            <div class="card-body" style="padding: 24px;">
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <h6 class="section-title"><i class="fas fa-user mr-1"></i> Informasi Pemohon</h6>
                        <table class="table table-sm info-table">
                            <tr><th>Nama</th><td>{{ $pengajuanGedung->nama_pemohon }}</td></tr>
                            <tr><th>Email</th><td>{{ $pengajuanGedung->email_pemohon }}</td></tr>
                            <tr><th>Telepon</th><td>{{ $pengajuanGedung->no_telepon }}</td></tr>
                            <tr><th>Instansi</th><td>{{ $pengajuanGedung->asal_instansi }}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6 mb-4">
                        <h6 class="section-title"><i class="fas fa-calendar-alt mr-1"></i> Informasi Kegiatan</h6>
                        <table class="table table-sm info-table">
                            <tr><th>Gedung</th><td>{{ $pengajuanGedung->gedung->nama_gedung ?? '-' }}</td></tr>
                            <tr><th>Jenis</th><td>{{ $pengajuanGedung->jenis_kegiatan }}</td></tr>
                            <tr><th>Nama Kegiatan</th><td>{{ $pengajuanGedung->nama_kegiatan }}</td></tr>
                            <tr>
                                <th>Tanggal</th>
                                <td>
                                    {{ $pengajuanGedung->tanggal_mulai->format('d M Y') }}
                                    @if($pengajuanGedung->tanggal_mulai != $pengajuanGedung->tanggal_selesai)
                                        — {{ $pengajuanGedung->tanggal_selesai->format('d M Y') }}
                                    @endif
                                </td>
                            </tr>
                            <tr><th>Jam</th><td>{{ $pengajuanGedung->jam_mulai }} — {{ $pengajuanGedung->jam_selesai }}</td></tr>
                            <tr><th>Peserta</th><td>{{ $pengajuanGedung->jumlah_peserta ?? '-' }} orang</td></tr>
                        </table>
                    </div>
                </div>

                @if($pengajuanGedung->keperluan)
                    <div class="mt-2">
                        <h6 class="section-title"><i class="fas fa-info-circle mr-1"></i> Keperluan</h6>
                        <p>{{ $pengajuanGedung->keperluan }}</p>
                    </div>
                @endif

                @if($pengajuanGedung->catatan_admin)
                    <div class="mt-3">
                        <h6 class="section-title"><i class="fas fa-comment-dots mr-1"></i> Catatan Admin</h6>
                        <div class="alert alert-catatan">
                            <i class="fas fa-quote-left text-muted mr-1"></i>
                            {{ $pengajuanGedung->catatan_admin }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
