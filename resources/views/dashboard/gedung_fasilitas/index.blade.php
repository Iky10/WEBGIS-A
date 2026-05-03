@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Master Ruangan / Fasilitas</h1>
                </div>
                <div class="col-sm-6">
                    <a class="btn btn-primary float-right"
                       href="{{ route('gedung_fasilitas.create') }}">
                        <i class="fas fa-plus mr-1"></i> Tambah Baru
                    </a>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">
        @include('flash::message')
        <div class="clearfix"></div>

        <div class="card shadow-sm border-0 mb-4">
            {{-- TOOLBAR ATAS (Static, tidak di-prepend via JS agar tidak berkedip/overflow) --}}
            <div class="card-header bg-white border-bottom-0 pt-3 pb-0 px-3 d-flex flex-column flex-md-row justify-content-between align-items-center">
                <div class="d-flex align-items-center mb-2 mb-md-0 w-100">
                    <div id="custom-length-menu" class="mr-3"></div>
                    
                    {{-- Bulk Delete --}}
                    <button class="btn btn-danger btn-sm d-none mr-2 shadow-sm" id="btn-bulk-delete">
                        <i class="fas fa-trash-alt mr-1"></i>Hapus (<span id="selected-count">0</span>)
                    </button>
                </div>
                
                <div class="d-flex align-items-center justify-content-md-end w-100">
                    {{-- Filter Dropdown --}}
                    <div class="dropdown mr-2" id="filter-dropdown-container">
                        <button class="btn btn-default btn-sm dropdown-toggle shadow-sm" type="button" id="dropdownMenuFilterGedung" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="height: 31px;">
                            <i class="fas fa-filter mr-1 text-muted"></i> Filter
                        </button>
                        <div class="dropdown-menu dropdown-menu-right p-3 shadow dropdown-animated" aria-labelledby="dropdownMenuFilterGedung" style="width: 280px; border-radius: 8px;">
                            <h6 class="dropdown-header px-0 text-dark font-weight-bold"><i class="fas fa-sliders-h mr-1"></i> Filter Data</h6>
                            <div class="dropdown-divider"></div>
                            <div class="form-group mb-3">
                                <label class="text-muted small mb-1"><i class="fas fa-building mr-1"></i> Gedung</label>
                                <select class="form-control form-control-sm" id="filter-gedung">
                                    <option value="">Semua Gedung</option>
                                    @php
                                        $gedungs = \App\Models\Gedung::pluck('nama_gedung', 'id');
                                    @endphp
                                    @foreach($gedungs as $id => $nama)
                                        <option value="{{ $nama }}">{{ $nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mb-1">
                                <label class="text-muted small mb-1"><i class="fas fa-tags mr-1"></i> Kategori</label>
                                <select class="form-control form-control-sm" id="filter-kategori">
                                    <option value="">Semua Kategori</option>
                                    @php
                                        $kategoris = \App\Models\GedungFasilitas::select('kategori')->distinct()->pluck('kategori');
                                    @endphp
                                    @foreach($kategoris as $kategori)
                                        <option value="{{ $kategori }}">{{ $kategori }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
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
                @include('dashboard.gedung_fasilitas.table')
            </div>
        </div>
    </div>
@endsection

@push('page_css')
<style>
    /* Force dropdown to appear instantly in fixed position — no slide/shift */
    .dropdown-animated {
        animation: none !important;
        transition: none !important;
        transform: none !important;
    }
    #filter-dropdown-container {
        position: relative;
    }
    #filter-dropdown-container .dropdown-menu {
        transition: none !important;
        transform: none !important;
        will-change: auto;
        top: 100% !important;
        left: auto !important;
        right: 0;
        margin-top: 4px !important;
        z-index: 1050;
    }
    #filter-dropdown-container .dropdown-menu.show {
        opacity: 1;
    }
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
    #gedungFasilitas-table_wrapper {
        padding-top: 0 !important;
    }
</style>
@endpush

