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

            {{-- Desktop: Table (≥ md) --}}
            <div class="card-body p-0 d-none d-md-block">
                @include('dashboard.gedungs.table')
            </div>

            {{-- Mobile: Card List (< md) --}}
            <div class="d-block d-md-none mobile-card-list gedungs-mobile-list">
                @forelse($gedungs as $gedung)
                    <div class="mobile-card"
                         data-search="{{ strtolower($gedung->nama_gedung.' '.$gedung->alamat.' '.$gedung->deskripsi) }}">
                        <div class="mobile-card-header">
                            <div class="mobile-card-title">
                                <strong>{{ $gedung->nama_gedung }}</strong>
                                @if($gedung->alamat)
                                    <small class="text-muted d-block">
                                        <i class="fas fa-map-marker-alt"></i> {{ Str::limit($gedung->alamat, 50) }}
                                    </small>
                                @endif
                            </div>
                            @if($gedung->bisa_diajukan)
                                <span class="badge badge-success"><i class="fas fa-check mr-1"></i>Bisa Diajukan</span>
                            @else
                                <span class="badge badge-secondary"><i class="fas fa-ban mr-1"></i>Tidak Bisa</span>
                            @endif
                        </div>
                        <div class="mobile-card-body">
                            @if($gedung->deskripsi)
                                <div class="mobile-card-row">
                                    <i class="fas fa-info-circle text-muted"></i>
                                    <span>{{ Str::limit($gedung->deskripsi, 120) }}</span>
                                </div>
                            @endif
                            <div class="mobile-card-row">
                                <i class="fas fa-crosshairs text-muted"></i>
                                <span class="text-monospace small">{{ $gedung->x }}, {{ $gedung->y }}</span>
                            </div>
                        </div>
                        <div class="mobile-card-actions">
                            <a href="{{ route('gedungs.show', [$gedung->id]) }}"
                               class="btn btn-outline-secondary btn-sm flex-grow-1">
                                <i class="far fa-eye mr-1"></i> Detail
                            </a>
                            <a href="{{ route('gedungs.edit', [$gedung->id]) }}"
                               class="btn btn-outline-primary btn-sm flex-grow-1">
                                <i class="far fa-edit mr-1"></i> Edit
                            </a>
                            {!! Form::open(['route' => ['gedungs.destroy', $gedung->id], 'method' => 'delete', 'class' => 'd-flex flex-grow-1 mb-0']) !!}
                                {!! Form::button('<i class="far fa-trash-alt mr-1"></i>', ['type' => 'button', 'class' => 'btn btn-outline-danger btn-sm flex-grow-1', 'onclick' => 'confirmDelete(this.closest(\'form\'), \'Yakin ingin menghapus gedung ini?\')']) !!}
                            {!! Form::close() !!}
                        </div>
                    </div>
                @empty
                    <div class="mobile-card-empty">
                        <i class="fas fa-building fa-3x text-muted mb-3" style="opacity:0.4;"></i>
                        <h6 class="text-muted">Belum ada data gedung</h6>
                        <p class="text-muted small mb-2">Tambahkan gedung baru untuk memulai.</p>
                        <a href="{{ route('gedungs.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus mr-1"></i> Tambah Gedung
                        </a>
                    </div>
                @endforelse
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
    #gedungs-table_wrapper {
        padding-top: 0 !important;
    }
</style>
@endpush

@push('page_scripts')
<script>
    $(function () {
        var table = $('#gedungs-table').DataTable({
            // Hanya menggunakan 'tr' (table), 'i' (info), dan 'p' (pagination) di DOM standar.
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
                            '<i class="fas fa-building fa-3x text-muted mb-3" style="opacity:0.4;"></i>' +
                            '<h6 class="text-muted">Belum ada data gedung</h6>' +
                            '<p class="text-muted small mb-0">Tambahkan gedung baru untuk memulai.</p>' +
                            '</div>',
                paginate: { first: "Awal", last: "Akhir", next: "›", previous: "‹" }
            },
            pageLength: 10,
            order: [[0, 'asc']],
            columnDefs: [
                { orderable: false, targets: [6] }
            ],
            initComplete: function() {
                var $lengthMenu = $('.dataTables_length');
                $lengthMenu.appendTo('#custom-length-menu');
            }
        });

        // ─── Mobile Card Filter ───
        function filterMobileCards() {
            var search = ($('#custom-search-input').val() || '').toLowerCase().trim();
            var $cards = $('.gedungs-mobile-list .mobile-card');
            var shown = 0;
            $cards.each(function() {
                var visible = !search || (($(this).data('search') || '').indexOf(search) !== -1);
                $(this).toggle(visible);
                if (visible) shown++;
            });
            var $empty = $('.gedungs-mobile-list .mobile-card-empty-filter');
            if (shown === 0 && $cards.length > 0) {
                if ($empty.length === 0) {
                    $('.gedungs-mobile-list').append(
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

        // ─── Custom Search Bind (desktop + mobile) ───
        $('#custom-search-input').on('keyup', function() {
            table.search(this.value).draw();
            filterMobileCards();
        });
    });
</script>
@endpush
