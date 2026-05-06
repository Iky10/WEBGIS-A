<!-- Fasilitas Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('gedung_fasilitas_id', 'Ruangan / Fasilitas:') !!}
    {!! Form::select('gedung_fasilitas_id', $fasilitas, null, ['class' => 'form-control custom-select', 'placeholder' => 'Pilih Ruangan']) !!}
</div>

<!-- Nama Kegiatan Field -->
<div class="form-group col-sm-6">
    {!! Form::label('nama_kegiatan', 'Nama Kegiatan / Kuliah:') !!}
    {!! Form::text('nama_kegiatan', null, ['class' => 'form-control','maxlength' => 255]) !!}
</div>

<!-- Hari Field -->
<div class="form-group col-sm-4">
    {!! Form::label('hari', 'Hari:') !!}
    {!! Form::select('hari', [
        'Senin' => 'Senin',
        'Selasa' => 'Selasa',
        'Rabu' => 'Rabu',
        'Kamis' => 'Kamis',
        'Jumat' => 'Jumat',
        'Sabtu' => 'Sabtu',
        'Minggu' => 'Minggu'
    ], null, ['class' => 'form-control custom-select']) !!}
</div>

<!-- Jam Mulai Field -->
<div class="form-group col-sm-4">
    {!! Form::label('jam_mulai', 'Jam Mulai:') !!}
    {!! Form::input('time', 'jam_mulai', null, ['class' => 'form-control']) !!}
</div>

<!-- Jam Selesai Field -->
<div class="form-group col-sm-4">
    {!! Form::label('jam_selesai', 'Jam Selesai:') !!}
    {!! Form::input('time', 'jam_selesai', null, ['class' => 'form-control']) !!}
</div>

<!-- Keterangan Field -->
<div class="form-group col-sm-12">
    {!! Form::label('keterangan', 'Keterangan (Opsional):') !!}
    {!! Form::textarea('keterangan', null, ['class' => 'form-control', 'rows' => 3]) !!}
</div>
