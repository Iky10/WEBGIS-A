<!-- Nama Gedung -->
<div class="col-sm-6">
    {!! Form::label('nama_gedung', 'Nama Gedung:') !!}
    <p>{{ $gedung->nama_gedung }}</p>
</div>



<!-- Alamat -->
<div class="col-sm-12">
    {!! Form::label('alamat', 'Alamat:') !!}
    <p>{{ $gedung->alamat }}</p>
</div>

<!-- Deskripsi -->
<div class="col-sm-12">
    {!! Form::label('deskripsi', 'Deskripsi:') !!}
    <p>{{ $gedung->deskripsi ?? '-' }}</p>
</div>



<!-- Kondisi -->
<div class="col-sm-12">
    {!! Form::label('kondisi', 'Status Pemakaian:') !!}
    <p>
        @if($gedung->status_dipakai == 'Sedang Dipakai')
            <span class="badge badge-primary">Sedang Dipakai</span>
        @elseif($gedung->status_dipakai == 'Tutup')
            <span class="badge badge-secondary">Tutup</span>
        @else
            <span class="badge badge-success">Terbuka</span>
        @endif
    </p>
</div>

<!-- Jam Operasional -->
<div class="col-sm-12">
    {!! Form::label('jam_operasional', 'Jam Operasional:') !!}
    <p>
        @if($gedung->jam_buka && $gedung->jam_tutup)
            <i class="fas fa-clock text-info"></i>
            {{ $gedung->jam_buka_formatted }} - {{ $gedung->jam_tutup_formatted }} WIB
        @else
            <span class="text-muted">Buka 24 Jam</span>
        @endif
    </p>
</div>

<!-- Koordinat -->
<div class="col-sm-6">
    {!! Form::label('x', 'Latitude:') !!}
    <p>{{ $gedung->x }}</p>
</div>

<div class="col-sm-6">
    {!! Form::label('y', 'Longitude:') !!}
    <p>{{ $gedung->y }}</p>
</div>

<!-- Foto Utama -->
<div class="col-sm-12 mt-2">
    {!! Form::label('foto_utama', 'Foto Utama:') !!}
    @if($gedung->foto_utama)
        <div>
            <img src="{{ asset($gedung->foto_utama) }}"
                 alt="Foto Utama"
                 class="img-thumbnail"
                 style="max-height: 200px;">
        </div>
    @else
        <p class="text-muted">Belum ada foto utama.</p>
    @endif
</div>

<!-- Foto Galeri -->
<div class="col-sm-12 mt-3">
    <label><strong>Foto Galeri:</strong></label>
    @if(isset($fotos) && $fotos->count() > 0)
        <div class="row">
            @foreach($fotos as $foto)
                <div class="col-sm-3 mb-3">
                    <div class="card">
                        <img src="{{ asset($foto->path_foto) }}"
                             class="card-img-top"
                             alt="{{ $foto->nama_file }}"
                             style="height: 150px; object-fit: cover;">
                        <div class="card-body p-2 text-center">
                            <small class="text-muted">{{ $foto->keterangan ?: $foto->nama_file }}</small>
                            <form action="{{ route('gedungs.foto.destroy', $foto->id) }}" method="POST" class="mt-1">
                                @csrf
                                @method('DELETE')
                                <button type="button"
                                        class="btn btn-danger btn-xs"
                                        onclick="confirmDelete(this.closest('form'), 'Hapus foto ini?')">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-muted">Belum ada foto galeri.</p>
    @endif
</div>

<!-- Timestamps -->
<div class="col-sm-6 mt-2">
    {!! Form::label('created_at', 'Dibuat:') !!}
    <p>{{ $gedung->created_at->format('d M Y H:i') }}</p>
</div>

<div class="col-sm-6 mt-2">
    {!! Form::label('updated_at', 'Diperbarui:') !!}
    <p>{{ $gedung->updated_at->format('d M Y H:i') }}</p>
</div>