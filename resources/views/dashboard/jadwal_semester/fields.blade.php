<!-- Gedung Field -->
<div class="form-group col-sm-6">
    {!! Form::label('gedung_id', 'Gedung:') !!}
    {!! Form::select('gedung_id', $gedungs, null, ['class' => 'form-control custom-select', 'placeholder' => 'Pilih Gedung']) !!}
</div>

<!-- Semester Field -->
<div class="form-group col-sm-6">
    {!! Form::label('semester', 'Semester:') !!}
    {!! Form::select('semester', [1=>'Semester 1', 2=>'Semester 2', 3=>'Semester 3', 4=>'Semester 4', 5=>'Semester 5', 6=>'Semester 6', 7=>'Semester 7', 8=>'Semester 8'], null, ['class' => 'form-control custom-select', 'placeholder' => 'Pilih Semester']) !!}
</div>

<!-- Tahun Ajaran Field -->
<div class="form-group col-sm-6">
    {!! Form::label('tahun_ajaran', 'Tahun Ajaran:') !!}
    {!! Form::select('tahun_ajaran', $tahunAjarans, null, ['class' => 'form-control custom-select', 'placeholder' => 'Pilih Tahun Ajaran']) !!}
</div>

<!-- File Jadwal Field -->
<div class="form-group col-sm-6">
    {!! Form::label('file_jadwal', 'File Jadwal (PNG/PDF):') !!}
    <div class="input-group">
        <div class="custom-file">
            {!! Form::file('file_jadwal', ['class' => 'custom-file-input']) !!}
            {!! Form::label('file_jadwal', 'Choose file', ['class' => 'custom-file-label']) !!}
        </div>
    </div>
    <small class="text-muted">Maksimal 5MB. Format: PNG, JPG, WebP, PDF. @if(isset($jadwalSemester) && $jadwalSemester->file_jadwal) (Kosongkan jika tidak ingin mengubah file) @endif</small>
</div>
<div class="clearfix"></div>

<!-- Keterangan Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('keterangan', 'Keterangan Tambahan:') !!}
    {!! Form::textarea('keterangan', null, ['class' => 'form-control', 'rows' => 3]) !!}
</div>
