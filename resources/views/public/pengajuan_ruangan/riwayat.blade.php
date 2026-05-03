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
    .badge-diproses  { background: #f39c12; color: #fff; }
    .badge-disetujui { background: #27ae60; color: #fff; }
    .badge-ditolak   { background: #e74c3c; color: #fff; }

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
            <div class="pengajuan-card card">
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
                                        @else
                                            <span class="badge badge-status badge-diproses">Diproses</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('pengajuan_ruangans.show', $pengajuan->id) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="far fa-eye mr-1"></i>Detail
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
