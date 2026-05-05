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
            <div class="card-header bg-white border-bottom-0 pt-3 pb-0 px-3 d-flex flex-column flex-md-row justify-content-between align-items-center">
                <div class="d-flex align-items-center mb-2 mb-md-0 w-100">
                    <div id="custom-length-menu" class="mr-3"></div>
                </div>

                <div class="d-flex align-items-center justify-content-md-end w-100">
                    <div class="input-group input-group-sm shadow-sm" style="width: 220px;">
                        <input type="text" id="custom-search-input" class="form-control border-right-0" placeholder="Cari data...">
                        <div class="input-group-append">
                            <span class="input-group-text bg-white text-muted"><i class="fas fa-search"></i></span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Desktop: Table (≥ md) --}}
            <div class="card-body p-0 d-none d-md-block">
                <table class="table table-hover" id="riwayatPengajuan-table">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Ruangan</th>
                            <th>Kegiatan</th>
                            <th>Tanggal</th>
                            <th>Jam</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($pengajuanRuangans as $pengajuan)
                        <tr>
                            <td><strong>{{ $pengajuan->kode_pengajuan }}</strong></td>
                            <td>
                                <strong>{{ $pengajuan->ruangan->nama_fasilitas ?? '-' }}</strong>
                                <br><small class="text-muted">{{ $pengajuan->ruangan->gedung->nama_gedung ?? '-' }}</small>
                            </td>
                            <td>{{ $pengajuan->nama_kegiatan }}</td>
                            <td>{{ $pengajuan->tanggal_mulai->format('d/m/Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($pengajuan->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($pengajuan->jam_selesai)->format('H:i') }}</td>
                            <td>
                                @if($pengajuan->status === 'disetujui')
                                    <span class="badge badge-success">Disetujui</span>
                                @elseif($pengajuan->status === 'ditolak')
                                    <span class="badge badge-danger">Ditolak</span>
                                @elseif($pengajuan->status === 'dibatalkan')
                                    <span class="badge badge-secondary">Dibatalkan</span>
                                @else
                                    <span class="badge badge-warning text-white">Diproses</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('pengajuan_ruangans.show', $pengajuan->id) }}"
                                   class="btn btn-default btn-sm">
                                    <i class="far fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                Belum ada pengajuan. <a href="{{ route('pengajuan_ruangans.create') }}">Buat pengajuan baru</a>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile: Card List (< md) --}}
            <div class="d-block d-md-none mobile-card-list riwayat-mobile-list">
                @forelse($pengajuanRuangans as $pengajuan)
                    <div class="mobile-card"
                         data-search="{{ strtolower($pengajuan->kode_pengajuan.' '.$pengajuan->nama_kegiatan.' '.($pengajuan->ruangan->nama_fasilitas ?? '').' '.($pengajuan->ruangan->gedung->nama_gedung ?? '')) }}">
                        <div class="mobile-card-header">
                            <div class="mobile-card-title">
                                <strong>{{ $pengajuan->kode_pengajuan }}</strong>
                                <small class="text-muted d-block">
                                    <i class="fas fa-tag"></i> {{ Str::limit($pengajuan->nama_kegiatan, 40) }}
                                </small>
                            </div>
                            @if($pengajuan->status === 'disetujui')
                                <span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i>Disetujui</span>
                            @elseif($pengajuan->status === 'ditolak')
                                <span class="badge badge-danger"><i class="fas fa-times-circle mr-1"></i>Ditolak</span>
                            @elseif($pengajuan->status === 'dibatalkan')
                                <span class="badge badge-secondary"><i class="fas fa-ban mr-1"></i>Dibatalkan</span>
                            @else
                                <span class="badge badge-warning text-white"><i class="fas fa-clock mr-1"></i>Diproses</span>
                            @endif
                        </div>
                        <div class="mobile-card-body">
                            <div class="mobile-card-row">
                                <i class="fas fa-door-open text-primary"></i>
                                <span>
                                    <strong>{{ $pengajuan->ruangan->nama_fasilitas ?? '-' }}</strong>
                                    <small class="text-muted d-block">{{ $pengajuan->ruangan->gedung->nama_gedung ?? '-' }}</small>
                                </span>
                            </div>
                            <div class="mobile-card-row">
                                <i class="far fa-calendar text-muted"></i>
                                <span>
                                    {{ $pengajuan->tanggal_mulai->format('d/m/Y') }}
                                    <small class="text-muted d-block">
                                        <i class="far fa-clock"></i>
                                        {{ \Carbon\Carbon::parse($pengajuan->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($pengajuan->jam_selesai)->format('H:i') }}
                                    </small>
                                </span>
                            </div>
                        </div>
                        <div class="mobile-card-actions">
                            <a href="{{ route('pengajuan_ruangans.show', $pengajuan->id) }}"
                               class="btn btn-outline-secondary btn-sm flex-grow-1">
                                <i class="far fa-eye mr-1"></i> Lihat Detail
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="mobile-card-empty">
                        <i class="fas fa-file-alt fa-3x text-muted mb-3" style="opacity:0.4;"></i>
                        <h6 class="text-muted">Belum ada riwayat pengajuan</h6>
                        <p class="text-muted small mb-2">Buat pengajuan baru untuk memulai.</p>
                        <a href="{{ route('pengajuan_ruangans.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus mr-1"></i> Buat Pengajuan
                        </a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection

@push('page_css')
<style>
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
    .dataTables_filter {
        display: none;
    }
    .table-responsive {
        border-bottom-left-radius: 0.25rem;
        border-bottom-right-radius: 0.25rem;
    }
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

        function filterMobileCards() {
            var search = ($('#custom-search-input').val() || '').toLowerCase().trim();
            var $cards = $('.riwayat-mobile-list .mobile-card');
            var shown = 0;
            $cards.each(function() {
                var visible = !search || (($(this).data('search') || '').indexOf(search) !== -1);
                $(this).toggle(visible);
                if (visible) shown++;
            });
            var $empty = $('.riwayat-mobile-list .mobile-card-empty-filter');
            if (shown === 0 && $cards.length > 0) {
                if ($empty.length === 0) {
                    $('.riwayat-mobile-list').append(
                        '<div class="mobile-card-empty mobile-card-empty-filter">' +
                        '<i class="fas fa-search fa-2x text-muted mb-2" style="opacity:0.4;"></i>' +
                        '<h6 class="text-muted">Tidak ada hasil yang cocok</h6>' +
                        '</div>'
                    );
                }
            } else {
                $empty.remove();
            }
        }

        $('#custom-search-input').on('keyup', function() {
            table.search(this.value).draw();
            filterMobileCards();
        });
    });
</script>
@endpush
