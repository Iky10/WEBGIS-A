@extends('layouts.public')

@section('title', 'Cek Status Pengajuan')

@section('content')

<div style="background: linear-gradient(135deg,#1a3c5e,#2d6a9f); color:#fff; padding: 30px 0 20px;">
    <div class="container">
        <a href="{{ route('publik.peta') }}" class="text-white-50 small">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Peta
        </a>
        <h2 class="mt-2 mb-0 font-weight-bold">
            <i class="fas fa-search mr-2"></i>Cek Status Pengajuan
        </h2>
        <p class="mb-0 opacity-75">Masukkan email untuk melihat status pengajuan Anda.</p>
    </div>
</div>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">

            {{-- Form Cek Status --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <form method="POST" action="{{ route('pengajuan.cek_status_result') }}">
                        @csrf
                        <div class="form-group">
                            <label for="email"><strong>Email Pemohon</strong> <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="email" class="form-control"
                                   value="{{ old('email', $email ?? '') }}"
                                   placeholder="Masukkan email yang digunakan saat pengajuan" required>
                        </div>
                        <div class="form-group">
                            <label for="kode"><strong>Kode Pengajuan</strong> <span class="text-muted">(opsional)</span></label>
                            <input type="text" name="kode" id="kode" class="form-control"
                                   value="{{ old('kode', $kode ?? '') }}"
                                   placeholder="Contoh: PG-20260423-001">
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle mr-1"></i>
                                Lupa kode? Cukup isi email saja, kami akan tampilkan semua pengajuan Anda.
                            </small>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search mr-1"></i> Cek Status
                        </button>
                    </form>
                </div>
            </div>

            {{-- Hasil Pencarian --}}
            @if(isset($results))
                @if($results->count() > 0)
                    <h5 class="mb-3">
                        <i class="fas fa-clipboard-list mr-1"></i>
                        Ditemukan {{ $results->count() }} pengajuan
                    </h5>

                    @foreach($results as $p)
                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <strong><i class="fas fa-file-alt mr-1"></i> {{ $p->kode_pengajuan }}</strong>
                                @if($p->status == 'diproses')
                                    <span class="badge badge-warning px-3 py-1">⏳ Diproses</span>
                                @elseif($p->status == 'disetujui')
                                    <span class="badge badge-success px-3 py-1">✅ Disetujui</span>
                                @elseif($p->status == 'ditolak')
                                    <span class="badge badge-danger px-3 py-1">❌ Ditolak</span>
                                @endif
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <td class="text-muted pl-3" width="35%">Gedung</td>
                                        <td><strong>{{ optional($p->gedung)->nama_gedung ?? '-' }}</strong></td>
                                    </tr>
                                    <tr class="bg-light">
                                        <td class="text-muted pl-3">Kegiatan</td>
                                        <td>{{ $p->nama_kegiatan }} ({{ $p->jenis_kegiatan }})</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted pl-3">Tanggal</td>
                                        <td>{{ $p->tanggal_mulai->format('d/m/Y') }} - {{ $p->tanggal_selesai->format('d/m/Y') }}</td>
                                    </tr>
                                    <tr class="bg-light">
                                        <td class="text-muted pl-3">Jam</td>
                                        <td>{{ $p->jam_mulai }} - {{ $p->jam_selesai }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted pl-3">Diajukan pada</td>
                                        <td>{{ $p->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                    @if($p->catatan_admin)
                                        <tr class="bg-light">
                                            <td class="text-muted pl-3">Catatan Admin</td>
                                            <td><em>{{ $p->catatan_admin }}</em></td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        Pengajuan tidak ditemukan. Pastikan email
                        @if(isset($kode) && $kode) dan kode pengajuan @endif
                        yang Anda masukkan benar.
                    </div>
                @endif
            @endif

        </div>
    </div>
</div>

@endsection
