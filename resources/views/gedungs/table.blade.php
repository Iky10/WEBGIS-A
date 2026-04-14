<div class="table-responsive">
    <table class="table" id="gedungs-table">
        <thead>
        <tr>
            <th>Nama Gedung</th>
        <th>Alamat</th>
        <th>Deskripsi</th>
        <th>Fungsi</th>
        <th>Jumlah Lantai</th>
        <th>Tahun Berdiri</th>
        <th>Kondisi</th>
        <th>X</th>
        <th>Y</th>
            <th colspan="3">Action</th>
        </tr>
        </thead>
        <tbody>
        @foreach($gedungs as $gedung)
            <tr>
                <td>{{ $gedung->nama_gedung }}</td>
            <td>{{ $gedung->alamat }}</td>
            <td>{{ $gedung->deskripsi }}</td>
            <td>{{ $gedung->fungsi }}</td>
            <td>{{ $gedung->jumlah_lantai }}</td>
            <td>{{ $gedung->tahun_berdiri }}</td>
            <td>{{ $gedung->kondisi }}</td>
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
                        {!! Form::button('<i class="far fa-trash-alt"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
