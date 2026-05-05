<!-- Gedung Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('gedung_id', 'Gedung:') !!}
    {!! Form::select('gedung_id', $gedungs, null, ['class' => 'form-control custom-select', 'placeholder' => 'Pilih Gedung']) !!}
</div>

<!-- Nama Vegetasi Field -->
<div class="form-group col-sm-6">
    {!! Form::label('nama_vegetasi', 'Nama Vegetasi:') !!}
    {!! Form::text('nama_vegetasi', null, ['class' => 'form-control', 'maxlength' => 255]) !!}
</div>

<!-- Kategori Field -->
<div class="form-group col-sm-6">
    {!! Form::label('kategori', 'Kategori:') !!}
    {!! Form::select('kategori', [
        'Pohon' => 'Pohon',
        'Perdu' => 'Perdu',
        'Semak' => 'Semak',
        'Rumput' => 'Rumput',
        'Tanaman Hias' => 'Tanaman Hias',
        'Lainnya' => 'Lainnya'
    ], null, ['class' => 'form-control custom-select', 'placeholder' => 'Pilih Kategori']) !!}
</div>

<!-- Foto Utama Field -->
<div class="form-group col-sm-6">
    {!! Form::label('foto_utama', 'Foto Utama (Thumbnail):') !!}
    <div class="input-group">
        <div class="custom-file">
            {!! Form::file('foto_utama', ['class' => 'custom-file-input', 'id' => 'foto_utama', 'accept' => 'image/*']) !!}
            <label class="custom-file-label" for="foto_utama">Pilih file</label>
        </div>
    </div>
    <div id="preview-utama" class="mt-2" style="display: {{ isset($vegetasi) && $vegetasi->foto_utama ? 'block' : 'none' }};">
        <img id="img-preview-utama" src="{{ isset($vegetasi) && $vegetasi->foto_utama ? asset($vegetasi->foto_utama) : '#' }}" 
             alt="Preview" style="max-height: 150px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    </div>
</div>

<!-- Keterangan Field -->
<div class="form-group col-sm-12">
    {!! Form::label('keterangan', 'Keterangan:') !!}
    {!! Form::textarea('keterangan', null, ['class' => 'form-control', 'rows' => 3]) !!}
</div>

<!-- Foto Tambahan Field -->
<div class="form-group col-sm-12">
    {!! Form::label('foto_tambahan', 'Foto Tambahan (Galeri):') !!}
    <div class="input-group">
        <div class="custom-file">
            <input type="file" name="foto_tambahan[]" class="custom-file-input" id="foto_tambahan" multiple accept="image/*">
            <label class="custom-file-label" for="foto_tambahan">Pilih beberapa file</label>
        </div>
    </div>
    <small class="text-muted">Bisa memilih lebih dari satu foto sekaligus.</small>
    
    @if(isset($vegetasi) && $vegetasi->gambarVegetasis->count() > 0)
        <div class="row mt-3" id="existing-photos">
            @foreach($vegetasi->gambarVegetasis as $gambar)
                <div class="col-sm-3 mb-3" id="photo-{{ $gambar->id }}">
                    <div class="card h-100">
                        <img src="{{ asset($gambar->path_foto) }}" class="card-img-top" style="height: 120px; object-fit: cover;">
                        <div class="card-footer p-1 text-center">
                            <button type="button" class="btn btn-danger btn-xs delete-photo" data-id="{{ $gambar->id }}">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<div class="col-sm-12">
    <hr>
    <h5><i class="fas fa-map-marker-alt text-danger"></i> Lokasi Vegetasi</h5>
    <p class="text-muted small">Klik pada peta untuk menentukan lokasi vegetasi atau masukkan koordinat secara manual.</p>
</div>

<!-- Latitude Field -->
<div class="form-group col-sm-6">
    {!! Form::label('latitude', 'Latitude:') !!}
    {!! Form::text('latitude', null, ['class' => 'form-control', 'id' => 'input_lat_vegetasi']) !!}
</div>

<!-- Longitude Field -->
<div class="form-group col-sm-6">
    {!! Form::label('longitude', 'Longitude:') !!}
    {!! Form::text('longitude', null, ['class' => 'form-control', 'id' => 'input_lng_vegetasi']) !!}
</div>

<!-- Map Picker -->
<div class="form-group col-sm-12">
    <div id="map-picker-vegetasi" style="height: 400px; border-radius: 8px; border: 1px solid #ddd;"></div>
</div>

@push('page_scripts')
    <!-- Leaflet Assets -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="{{ asset('js/admin-vegetasi-fields.js') }}"></script>
    
    <script>
        $(document).ready(function() {
            // Handle delete photo via AJAX
            $('.delete-photo').click(function() {
                const id = $(this).data('id');
                if(confirm('Apakah Anda yakin ingin menghapus foto ini?')) {
                    $.ajax({
                        url: '/vegetasis/foto/' + id,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if(response.success) {
                                $('#photo-' + id).remove();
                            }
                        }
                    });
                }
            });

            // Update label custom-file
            $('.custom-file-input').on('change', function() {
                let fileName = $(this).val().split('\\').pop();
                if($(this).attr('multiple')) {
                    const count = $(this)[0].files.length;
                    fileName = count + " file dipilih";
                }
                $(this).next('.custom-file-label').addClass("selected").html(fileName);
            });
        });
    </script>
@endpush
