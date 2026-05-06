@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Tambah Gedung</h1>
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

        @include('adminlte-templates::common.errors')

        <div class="card">
            <div class="card-body">
                {!! Form::open(['route' => 'gedungs.store', 'method' => 'POST', 'files' => true]) !!}

                    <div class="row">
                        @include('dashboard.gedungs.fields')
                    </div>

                    <div class="form-group col-sm-12 mt-3">
                        {!! Form::submit('Simpan', ['class' => 'btn btn-primary']) !!}
                        <a href="{{ route('gedungs.index') }}" class="btn btn-default ml-2">Batal</a>
                    </div>

                {!! Form::close() !!}
            </div>
        </div>
    </div>

@push('scripts')
<script src="{{ asset('js/admin-gedung-create.js') }}"></script>
@endpush
@endsection