<div class="table-responsive">
    <table class="table" id="gedungs-table">
        <thead>
        <tr>
            <th>Nama Gedung</th>
            <th>Alamat</th>
            <th>Deskripsi</th>
            <th>X</th>
            <th>Y</th>
            <th>Aksi</th>
        </tr>
        </thead>
        <tbody>
        @foreach($gedungs as $gedung)
            <tr>
                <td>{{ $gedung->nama_gedung }}</td>
                <td>{{ $gedung->alamat }}</td>
                <td>{{ $gedung->deskripsi }}</td>
                <td>{{ $gedung->x }}</td>
                <td>{{ $gedung->y }}</td>
                <td width="120">
                    {!! Form::open(['route' => ['gedungs.destroy', $gedung->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('gedungs.show', [$gedung->id]) }}"
                           class='btn btn-default btn-xs'>
                            <i class="far fa-eye"></i>
                        </a>
                        <a href="{{ route('gedungs.edit', [$gedung->id]) }}"
                           class='btn btn-default btn-xs'>
                            <i class="far fa-edit"></i>
                        </a>
                        {!! Form::button('<i class="far fa-trash-alt"></i>', ['type' => 'button', 'class' => 'btn btn-danger btn-xs', 'onclick' => 'confirmDelete(this.closest("form"), "Yakin ingin menghapus gedung ini?")']) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
