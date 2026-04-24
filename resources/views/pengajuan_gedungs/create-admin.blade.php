@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1>Ajukan Ulang Pengajuan Penggunaan Gedung</h1>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">

        @include('adminlte-templates::common.errors')

        <div class="card">

            {!! Form::open(['route' => 'pengajuan_gedungs.store']) !!}

            <div class="card-body">

                <div class="row">
                    @include('pengajuan_gedungs.fields')
                </div>

            </div>

            <div class="card-footer">
                {!! Form::submit('Kirim Pengajuan', ['class' => 'btn btn-primary']) !!}
                <a href="{{ route('pengajuan_gedungs.index') }}" class="btn btn-default">Batal</a>
            </div>

            {!! Form::close() !!}

        </div>
    </div>
@endsection
