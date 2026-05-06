@extends('layouts.app')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-map-marked-alt"></i> WebGIS Gedung</h1>
            </div>
            <div class="col-sm-6">
                <div class="float-right">
                    <a href="{{ route('gedungs.index') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-building"></i> Kelola Gedung
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="content px-3">
    <div class="row">

        {{-- Panel Kiri: Filter + Daftar Gedung --}}
        <div class="col-md-3">

            {{-- Filter --}}
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fas fa-filter"></i> Filter</h5>
                </div>
                <div class="card-body p-2">
                    <div class="form-group mb-2">
                        <label class="mb-1">Fungsi Gedung</label>
                        <select id="filter-fungsi" class="form-control form-control-sm">
                            <option value="">-- Semua --</option>
                            <option value="Perkantoran">Perkantoran</option>
                            <option value="Pendidikan">Pendidikan</option>
                            <option value="Kesehatan">Kesehatan</option>
                            <option value="Komersial">Komersial</option>
                            <option value="Publik">Publik</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label class="mb-1">Kondisi</label>
                        <select id="filter-kondisi" class="form-control form-control-sm">
                            <option value="">-- Semua --</option>
                            <option value="Baik">Baik</option>
                            <option value="Sedang">Sedang</option>
                            <option value="Rusak">Rusak</option>
                        </select>
                    </div>
                    <button id="btn-reset-filter" class="btn btn-secondary btn-sm btn-block">
                        <i class="fas fa-undo"></i> Reset Filter
                    </button>
                </div>
            </div>

            {{-- Daftar Gedung --}}
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list"></i> Daftar Gedung
                        <span id="jumlah-gedung" class="badge badge-info ml-1">0</span>
                    </h5>
                </div>
                <div class="card-body p-0" style="max-height: 400px; overflow-y: auto;">
                    <ul class="list-group list-group-flush" id="list-gedung">
                        <li class="list-group-item text-center text-muted py-3">
                            <i class="fas fa-spinner fa-spin"></i> Memuat...
                        </li>
                    </ul>
                </div>
            </div>

        </div>

        {{-- Panel Kanan: Peta --}}
        <div class="col-md-9">
            <div class="card card-outline card-success">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-map"></i> Peta Lokasi Gedung
                    </h5>
                    <div class="card-tools">
                        <button id="btn-fit-bounds" class="btn btn-sm btn-default" title="Tampilkan semua gedung">
                            <i class="fas fa-compress-arrows-alt"></i> Fit All
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div id="map" style="height: 600px; width: 100%;"></div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('third_party_stylesheets')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<link rel="stylesheet" href="{{ asset('css/webgis-index.css') }}">
@endpush

@push('page_scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    window.WEBGIS_URL = '{{ route("webgis.geojson") }}';
</script>
<script src="{{ asset('js/webgis-index.js') }}"></script>
@endpush