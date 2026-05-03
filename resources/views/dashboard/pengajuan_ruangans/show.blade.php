@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Detail Pengajuan Ruangan</h1>
                </div>
                <div class="col-sm-6">
                    <a class="btn btn-default float-right" href="{{ url()->previous() }}">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">
        @include('flash::message')

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">
                    <i class="fas fa-file-alt mr-2 text-primary"></i>
                    {{ $pengajuanRuangan->kode_pengajuan }}
                </h3>
                <div>
                    @if($pengajuanRuangan->status === 'disetujui')
                        <span class="badge badge-success badge-lg"><i class="fas fa-check-circle mr-1"></i>Disetujui</span>
                    @elseif($pengajuanRuangan->status === 'ditolak')
                        <span class="badge badge-danger badge-lg"><i class="fas fa-times-circle mr-1"></i>Ditolak</span>
                    @elseif($pengajuanRuangan->status === 'dibatalkan')
                        <span class="badge badge-secondary badge-lg"><i class="fas fa-ban mr-1"></i>Dibatalkan oleh Pemohon</span>
                    @else
                        <span class="badge badge-warning text-white badge-lg"><i class="fas fa-clock mr-1"></i>Diproses</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="text-muted"><i class="fas fa-user mr-1"></i> Informasi Pemohon</h5>
                        <table class="table table-sm">
                            <tr><th style="width:40%">Nama</th><td>{{ $pengajuanRuangan->nama_pemohon }}</td></tr>
                            <tr><th>Email</th><td>{{ $pengajuanRuangan->email_pemohon }}</td></tr>
                            <tr><th>Telepon</th><td>{{ $pengajuanRuangan->no_telepon }}</td></tr>
                            <tr><th>Instansi</th><td>{{ $pengajuanRuangan->asal_instansi }}</td></tr>
                            <tr><th>Akun</th><td>{{ $pengajuanRuangan->user->name ?? '-' }} ({{ $pengajuanRuangan->user->email ?? '-' }})</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5 class="text-muted"><i class="fas fa-calendar-alt mr-1"></i> Informasi Kegiatan</h5>
                        <table class="table table-sm">
                            <tr>
                                <th style="width:40%">Ruangan</th>
                                <td>
                                    <strong>{{ $pengajuanRuangan->ruangan->nama_fasilitas ?? '-' }}</strong>
                                    @if($pengajuanRuangan->ruangan && $pengajuanRuangan->ruangan->kategori)
                                        <span class="badge badge-info ml-1">{{ $pengajuanRuangan->ruangan->kategori }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Gedung</th>
                                <td><i class="fas fa-building text-muted mr-1"></i>{{ $pengajuanRuangan->ruangan->gedung->nama_gedung ?? '-' }}</td>
                            </tr>
                            <tr><th>Jenis Kegiatan</th><td>{{ $pengajuanRuangan->jenis_kegiatan }}</td></tr>
                            <tr><th>Nama Kegiatan</th><td>{{ $pengajuanRuangan->nama_kegiatan }}</td></tr>
                            <tr><th>Tanggal</th>
                                <td>
                                    {{ $pengajuanRuangan->tanggal_mulai->format('d M Y') }}
                                    @if($pengajuanRuangan->tanggal_mulai != $pengajuanRuangan->tanggal_selesai)
                                        — {{ $pengajuanRuangan->tanggal_selesai->format('d M Y') }}
                                    @endif
                                </td>
                            </tr>
                            <tr><th>Jam</th><td>{{ $pengajuanRuangan->jam_mulai }} — {{ $pengajuanRuangan->jam_selesai }}</td></tr>
                            <tr><th>Peserta</th><td>{{ $pengajuanRuangan->jumlah_peserta ?? '-' }} orang</td></tr>
                        </table>
                    </div>
                </div>

                @if($pengajuanRuangan->keperluan)
                    <div class="mt-3">
                        <h5 class="text-muted"><i class="fas fa-align-left mr-1"></i> Keperluan</h5>
                        <p class="mb-0">{{ $pengajuanRuangan->keperluan }}</p>
                    </div>
                @endif

                @if($pengajuanRuangan->catatan_admin)
                    <div class="mt-3">
                        <h5 class="text-muted"><i class="fas fa-comment-dots mr-1"></i> Catatan Admin</h5>
                        <div class="alert alert-info mb-0">{{ $pengajuanRuangan->catatan_admin }}</div>
                    </div>
                @endif

                {{-- Audit trail: siapa & kapan keputusan dibuat --}}
                @if($pengajuanRuangan->approved_at)
                    <div class="mt-3">
                        <small class="text-muted">
                            <i class="fas fa-user-check mr-1"></i>
                            Diputuskan oleh
                            <strong>{{ optional($pengajuanRuangan->approvedBy)->name ?? 'Admin' }}</strong>
                            pada
                            <strong>{{ $pengajuanRuangan->approved_at->format('d M Y, H:i') }}</strong>
                        </small>
                    </div>
                @endif

                {{-- Admin: Form update status --}}
                @if(Auth::user()->isAdmin() && $pengajuanRuangan->status === 'diproses')
                    <hr>
                    <h5 class="text-muted"><i class="fas fa-gavel mr-1"></i> Tindakan Admin</h5>
                    <form action="{{ route('pengajuan_ruangans.update-status', $pengajuanRuangan->id) }}" method="POST">
                        @csrf @method('PATCH')
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label>Ubah Status:</label>
                                <select name="status" id="status-select" class="form-control" required>
                                    <option value="">-- Pilih --</option>
                                    <option value="disetujui">Setujui</option>
                                    <option value="ditolak">Tolak</option>
                                </select>
                            </div>
                            <div class="form-group col-md-8">
                                <label>Catatan <span id="catatan-required" class="text-danger" style="display:none;">(wajib saat menolak)</span>:</label>
                                <input type="text" name="catatan_admin" id="catatan-input" class="form-control"
                                       placeholder="Alasan persetujuan/penolakan...">
                            </div>
                        </div>
                        <button type="button" class="btn btn-primary"
                                onclick="confirmAction(this.closest('form'), 'Perbarui Status?', 'Yakin memperbarui status pengajuan ini?', 'question', 'Ya, simpan!')">
                            <i class="fas fa-save mr-1"></i> Simpan Keputusan
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    @push('page_scripts')
    <script>
        $(function() {
            // Tampilkan indikator wajib pada catatan saat status = ditolak
            $('#status-select').on('change', function() {
                var isReject = $(this).val() === 'ditolak';
                $('#catatan-required').toggle(isReject);
                $('#catatan-input').prop('required', isReject);
            });
        });
    </script>
    @endpush
@endsection
