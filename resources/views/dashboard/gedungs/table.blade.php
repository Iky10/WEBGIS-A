    <table class="table" id="gedungs-table">
        <thead>
        <tr>
            <th>Nama Gedung</th>
            <th>Alamat</th>
            <th>Deskripsi</th>
            <th>Status Pengajuan</th>
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
                <td>
                    @if($gedung->bisa_diajukan)
                        <span class="badge badge-success"><i class="fas fa-check mr-1"></i>Bisa Diajukan</span>
                    @else
                        <span class="badge badge-secondary"><i class="fas fa-ban mr-1"></i>Tidak Bisa</span>
                    @endif
                </td>
                <td>{{ $gedung->x }}</td>
                <td>{{ $gedung->y }}</td>
                <td width="120">
                    {!! Form::open(['route' => ['gedungs.destroy', $gedung->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('gedungs.show', [$gedung->id]) }}"
                           class='btn btn-default btn-sm'>
                            <i class="far fa-eye"></i>
                        </a>
                        <a href="{{ route('gedungs.edit', [$gedung->id]) }}"
                           class='btn btn-default btn-sm'>
                            <i class="far fa-edit"></i>
                        </a>
                        {!! Form::button('<i class="far fa-trash-alt"></i>', ['type' => 'button', 'class' => 'btn btn-danger btn-sm', 'onclick' => 'confirmDelete(this.closest("form"), "Yakin ingin menghapus gedung ini?")']) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
