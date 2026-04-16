@extends('layouts.app')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-tachometer-alt"></i> Dashboard</h1>
            </div>
            <div class="col-sm-6">
                <small class="float-right text-muted">
                    <i class="fas fa-clock"></i> {{ now()->format('d F Y') }}
                </small>
            </div>
        </div>
    </div>
</section>

<div class="content px-3">

    {{-- Baris 1: Kartu Statistik --}}
    <div class="row">

        {{-- Total Gedung --}}
        <div class="col-lg-3 col-sm-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $totalGedung }}</h3>
                    <p>Total Gedung</p>
                </div>
                <div class="icon"><i class="fas fa-building"></i></div>
                <a href="{{ route('gedungs.index') }}" class="small-box-footer">
                    Lihat Semua <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        {{-- Gedung Kondisi Baik --}}
        <div class="col-lg-3 col-sm-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $gedungBaik }}</h3>
                    <p>Kondisi Baik</p>
                </div>
                <div class="icon"><i class="fas fa-check-circle"></i></div>
                <a href="{{ route('gedungs.index') }}" class="small-box-footer">
                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        {{-- Gedung Kondisi Sedang --}}
        <div class="col-lg-3 col-sm-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $gedungSedang }}</h3>
                    <p>Kondisi Sedang</p>
                </div>
                <div class="icon"><i class="fas fa-exclamation-circle"></i></div>
                <a href="{{ route('gedungs.index') }}" class="small-box-footer">
                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        {{-- Gedung Kondisi Rusak --}}
        <div class="col-lg-3 col-sm-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $gedungRusak }}</h3>
                    <p>Kondisi Rusak</p>
                </div>
                <div class="icon"><i class="fas fa-times-circle"></i></div>
                <a href="{{ route('gedungs.index') }}" class="small-box-footer">
                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

    </div>

    {{-- Baris 2: Grafik + Tabel Terbaru --}}
    <div class="row">

        {{-- Grafik Kondisi --}}
        <div class="col-md-4">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-pie"></i> Kondisi Gedung
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="chartKondisi" height="220"></canvas>
                </div>
            </div>

            {{-- Statistik Per Fungsi --}}
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-layer-group"></i> Per Fungsi
                    </h5>
                </div>
                <div class="card-body p-0">
                    @forelse($perFungsi as $item)
                    <div class="d-flex align-items-center px-3 py-2 border-bottom">
                        <span class="badge badge-info mr-2">{{ $item->fungsi }}</span>
                        <div class="flex-grow-1">
                            <div class="progress progress-sm mb-0">
                                <div class="progress-bar bg-info"
                                     style="width: {{ $totalGedung > 0 ? ($item->total / $totalGedung * 100) : 0 }}%">
                                </div>
                            </div>
                        </div>
                        <span class="ml-2 font-weight-bold">{{ $item->total }}</span>
                    </div>
                    @empty
                    <p class="text-center text-muted py-3 mb-0">Belum ada data.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Tabel Gedung Terbaru --}}
        <div class="col-md-8">
            <div class="card card-outline card-success">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clock"></i> Gedung Terbaru
                    </h5>
                    <div class="card-tools">
                        <a href="{{ route('gedungs.create') }}" class="btn btn-sm btn-success">
                            <i class="fas fa-plus"></i> Tambah Gedung
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="pl-3">Nama Gedung</th>
                                <th>Fungsi</th>
                                <th>Kondisi</th>
                                <th>Ditambahkan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($gedungTerbaru as $gedung)
                            <tr>
                                <td class="pl-3">
                                    @if($gedung->foto_utama)
                                        <img src="{{ asset('storage/' . $gedung->foto_utama) }}"
                                             class="img-circle mr-1"
                                             style="width:28px; height:28px; object-fit:cover;">
                                    @else
                                        <span class="img-circle bg-secondary d-inline-flex align-items-center justify-content-center mr-1"
                                              style="width:28px; height:28px;">
                                            <i class="fas fa-building text-white" style="font-size:11px;"></i>
                                        </span>
                                    @endif
                                    <strong>{{ $gedung->nama_gedung }}</strong>
                                </td>
                                <td>
                                    @if($gedung->fungsi)
                                        <span class="badge badge-info">{{ $gedung->fungsi }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
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
                                <td>
                                    <small class="text-muted">
                                        {{ $gedung->created_at->format('d M Y') }}
                                    </small>
                                </td>
                                <td>
                                    <a href="{{ route('gedungs.show', $gedung->id) }}"
                                       class="btn btn-xs btn-default">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('gedungs.edit', $gedung->id) }}"
                                       class="btn btn-xs btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    Belum ada data gedung.
                                    <a href="{{ route('gedungs.create') }}">Tambah sekarang</a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($totalGedung > 5)
                <div class="card-footer text-right">
                    <a href="{{ route('gedungs.index') }}" class="btn btn-sm btn-default">
                        Lihat Semua {{ $totalGedung }} Gedung <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                @endif
            </div>

            {{-- Info Box Total Foto --}}
            <div class="info-box bg-gradient-secondary">
                <span class="info-box-icon">
                    <i class="fas fa-images"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Foto Galeri Tersimpan</span>
                    <span class="info-box-number">{{ $totalFoto }} Foto</span>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection

@push('page_scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    window.CHART_DATA_BAIK = {{ $gedungBaik }};
    window.CHART_DATA_SEDANG = {{ $gedungSedang }};
    window.CHART_DATA_RUSAK = {{ $gedungRusak }};
</script>
<script src="{{ asset('js/admin-home.js') }}"></script>
@endpush