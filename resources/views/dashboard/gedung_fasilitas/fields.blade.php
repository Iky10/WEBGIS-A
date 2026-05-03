<!-- Gedung Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('gedung_id', 'Gedung:') !!}
    {!! Form::select('gedung_id', $gedungs, null, ['class' => 'form-control custom-select', 'placeholder' => 'Pilih Gedung']) !!}
</div>

<!-- Nama Fasilitas Field -->
<div class="form-group col-sm-6">
    {!! Form::label('nama_fasilitas', 'Nama Ruangan / Fasilitas:') !!}
    {!! Form::text('nama_fasilitas', null, ['class' => 'form-control','maxlength' => 255, 'placeholder' => 'Masukkan nama ruangan']) !!}
</div>

<!-- Kategori Field -->
<div class="form-group col-sm-6">
    {!! Form::label('kategori', 'Kategori:') !!}
    {!! Form::select('kategori', [
        'Ruang Kelas' => 'Ruang Kelas',
        'Laboratorium' => 'Laboratorium',
        'Post Penjagaan' => 'Post Penjagaan',
        'Ruang Kuliah Umum' => 'Ruang Kuliah Umum',
        'Perpustakaan' => 'Perpustakaan',
        'Kepala Ruangan / Pengurus' => 'Kepala Ruangan / Pengurus',
        'Ruangan Sekretariatan / Administrasi' => 'Ruangan Sekretariatan / Administrasi'
    ], null, ['class' => 'form-control custom-select', 'placeholder' => 'Pilih Kategori']) !!}
</div>

<!-- Keterangan Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('keterangan', 'Keterangan:') !!}
    {!! Form::textarea('keterangan', null, ['class' => 'form-control', 'rows' => 3, 'placeholder' => 'Keterangan tambahan (opsional)']) !!}
</div>

<!-- Status Toggles -->
<div class="form-group col-sm-6">
    <label>Status Operasional:</label>
    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
        {!! Form::hidden('is_aktif', 0) !!}
        <input type="checkbox" name="is_aktif" value="1"
               class="custom-control-input" id="input_is_aktif"
               {{ isset($gedungFasilitas) ? ($gedungFasilitas->is_aktif ? 'checked' : '') : 'checked' }}>
        <label class="custom-control-label" for="input_is_aktif">
            <span id="label-is-aktif">Aktif (ruangan beroperasi normal)</span>
        </label>
    </div>
    <small class="text-muted">Set ke <strong>Tidak Aktif</strong> kalau ruangan sedang perbaikan atau tidak tersedia.</small>
</div>

<div class="form-group col-sm-6">
    <label>Boleh Diajukan Pengguna:</label>
    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-primary">
        {!! Form::hidden('bisa_diajukan', 0) !!}
        <input type="checkbox" name="bisa_diajukan" value="1"
               class="custom-control-input" id="input_bisa_diajukan"
               {{ isset($gedungFasilitas) && $gedungFasilitas->bisa_diajukan ? 'checked' : '' }}>
        <label class="custom-control-label" for="input_bisa_diajukan">
            <span id="label-bisa-diajukan">Ya (user boleh mengajukan penggunaan)</span>
        </label>
    </div>
    <small class="text-muted">Hanya ruangan seperti <em>Auditorium, RKU, Ruang Seminar</em> yang biasanya di-set <strong>Ya</strong>. Kelas reguler biasanya <strong>Tidak</strong> karena sudah dipakai jadwal semester.</small>
</div>

<!-- Koordinat -->
<div class="form-group col-sm-6">
    {!! Form::label('latitude', 'Latitude:') !!}
    {!! Form::text('latitude', null, ['class' => 'form-control', 'id' => 'input_lat_ruangan', 'placeholder' => 'contoh: -0.53597801']) !!}
</div>

<div class="form-group col-sm-6">
    {!! Form::label('longitude', 'Longitude:') !!}
    {!! Form::text('longitude', null, ['class' => 'form-control', 'id' => 'input_lng_ruangan', 'placeholder' => 'contoh: 117.12345243']) !!}
</div>

<!-- Peta Pilih Koordinat -->
<div class="form-group col-sm-12">
    <label>Klik peta untuk menentukan lokasi ruangan:</label>
    <div id="map-picker-ruangan" style="height: 350px; border-radius: 8px; border: 1px solid #ced4da;"></div>
    <small class="text-muted">Klik pada peta untuk mengisi koordinat secara otomatis. Marker bisa digeser.</small>
</div>

<!-- Foto Ruangan Field -->
<div class="form-group col-sm-12">
    {!! Form::label('foto_ruangan', 'Foto Ruangan:') !!}
    <div class="custom-file">
        {!! Form::file('foto_ruangan', ['class' => 'custom-file-input', 'id' => 'foto_ruangan', 'accept' => 'image/*']) !!}
        <label class="custom-file-label" for="foto_ruangan">Pilih foto ruangan...</label>
    </div>
    <small class="text-muted">Format: JPG, PNG, WEBP. Maks 2MB.</small>

    <!-- Preview foto saat ini (edit mode) -->
    @if(isset($gedungFasilitas) && $gedungFasilitas->foto_ruangan)
        <div class="mt-2">
            <p class="mb-1"><strong>Foto saat ini:</strong></p>
            <img src="{{ asset($gedungFasilitas->foto_ruangan) }}"
                 alt="Foto Ruangan"
                 class="img-thumbnail"
                 style="max-height: 150px;">
        </div>
    @endif

    <div id="preview-ruangan" class="mt-2" style="display:none;">
        <p class="mb-1"><strong>Preview:</strong></p>
        <img id="img-preview-ruangan" src="#" alt="Preview" class="img-thumbnail" style="max-height: 150px;">
    </div>
</div>

@push('page_css')
<!-- Leaflet CSS untuk peta picker -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
@endpush

@push('page_scripts')
<!-- Leaflet JS untuk peta picker -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="{{ asset('js/admin-ruangan-fields.js') }}"></script>
@endpush
