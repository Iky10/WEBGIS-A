<div class="table-responsive">
    <table class="table" id="jadwalRuangans-table">
        <thead>
        <tr>
            <th>Gedung & Ruangan</th>
            <th>Kegiatan</th>
            <th>Hari</th>
            <th>Mulai</th>
            <th>Selesai</th>
            <th>Aksi</th>
        </tr>
        </thead>
        <tbody>
        @foreach($jadwalRuangans as $jadwal)
            <tr>
                <td>{{ optional(optional($jadwal->fasilitas)->gedung)->nama_gedung ?? 'N/A' }} - {{ optional($jadwal->fasilitas)->nama_fasilitas ?? 'N/A' }}</td>
                <td>{{ $jadwal->nama_kegiatan }}</td>
                <td><span class="badge badge-info">{{ $jadwal->hari }}</span></td>
                <td>{{ $jadwal->jam_mulai }}</td>
                <td>{{ $jadwal->jam_selesai }}</td>
                <td width="120">
                    {!! Form::open(['route' => ['jadwal_ruangans.destroy', $jadwal->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('jadwal_ruangans.edit', [$jadwal->id]) }}"
                           class='btn btn-default btn-xs'>
                            <i class="far fa-edit"></i>
                        </a>
                        {!! Form::button('<i class="far fa-trash-alt"></i>', ['type' => 'button', 'class' => 'btn btn-danger btn-xs', 'onclick' => 'confirmDelete(this.closest("form"), "Yakin ingin menghapus jadwal ruangan ini?")']) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
