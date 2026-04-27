@extends('layouts.public')

@section('title', 'Ajukan Penggunaan Gedung — WebGIS Gedung')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/public-gedung.css') }}">
<style>
    .form-header {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        padding: 40px 0 30px;
        color: #fff;
    }
    .form-header h2 { font-weight: 700; margin: 0; }
    .form-header p { opacity: .8; margin: 5px 0 0; }

    .form-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,.08);
    }
    .form-card .card-body { padding: 30px; }
    .form-card .card-footer {
        background: #f8f9fa;
        border-top: 1px solid #eee;
        border-radius: 0 0 12px 12px;
        padding: 16px 30px;
    }

    .btn-kirim {
        background: linear-gradient(135deg, #27ae60, #219a52);
        border: none; color: #fff;
        border-radius: 8px; padding: 10px 28px;
        font-weight: 600;
    }
    .btn-kirim:hover { background: linear-gradient(135deg, #219a52, #1e8449); color: #fff; }

    .btn-batal {
        border-radius: 8px; padding: 10px 28px;
    }

    .alert-danger { border-radius: 8px; }
</style>
@endpush

@section('content')
    <div class="form-header">
        <div class="container">
            <h2><i class="fas fa-file-signature mr-2"></i>Ajukan Penggunaan Gedung</h2>
            <p>Isi form berikut untuk mengajukan penggunaan gedung</p>
        </div>
    </div>

    <div class="container py-4">
        {{-- Validation Errors --}}
        @if($errors->any())
            <div class="alert alert-danger">
                <strong><i class="fas fa-exclamation-triangle mr-1"></i> Terjadi Kesalahan:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="form-card card">
            {!! Form::open(['route' => 'pengajuan_gedungs.store']) !!}

            <div class="card-body">
                <div class="row">
                    @include('dashboard.pengajuan_gedungs.fields')
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-kirim">
                    <i class="fas fa-paper-plane mr-1"></i> Kirim Pengajuan
                </button>
                <a href="{{ route('pengajuan_gedungs.riwayat') }}" class="btn btn-batal btn-default ml-2">Batal</a>
            </div>

            {!! Form::close() !!}
        </div>
    </div>
@endsection
