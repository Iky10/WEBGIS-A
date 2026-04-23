@extends('layouts.public')

@section('title', 'Ajukan Penggunaan Gedung')

@section('content')

<div style="background: linear-gradient(135deg,#1a3c5e,#2d6a9f); color:#fff; padding: 30px 0 20px;">
    <div class="container">
        <a href="{{ route('publik.peta') }}" class="text-white-50 small">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Peta
        </a>
        <h2 class="mt-2 mb-0 font-weight-bold">
            <i class="fas fa-file-alt mr-2"></i>Ajukan Penggunaan Gedung
        </h2>
        <p class="mb-0 opacity-75">Isi formulir di bawah untuk mengajukan penggunaan gedung.</p>
    </div>
</div>

<div class="container py-4">

    {{-- Tampilkan error validasi --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong><i class="fas fa-exclamation-triangle mr-1"></i> Terjadi kesalahan:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-primary text-white">
            <strong><i class="fas fa-edit mr-1"></i> Formulir Pengajuan</strong>
        </div>
        <div class="card-body">

            {!! Form::open(['route' => 'pengajuan_gedungs.store']) !!}

            <div class="row">
                @include('pengajuan_gedungs.fields')
            </div>

            <hr>

            <div class="d-flex justify-content-between">
                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
                {!! Form::submit('Kirim Pengajuan', ['class' => 'btn btn-primary px-4']) !!}
            </div>

            {!! Form::close() !!}

        </div>
    </div>
</div>

@endsection