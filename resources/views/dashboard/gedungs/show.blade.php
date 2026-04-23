@extends('layouts.app')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-building"></i> Detail Gedung</h1>
            </div>
            <div class="col-sm-6">
                <div class="float-right">
                    <a href="{{ route('gedungs.edit', $gedung->id) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('gedungs.index') }}" class="btn btn-default btn-sm ml-1">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="content px-3">
    <div class="row">

        {{-- Kolom Kiri: Info Gedung --}}
        <div class="col-md-5">

            {{-- Foto Utama --}}
            <div class="card card-outline card-primary">
                <div class="card-body p-0">
                    @if($gedung->foto_utama)
                        <img src="{{ asset($gedung->foto_utama) }}"
                             alt="{{ $gedung->nama_gedung }}"
                             class="img-fluid w-100"
                             style="max-height: 280px; object-fit: cover; border-radius: 4px;">
                    @else
                        <div class="text-center py-5 text-muted bg-light" style="border-radius:4px;">
                            <i class="fas fa-image fa-3x mb-2"></i>
                            <p class="mb-0">Belum ada foto utama</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Info Singkat --}}
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fas fa-info-circle"></i> Info Gedung</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted pl-3" width="40%">Nama</td>
                            <td><strong>{{ $gedung->nama_gedung }}</strong></td>
                        </tr>
                        <tr class="bg-light">
                            <td class="text-muted pl-3">Fungsi</td>
                            <td>
                                @if($gedung->fungsi)
                                    <span class="badge badge-info">{{ $gedung->fungsi }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted pl-3">Kondisi</td>
                            <td>
                                @if($gedung->kondisi == 'Baik')
                                    <span class="badge badge-success">Baik</span>
                                @elseif($gedung->kondisi == 'Sedang')
                                    <span class="badge badge-warning">Sedang</span>
                                @elseif($gedung->kondisi == 'Rusak')
                                    <span class="badge badge-danger">Rusak</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        <tr class="bg-light">
                            <td class="text-muted pl-3">Jumlah Lantai</td>
                            <td>{{ $gedung->jumlah_lantai ?? '-' }} lantai</td>
                        </tr>
                        <tr>
                            <td class="text-muted pl-3">Tahun Berdiri</td>
                            <td>{{ $gedung->tahun_berdiri ?? '-' }}</td>
                        </tr>
                        <tr class="bg-light">
                            <td class="text-muted pl-3">Koordinat</td>
                            <td>
                                <small class="text-muted">
                                    {{ $gedung->x }}, {{ $gedung->y }}
                                </small>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted pl-3">Ditambahkan</td>
                            <td><small>{{ $gedung->created_at->format('d M Y') }}</small></td>
                        </tr>
                    </table>
                </div>
            </div>

        </div>

        {{-- Kolom Kanan: Detail + Peta + Galeri --}}
        <div class="col-md-7">

            {{-- Alamat & Deskripsi --}}
            <div class="card card-outline card-secondary">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fas fa-map-marker-alt"></i> Alamat & Deskripsi</h5>
                </div>
                <div class="card-body">
                    <label class="text-muted mb-1">Alamat</label>
                    <p>{{ $gedung->alamat }}</p>

                    <label class="text-muted mb-1">Deskripsi</label>
                    <p class="mb-0">{{ $gedung->deskripsi ?? '-' }}</p>
                </div>
            </div>

            {{-- Mini Peta --}}
            @if($gedung->x && $gedung->y)
            <div class="card card-outline card-success">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fas fa-map"></i> Lokasi di Peta</h5>
                    <div class="card-tools">
                        <a href="{{ route('webgis.index') }}" class="btn btn-sm btn-default">
                            <i class="fas fa-external-link-alt"></i> Buka WebGIS
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div id="mini-map" style="height: 220px; border-radius: 0 0 4px 4px;"></div>
                </div>
            </div>
            @endif

            {{-- Foto Galeri --}}
            <div class="card card-outline card-warning">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-images"></i> Foto Galeri
                        <span class="badge badge-warning ml-1">{{ $fotos->count() }}</span>
                    </h5>
                    <div class="card-tools">
                        <a href="{{ route('gedungs.edit', $gedung->id) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-plus"></i> Tambah Foto
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($fotos->count() > 0)
                        <div class="row">
                            @foreach($fotos as $foto)
                                <div class="col-sm-4 mb-3">
                                    <div class="card shadow-sm h-100">
                                        <a href="{{ asset($foto->path_foto) }}"
                                           target="_blank">
                                            <img src="{{ asset($foto->path_foto) }}"
                                                 class="card-img-top"
                                                 alt="{{ $foto->nama_file }}"
                                                 style="height: 130px; object-fit: cover;">
                                        </a>
                                        <div class="card-body p-2">
                                            <small class="text-muted d-block text-truncate">
                                                {{ $foto->keterangan ?: $foto->nama_file }}
                                            </small>
                                            <form action="{{ route('gedungs.foto.destroy', $foto->id) }}"
                                                  method="POST" class="mt-1">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-danger btn-xs btn-block"
                                                        onclick="return confirm('Hapus foto ini?')">
                                                    <i class="fas fa-trash"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-images fa-2x mb-2"></i>
                            <p class="mb-0">Belum ada foto galeri.</p>
                            <a href="{{ route('gedungs.edit', $gedung->id) }}" class="btn btn-sm btn-warning mt-2">
                                <i class="fas fa-plus"></i> Tambah Foto
                            </a>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('third_party_stylesheets')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
@endpush

@push('page_scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@if($gedung->x && $gedung->y)
<script>
    window.GEDUNG_X = {{ $gedung->x }};
    window.GEDUNG_Y = {{ $gedung->y }};
    window.GEDUNG_NAMA = "{{ $gedung->nama_gedung }}";
</script>
<script src="{{ asset('js/admin-gedung-show.js') }}"></script>
@endif
@endpush