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

    {{-- ═══ Baris 1: Statistik Gedung ═══ --}}
    <div class="row">
        <div class="col-lg-4 col-sm-6">
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

        <div class="col-lg-4 col-sm-6">
            <div class="small-box bg-secondary">
                <div class="inner">
                    <h3>{{ $gedungKosong }}</h3>
                    <p>Gedung Kosong</p>
                </div>
                <div class="icon"><i class="fas fa-door-closed"></i></div>
                <a href="{{ route('gedungs.index') }}" class="small-box-footer">
                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-4 col-sm-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $gedungDipakai }}</h3>
                    <p>Gedung Sedang Dipakai</p>
                </div>
                <div class="icon"><i class="fas fa-door-open"></i></div>
                <a href="{{ route('gedungs.index') }}" class="small-box-footer">
                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- ═══ Baris 2: Statistik Pengajuan ═══ --}}
    <div class="row">
        <div class="col-lg-3 col-sm-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $totalPengajuan }}</h3>
                    <p>Total Pengajuan</p>
                </div>
                <div class="icon"><i class="fas fa-file-alt"></i></div>
                <a href="{{ route('pengajuan_ruangans.index') }}" class="small-box-footer">
                    Lihat Semua <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $pengajuanMenunggu }}</h3>
                    <p>Menunggu Persetujuan</p>
                </div>
                <div class="icon"><i class="fas fa-hourglass-half"></i></div>
                <a href="{{ route('pengajuan_ruangans.index') }}" class="small-box-footer">
                    Proses Sekarang <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6">
            <div class="small-box bg-teal">
                <div class="inner">
                    <h3>{{ $pengajuanDisetujui }}</h3>
                    <p>Disetujui</p>
                </div>
                <div class="icon"><i class="fas fa-check-double"></i></div>
                <a href="{{ route('pengajuan_ruangans.index') }}" class="small-box-footer">
                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $pengajuanDitolak }}</h3>
                    <p>Ditolak</p>
                </div>
                <div class="icon"><i class="fas fa-times-circle"></i></div>
                <a href="{{ route('pengajuan_ruangans.index') }}" class="small-box-footer">
                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- ═══ Baris 3: Grafik + Gedung Terbaru ═══ --}}
    <div class="row">

        {{-- Grafik Status Pemakaian --}}
        <div class="col-md-5">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-pie"></i> Status Pemakaian Gedung
                    </h5>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <canvas id="chartKondisi" style="max-height: 240px;"></canvas>
                </div>
            </div>
        </div>

        {{-- Tabel Gedung Terbaru --}}
        <div class="col-md-7">
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
                    {{-- ═══ DESKTOP: Tabel ═══ --}}
                    <table class="table table-sm table-hover mb-0 d-none d-md-table">
                        <thead class="bg-light">
                            <tr>
                                <th class="pl-3">Nama Gedung</th>
                                <th>Status Pemakaian</th>
                                <th>Ditambahkan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($gedungTerbaru as $gedung)
                            <tr>
                                <td class="pl-3">
                                    @if($gedung->foto_utama)
                                        <img src="{{ asset($gedung->foto_utama) }}"
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
                                    @if($gedung->status_dipakai == 'Sedang Dipakai')
                                        <span class="badge badge-primary">Sedang Dipakai</span>
                                    @elseif($gedung->status_dipakai == 'Tutup')
                                        <span class="badge badge-secondary">Tutup</span>
                                    @else
                                        <span class="badge badge-success">Terbuka</span>
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
                                <td colspan="4" class="text-center text-muted py-4">
                                    Belum ada data gedung.
                                    <a href="{{ route('gedungs.create') }}">Tambah sekarang</a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{-- ═══ MOBILE: Card List ═══ --}}
                    <div class="d-block d-md-none mobile-card-list">
                        @forelse($gedungTerbaru as $gedung)
                        <div class="mobile-card">
                            <div class="mobile-card-header">
                                @if($gedung->foto_utama)
                                    <img src="{{ asset($gedung->foto_utama) }}" class="mobile-card-thumb">
                                @else
                                    <span class="mobile-card-thumb mobile-card-thumb-placeholder">
                                        <i class="fas fa-building"></i>
                                    </span>
                                @endif
                                <div class="mobile-card-title">
                                    <strong>{{ $gedung->nama_gedung }}</strong>
                                    <small class="text-muted d-block">
                                        <i class="far fa-calendar-alt"></i> {{ $gedung->created_at->format('d M Y') }}
                                    </small>
                                </div>
                                @if($gedung->status_dipakai == 'Sedang Dipakai')
                                    <span class="badge badge-primary">Dipakai</span>
                                @elseif($gedung->status_dipakai == 'Tutup')
                                    <span class="badge badge-secondary">Tutup</span>
                                @else
                                    <span class="badge badge-success">Terbuka</span>
                                @endif
                            </div>
                            <div class="mobile-card-actions">
                                <a href="{{ route('gedungs.show', $gedung->id) }}"
                                   class="btn btn-sm btn-outline-secondary flex-fill">
                                    <i class="fas fa-eye mr-1"></i> Detail
                                </a>
                                <a href="{{ route('gedungs.edit', $gedung->id) }}"
                                   class="btn btn-sm btn-outline-warning flex-fill">
                                    <i class="fas fa-edit mr-1"></i> Edit
                                </a>
                            </div>
                        </div>
                        @empty
                        <div class="mobile-card-empty text-center text-muted py-4">
                            Belum ada data gedung.<br>
                            <a href="{{ route('gedungs.create') }}" class="btn btn-sm btn-success mt-2">
                                <i class="fas fa-plus mr-1"></i> Tambah Gedung
                            </a>
                        </div>
                        @endforelse
                    </div>
                </div>
                @if($totalGedung > 5)
                <div class="card-footer text-right">
                    <a href="{{ route('gedungs.index') }}" class="btn btn-sm btn-default">
                        Lihat Semua {{ $totalGedung }} Gedung <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                @endif
            </div>
        </div>

    </div>

    {{-- ═══ Baris 4: Pengajuan Menunggu Persetujuan ═══ --}}
    @if($pengajuanTerbaru->isNotEmpty())
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-warning">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-exclamation-triangle text-warning"></i> Pengajuan Menunggu Persetujuan
                        <span class="badge badge-warning ml-1">{{ $pengajuanMenunggu }}</span>
                    </h5>
                    <div class="card-tools">
                        <a href="{{ route('pengajuan_ruangans.index') }}" class="btn btn-sm btn-default">
                            Kelola Semua <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    {{-- ═══ DESKTOP: Tabel ═══ --}}
                    <table class="table table-sm table-hover mb-0 d-none d-md-table">
                        <thead class="bg-light">
                            <tr>
                                <th class="pl-3">Kode</th>
                                <th>Pemohon</th>
                                <th>Ruangan</th>
                                <th>Kegiatan</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pengajuanTerbaru as $pj)
                            <tr>
                                <td class="pl-3"><strong>{{ $pj->kode_pengajuan }}</strong></td>
                                <td>{{ $pj->nama_pemohon }}</td>
                                <td>
                                    <strong>{{ $pj->ruangan->nama_fasilitas ?? '-' }}</strong>
                                    <br><small class="text-muted">{{ $pj->ruangan->gedung->nama_gedung ?? '-' }}</small>
                                </td>
                                <td>{{ $pj->nama_kegiatan }}</td>
                                <td>{{ $pj->tanggal_mulai->format('d/m/Y') }}</td>
                                <td>
                                    <a href="{{ route('pengajuan_ruangans.show', $pj->id) }}"
                                       class="btn btn-xs btn-default">
                                        <i class="fas fa-eye"></i> Lihat
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{-- ═══ MOBILE: Card List ═══ --}}
                    <div class="d-block d-md-none mobile-card-list">
                        @foreach($pengajuanTerbaru as $pj)
                        <div class="mobile-card mobile-card-pending">
                            <div class="mobile-card-header">
                                <div class="mobile-card-title">
                                    <strong class="text-primary">{{ $pj->kode_pengajuan }}</strong>
                                    <small class="text-muted d-block">
                                        <i class="far fa-calendar-alt"></i> {{ $pj->tanggal_mulai->format('d/m/Y') }}
                                    </small>
                                </div>
                                <span class="badge badge-warning">Menunggu</span>
                            </div>
                            <div class="mobile-card-body">
                                <div class="mobile-card-row">
                                    <i class="fas fa-user text-secondary"></i>
                                    <span>{{ $pj->nama_pemohon }}</span>
                                </div>
                                <div class="mobile-card-row">
                                    <i class="fas fa-door-open text-secondary"></i>
                                    <span>
                                        <strong>{{ $pj->ruangan->nama_fasilitas ?? '-' }}</strong>
                                        — {{ $pj->ruangan->gedung->nama_gedung ?? '-' }}
                                    </span>
                                </div>
                                <div class="mobile-card-row">
                                    <i class="fas fa-clipboard-list text-secondary"></i>
                                    <span>{{ $pj->nama_kegiatan }}</span>
                                </div>
                            </div>
                            <div class="mobile-card-actions">
                                <a href="{{ route('pengajuan_ruangans.show', $pj->id) }}"
                                   class="btn btn-sm btn-primary flex-fill">
                                    <i class="fas fa-eye mr-1"></i> Lihat Detail
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>
@endsection

@push('page_scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    window.CHART_DATA_KOSONG = {{ $gedungKosong }};
    window.CHART_DATA_DIPAKAI = {{ $gedungDipakai }};
</script>
<script src="{{ asset('js/admin-home.js') }}"></script>
@endpush