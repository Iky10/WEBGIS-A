{{-- Menu Dashboard --}}
<li class="nav-item">
    <a href="{{ route('home') }}"
       class="nav-link {{ Request::is('home*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-tachometer-alt"></i>
        <p>Dashboard</p>
    </a>
</li>

{{-- Menu Gedung --}}
<li class="nav-item">
    <a href="{{ route('gedungs.index') }}"
       class="nav-link {{ Request::is('gedungs*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-building"></i>
        <p>Data Gedung</p>
    </a>
</li>

<!--
<li class="nav-item">
    <a href="{{ route('webgis.index') }}"
       class="nav-link {{ Request::is('webgis*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-map-marked-alt"></i>
        <p>WebGIS Peta</p>
    </a>
</li>-->

{{-- Menu Fasilitas & Ruangan --}}
<li class="nav-item">
    <a href="{{ route('gedung_fasilitas.index') }}"
       class="nav-link {{ Request::is('gedung_fasilitas*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-door-open"></i>
        <p>Master Ruangan</p>
    </a>
</li>

{{-- Menu Vegetasi --}}
<li class="nav-item">
    <a href="{{ route('vegetasis.index') }}"
       class="nav-link {{ Request::is('vegetasis*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-leaf"></i>
        <p>Master Vegetasi</p>
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

{{-- Pengaturan Semester Aktif --}}
<li class="nav-item">
    <a href="{{ route('semester_aktif.index') }}"
       class="nav-link {{ Request::is('semester-aktif*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-cog"></i>
        <p>Pengaturan Global</p>
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