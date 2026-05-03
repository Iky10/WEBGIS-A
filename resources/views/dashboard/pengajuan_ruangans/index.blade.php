@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Pengajuan Penggunaan Ruangan</h1>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">
        @include('flash::message')
        <div class="clearfix"></div>

        <div class="card shadow-sm border-0 mb-4">
            {{-- TOOLBAR ATAS --}}
            <div class="card-header bg-white border-bottom-0 pt-3 pb-0 px-3 d-flex flex-column flex-md-row justify-content-between align-items-center">
                <div class="d-flex align-items-center mb-2 mb-md-0 w-100">
                    <div id="custom-length-menu" class="mr-3"></div>

                    {{-- Bulk Delete --}}
                    <button class="btn btn-danger btn-sm d-none mr-2 shadow-sm" id="btn-bulk-delete-pengajuan">
                        <i class="fas fa-trash-alt mr-1"></i>Hapus (<span id="selected-count-pengajuan">0</span>)
                    </button>
                </div>

                <div class="d-flex align-items-center justify-content-md-end w-100">
                    {{-- Filter Dropdown --}}
                    <div class="dropdown mr-2" id="filter-dropdown-pengajuan">
                        <button class="btn btn-default btn-sm dropdown-toggle shadow-sm" type="button" id="dropdownMenuFilter" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="height: 31px;">
                            <i class="fas fa-filter mr-1 text-muted"></i> Filter
                        </button>
                        <div class="dropdown-menu dropdown-menu-right p-3 shadow dropdown-animated" aria-labelledby="dropdownMenuFilter" style="width: 280px; border-radius: 8px;">
                            <h6 class="dropdown-header px-0 text-dark font-weight-bold"><i class="fas fa-sliders-h mr-1"></i> Filter Data</h6>
                            <div class="dropdown-divider"></div>
                            <div class="form-group mb-3">
                                <label class="text-muted small mb-1"><i class="fas fa-building mr-1"></i> Gedung</label>
                                <select class="form-control form-control-sm" id="filter-gedung-pengajuan">
                                    <option value="">Semua Gedung</option>
                                    @foreach($gedungList as $id => $nama)
                                        <option value="{{ $nama }}">{{ $nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mb-1">
                                <label class="text-muted small mb-1"><i class="fas fa-info-circle mr-1"></i> Status</label>
                                <select class="form-control form-control-sm" id="filter-status-pengajuan">
                                    <option value="">Semua Status</option>
                                    <option value="Diproses">Diproses</option>
                                    <option value="Disetujui">Disetujui</option>
                                    <option value="Dibatalkan">Dibatalkan</option>
                                    <option value="Ditolak">Ditolak</option>
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

            {{-- ══ TABLE ══ --}}
            <div class="card-body p-0">
                @include('dashboard.pengajuan_ruangans.table')
            </div>
        </div>
    </div>
@endsection

@push('page_css')
<style>
    .dropdown-animated {
        animation: none !important;
        transition: none !important;
        transform: none !important;
    }
    #filter-dropdown-pengajuan {
        position: relative;
    }
    #filter-dropdown-pengajuan .dropdown-menu {
        transition: none !important;
        transform: none !important;
        will-change: auto;
        top: 100% !important;
        left: auto !important;
        right: 0;
        margin-top: 4px !important;
        z-index: 1050;
    }
    #filter-dropdown-pengajuan .dropdown-menu.show {
        opacity: 1;
    }
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
    #pengajuanRuangans-table_wrapper {
        padding-top: 0 !important;
    }
    /* Ruangan cell: compact layout */
    .ruangan-cell {
        min-width: 150px;
    }
    .ruangan-cell .ruangan-name {
        font-weight: 600;
        color: #212529;
    }
    .ruangan-cell .gedung-name {
        font-size: 12px;
        color: #6c757d;
    }
</style>
@endpush

@push('page_scripts')
<script>
    $(function () {
        var table = $('#pengajuanRuangans-table').DataTable({
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
                            '<h6 class="text-muted">Belum ada pengajuan masuk</h6>' +
                            '<p class="text-muted small mb-0">Pengajuan dari user akan muncul di sini.</p>' +
                            '</div>',
                paginate: { first: "Awal", last: "Akhir", next: "›", previous: "‹" }
            },
            pageLength: 10,
            order: [[1, 'desc']],
            columnDefs: [
                { orderable: false, targets: [0, 7] },
                { className: 'noVis', targets: [0, 7] }
            ],
            initComplete: function() {
                var $lengthMenu = $('.dataTables_length');
                $lengthMenu.appendTo('#custom-length-menu');
            }
        });

        // Custom Search
        $('#custom-search-input').on('keyup', function() {
            table.search(this.value).draw();
        });

        // Filter Gedung (kolom index 3: Ruangan cell berisi gedung-name)
        $('#filter-gedung-pengajuan').on('change', function () {
            table.column(3).search(this.value).draw();
        });

        // Filter Status (kolom index 6: Status)
        $('#filter-status-pengajuan').on('change', function () {
            table.column(6).search(this.value).draw();
        });

        $('.dropdown-menu').on('click', function(e) {
            e.stopPropagation();
        });

        // Check All
        $('#checkAllPengajuan').on('click', function() {
            var isChecked = $(this).prop('checked');
            $('.check-row-pengajuan').prop('checked', isChecked);
            toggleBulkDeleteBtn();
        });

        $(document).on('click', '.check-row-pengajuan', function() {
            var total = $('.check-row-pengajuan').length;
            var checked = $('.check-row-pengajuan:checked').length;
            $('#checkAllPengajuan').prop('checked', total === checked && total > 0);
            toggleBulkDeleteBtn();
        });

        function toggleBulkDeleteBtn() {
            var count = $('.check-row-pengajuan:checked').length;
            if (count > 0) {
                $('#selected-count-pengajuan').text(count);
                $('#btn-bulk-delete-pengajuan').removeClass('d-none');
            } else {
                $('#btn-bulk-delete-pengajuan').addClass('d-none');
            }
        }

        // Tombol Tolak: prompt catatan via SweetAlert
        $(document).on('click', '.btn-tolak-pengajuan', function() {
            var $form = $(this).closest('form.form-tolak-pengajuan');

            Swal.fire({
                title: 'Tolak Pengajuan?',
                text: 'Catatan akan dikirim ke pemohon sebagai alasan penolakan.',
                input: 'textarea',
                inputLabel: 'Alasan Penolakan',
                inputPlaceholder: 'Tuliskan alasan penolakan secara jelas...',
                inputAttributes: { 'aria-label': 'Alasan penolakan', 'maxlength': 1000 },
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-times mr-1"></i> Ya, Tolak',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                inputValidator: function(value) {
                    if (!value || value.trim().length < 5) {
                        return 'Alasan penolakan wajib diisi minimal 5 karakter.';
                    }
                }
            }).then(function(result) {
                if (result.isConfirmed) {
                    $form.find('input[name="catatan_admin"]').val(result.value.trim());
                    $form.trigger('submit');
                }
            });
        });

        // Bulk Delete
        $('#btn-bulk-delete-pengajuan').on('click', function() {
            var selectedIds = [];
            $('.check-row-pengajuan:checked').each(function() {
                selectedIds.push($(this).val());
            });
            if (selectedIds.length === 0) return;

            Swal.fire({
                title: 'Hapus ' + selectedIds.length + ' Pengajuan?',
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
                        url: '{{ route("pengajuan_ruangans.bulk-delete") }}',
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}',
                            ids: selectedIds
                        },
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message);
                                setTimeout(function() { location.reload(); }, 1000);
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
    });
</script>
@endpush
