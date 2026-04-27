@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Riwayat Pengajuan Saya</h1>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">
        @include('flash::message')

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Gedung</th>
                                <th>Kegiatan</th>
                                <th>Tanggal</th>
                                <th>Jam</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($pengajuanGedungs as $pengajuan)
                            <tr>
                                <td><strong>{{ $pengajuan->kode_pengajuan }}</strong></td>
                                <td>{{ $pengajuan->gedung->nama_gedung ?? '-' }}</td>
                                <td>{{ $pengajuan->nama_kegiatan }}</td>
                                <td>{{ $pengajuan->tanggal_mulai->format('d/m/Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($pengajuan->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($pengajuan->jam_selesai)->format('H:i') }}</td>
                                <td>
                                    @if($pengajuan->status === 'disetujui')
                                        <span class="badge badge-success">Disetujui</span>
                                    @elseif($pengajuan->status === 'ditolak')
                                        <span class="badge badge-danger">Ditolak</span>
                                    @else
                                        <span class="badge badge-warning text-white">Diproses</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('pengajuan_gedungs.show', $pengajuan->id) }}"
                                       class="btn btn-default btn-sm">
                                        <i class="far fa-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    Belum ada pengajuan. <a href="{{ route('pengajuan_gedungs.create') }}">Buat pengajuan baru</a>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
