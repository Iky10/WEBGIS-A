<!-- Gedung Field -->
<div class="form-group col-sm-6">
    {!! Form::label('gedung_id', 'Gedung:') !!}
    {!! Form::select('gedung_id', $gedungs, $selectedGedung ?? (isset($pengajuanLama) ? $pengajuanLama->gedung_id : null), ['class' => 'form-control custom-select', 'placeholder' => 'Pilih Gedung']) !!}
</div>

<!-- Nama Pemohon Field -->
<div class="form-group col-sm-6">
    {!! Form::label('nama_pemohon', 'Nama Pemohon:') !!}
    {!! Form::text('nama_pemohon', isset($pengajuanLama) ? $pengajuanLama->nama_pemohon : null, ['class' => 'form-control', 'maxlength' => 255]) !!}
</div>

<!-- Email Pemohon Field -->
<div class="form-group col-sm-6">
    {!! Form::label('email_pemohon', 'Email:') !!}
    {!! Form::email('email_pemohon', isset($pengajuanLama) ? $pengajuanLama->email_pemohon : null, ['class' => 'form-control', 'maxlength' => 255]) !!}
</div>

<!-- No Telepon Field -->
<div class="form-group col-sm-6">
    {!! Form::label('no_telepon', 'No. Telepon (Opsional):') !!}
    {!! Form::text('no_telepon', isset($pengajuanLama) ? $pengajuanLama->no_telepon : null, ['class' => 'form-control', 'maxlength' => 20]) !!}
</div>

<!-- Asal Instansi Field -->
<div class="form-group col-sm-6">
    {!! Form::label('asal_instansi', 'Asal / Jurusan / Organisasi (Opsional):') !!}
    {!! Form::text('asal_instansi', isset($pengajuanLama) ? $pengajuanLama->asal_instansi : null, ['class' => 'form-control', 'maxlength' => 255]) !!}
</div>

<!-- Jenis Kegiatan Field -->
<div class="form-group col-sm-6">
    {!! Form::label('jenis_kegiatan', 'Jenis Kegiatan:') !!}
    {!! Form::select('jenis_kegiatan', [
    'Seminar' => 'Seminar',
    'Wisuda' => 'Wisuda',
    'Sidang' => 'Sidang',
    'Rapat' => 'Rapat',
    'Workshop' => 'Workshop',
    'Pelatihan' => 'Pelatihan',
    'Lainnya' => 'Lainnya',
], isset($pengajuanLama) ? $pengajuanLama->jenis_kegiatan : null, ['class' => 'form-control custom-select', 'placeholder' => 'Pilih Jenis Kegiatan']) !!}
</div>

<!-- Nama Kegiatan Field -->
<div class="form-group col-sm-12">
    {!! Form::label('nama_kegiatan', 'Nama Kegiatan:') !!}
    {!! Form::text('nama_kegiatan', isset($pengajuanLama) ? $pengajuanLama->nama_kegiatan : null, ['class' => 'form-control', 'maxlength' => 255]) !!}
</div>

<!-- Tanggal Mulai Field -->
<div class="form-group col-sm-3">
    {!! Form::label('tanggal_mulai', 'Tanggal Mulai:') !!}
    {!! Form::date('tanggal_mulai', isset($pengajuanLama) ? $pengajuanLama->tanggal_mulai : null, ['class' => 'form-control']) !!}
</div>

<!-- Tanggal Selesai Field -->
<div class="form-group col-sm-3">
    {!! Form::label('tanggal_selesai', 'Tanggal Selesai:') !!}
    {!! Form::date('tanggal_selesai', isset($pengajuanLama) ? $pengajuanLama->tanggal_selesai : null, ['class' => 'form-control']) !!}
</div>

<!-- Jam Mulai Field -->
<div class="form-group col-sm-3">
    {!! Form::label('jam_mulai', 'Jam Mulai:') !!}
    {!! Form::input('time', 'jam_mulai', isset($pengajuanLama) ? $pengajuanLama->jam_mulai : null, ['class' => 'form-control']) !!}
</div>

<!-- Jam Selesai Field -->
<div class="form-group col-sm-3">
    {!! Form::label('jam_selesai', 'Jam Selesai:') !!}
    {!! Form::input('time', 'jam_selesai', isset($pengajuanLama) ? $pengajuanLama->jam_selesai : null, ['class' => 'form-control']) !!}
</div>

<!-- Jumlah Peserta Field -->
<div class="form-group col-sm-6">
    {!! Form::label('jumlah_peserta', 'Jumlah Peserta (Opsional):') !!}
    {!! Form::number('jumlah_peserta', isset($pengajuanLama) ? $pengajuanLama->jumlah_peserta : null, ['class' => 'form-control', 'min' => 1]) !!}
</div>

<!-- Keperluan Field -->
<div class="form-group col-sm-12">
    {!! Form::label('keperluan', 'Keperluan / Keterangan Tambahan (Opsional):') !!}
    {!! Form::textarea('keperluan', isset($pengajuanLama) ? $pengajuanLama->keperluan : null, ['class' => 'form-control', 'rows' => 3]) !!}
</div>