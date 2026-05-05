@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Manajemen User</h1>
                    <small class="text-muted">Kelola akun admin & user di sistem.</small>
                </div>
                <div class="col-sm-6">
                    <a class="btn btn-primary float-right"
                       href="{{ route('users.create') }}">
                        <i class="fas fa-user-plus mr-1"></i> Tambah User
                    </a>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">
        @include('flash::message')
        <div class="clearfix"></div>

        {{-- Stat cards --}}
        <div class="row mb-3">
            <div class="col-sm-6 col-md-4 mb-2">
                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center p-3">
                        <div class="stat-icon stat-icon-admin mr-3">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Total Admin</div>
                            <h4 class="mb-0">{{ $totalAdmin }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-4 mb-2">
                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center p-3">
                        <div class="stat-icon stat-icon-user mr-3">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Total User</div>
                            <h4 class="mb-0">{{ $totalUser }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-4 mb-2">
                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center p-3">
                        <div class="stat-icon stat-icon-total mr-3">
                            <i class="fas fa-users"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Total Akun</div>
                            <h4 class="mb-0">{{ $users->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            {{-- TOOLBAR ATAS --}}
            <div class="card-header bg-white border-bottom-0 pt-3 pb-0 px-3 d-flex flex-column flex-md-row justify-content-between align-items-center">
                <div class="d-flex align-items-center mb-2 mb-md-0 w-100">
                    <div id="custom-length-menu" class="mr-3"></div>
                    {{-- Filter role --}}
                    <select id="custom-role-filter" class="form-control form-control-sm" style="width: 160px;">
                        <option value="">Semua Role</option>
                        <option value="admin">Admin saja</option>
                        <option value="user">User saja</option>
                    </select>
                </div>

                <div class="d-flex align-items-center justify-content-md-end w-100">
                    <div class="input-group input-group-sm shadow-sm" style="width: 220px;">
                        <input type="text" id="custom-search-input" class="form-control border-right-0" placeholder="Cari nama / email...">
                        <div class="input-group-append">
                            <span class="input-group-text bg-white text-muted"><i class="fas fa-search"></i></span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Desktop: Table --}}
            <div class="card-body p-0 d-none d-md-block">
                @include('dashboard.users.table')
            </div>

            {{-- Mobile: Card List --}}
            <div class="d-block d-md-none mobile-card-list users-mobile-list">
                @forelse($users as $user)
                    @php
                        $nameParts = preg_split('/\s+/', trim($user->name));
                        if (count($nameParts) >= 2) {
                            $initials = strtoupper(mb_substr($nameParts[0], 0, 1) . mb_substr($nameParts[1], 0, 1));
                        } else {
                            $initials = strtoupper(mb_substr($user->name, 0, 2));
                        }
                        $avatarColors = [
                            ['#3498db', '#2980b9'], ['#27ae60', '#1e8449'],
                            ['#e67e22', '#d35400'], ['#9b59b6', '#7d3c98'],
                            ['#1abc9c', '#16a085'], ['#e74c3c', '#c0392b'],
                        ];
                        $colorIdx = abs(crc32($user->name)) % count($avatarColors);
                        $avatarGradient = 'linear-gradient(135deg, ' . $avatarColors[$colorIdx][0] . ', ' . $avatarColors[$colorIdx][1] . ')';
                        $isSelf = $user->id === Auth::id();
                    @endphp
                    <div class="mobile-card"
                         data-search="{{ strtolower($user->name . ' ' . $user->email) }}"
                         data-role="{{ $user->role }}">
                        <div class="mobile-card-header">
                            <div class="mobile-card-title d-flex align-items-center">
                                <div class="user-avatar-mobile mr-2" style="background: {{ $avatarGradient }};">
                                    {{ $initials }}
                                </div>
                                <div>
                                    <strong>{{ $user->name }}</strong>
                                    @if($isSelf)
                                        <span class="badge badge-info ml-1" style="font-size: 0.65rem;">Anda</span>
                                    @endif
                                    <small class="text-muted d-block">{{ $user->email }}</small>
                                </div>
                            </div>
                            @if($user->isAdmin())
                                <span class="badge badge-danger">
                                    <i class="fas fa-user-shield mr-1"></i>Admin
                                </span>
                            @else
                                <span class="badge badge-info">
                                    <i class="fas fa-user mr-1"></i>User
                                </span>
                            @endif
                        </div>
                        <div class="mobile-card-body">
                            <div class="mobile-card-row">
                                <i class="far fa-clock text-muted"></i>
                                <span class="small">Daftar {{ $user->created_at ? $user->created_at->isoFormat('D MMM YYYY') : '-' }}</span>
                            </div>
                        </div>
                        <div class="mobile-card-actions">
                            <a href="{{ route('users.edit', [$user->id]) }}"
                               class="btn btn-outline-primary btn-sm flex-grow-1">
                                <i class="far fa-edit mr-1"></i> Edit
                            </a>
                            @if(!$isSelf)
                                {!! Form::open(['route' => ['users.destroy', $user->id], 'method' => 'delete', 'class' => 'd-flex flex-grow-1 mb-0']) !!}
                                    {!! Form::button('<i class="far fa-trash-alt mr-1"></i> Hapus', [
                                        'type'    => 'button',
                                        'class'   => 'btn btn-outline-danger btn-sm flex-grow-1',
                                        'onclick' => 'confirmDelete(this.closest(\'form\'), \'Yakin ingin menghapus user ' . addslashes($user->name) . '?\')'
                                    ]) !!}
                                {!! Form::close() !!}
                            @else
                                <button class="btn btn-outline-secondary btn-sm flex-grow-1" disabled>
                                    <i class="fas fa-lock mr-1"></i> Akun Sendiri
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="mobile-card-empty">
                        <i class="fas fa-users fa-3x text-muted mb-3" style="opacity:0.4;"></i>
                        <h6 class="text-muted">Belum ada data user</h6>
                        <p class="text-muted small mb-2">Tambahkan user baru untuk memulai.</p>
                        <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-user-plus mr-1"></i> Tambah User
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
        display: flex; align-items: center;
        margin-bottom: 0; font-weight: normal;
    }
    div.dataTables_length select { margin: 0 0.5rem; width: auto; }
    .dataTables_filter { display: none; }
    .table-responsive {
        border-bottom-left-radius: 0.25rem;
        border-bottom-right-radius: 0.25rem;
    }
    /* Avatar circle (table & mobile) */
    .user-avatar-table {
        width: 36px; height: 36px;
        border-radius: 50%;
        color: #fff;
        font-weight: 600;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .user-avatar-mobile {
        width: 40px; height: 40px;
        border-radius: 50%;
        color: #fff;
        font-weight: 600;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    /* Stat icons */
    .stat-icon {
        width: 48px; height: 48px;
        border-radius: 12px;
        color: #fff;
        font-size: 1.3rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .stat-icon-admin { background: linear-gradient(135deg, #e74c3c, #c0392b); }
    .stat-icon-user  { background: linear-gradient(135deg, #3498db, #2980b9); }
    .stat-icon-total { background: linear-gradient(135deg, #16a085, #1abc9c); }
</style>
@endpush

@push('page_scripts')
<script>
    $(function () {
        var table = $('#users-table').DataTable({
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
                            '<i class="fas fa-users fa-3x text-muted mb-3" style="opacity:0.4;"></i>' +
                            '<h6 class="text-muted">Belum ada data user</h6>' +
                            '</div>',
                paginate: { first: "Awal", last: "Akhir", next: "›", previous: "‹" }
            },
            pageLength: 10,
            order: [[2, 'asc'], [0, 'asc']], // sort by Role asc (admin first), then Name
            columnDefs: [
                { orderable: false, targets: [4] }
            ],
            initComplete: function() {
                $('.dataTables_length').appendTo('#custom-length-menu');
            }
        });

        // ─── Filter & Search Sync ───
        function filterMobileCards() {
            var search = ($('#custom-search-input').val() || '').toLowerCase().trim();
            var role = ($('#custom-role-filter').val() || '').trim();
            var $cards = $('.users-mobile-list .mobile-card');
            var shown = 0;
            $cards.each(function() {
                var $c = $(this);
                var matchSearch = !search || (($c.data('search') || '').indexOf(search) !== -1);
                var matchRole = !role || ($c.data('role') === role);
                var visible = matchSearch && matchRole;
                $c.toggle(visible);
                if (visible) shown++;
            });
            var $empty = $('.users-mobile-list .mobile-card-empty-filter');
            if (shown === 0 && $cards.length > 0) {
                if ($empty.length === 0) {
                    $('.users-mobile-list').append(
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

        $('#custom-role-filter').on('change', function() {
            var v = this.value;
            // Filter desktop table by role column (index 2)
            // Use exact match with regex
            if (v) {
                table.column(2).search(v === 'admin' ? 'Admin' : 'User', true, false).draw();
            } else {
                table.column(2).search('').draw();
            }
            filterMobileCards();
        });
    });
</script>
@endpush
