@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Detail Vegetasi</h1>
                </div>
                <div class="col-sm-6">
                    <a class="btn btn-default float-right"
                       href="{{ route('vegetasis.index') }}">
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="text-center mb-3">
                            @if($vegetasi->foto_utama)
                                <img src="{{ asset($vegetasi->foto_utama) }}" class="img-fluid rounded shadow-sm" style="max-height: 250px;">
                            @else
                                <div style="height: 200px; background: #f8f9fa; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                                    <i class="fas fa-leaf fa-4x text-success"></i>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-sm-8">
                        <table class="table table-borderless">
                            <tr>
                                <th width="150">Nama Vegetasi</th>
                                <td>: {{ $vegetasi->nama_vegetasi }}</td>
                            </tr>
                            <tr>
                                <th>Gedung</th>
                                <td>: {{ optional($vegetasi->gedung)->nama_gedung }}</td>
                            </tr>
                            <tr>
                                <th>Kategori</th>
                                <td>: <span class="badge badge-success">{{ $vegetasi->kategori }}</span></td>
                            </tr>
                            <tr>
                                <th>Koordinat</th>
                                <td>: {{ $vegetasi->latitude }}, {{ $vegetasi->longitude }}</td>
                            </tr>
                            <tr>
                                <th>Keterangan</th>
                                <td>: {{ $vegetasi->keterangan ?: '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($vegetasi->gambarVegetasis->count() > 0)
                    <hr>
                    <h5>Galeri Foto Tambahan</h5>
                    <div class="row">
                        @foreach($vegetasi->gambarVegetasis as $gambar)
                            <div class="col-sm-2 mb-3">
                                <a href="{{ asset($gambar->path_foto) }}" target="_blank">
                                    <img src="{{ asset($gambar->path_foto) }}" class="img-thumbnail" style="height: 100px; width: 100%; object-fit: cover;">
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
                
                <hr>
                <h5>Lokasi di Peta</h5>
                <div id="map-show-vegetasi" style="height: 300px; border-radius: 8px; border: 1px solid #ddd;"></div>
            </div>
        </div>
    </div>
@endsection

@push('page_scripts')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        $(document).ready(function() {
            const lat = {{ $vegetasi->latitude ?: -0.53604774 }};
            const lng = {{ $vegetasi->longitude ?: 117.12357581 }};
            const map = L.map('map-show-vegetasi').setView([lat, lng], 19);

            L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
                attribution: '© Google', subdomains: ['mt0', 'mt1', 'mt2', 'mt3'], maxZoom: 21
            }).addTo(map);

            L.marker([lat, lng]).addTo(map)
                .bindPopup('{{ $vegetasi->nama_vegetasi }}')
                .openPopup();
        });
    </script>
@endpush
