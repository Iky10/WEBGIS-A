<!-- Nama Gedung Field -->
<div class="form-group col-sm-6">
    {!! Form::label('nama_gedung', 'Nama Gedung:') !!}
    {!! Form::text('nama_gedung', null, ['class' => 'form-control', 'placeholder' => 'Masukkan nama gedung']) !!}
</div>



<!-- Alamat Field -->
<div class="form-group col-sm-12">
    {!! Form::label('alamat', 'Alamat:') !!}
    {!! Form::textarea('alamat', null, ['class' => 'form-control', 'rows' => 3, 'placeholder' => 'Masukkan alamat lengkap']) !!}
</div>

<!-- Deskripsi Field -->
<div class="form-group col-sm-12">
    {!! Form::label('deskripsi', 'Deskripsi:') !!}
    {!! Form::textarea('deskripsi', null, ['class' => 'form-control', 'rows' => 4, 'placeholder' => 'Deskripsi singkat gedung']) !!}
</div>



<!-- Koordinat -->
<div class="form-group col-sm-6">
    {!! Form::label('x', 'Latitude (X):') !!}
    {!! Form::text('x', null, ['class' => 'form-control', 'id' => 'input_lat', 'placeholder' => 'contoh: -6.200000']) !!}
</div>

<div class="form-group col-sm-6">
    {!! Form::label('y', 'Longitude (Y):') !!}
    {!! Form::text('y', null, ['class' => 'form-control', 'id' => 'input_lng', 'placeholder' => 'contoh: 106.816667']) !!}
</div>

<!-- Peta Pilih Koordinat -->
<div class="form-group col-sm-12">
    <label>Klik peta untuk menentukan lokasi gedung:</label>
    <div id="map-picker" style="height: 350px; border-radius: 8px; border: 1px solid #ced4da;"></div>
    <small class="text-muted">Klik pada peta untuk mengisi koordinat secara otomatis.</small>
</div>

<!-- Foto Utama Field -->
<div class="form-group col-sm-12">
    {!! Form::label('foto_utama', 'Foto Utama (Thumbnail):') !!}
    <div class="custom-file">
        {!! Form::file('foto_utama', ['class' => 'custom-file-input', 'id' => 'foto_utama', 'accept' => 'image/*']) !!}
        <label class="custom-file-label" for="foto_utama">Pilih foto utama...</label>
    </div>
    <small class="text-muted">Format: JPG, PNG. Maks 2MB. Foto ini tampil di popup peta.</small>

    <!-- Preview foto utama -->
    @if(isset($gedung) && $gedung->foto_utama)
        <div class="mt-2">
            <p class="mb-1"><strong>Foto saat ini:</strong></p>
            <img src="{{ asset($gedung->foto_utama) }}"
                 alt="Foto Utama"
                 class="img-thumbnail"
                 style="max-height: 150px;">
        </div>
    @endif

    <div id="preview-utama" class="mt-2" style="display:none;">
        <p class="mb-1"><strong>Preview:</strong></p>
        <img id="img-preview-utama" src="#" alt="Preview" class="img-thumbnail" style="max-height: 150px;">
    </div>
</div>

<!-- Jam Operasional -->
<div class="form-group col-sm-12">
    <label><strong>Jam Operasional Gedung:</strong></label>
    <small class="text-muted d-block mb-2">
        Atur jam buka dan tutup gedung. Kosongkan jika gedung buka 24 jam.
    </small>
    <div class="row">
        <div class="col-sm-6">
            {!! Form::label('jam_buka', 'Jam Buka:') !!}
            {!! Form::time('jam_buka', isset($gedung) ? $gedung->jam_buka_formatted : null, [
                'class' => 'form-control',
                'placeholder' => '07:00'
            ]) !!}
        </div>
        <div class="col-sm-6">
            {!! Form::label('jam_tutup', 'Jam Tutup:') !!}
            {!! Form::time('jam_tutup', isset($gedung) ? $gedung->jam_tutup_formatted : null, [
                'class' => 'form-control',
                'placeholder' => '17:00'
            ]) !!}
        </div>
    </div>
</div>

<!-- Bisa Diajukan Toggle -->
<div class="form-group col-sm-12">
    <div class="custom-control custom-switch">
        {!! Form::hidden('bisa_diajukan', 0) !!}
        {!! Form::checkbox('bisa_diajukan', 1, isset($gedung) ? $gedung->bisa_diajukan : true, [
            'class' => 'custom-control-input',
            'id' => 'bisa_diajukan'
        ]) !!}
        <label class="custom-control-label" for="bisa_diajukan">
            <strong>Gedung ini bisa diajukan penggunaannya</strong>
        </label>
    </div>
    <small class="text-muted">
        Nonaktifkan jika gedung ini tidak bisa diajukan (misal: Rektorat, Koperasi, Ruang Dosen, dll).
    </small>
</div>

<!-- Foto Galeri Field -->
<div class="form-group col-sm-12">
    {!! Form::label('foto_gedung', 'Foto Galeri (bisa lebih dari satu):') !!}
    <div class="custom-file">
        {!! Form::file('foto_gedung[]', ['class' => 'custom-file-input', 'id' => 'foto_gedung', 'accept' => 'image/*', 'multiple' => true]) !!}
        <label class="custom-file-label" for="foto_gedung">Pilih foto galeri...</label>
    </div>
    <small class="text-muted">Bisa pilih beberapa foto sekaligus. Format: JPG, PNG. Maks 2MB per foto.</small>

    <!-- Preview galeri -->
    <div id="preview-galeri" class="mt-2 d-flex flex-wrap gap-2"></div>
</div>

@push('page_scripts')
<!-- Leaflet untuk peta picker -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script src="{{ asset('js/admin-gedung-fields.js') }}"></script>
@endpush