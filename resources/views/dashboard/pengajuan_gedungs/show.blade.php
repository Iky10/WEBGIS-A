@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Detail Pengajuan</h1>
                </div>
                <div class="col-sm-6">
                    <a class="btn btn-default float-right" href="{{ url()->previous() }}">
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
                <h3 class="card-title">
                    {{ $pengajuanGedung->kode_pengajuan }}
                    @if($pengajuanGedung->status === 'disetujui')
                        <span class="badge badge-success ml-2">Disetujui</span>
                    @elseif($pengajuanGedung->status === 'ditolak')
                        <span class="badge badge-danger ml-2">Ditolak</span>
                    @else
                        <span class="badge badge-warning ml-2">Diproses</span>
                    @endif
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="text-muted">Informasi Pemohon</h5>
                        <table class="table table-sm">
                            <tr><th style="width:40%">Nama</th><td>{{ $pengajuanGedung->nama_pemohon }}</td></tr>
                            <tr><th>Email</th><td>{{ $pengajuanGedung->email_pemohon }}</td></tr>
                            <tr><th>Telepon</th><td>{{ $pengajuanGedung->no_telepon }}</td></tr>
                            <tr><th>Instansi</th><td>{{ $pengajuanGedung->asal_instansi }}</td></tr>
                            <tr><th>Akun</th><td>{{ $pengajuanGedung->user->name ?? '-' }} ({{ $pengajuanGedung->user->email ?? '-' }})</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5 class="text-muted">Informasi Kegiatan</h5>
                        <table class="table table-sm">
                            <tr><th style="width:40%">Gedung</th><td>{{ $pengajuanGedung->gedung->nama_gedung ?? '-' }}</td></tr>
                            <tr><th>Jenis Kegiatan</th><td>{{ $pengajuanGedung->jenis_kegiatan }}</td></tr>
                            <tr><th>Nama Kegiatan</th><td>{{ $pengajuanGedung->nama_kegiatan }}</td></tr>
                            <tr><th>Tanggal</th>
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
                    <div class="mt-3">
                        <h5 class="text-muted">Keperluan</h5>
                        <p>{{ $pengajuanGedung->keperluan }}</p>
                    </div>
                @endif

                @if($pengajuanGedung->catatan_admin)
                    <div class="mt-3">
                        <h5 class="text-muted">Catatan Admin</h5>
                        <div class="alert alert-info">{{ $pengajuanGedung->catatan_admin }}</div>
                    </div>
                @endif

                {{-- Admin: Form update status --}}
                @if(Auth::user()->isAdmin() && $pengajuanGedung->status === 'diproses')
                    <hr>
                    <h5 class="text-muted">Tindakan Admin</h5>
                    <form action="{{ route('pengajuan_gedungs.update-status', $pengajuanGedung->id) }}" method="POST">
                        @csrf @method('PATCH')
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label>Ubah Status:</label>
                                <select name="status" class="form-control" required>
                                    <option value="">-- Pilih --</option>
                                    <option value="disetujui">Setujui</option>
                                    <option value="ditolak">Tolak</option>
                                </select>
                            </div>
                            <div class="form-group col-md-8">
                                <label>Catatan (opsional):</label>
                                <input type="text" name="catatan_admin" class="form-control"
                                       placeholder="Alasan persetujuan/penolakan...">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary"
                                onclick="return confirm('Yakin memperbarui status pengajuan ini?')">
                            <i class="fas fa-save"></i> Simpan Keputusan
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection
