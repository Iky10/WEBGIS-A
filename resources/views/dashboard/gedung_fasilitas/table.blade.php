<div class="table-responsive">
    <table class="table" id="gedungFasilitas-table">
        <thead>
        <tr>
            <th>Foto</th>
            <th>Gedung</th>
            <th>Nama Ruangan / Fasilitas</th>
            <th>Kategori</th>
            <th>Koordinat</th>
            <th>Status</th>
            <th colspan="3">Action</th>
        </tr>
        </thead>
        <tbody>
        @foreach($gedungFasilitas as $gf)
            <tr>
                <td width="80">
                    @if($gf->foto_ruangan)
                        <img src="{{ asset($gf->foto_ruangan) }}"
                             alt="Foto"
                             class="img-thumbnail"
                             style="width: 60px; height: 60px; object-fit: cover;">
                    @else
                        <span class="text-muted"><i class="fas fa-image"></i></span>
                    @endif
                </td>
                <td>{{ optional($gf->gedung)->nama_gedung ?? 'N/A' }}</td>
                <td>{{ $gf->nama_fasilitas }}</td>
                <td><span class="badge badge-info">{{ $gf->kategori }}</span></td>
                <td>
                    @if($gf->latitude && $gf->longitude)
                        <small>{{ $gf->latitude }}, {{ $gf->longitude }}</small>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td>
                    @if($gf->is_aktif)
                        <span class="badge badge-success"><i class="fas fa-check-circle"></i> Aktif</span>
                    @else
                        <span class="badge badge-danger"><i class="fas fa-times-circle"></i> Tidak Aktif</span>
                    @endif
                </td>
                <td width="120">
                    {!! Form::open(['route' => ['gedung_fasilitas.destroy', $gf->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('gedung_fasilitas.edit', [$gf->id]) }}"
                           class='btn btn-default btn-xs'>
                            <i class="far fa-edit"></i>
                        </a>
                        {!! Form::button('<i class="far fa-trash-alt"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
