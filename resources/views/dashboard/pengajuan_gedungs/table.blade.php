    <table class="table table-hover" id="pengajuanGedungs-table">
        <thead>
            <tr>
                <th width="40" class="text-center align-middle">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="checkAllPengajuan">
                        <label class="custom-control-label" for="checkAllPengajuan"></label>
                    </div>
                </th>
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
                <td class="text-center align-middle">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input check-row-pengajuan" id="check_pg_{{ $pengajuan->id }}" value="{{ $pengajuan->id }}">
                        <label class="custom-control-label" for="check_pg_{{ $pengajuan->id }}"></label>
                    </div>
                </td>
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
                        <span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i>Disetujui</span>
                    @elseif($pengajuan->status === 'ditolak')
                        <span class="badge badge-danger"><i class="fas fa-times-circle mr-1"></i>Ditolak</span>
                    @else
                        <span class="badge badge-warning text-white"><i class="fas fa-clock mr-1"></i>Diproses</span>
                    @endif
                </td>
                <td style="width: 180px;">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('pengajuan_gedungs.show', $pengajuan->id) }}"
                           class="btn btn-default btn-sm mr-1" title="Lihat Detail">
                            <i class="far fa-eye"></i>
                        </a>

                        @if($pengajuan->status === 'diproses')
                            <form action="{{ route('pengajuan_gedungs.update-status', $pengajuan->id) }}"
                                  method="POST" class="mr-1 mb-0" style="display:inline;">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="disetujui">
                                <button type="button" class="btn btn-success btn-sm"
                                        title="Setujui"
                                        onclick="confirmAction(this.closest('form'), 'Setujui Pengajuan?', 'Pengajuan ini akan disetujui.', 'question', 'Ya, setujui!')">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                            <form action="{{ route('pengajuan_gedungs.update-status', $pengajuan->id) }}"
                                  method="POST" class="mr-1 mb-0 form-tolak-pengajuan" style="display:inline;">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="ditolak">
                                <input type="hidden" name="catatan_admin" value="">
                                <button type="button" class="btn btn-secondary btn-sm btn-tolak-pengajuan"
                                        title="Tolak">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                        @endif

                        <form action="{{ route('pengajuan_gedungs.destroy', $pengajuan->id) }}"
                              method="POST" class="mb-0" style="display:inline;">
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
                <td colspan="8" class="text-center text-muted py-4">Belum ada pengajuan.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
