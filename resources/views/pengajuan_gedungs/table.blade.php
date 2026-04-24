<div class="table-responsive">
    <table class="table" id="pengajuanGedungs-table">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Pemohon</th>
                <th>Gedung</th>
                <th>Kegiatan</th>
                <th>Tanggal</th>
                <th>Status</th>
                <th colspan="3">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pengajuanGedungs as $pengajuan)
                <tr>
                    <td><code>{{ $pengajuan->kode_pengajuan }}</code></td>
                    <td>{{ $pengajuan->nama_pemohon }}</td>
                    <td>{{ optional($pengajuan->gedung)->nama_gedung ?? 'N/A' }}</td>
                    <td>{{ $pengajuan->nama_kegiatan }} <br><small
                            class="text-muted">{{ $pengajuan->jenis_kegiatan }}</small></td>
                    <td>{{ $pengajuan->tanggal_mulai->format('d/m/Y') }}</td>
                    <td>
                        @if($pengajuan->status == 'diproses')
                            <span class="badge badge-warning">Diproses</span>
                        @elseif($pengajuan->status == 'disetujui')
                            <span class="badge badge-success">Disetujui</span>
                        @elseif($pengajuan->status == 'ditolak')
                            <span class="badge badge-danger">Ditolak</span>
                        @endif
                    </td>
                    <td width="120">
                        {!! Form::open(['route' => ['pengajuan_gedungs.destroy', $pengajuan->id], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                            <a href="{{ route('pengajuan_gedungs.show', [$pengajuan->id]) }}"
                                class='btn btn-default btn-xs'>
                                <i class="far fa-eye"></i>
                            </a>
                            <a href="{{ route('pengajuan_gedungs.edit', [$pengajuan->id]) }}"
                                class='btn btn-default btn-xs'>
                                <i class="far fa-edit"></i>
                            </a>
                            {!! Form::button('<i class="far fa-trash-alt"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Apakah Anda yakin ingin menghapus pengajuan ini?')"]) !!}
                        </div>
                        {!! Form::close() !!}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>