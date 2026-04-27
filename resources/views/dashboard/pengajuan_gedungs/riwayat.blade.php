@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Riwayat Pengajuan Saya</h1>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">
        @include('flash::message')
        <div class="clearfix"></div>

        <div class="card shadow-sm border-0 mb-4">
            {{-- TOOLBAR ATAS (Static) --}}
            <div class="card-header bg-white border-bottom-0 pt-3 pb-0 px-3 d-flex flex-column flex-md-row justify-content-between align-items-center">
                <div class="d-flex align-items-center mb-2 mb-md-0 w-100">
                    <div id="custom-length-menu" class="mr-3"></div>
                </div>

                <div class="d-flex align-items-center justify-content-md-end w-100">
                    {{-- Custom Search --}}
                    <div class="input-group input-group-sm shadow-sm" style="width: 220px;">
                        <input type="text" id="custom-search-input" class="form-control border-right-0" placeholder="Cari data...">
                        <div class="input-group-append">
                            <span class="input-group-text bg-white text-muted"><i class="fas fa-search"></i></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                    <table class="table table-hover" id="riwayatPengajuan-table">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Gedung</th>
                                <th>Kegiatan</th>
                                <th>Tanggal</th>
                                <th>Jam</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($pengajuanGedungs as $pengajuan)
                            <tr>
                                <td><strong>{{ $pengajuan->kode_pengajuan }}</strong></td>
                                <td>{{ $pengajuan->gedung->nama_gedung ?? '-' }}</td>
                                <td>{{ $pengajuan->nama_kegiatan }}</td>
                                <td>{{ $pengajuan->tanggal_mulai->format('d/m/Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($pengajuan->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($pengajuan->jam_selesai)->format('H:i') }}</td>
                                <td>
                                    @if($pengajuan->status === 'disetujui')
                                        <span class="badge badge-success">Disetujui</span>
                                    @elseif($pengajuan->status === 'ditolak')
                                        <span class="badge badge-danger">Ditolak</span>
                                    @else
                                        <span class="badge badge-warning text-white">Diproses</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('pengajuan_gedungs.show', $pengajuan->id) }}"
                                       class="btn btn-default btn-sm">
                                        <i class="far fa-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    Belum ada pengajuan. <a href="{{ route('pengajuan_gedungs.create') }}">Buat pengajuan baru</a>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
            </div>
        </div>
    </div>
@endsection

@push('page_css')
<style>
    /* Fix DataTables Length Menu Spacing */
    div.dataTables_length label {
        display: flex;
        align-items: center;
        margin-bottom: 0;
        font-weight: normal;
    }
    div.dataTables_length select {
        margin: 0 0.5rem;
        width: auto;
    }
    /* Hide default search since we use custom */
    .dataTables_filter {
        display: none;
    }
    /* Ensure table-responsive doesn't break border radius */
    .table-responsive {
        border-bottom-left-radius: 0.25rem;
        border-bottom-right-radius: 0.25rem;
    }
    /* Table wrapper padding fix */
    #riwayatPengajuan-table_wrapper {
        padding-top: 0 !important;
    }
</style>
@endpush

@push('page_scripts')
<script>
    $(function () {
        var table = $('#riwayatPengajuan-table').DataTable({
            dom: "<'d-none'l>" +
                 "<'row'<'col-sm-12'<'table-responsive'tr>>>" +
                 "<'row px-3 pb-3 pt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                infoEmpty: "Tidak ada data",
                infoFiltered: "(disaring dari _MAX_ total data)",
                zeroRecords: "Data tidak ditemukan",
                emptyTable: '<div class="text-center py-5">' +
                            '<i class="fas fa-file-alt fa-3x text-muted mb-3" style="opacity:0.4;"></i>' +
                            '<h6 class="text-muted">Belum ada riwayat pengajuan</h6>' +
                            '<p class="text-muted small mb-0">Buat pengajuan baru untuk memulai.</p>' +
                            '</div>',
                paginate: { first: "Awal", last: "Akhir", next: "›", previous: "‹" }
            },
            pageLength: 10,
            order: [[0, 'desc']],
            columnDefs: [
                { orderable: false, targets: [6] }
            ],
            initComplete: function() {
                var $lengthMenu = $('.dataTables_length');
                $lengthMenu.appendTo('#custom-length-menu');
            }
        });

        // ─── Custom Search Bind ───
        $('#custom-search-input').on('keyup', function() {
            table.search(this.value).draw();
        });
    });
</script>
@endpush
