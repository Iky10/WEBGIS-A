@extends('layouts.public')

@section('title', 'Daftar Gedung')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/public-gedung.css') }}">
@endpush

@section('content')

<div class="page-header">
    <div class="container">
        <h2 class="mb-1"><i class="fas fa-building mr-2"></i>Daftar Gedung</h2>
        <p class="mb-0 opacity-75">Total {{ $gedungs->total() }} gedung ditemukan</p>
    </div>
</div>

<div class="container">

    {{-- Filter --}}
    <div class="filter-bar">
        <form method="GET" action="{{ route('publik.gedung') }}">
            <div class="row align-items-end">
                <div class="col-md-4 mb-2 mb-md-0">
                    <label class="small text-muted mb-1">Cari Gedung</label>
                    <input type="text" name="search" class="form-control"
                           placeholder="Nama gedung atau alamat..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3 mb-2 mb-md-0">
                    <label class="small text-muted mb-1">Fungsi</label>
                    <select name="fungsi" class="form-control">
                        <option value="">-- Semua Fungsi --</option>
                        @foreach(['Perkantoran','Pendidikan','Kesehatan','Komersial','Publik','Lainnya'] as $f)
                            <option value="{{ $f }}" {{ request('fungsi') == $f ? 'selected' : '' }}>{{ $f }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-search mr-1"></i> Cari
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Grid Gedung --}}
    <div class="row">
        @forelse($gedungs as $gedung)
        <div class="col-md-4 col-sm-6 mb-4">
            <div class="gedung-card card">
                @if($gedung->foto_utama)
                    <img src="{{ asset($gedung->foto_utama) }}"
                         alt="{{ $gedung->nama_gedung }}">
                @else
                    <div class="no-foto"><i class="fas fa-building"></i></div>
                @endif
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <h6 class="card-title mb-0 font-weight-bold">{{ $gedung->nama_gedung }}</h6>
                        @if($gedung->status_dipakai == 'Sedang Dipakai')
                            <span class="badge badge-success ml-1">Sedang Dipakai</span>
                        @else
                            <span class="badge badge-secondary ml-1">Kosong</span>
                        @endif
                    </div>
                    @if($gedung->fungsi)
                        <span class="badge badge-info mb-2">{{ $gedung->fungsi }}</span>
                    @endif
                    <p class="text-muted small mb-3">
                        <i class="fas fa-map-marker-alt mr-1"></i>
                        {{ Str::limit($gedung->alamat, 55) }}
                    </p>
                    <div class="d-flex gap-2">
                        <a href="{{ route('publik.gedung.detail', $gedung->id) }}"
                           class="btn btn-outline-primary btn-sm flex-grow-1">
                            <i class="fas fa-info-circle mr-1"></i> Detail
                        </a>
                        <a href="{{ route('publik.peta') }}?id={{ $gedung->id }}"
                           class="btn btn-outline-success btn-sm">
                            <i class="fas fa-map-marker-alt"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5 text-muted">
            <i class="fas fa-search fa-3x mb-3"></i>
            <p>Tidak ada gedung yang sesuai filter.</p>
            <a href="{{ route('publik.gedung') }}" class="btn btn-outline-primary">Reset Filter</a>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="d-flex justify-content-center mt-2 mb-4">
        {{ $gedungs->withQueryString()->links() }}
    </div>

</div>
@endsection