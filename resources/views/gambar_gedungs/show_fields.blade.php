<!-- Gedung Id Field -->
<div class="col-sm-12">
    {!! Form::label('gedung_id', 'Gedung Id:') !!}
    <p>{{ $gambarGedung->gedung_id }}</p>
</div>

<!-- Nama File Field -->
<div class="col-sm-12">
    {!! Form::label('nama_file', 'Nama File:') !!}
    <p>{{ $gambarGedung->nama_file }}</p>
</div>

<!-- Path Foto Field -->
<div class="col-sm-12">
    {!! Form::label('path_foto', 'Path Foto:') !!}
    <p>{{ $gambarGedung->path_foto }}</p>
</div>

<!-- Keterangan Field -->
<div class="col-sm-12">
    {!! Form::label('keterangan', 'Keterangan:') !!}
    <p>{{ $gambarGedung->keterangan }}</p>
</div>

<!-- Urutan Field -->
<div class="col-sm-12">
    {!! Form::label('urutan', 'Urutan:') !!}
    <p>{{ $gambarGedung->urutan }}</p>
</div>

<!-- Created At Field -->
<div class="col-sm-12">
    {!! Form::label('created_at', 'Created At:') !!}
    <p>{{ $gambarGedung->created_at }}</p>
</div>

<!-- Updated At Field -->
<div class="col-sm-12">
    {!! Form::label('updated_at', 'Updated At:') !!}
    <p>{{ $gambarGedung->updated_at }}</p>
</div>

