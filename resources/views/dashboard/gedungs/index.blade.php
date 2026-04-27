@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Data Gedung</h1>
                </div>
                <div class="col-sm-6">
                    <a class="btn btn-primary float-right"
                       href="{{ route('gedungs.create') }}">
                        <i class="fas fa-plus mr-1"></i> Tambah Gedung
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
                @include('dashboard.gedungs.table')
            </div>
        </div>
    </div>

@endsection

@push('page_scripts')
<script>
    $(function () {
        $('#gedungs-table').DataTable({
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                infoEmpty: "Tidak ada data",
                infoFiltered: "(disaring dari _MAX_ total data)",
                zeroRecords: "Data tidak ditemukan",
                emptyTable: "Belum ada data gedung",
                paginate: { first: "Awal", last: "Akhir", next: "›", previous: "‹" }
            },
            pageLength: 10,
            order: [[0, 'asc']],
        });
    });
</script>
@endpush
