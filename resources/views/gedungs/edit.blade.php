@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Gedung</h1>
                </div>
                <div class="col-sm-6">
                    <a class="btn btn-default float-right" href="{{ route('gedungs.index') }}">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">
        @include('flash::message')

        <div class="card">
            <div class="card-body">
                {!! Form::model($gedung, ['route' => ['gedungs.update', $gedung->id], 'method' => 'patch', 'files' => true]) !!}

                    <div class="row">
                        @include('gedungs.fields')
                    </div>

                    <div class="form-group col-sm-12 mt-3">
                        {!! Form::submit('Perbarui', ['class' => 'btn btn-primary']) !!}
                        <a href="{{ route('gedungs.index') }}" class="btn btn-default ml-2">Batal</a>
                    </div>

                {!! Form::close() !!}
            </div>
        </div>

        <!-- Foto Galeri yang sudah ada (di luar form utama) -->
        @if(isset($fotos) && $fotos->count() > 0)
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">Foto Galeri Saat Ini</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($fotos as $foto)
                        <div class="col-sm-2 mb-2">
                            <div class="card">
                                <img src="{{ asset('storage/' . $foto->path_foto) }}"
                                     class="card-img-top"
                                     style="height: 100px; object-fit: cover;"
                                     alt="{{ $foto->nama_file }}">
                                <div class="card-body p-1 text-center">
                                    <form action="{{ route('gedungs.foto.destroy', $foto->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-danger btn-xs"
                                                onclick="return confirm('Hapus foto ini?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
@endsection