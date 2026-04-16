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

        <div class="card">
            <div class="card-body">
                {!! Form::open(['route' => 'gedungs.store', 'method' => 'POST', 'files' => true]) !!}

                    <div class="row">
                        @include('gedungs.fields')
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fotoUtamaInput = document.querySelector('#foto_utama');
    if (fotoUtamaInput) {
        fotoUtamaInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const previewDiv = document.querySelector('#preview-utama');
                    const previewImg = document.querySelector('#img-preview-utama');
                    if (previewDiv && previewImg) {
                        previewImg.src = event.target.result;
                        previewDiv.style.display = 'block';
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }
});
</script>
@endpush
@endsection