<!-- Nama Gedung Field -->
<div class="form-group col-sm-6">
    {!! Form::label('nama_gedung', 'Nama Gedung:') !!}
    {!! Form::text('nama_gedung', null, ['class' => 'form-control', 'placeholder' => 'Masukkan nama gedung']) !!}
</div>

<!-- Fungsi Field -->
<div class="form-group col-sm-6">
    {!! Form::label('fungsi', 'Fungsi Gedung:') !!}
    {!! Form::select('fungsi', [
        ''            => '-- Pilih Fungsi --',
        'Perkantoran' => 'Perkantoran',
        'Pendidikan'  => 'Pendidikan',
        'Kesehatan'   => 'Kesehatan',
        'Komersial'   => 'Komersial',
        'Publik'      => 'Publik',
        'Lainnya'     => 'Lainnya',
    ], null, ['class' => 'form-control select2']) !!}
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

<!-- Jumlah Lantai Field -->
<div class="form-group col-sm-4">
    {!! Form::label('jumlah_lantai', 'Jumlah Lantai:') !!}
    {!! Form::number('jumlah_lantai', null, ['class' => 'form-control', 'min' => 1]) !!}
</div>

<!-- Tahun Berdiri Field -->
<div class="form-group col-sm-4">
    {!! Form::label('tahun_berdiri', 'Tahun Berdiri:') !!}
    {!! Form::number('tahun_berdiri', null, ['class' => 'form-control', 'min' => 1900, 'max' => 2099]) !!}
</div>

<!-- Kondisi Field -->
<div class="form-group col-sm-4">
    {!! Form::label('kondisi', 'Kondisi:') !!}
    {!! Form::select('kondisi', [
        ''       => '-- Pilih Kondisi --',
        'Baik'   => 'Baik',
        'Sedang' => 'Sedang',
        'Rusak'  => 'Rusak',
    ], null, ['class' => 'form-control']) !!}
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
            <img src="{{ asset('storage/' . $gedung->foto_utama) }}"
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

<script>
    // ── Peta Koordinat Picker ──────────────────────────────
    var defaultLat = parseFloat(document.getElementById('input_lat').value) || -2.5;
    var defaultLng = parseFloat(document.getElementById('input_lng').value) || 118.0;

    var map = L.map('map-picker').setView([defaultLat, defaultLng], 5);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    var marker = null;

    // Kalau sudah ada koordinat (mode edit), tampilkan marker
    if (document.getElementById('input_lat').value && document.getElementById('input_lng').value) {
        marker = L.marker([defaultLat, defaultLng]).addTo(map);
        map.setView([defaultLat, defaultLng], 15);
    }

    map.on('click', function(e) {
        var lat = e.latlng.lat.toFixed(8);
        var lng = e.latlng.lng.toFixed(8);

        document.getElementById('input_lat').value = lat;
        document.getElementById('input_lng').value = lng;

        if (marker) {
            marker.setLatLng(e.latlng);
        } else {
            marker = L.marker(e.latlng).addTo(map);
        }
    });

    // ── Preview Foto Utama ─────────────────────────────────
    document.getElementById('foto_utama').addEventListener('change', function(e) {
        var file = e.target.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(ev) {
                document.getElementById('img-preview-utama').src = ev.target.result;
                document.getElementById('preview-utama').style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });

    // ── Preview Foto Galeri ────────────────────────────────
    document.getElementById('foto_gedung').addEventListener('change', function(e) {
        var container = document.getElementById('preview-galeri');
        container.innerHTML = '';
        Array.from(e.target.files).forEach(function(file) {
            var reader = new FileReader();
            reader.onload = function(ev) {
                var img = document.createElement('img');
                img.src = ev.target.result;
                img.className = 'img-thumbnail';
                img.style.maxHeight = '100px';
                img.style.marginRight = '8px';
                img.style.marginBottom = '8px';
                container.appendChild(img);
            };
            reader.readAsDataURL(file);
        });
    });
</script>
@endpush