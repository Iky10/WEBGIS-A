@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Jadwal Semester Ruang Kelas</h1>
                </div>
                <div class="col-sm-6">
                    <a class="btn btn-primary float-right"
                       href="{{ route('jadwal_semester.create') }}">
                        Tambah Baru
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
                    <table class="table" id="jadwal-semester-table">
                        <thead>
                        <tr>
                            <th>Gedung</th>
                            <th>Semester</th>
                            <th>Tahun Ajaran</th>
                            <th>File Jadwal</th>
                            <th>Keterangan</th>
                            <th colspan="3">Aksi</th>
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
                                        <a href="{{ asset($jadwal->file_jadwal) }}" target="_blank" class="btn btn-sm btn-info">Lihat File</a>
                                    @else
                                        -
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
                                        {!! Form::button('<i class="far fa-trash-alt"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Yakin ingin menghapus?')"]) !!}
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
