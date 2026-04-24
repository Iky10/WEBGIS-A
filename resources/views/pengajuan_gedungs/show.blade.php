@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Detail Pengajuan</h1>
                </div>
                <div class="col-sm-6">
                    <a class="btn btn-default float-right" href="{{ route('pengajuan_gedungs.index') }}">
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">

        @include('flash::message')

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Informasi Pengajuan</h3>
                <div class="card-tools">
                    @if($pengajuanGedung->status == 'diproses')
                        <span class="badge badge-warning">Diproses</span>
                    @elseif($pengajuanGedung->status == 'disetujui')
                        <span class="badge badge-success">Disetujui</span>
                    @elseif($pengajuanGedung->status == 'ditolak')
                        <span class="badge badge-danger">Ditolak</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="180">Kode Pengajuan</th>
                                <td><code style="font-size:1.1em;">{{ $pengajuanGedung->kode_pengajuan }}</code></td>
                            </tr>
                            <tr>
                                <th>Nama Pemohon</th>
                                <td>{{ $pengajuanGedung->nama_pemohon }}</td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td>{{ $pengajuanGedung->email_pemohon }}</td>
                            </tr>
                            <tr>
                                <th>No. Telepon</th>
                                <td>{{ $pengajuanGedung->no_telepon ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Asal / Jurusan</th>
                                <td>{{ $pengajuanGedung->asal_instansi ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="180">Gedung</th>
                                <td>{{ optional($pengajuanGedung->gedung)->nama_gedung ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Jenis Kegiatan</th>
                                <td>{{ $pengajuanGedung->jenis_kegiatan }}</td>
                            </tr>
                            <tr>
                                <th>Nama Kegiatan</th>
                                <td>{{ $pengajuanGedung->nama_kegiatan }}</td>
                            </tr>
                            <tr>
                                <th>Jumlah Peserta</th>
                                <td>{{ $pengajuanGedung->jumlah_peserta ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="180">Tanggal Mulai</th>
                                <td>{{ $pengajuanGedung->tanggal_mulai->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Selesai</th>
                                <td>{{ $pengajuanGedung->tanggal_selesai->format('d/m/Y') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="180">Jam Mulai</th>
                                <td>{{ $pengajuanGedung->jam_mulai }}</td>
                            </tr>
                            <tr>
                                <th>Jam Selesai</th>
                                <td>{{ $pengajuanGedung->jam_selesai }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($pengajuanGedung->keperluan)
                    <hr>
                    <h5>Keperluan</h5>
                    <p>{{ $pengajuanGedung->keperluan }}</p>
                @endif

                @if($pengajuanGedung->catatan_admin)
                    <hr>
                    <h5>Catatan Admin</h5>
                    <p>{{ $pengajuanGedung->catatan_admin }}</p>
                @endif
            </div>
        </div>

        {{-- Form Update Status (Admin) --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Ubah Status Pengajuan</h3>
            </div>
            <div class="card-body">
                {!! Form::open(['route' => ['pengajuan_gedungs.status', $pengajuanGedung->id], 'method' => 'post']) !!}
                <div class="row">
                    <div class="form-group col-sm-4">
                        {!! Form::label('status', 'Status:') !!}
                        {!! Form::select('status', [
        'diproses' => 'Diproses',
        'disetujui' => 'Disetujui',
        'ditolak' => 'Ditolak',
    ], $pengajuanGedung->status, ['class' => 'form-control custom-select']) !!}
                    </div>
                    <div class="form-group col-sm-8">
                        {!! Form::label('catatan_admin', 'Catatan Admin (Opsional):') !!}
                        {!! Form::textarea('catatan_admin', $pengajuanGedung->catatan_admin, ['class' => 'form-control', 'rows' => 2]) !!}
                    </div>
                </div>
                {!! Form::submit('Simpan Status', ['class' => 'btn btn-primary']) !!}
                {!! Form::close() !!}
            </div>
        </div>

        {{-- Tombol Ajukan Ulang (jika ditolak) --}}
        @if($pengajuanGedung->status == 'ditolak')
            <div class="card bg-light">
                <div class="card-body text-center">
                    <p class="mb-2">Pengajuan ini ditolak. Anda bisa mengajukan ulang dengan data yang sama.</p>
                    <a href="{{ route('pengajuan_gedungs.ajukan_ulang', $pengajuanGedung->id) }}" class="btn btn-warning">
                        <i class="fas fa-redo"></i> Ajukan Ulang
                    </a>
                </div>
            </div>
        @endif

    </div>
@endsection