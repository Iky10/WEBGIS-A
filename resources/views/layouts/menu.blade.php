{{-- Menu Dashboard --}}
<li class="nav-item">
    <a href="{{ route('home') }}"
       class="nav-link {{ Request::is('home*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-tachometer-alt"></i>
        <p>Dashboard</p>
    </a>
</li>

{{-- ═══ Menu Khusus Admin ═══ --}}
@if(Auth::user()->isAdmin())

{{-- Menu Gedung --}}
<li class="nav-item">
    <a href="{{ route('gedungs.index') }}"
       class="nav-link {{ Request::is('gedungs*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-building"></i>
        <p>Data Gedung</p>
    </a>
</li>

{{-- Menu Fasilitas & Ruangan --}}
<li class="nav-item">
    <a href="{{ route('gedung_fasilitas.index') }}"
       class="nav-link {{ Request::is('gedung_fasilitas*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-door-open"></i>
        <p>Master Ruangan</p>
    </a>
</li>

{{-- Menu Jadwal Ruangan --}}
<li class="nav-item">
    <a href="{{ route('jadwal_ruangans.index') }}"
       class="nav-link {{ Request::is('jadwal_ruangans*') ? 'active' : '' }}">
        <i class="nav-icon far fa-calendar-alt"></i>
        <p>Jadwal Ruangan</p>
    </a>
</li>

{{-- Menu Jadwal Semester --}}
<li class="nav-item">
    <a href="{{ route('jadwal_semester.index') }}"
       class="nav-link {{ Request::is('jadwal_semester*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-file-image"></i>
        <p>Jadwal Semester</p>
    </a>
</li>

{{-- Menu Pengajuan Gedung (Semua Pengajuan) --}}
@php
    $pengajuanMenungguCount = \App\Models\PengajuanGedung::where('status', 'diproses')->count();
@endphp
<li class="nav-item">
    <a href="{{ route('pengajuan_gedungs.index') }}"
       class="nav-link {{ Request::is('pengajuan_gedungs*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-file-alt"></i>
        <p>
            Pengajuan Gedung
            @if($pengajuanMenungguCount > 0)
                <span class="badge badge-danger right">{{ $pengajuanMenungguCount }}</span>
            @endif
        </p>
    </a>
</li>

@endif

{{-- ═══ Menu Semua User ═══ --}}

{{-- Riwayat Pengajuan Saya --}}
<li class="nav-item">
    <a href="{{ route('pengajuan_gedungs.riwayat') }}"
       class="nav-link {{ Request::is('pengajuan-gedung/riwayat*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-history"></i>
        <p>Riwayat Pengajuan</p>
    </a>
</li>

{{-- Kembali ke Publik --}}
<li class="nav-item">
    <a href="{{ route('publik.home') }}"
       class="nav-link {{ Request::is('/') ? 'active' : '' }}">
        <i class="nav-icon fas fa-globe"></i>
        <p>Kembali ke Publik</p>
    </a>
</li>