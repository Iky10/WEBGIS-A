<div class="table-responsive">
    <table class="table" id="vegetasis-table">
        <thead>
        <tr>
            <th width="80px">Foto</th>
            <th>Nama Vegetasi</th>
            <th>Gedung</th>
            <th>Kategori</th>
            <th>Koordinat</th>
            <th colspan="3">Aksi</th>
        </tr>
        </thead>
        <tbody>
        @foreach($vegetasis as $vegetasi)
            <tr>
                <td>
                    @if($vegetasi->foto_utama)
                        <img src="{{ asset($vegetasi->foto_utama) }}" width="60" height="60" style="object-fit: cover; border-radius: 4px;">
                    @else
                        <div style="width: 60px; height: 60px; background: #eee; display: flex; align-items: center; justify-content: center; border-radius: 4px;">
                            <i class="fas fa-leaf text-success"></i>
                        </div>
                    @endif
                </td>
                <td>{{ $vegetasi->nama_vegetasi }}</td>
                <td>{{ optional($vegetasi->gedung)->nama_gedung }}</td>
                <td><span class="badge badge-success">{{ $vegetasi->kategori }}</span></td>
                <td>
                    <small>Lat: {{ $vegetasi->latitude }}</small><br>
                    <small>Lng: {{ $vegetasi->longitude }}</small>
                </td>
                <td width="120">
                    {!! Form::open(['route' => ['vegetasis.destroy', $vegetasi->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('vegetasis.show', [$vegetasi->id]) }}"
                           class='btn btn-default btn-sm'>
                            <i class="far fa-eye"></i>
                        </a>
                        <a href="{{ route('vegetasis.edit', [$vegetasi->id]) }}"
                           class='btn btn-default btn-sm'>
                            <i class="far fa-edit"></i>
                        </a>
                        {!! Form::button('<i class="far fa-trash-alt"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-sm', 'onclick' => "return confirm('Apakah Anda yakin?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
