<!-- Gedung Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('gedung_id', 'Gedung Id:') !!}
    {!! Form::number('gedung_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Nama File Field -->
<div class="form-group col-sm-6">
    {!! Form::label('nama_file', 'Nama File:') !!}
    {!! Form::text('nama_file', null, ['class' => 'form-control']) !!}
</div>

<!-- Path Foto Field -->
<div class="form-group col-sm-6">
    {!! Form::label('path_foto', 'Path Foto:') !!}
    {!! Form::text('path_foto', null, ['class' => 'form-control']) !!}
</div>

<!-- Keterangan Field -->
<div class="form-group col-sm-6">
    {!! Form::label('keterangan', 'Keterangan:') !!}
    {!! Form::text('keterangan', null, ['class' => 'form-control']) !!}
</div>

<!-- Urutan Field -->
<div class="form-group col-sm-6">
    {!! Form::label('urutan', 'Urutan:') !!}
    {!! Form::number('urutan', null, ['class' => 'form-control']) !!}
</div>