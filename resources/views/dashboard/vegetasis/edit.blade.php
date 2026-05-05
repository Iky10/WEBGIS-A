@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1>Edit Data Vegetasi</h1>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">

        @include('adminlte-templates::common.errors')

        <div class="card shadow-sm">

            {!! Form::model($vegetasi, ['route' => ['vegetasis.update', $vegetasi->id], 'method' => 'patch', 'files' => true]) !!}

            <div class="card-body">
                <div class="row">
                    @include('dashboard.vegetasis.fields')
                </div>
            </div>

            <div class="card-footer">
                {!! Form::submit('Perbarui', ['class' => 'btn btn-primary']) !!}
                <a href="{{ route('vegetasis.index') }}" class="btn btn-default">Batal</a>
            </div>

            {!! Form::close() !!}

        </div>
    </div>
@endsection
