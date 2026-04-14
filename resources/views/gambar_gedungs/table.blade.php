<div class="table-responsive">
    <table class="table" id="gambarGedungs-table">
        <thead>
        <tr>
            <th>Gedung Id</th>
        <th>Nama File</th>
        <th>Path Foto</th>
        <th>Keterangan</th>
        <th>Urutan</th>
            <th colspan="3">Action</th>
        </tr>
        </thead>
        <tbody>
        @foreach($gambarGedungs as $gambarGedung)
            <tr>
                <td>{{ $gambarGedung->gedung_id }}</td>
            <td>{{ $gambarGedung->nama_file }}</td>
            <td>{{ $gambarGedung->path_foto }}</td>
            <td>{{ $gambarGedung->keterangan }}</td>
            <td>{{ $gambarGedung->urutan }}</td>
                <td width="120">
                    {!! Form::open(['route' => ['gambarGedungs.destroy', $gambarGedung->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('gambarGedungs.show', [$gambarGedung->id]) }}"
                           class='btn btn-default btn-xs'>
                            <i class="far fa-eye"></i>
                        </a>
                        <a href="{{ route('gambarGedungs.edit', [$gambarGedung->id]) }}"
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
