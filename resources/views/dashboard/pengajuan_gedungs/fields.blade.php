{{-- Gedung --}}
<div class="form-group col-sm-6">
    {!! Form::label('gedung_id', 'Gedung:') !!}
    {!! Form::select('gedung_id', $gedungs, $selectedGedung ?? null, [
        'class' => 'form-control',
        'placeholder' => '-- Pilih Gedung --',
        'required' => true
    ]) !!}
</div>

{{-- Nama Pemohon --}}
<div class="form-group col-sm-6">
    {!! Form::label('nama_pemohon', 'Nama Pemohon:') !!}
    {!! Form::text('nama_pemohon', Auth::user()->name ?? null, ['class' => 'form-control', 'required' => true]) !!}
</div>

{{-- Email --}}
<div class="form-group col-sm-6">
    {!! Form::label('email_pemohon', 'Email:') !!}
    {!! Form::email('email_pemohon', Auth::user()->email ?? null, ['class' => 'form-control', 'required' => true]) !!}
</div>

{{-- Telepon --}}
<div class="form-group col-sm-6">
    {!! Form::label('no_telepon', 'No. Telepon:') !!}
    {!! Form::text('no_telepon', null, ['class' => 'form-control', 'required' => true, 'placeholder' => '08xxxxxxxxxx']) !!}
</div>

{{-- Asal Instansi --}}
<div class="form-group col-sm-6">
    {!! Form::label('asal_instansi', 'Asal Instansi:') !!}
    {!! Form::text('asal_instansi', null, ['class' => 'form-control', 'required' => true, 'placeholder' => 'Contoh: Politani Samarinda']) !!}
</div>

{{-- Jenis Kegiatan --}}
<div class="form-group col-sm-6">
    {!! Form::label('jenis_kegiatan', 'Jenis Kegiatan:') !!}
    {!! Form::select('jenis_kegiatan', [
        'Seminar' => 'Seminar',
        'Workshop' => 'Workshop',
        'Rapat' => 'Rapat',
        'Wisuda' => 'Wisuda',
        'Sidang' => 'Sidang',
        'Pelatihan' => 'Pelatihan',
        'Olahraga' => 'Olahraga',
        'Lainnya' => 'Lainnya',
    ], null, ['class' => 'form-control', 'placeholder' => '-- Pilih Jenis --', 'required' => true]) !!}
</div>

{{-- Nama Kegiatan --}}
<div class="form-group col-sm-12">
    {!! Form::label('nama_kegiatan', 'Nama Kegiatan:') !!}
    {!! Form::text('nama_kegiatan', null, ['class' => 'form-control', 'required' => true, 'placeholder' => 'Contoh: Seminar Nasional Teknologi 2026']) !!}
</div>

{{-- Tanggal Mulai --}}
<div class="form-group col-sm-3">
    {!! Form::label('tanggal_mulai', 'Tanggal Mulai:') !!}
    {!! Form::date('tanggal_mulai', null, ['class' => 'form-control', 'required' => true]) !!}
</div>

{{-- Tanggal Selesai --}}
<div class="form-group col-sm-3">
    {!! Form::label('tanggal_selesai', 'Tanggal Selesai:') !!}
    {!! Form::date('tanggal_selesai', null, ['class' => 'form-control', 'required' => true]) !!}
</div>

{{-- Jam Mulai --}}
<div class="form-group col-sm-3">
    {!! Form::label('jam_mulai', 'Jam Mulai:') !!}
    {!! Form::time('jam_mulai', null, ['class' => 'form-control', 'required' => true]) !!}
</div>

{{-- Jam Selesai --}}
<div class="form-group col-sm-3">
    {!! Form::label('jam_selesai', 'Jam Selesai:') !!}
    {!! Form::time('jam_selesai', null, ['class' => 'form-control', 'required' => true]) !!}
</div>

{{-- Jumlah Peserta --}}
<div class="form-group col-sm-6">
    {!! Form::label('jumlah_peserta', 'Jumlah Peserta (opsional):') !!}
    {!! Form::number('jumlah_peserta', null, ['class' => 'form-control', 'min' => 1, 'placeholder' => 'Estimasi peserta']) !!}
</div>

{{-- Keperluan --}}
<div class="form-group col-sm-12">
    {!! Form::label('keperluan', 'Keperluan / Keterangan Tambahan (opsional):') !!}
    {!! Form::textarea('keperluan', null, ['class' => 'form-control', 'rows' => 3, 'placeholder' => 'Informasi tambahan...']) !!}
</div>
