@extends('layouts.public')

@section('title', 'Pengajuan Berhasil')

@section('content')

<div style="background: linear-gradient(135deg,#1b5e20,#4caf50); color:#fff; padding: 40px 0;">
    <div class="container text-center">
        <i class="fas fa-check-circle fa-3x mb-3"></i>
        <h2 class="font-weight-bold">Pengajuan Berhasil Dikirim!</h2>
        <p class="mb-0 opacity-75">Pengajuan penggunaan gedung Anda telah kami terima.</p>
    </div>
</div>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">

            {{-- Kode Pengajuan --}}
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body text-center py-4">
                    <p class="text-muted mb-1">Kode Pengajuan Anda</p>
                    <h1 class="font-weight-bold text-success mb-2" style="font-size: 2.5rem; letter-spacing: 3px;" id="kodePengajuan">
                        {{ $pengajuan->kode_pengajuan }}
                    </h1>
                    <button class="btn btn-outline-success btn-sm" onclick="copyKode()">
                        <i class="fas fa-copy mr-1"></i> Salin Kode
                    </button>
                    <p class="text-muted mt-3 mb-0">
                        <i class="fas fa-envelope mr-1"></i>
                        Detail pengajuan juga dikirim ke <strong>{{ $pengajuan->email_pemohon }}</strong>
                    </p>
                </div>
            </div>

            {{-- Ringkasan --}}
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-primary text-white">
                    <strong><i class="fas fa-clipboard-list mr-1"></i> Ringkasan Pengajuan</strong>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted pl-3" width="40%">Gedung</td>
                            <td><strong>{{ optional($pengajuan->gedung)->nama_gedung ?? '-' }}</strong></td>
                        </tr>
                        <tr class="bg-light">
                            <td class="text-muted pl-3">Nama Pemohon</td>
                            <td>{{ $pengajuan->nama_pemohon }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted pl-3">Jenis Kegiatan</td>
                            <td>{{ $pengajuan->jenis_kegiatan }}</td>
                        </tr>
                        <tr class="bg-light">
                            <td class="text-muted pl-3">Nama Kegiatan</td>
                            <td>{{ $pengajuan->nama_kegiatan }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted pl-3">Tanggal</td>
                            <td>{{ $pengajuan->tanggal_mulai->format('d/m/Y') }} - {{ $pengajuan->tanggal_selesai->format('d/m/Y') }}</td>
                        </tr>
                        <tr class="bg-light">
                            <td class="text-muted pl-3">Jam</td>
                            <td>{{ $pengajuan->jam_mulai }} - {{ $pengajuan->jam_selesai }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted pl-3">Status</td>
                            <td><span class="badge badge-warning">Diproses</span></td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Info & Link --}}
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <h5><i class="fas fa-info-circle text-primary mr-1"></i> Apa selanjutnya?</h5>
                    <ul class="mb-0">
                        <li>Admin akan meninjau pengajuan Anda.</li>
                        <li>Anda akan menerima <strong>email notifikasi</strong> saat status berubah.</li>
                        <li>Anda juga bisa <a href="{{ route('pengajuan.cek_status') }}">cek status pengajuan</a> kapan saja menggunakan kode di atas.</li>
                    </ul>
                </div>
            </div>

            {{-- Tombol --}}
            <div class="d-flex justify-content-between">
                <a href="{{ route('pengajuan.cek_status') }}" class="btn btn-outline-primary">
                    <i class="fas fa-search mr-1"></i> Cek Status Pengajuan
                </a>
                <a href="{{ route('publik.peta') }}" class="btn btn-primary">
                    <i class="fas fa-map mr-1"></i> Kembali ke Peta
                </a>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
function copyKode() {
    var kode = document.getElementById('kodePengajuan').textContent.trim();
    navigator.clipboard.writeText(kode).then(function() {
        alert('Kode berhasil disalin: ' + kode);
    });
}
</script>
@endpush

@endsection
