@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-file-image"></i> Jadwal Semester Ruang Kelas</h1>
                </div>
                <div class="col-sm-6">
                    <a class="btn btn-primary float-right"
                       href="{{ route('jadwal_semester.create') }}">
                        <i class="fas fa-plus"></i> Tambah Jadwal Semester
                    </a>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">

        @include('flash::message')

        <div class="clearfix"></div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table" id="jadwalSemester-table">
                        <thead>
                        <tr>
                            <th>Gedung</th>
                            <th>Semester</th>
                            <th>Tahun Ajaran</th>
                            <th>File Jadwal</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($jadwalSemesters as $jadwal)
                            <tr>
                                <td>{{ $jadwal->gedung->nama_gedung ?? '-' }}</td>
                                <td>Semester {{ $jadwal->semester }}</td>
                                <td>{{ $jadwal->tahun_ajaran }}</td>
                                <td>
                                    @if($jadwal->file_jadwal)
                                        <a href="{{ asset($jadwal->file_jadwal) }}" target="_blank" class="btn btn-sm btn-info">
                                            <i class="fas fa-file-download"></i> Lihat File
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $jadwal->keterangan }}</td>
                                <td width="120">
                                    {!! Form::open(['route' => ['jadwal_semester.destroy', $jadwal->id], 'method' => 'delete']) !!}
                                    <div class='btn-group'>
                                        <a href="{{ route('jadwal_semester.edit', [$jadwal->id]) }}"
                                           class='btn btn-default btn-xs'>
                                            <i class="far fa-edit"></i>
                                        </a>
                                        {!! Form::button('<i class="far fa-trash-alt"></i>', ['type' => 'button', 'class' => 'btn btn-danger btn-xs', 'onclick' => 'confirmDelete(this.closest("form"), "Yakin ingin menghapus jadwal semester ini?")']) !!}
                                    </div>
                                    {!! Form::close() !!}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page_scripts')
<script>
    $(function () {
        $('#jadwalSemester-table').DataTable({
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                infoEmpty: "Tidak ada data",
                infoFiltered: "(disaring dari _MAX_ total data)",
                zeroRecords: "Data tidak ditemukan",
                emptyTable: "Belum ada data jadwal semester",
                paginate: { first: "Awal", last: "Akhir", next: "›", previous: "‹" }
            },
            pageLength: 10,
            order: [[0, 'asc']],
        });
    });
</script>
@endpush
