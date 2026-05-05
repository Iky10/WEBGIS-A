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

            {{-- Desktop: Table (≥ md) --}}
            <div class="card-body p-0 d-none d-md-block">
                @include('dashboard.gedung_fasilitas.table')
            </div>

            {{-- Mobile: Card List (< md) --}}
            <div class="d-block d-md-none mobile-card-list gedung-fasilitas-mobile-list">
                @forelse($gedungFasilitas as $gf)
                    <div class="mobile-card"
                         data-gedung="{{ optional($gf->gedung)->nama_gedung ?? '' }}"
                         data-kategori="{{ $gf->kategori }}"
                         data-search="{{ strtolower($gf->nama_fasilitas.' '.(optional($gf->gedung)->nama_gedung ?? '').' '.$gf->kategori) }}">
                        <div class="mobile-card-header">
                            @if($gf->foto_ruangan)
                                <img src="{{ asset($gf->foto_ruangan) }}" alt="Foto" class="mobile-card-thumb">
                            @else
                                <div class="mobile-card-thumb mobile-card-thumb-placeholder text-white">
                                    <i class="fas fa-door-open"></i>
                                </div>
                            @endif
                            <div class="mobile-card-title">
                                <strong>{{ $gf->nama_fasilitas }}</strong>
                                <small class="text-muted d-block">
                                    <i class="fas fa-building"></i> {{ optional($gf->gedung)->nama_gedung ?? 'N/A' }}
                                </small>
                            </div>
                            <span class="badge badge-info">{{ $gf->kategori }}</span>
                        </div>
                        <div class="mobile-card-body">
                            @if($gf->latitude && $gf->longitude)
                                <div class="mobile-card-row">
                                    <i class="fas fa-crosshairs text-muted"></i>
                                    <span class="text-monospace small">{{ $gf->latitude }}, {{ $gf->longitude }}</span>
                                </div>
                            @endif
                            <div class="mobile-card-row">
                                <i class="fas fa-door-open text-muted"></i>
                                <span>
                                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-primary">
                                        <input type="checkbox" class="custom-control-input toggle-bisa-diajukan"
                                               id="bisa_diajukan_m_{{ $gf->id }}" data-id="{{ $gf->id }}"
                                               {{ $gf->bisa_diajukan ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="bisa_diajukan_m_{{ $gf->id }}">
                                            Bisa Diajukan:
                                            <span class="bisa-diajukan-label-{{ $gf->id }} font-weight-bold">{{ $gf->bisa_diajukan ? 'Ya' : 'Tidak' }}</span>
                                        </label>
                                    </div>
                                </span>
                            </div>
                        </div>
                        <div class="mobile-card-actions">
                            <a href="{{ route('gedung_fasilitas.edit', [$gf->id]) }}"
                               class="btn btn-outline-secondary btn-sm flex-grow-1">
                                <i class="far fa-edit mr-1"></i> Edit
                            </a>
                            {!! Form::open(['route' => ['gedung_fasilitas.destroy', $gf->id], 'method' => 'delete', 'class' => 'd-flex flex-grow-1 mb-0']) !!}
                                {!! Form::button('<i class="far fa-trash-alt mr-1"></i> Hapus', ['type' => 'button', 'class' => 'btn btn-outline-danger btn-sm flex-grow-1', 'onclick' => 'confirmDelete(this.closest(\'form\'), \'Yakin ingin menghapus fasilitas ini?\')']) !!}
                            {!! Form::close() !!}
                        </div>
                    </div>
                @empty
                    <div class="mobile-card-empty">
                        <i class="fas fa-door-open fa-3x text-muted mb-3" style="opacity:0.4;"></i>
                        <h6 class="text-muted">Belum ada data ruangan di sini</h6>
                        <p class="text-muted small mb-2">Ayo mulai dengan menambahkan ruangan atau fasilitas pertama!</p>
                        <a href="{{ route('gedung_fasilitas.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus mr-1"></i> Tambah Baru
                        </a>
                    </div>
                @endforelse
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

        // ─── Mobile Card Filter ---
        function filterMobileCards() {
            var search = ($('#custom-search-input').val() || '').toLowerCase().trim();
            var filterGedung = $('#filter-gedung').val();
            var filterKategori = $('#filter-kategori').val();
            var $cards = $('.gedung-fasilitas-mobile-list .mobile-card');
            var shown = 0;
            $cards.each(function() {
                var $card = $(this);
                var matchSearch = !search || ($card.data('search') || '').indexOf(search) !== -1;
                var matchGedung = !filterGedung || $card.data('gedung') === filterGedung;
                var matchKategori = !filterKategori || $card.data('kategori') === filterKategori;
                var visible = matchSearch && matchGedung && matchKategori;
                $card.toggle(visible);
                if (visible) shown++;
            });
            var $empty = $('.gedung-fasilitas-mobile-list .mobile-card-empty-filter');
            if (shown === 0 && $cards.length > 0) {
                if ($empty.length === 0) {
                    $('.gedung-fasilitas-mobile-list').append(
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

        // Filter Gedung (Kolom indeks 2 adalah Gedung)
        $('#filter-gedung').on('change', function () {
            table.column(2).search(this.value).draw();
            filterMobileCards();
        });

        // Filter Kategori (Kolom indeks 4 adalah Kategori)
        $('#filter-kategori').on('change', function () {
            table.column(4).search(this.value).draw();
            filterMobileCards();
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

        // Toggle Bisa Diajukan AJAX (dengan konfirmasi SweetAlert)
        // Aksi ini berdampak ke user (sembunyikan/tampilkan ruangan dari form pengajuan)
        // jadi minta konfirmasi dulu sebelum eksekusi.
        $(document).on('change', '.toggle-bisa-diajukan', function() {
            var $toggle    = $(this);
            var id         = $toggle.data('id');
            var isChecked  = $toggle.prop('checked'); // state setelah user klik (target state)
            var labelSpan  = $('.bisa-diajukan-label-' + id);

            // Cari nama ruangan — support tabel desktop ATAU mobile card
            var $row = $toggle.closest('tr');
            var $card = $toggle.closest('.mobile-card');
            var namaRuangan;
            if ($row.length > 0) {
                namaRuangan = $row.find('td').eq(3).text().trim() || 'ruangan ini';
            } else if ($card.length > 0) {
                namaRuangan = $card.find('.mobile-card-title strong').text().trim() || 'ruangan ini';
            } else {
                namaRuangan = 'ruangan ini';
            }

            // Revert state visual segera supaya user bisa cancel tanpa side-effect
            $toggle.prop('checked', !isChecked);

            // Pesan kontekstual berdasarkan target state
            var swalConfig = isChecked
                ? {
                    title: 'Buka untuk Pengajuan?',
                    html: '<strong>' + namaRuangan + '</strong> akan dibuka untuk pengajuan.<br>' +
                          '<small class="text-muted">User akan bisa melihat dan mengajukan ruangan ini di form pengajuan.</small>',
                    icon: 'question',
                    confirmButtonText: 'Ya, Buka',
                    confirmButtonColor: '#3498db'
                }
                : {
                    title: 'Tutup dari Pengajuan?',
                    html: '<strong>' + namaRuangan + '</strong> akan disembunyikan dari form pengajuan.<br>' +
                          '<small class="text-muted">User tidak akan bisa mengajukan ruangan ini sampai dibuka kembali. Pengajuan yang sudah ada tetap berlaku.</small>',
                    icon: 'warning',
                    confirmButtonText: 'Ya, Tutup',
                    confirmButtonColor: '#e67e22'
                };

            Swal.fire(Object.assign({
                showCancelButton: true,
                cancelButtonText: 'Batal',
                cancelButtonColor: '#95a5a6',
                reverseButtons: true
            }, swalConfig)).then(function(result) {
                if (!result.isConfirmed) return; // user cancel — toggle tetap di state lama

                // User konfirmasi: lakukan AJAX
                // Sync SEMUA toggle dengan data-id sama (desktop + mobile) supaya konsisten
                var $allToggles = $('.toggle-bisa-diajukan[data-id="' + id + '"]');
                $allToggles.prop('disabled', true);
                $.ajax({
                    url: '{{ url("gedung_fasilitas") }}/' + id + '/toggle-bisa-diajukan',
                    type: 'POST',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        $allToggles.prop('disabled', false);
                        if (response.success) {
                            $allToggles.prop('checked', response.bisa_diajukan);
                            labelSpan.text(response.bisa_diajukan ? 'Ya' : 'Tidak');
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function() {
                        $allToggles.prop('disabled', false);
                        toastr.error('Terjadi kesalahan pada server.');
                    }
                });
            });
        });
    });
</script>
@endpush
