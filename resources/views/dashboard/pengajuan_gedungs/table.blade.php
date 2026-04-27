<div class="table-responsive">
    <table class="table table-hover" id="pengajuanGedungs-table">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Pemohon</th>
                <th>Gedung</th>
                <th>Kegiatan</th>
                <th>Tanggal</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        @forelse($pengajuanGedungs as $pengajuan)
            <tr>
                <td><strong>{{ $pengajuan->kode_pengajuan }}</strong></td>
                <td>
                    {{ $pengajuan->nama_pemohon }}
                    <br><small class="text-muted">{{ $pengajuan->user->name ?? '-' }}</small>
                </td>
                <td>{{ $pengajuan->gedung->nama_gedung ?? '-' }}</td>
                <td>{{ $pengajuan->nama_kegiatan }}</td>
                <td>
                    {{ $pengajuan->tanggal_mulai->format('d/m/Y') }}
                    @if($pengajuan->tanggal_mulai != $pengajuan->tanggal_selesai)
                        - {{ $pengajuan->tanggal_selesai->format('d/m/Y') }}
                    @endif
                </td>
                <td>
                    @if($pengajuan->status === 'disetujui')
                        <span class="badge badge-success">Disetujui</span>
                    @elseif($pengajuan->status === 'ditolak')
                        <span class="badge badge-danger">Ditolak</span>
                    @else
                        <span class="badge badge-warning text-white">Diproses</span>
                    @endif
                </td>
                <td style="width: 180px;">
                    <div class="btn-group">
                        <a href="{{ route('pengajuan_gedungs.show', $pengajuan->id) }}"
                           class="btn btn-default btn-sm">
                            <i class="far fa-eye"></i>
                        </a>

                        @if($pengajuan->status === 'diproses')
                            <form action="{{ route('pengajuan_gedungs.update-status', $pengajuan->id) }}"
                                  method="POST" style="display:inline;">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="disetujui">
                                <button type="button" class="btn btn-success btn-sm"
                                        title="Setujui"
                                        onclick="confirmAction(this.closest('form'), 'Setujui Pengajuan?', 'Pengajuan ini akan disetujui.', 'question', 'Ya, setujui!')">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                            <form action="{{ route('pengajuan_gedungs.update-status', $pengajuan->id) }}"
                                  method="POST" style="display:inline;">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="ditolak">
                                <button type="button" class="btn btn-danger btn-sm"
                                        title="Tolak"
                                        onclick="confirmAction(this.closest('form'), 'Tolak Pengajuan?', 'Pengajuan ini akan ditolak.', 'warning', 'Ya, tolak!')">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                        @endif

                        <form action="{{ route('pengajuan_gedungs.destroy', $pengajuan->id) }}"
                              method="POST" style="display:inline;">
                            @csrf @method('DELETE')
                            <button type="button" class="btn btn-danger btn-sm"
                                    title="Hapus"
                                    onclick="confirmDelete(this.closest('form'), 'Hapus pengajuan ini? Data tidak bisa dikembalikan!')">
                                <i class="far fa-trash-alt"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center text-muted py-4">Belum ada pengajuan.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>
