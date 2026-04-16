<!-- Gedung Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('gedung_id', 'Gedung:') !!}
    {!! Form::select('gedung_id', $gedungs, null, ['class' => 'form-control custom-select', 'placeholder' => 'Pilih Gedung']) !!}
</div>

<!-- Nama Fasilitas Field -->
<div class="form-group col-sm-6">
    {!! Form::label('nama_fasilitas', 'Nama Ruangan / Fasilitas:') !!}
    {!! Form::text('nama_fasilitas', null, ['class' => 'form-control','maxlength' => 255,'maxlength' => 255]) !!}
</div>

<!-- Kategori Field -->
<div class="form-group col-sm-6">
    {!! Form::label('kategori', 'Kategori:') !!}
    {!! Form::select('kategori', [
        'Ruangan' => 'Ruangan',
        'Kelas' => 'Kelas',
        'Laboratorium' => 'Laboratorium',
        'Fasilitas Umum' => 'Fasilitas Umum',
        'Lainnya' => 'Lainnya'
    ], null, ['class' => 'form-control custom-select']) !!}
</div>

<!-- Keterangan Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('keterangan', 'Keterangan:') !!}
    {!! Form::textarea('keterangan', null, ['class' => 'form-control', 'rows' => 3]) !!}
</div>