@push('page_scripts')
<script>
    $(function () {
        var table = $('#gedungFasilitas-table').DataTable({
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
                            '<i class="fas fa-door-open fa-3x text-muted mb-3" style="opacity:0.4;"></i>' +
                            '<h6 class="text-muted">Belum ada data ruangan di sini</h6>' +
                            '<p class="text-muted small mb-0">Ayo mulai dengan menambahkan ruangan atau fasilitas pertama!</p>' +
                            '</div>',
                paginate: { first: "Awal", last: "Akhir", next: "›", previous: "‹" }
            },
            pageLength: 10,
            order: [[2, 'asc']],
            columnDefs: [
                { orderable: false, targets: [0, 1, 7] },
                { className: 'noVis', targets: [0, 7] }
            ],
            initComplete: function() {
                // Pindahkan length menu ke toolbar custom
                var $lengthMenu = $('.dataTables_length');
                $lengthMenu.appendTo('#custom-length-menu');
            }
        });

        // ─── Custom Search Bind ───
        $('#custom-search-input').on('keyup', function() {
            table.search(this.value).draw();
        });

        // Filter Gedung (Kolom indeks 2 adalah Gedung)
        $('#filter-gedung').on('change', function () {
            table.column(2).search(this.value).draw();
        });

        // Filter Kategori (Kolom indeks 4 adalah Kategori)
        $('#filter-kategori').on('change', function () {
            table.column(4).search(this.value).draw();
        });

        // Mencegah dropdown filter menutup saat diklik di dalamnya
        $('.dropdown-menu').on('click', function(e) {
            e.stopPropagation();
        });

        // Check All
        $('#checkAll').on('click', function() {
            var isChecked = $(this).prop('checked');
            $('.check-row').prop('checked', isChecked);
            toggleBulkDeleteBtn();
        });

        // Check individual row
        $(document).on('click', '.check-row', function() {
            var totalCheckboxes = $('.check-row').length;
            var checkedCheckboxes = $('.check-row:checked').length;
            
            $('#checkAll').prop('checked', totalCheckboxes === checkedCheckboxes && totalCheckboxes > 0);
            toggleBulkDeleteBtn();
        });

        function toggleBulkDeleteBtn() {
            var count = $('.check-row:checked').length;
            if (count > 0) {
                $('#selected-count').text(count);
                $('#btn-bulk-delete').removeClass('d-none');
            } else {
                $('#btn-bulk-delete').addClass('d-none');
            }
        }

        // Bulk Delete Action
        $('#btn-bulk-delete').on('click', function() {
            var selectedIds = [];
            $('.check-row:checked').each(function() {
                selectedIds.push($(this).val());
            });

            if (selectedIds.length === 0) return;

            Swal.fire({
                title: 'Hapus ' + selectedIds.length + ' Ruangan?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-trash-alt mr-1"></i> Ya, Hapus!',
                cancelButtonText: '<i class="fas fa-times mr-1"></i> Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('gedung_fasilitas.bulk-delete') }}',
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}',
                            ids: selectedIds
                        },
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message);
                                setTimeout(function() {
                                    location.reload();
                                }, 1000);
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function() {
                            toastr.error('Terjadi kesalahan saat menghapus data.');
                        }
                    });
                }
            });
        });

        // Toggle Bisa Diajukan AJAX
        $(document).on('change', '.toggle-bisa-diajukan', function() {
            var id = $(this).data('id');
            var isChecked = $(this).prop('checked');
            var labelSpan = $('.bisa-diajukan-label-' + id);

            $(this).prop('checked', !isChecked);
            $(this).prop('disabled', true);

            $.ajax({
                url: '{{ url("gedung_fasilitas") }}/' + id + '/toggle-bisa-diajukan',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('.toggle-bisa-diajukan[data-id="'+id+'"]').prop('disabled', false);
                    if (response.success) {
                        $('.toggle-bisa-diajukan[data-id="'+id+'"]').prop('checked', response.bisa_diajukan);
                        labelSpan.text(response.bisa_diajukan ? 'Ya' : 'Tidak');
                        toastr.success(response.message);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function() {
                    $('.toggle-bisa-diajukan[data-id="'+id+'"]').prop('disabled', false);
                    toastr.error('Terjadi kesalahan pada server.');
                }
            });
        });
    });
</script>
@endpush
