@extends('layouts.public')

@section('title', $gedung->nama_gedung . ' - Detail Gedung')

@section('content')

<div style="background: linear-gradient(135deg,#1a3c5e,#2d6a9f); color:#fff; padding: 30px 0 20px;">
    <div class="container">
        <a href="{{ route('publik.gedung') }}" class="text-white-50 small">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar Gedung
        </a>
        <h2 class="mt-2 mb-0 font-weight-bold">{{ $gedung->nama_gedung }}</h2>
        <p class="mb-0 opacity-75">
            @if($gedung->fungsi)
                <span class="badge badge-light mr-1">{{ $gedung->fungsi }}</span>
            @endif
            <i class="fas fa-map-marker-alt mr-1"></i>{{ $gedung->alamat }}
        </p>
    </div>
</div>

<div class="container py-4">
    <div class="row">

        {{-- Kolom Kiri --}}
        <div class="col-md-5 mb-4">

            {{-- Foto Utama --}}
            <div class="card border-0 shadow-sm mb-3">
                @if($gedung->foto_utama)
                    <img src="{{ asset($gedung->foto_utama) }}"
                         class="card-img-top"
                         style="max-height:300px; object-fit:cover; border-radius:8px;"
                         alt="{{ $gedung->nama_gedung }}">
                @else
                    <div class="text-center py-5 bg-light text-muted" style="border-radius:8px;">
                        <i class="fas fa-image fa-3x mb-2"></i>
                        <p class="mb-0">Belum ada foto</p>
                    </div>
                @endif
            </div>

            {{-- Info Table --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <strong><i class="fas fa-info-circle mr-1"></i> Informasi Gedung</strong>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted pl-3" width="45%">Fungsi</td>
                            <td>{{ $gedung->fungsi ?? '-' }}</td>
                        </tr>
                        <tr class="bg-light">
                            <td class="text-muted pl-3">Status Pemakaian</td>
                            <td>
                                @if($gedung->status_dipakai == 'Sedang Dipakai')
                                    <span class="badge badge-success">Sedang Dipakai</span>
                                @else
                                    <span class="badge badge-secondary">Kosong</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted pl-3">Jumlah Lantai</td>
                            <td>{{ $gedung->jumlah_lantai ? $gedung->jumlah_lantai . ' lantai' : '-' }}</td>
                        </tr>
                        <tr class="bg-light">
                            <td class="text-muted pl-3">Tahun Berdiri</td>
                            <td>{{ $gedung->tahun_berdiri ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted pl-3">Alamat</td>
                            <td>{{ $gedung->alamat }}</td>
                        </tr>
                    </table>
                </div>
            </div>

        </div>

        {{-- Kolom Kanan --}}
        <div class="col-md-7">

            {{-- Deskripsi --}}
            @if($gedung->deskripsi)
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header"><strong><i class="fas fa-align-left mr-1"></i> Deskripsi</strong></div>
                <div class="card-body">
                    <p class="mb-0">{{ $gedung->deskripsi }}</p>
                </div>
            </div>
            @endif

            {{-- Peta Lokasi --}}
            @if($gedung->x && $gedung->y)
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header">
                    <strong><i class="fas fa-map-marker-alt mr-1"></i> Lokasi di Peta</strong>
                </div>
                <div class="card-body p-0">
                    <div id="detail-map" style="height: 220px; border-radius: 0 0 8px 8px;"></div>
                </div>
            </div>
            @endif

            {{-- Foto Galeri --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header">
                    <strong>
                        <i class="fas fa-images mr-1"></i> Foto Galeri
                        <span class="badge badge-secondary ml-1">{{ $fotos->count() }}</span>
                    </strong>
                </div>
                <div class="card-body">
                    @if($fotos->count() > 0)
                        <div class="row">
                            @foreach($fotos as $foto)
                            <div class="col-4 mb-2">
                                <a href="{{ asset($foto->path_foto) }}" target="_blank">
                                    <img src="{{ asset($foto->path_foto) }}"
                                         class="img-fluid rounded"
                                         style="height: 100px; width: 100%; object-fit: cover;"
                                         alt="{{ $foto->nama_file }}">
                                </a>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center mb-0 py-3">
                            <i class="fas fa-images mr-1"></i> Belum ada foto galeri.
                        </p>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
@if($gedung->x && $gedung->y)
<script>
    window.GEDUNG_X = {{ $gedung->x }};
    window.GEDUNG_Y = {{ $gedung->y }};
    window.GEDUNG_NAMA = "{{ $gedung->nama_gedung }}";
</script>
<script src="{{ asset('js/public-gedung-show.js') }}"></script>
@endif
@endpush

@endsection