    <table class="table" id="gedungFasilitas-table">
        <thead>
        <tr>
            <th width="40" class="text-center align-middle">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="checkAll">
                    <label class="custom-control-label" for="checkAll"></label>
                </div>
            </th>
            <th>Foto</th>
            <th>Gedung</th>
            <th>Nama Ruangan / Fasilitas</th>
            <th>Kategori</th>
            <th>Koordinat</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
        </thead>
        <tbody>
        @foreach($gedungFasilitas as $gf)
            <tr>
                <td class="text-center align-middle">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input check-row" id="check_{{ $gf->id }}" value="{{ $gf->id }}">
                        <label class="custom-control-label" for="check_{{ $gf->id }}"></label>
                    </div>
                </td>
                <td width="80">
                    @if($gf->foto_ruangan)
                        <img src="{{ asset($gf->foto_ruangan) }}"
                             alt="Foto"
                             class="img-thumbnail"
                             style="width: 60px; height: 60px; object-fit: cover;">
                    @else
                        <span class="text-muted"><i class="fas fa-image"></i></span>
                    @endif
                </td>
                <td>{{ optional($gf->gedung)->nama_gedung ?? 'N/A' }}</td>
                <td>{{ $gf->nama_fasilitas }}</td>
                <td><span class="badge badge-info">{{ $gf->kategori }}</span></td>
                <td>
                    @if($gf->latitude && $gf->longitude)
                        <small>{{ $gf->latitude }}, {{ $gf->longitude }}</small>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td>
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        <input type="checkbox" class="custom-control-input toggle-status" id="status_{{ $gf->id }}" data-id="{{ $gf->id }}" {{ $gf->is_aktif ? 'checked' : '' }}>
                        <label class="custom-control-label" for="status_{{ $gf->id }}">
                            <span class="status-label-{{ $gf->id }}">{{ $gf->is_aktif ? 'Aktif' : 'Tidak Aktif' }}</span>
                        </label>
                    </div>
                </td>
                <td width="120">
                    {!! Form::open(['route' => ['gedung_fasilitas.destroy', $gf->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('gedung_fasilitas.edit', [$gf->id]) }}"
                           class='btn btn-default btn-sm' title="Edit">
                            <i class="far fa-edit"></i>
                        </a>
                        {!! Form::button('<i class="far fa-trash-alt"></i>', ['type' => 'button', 'class' => 'btn btn-danger btn-sm', 'title' => 'Hapus', 'onclick' => 'confirmDelete(this.closest("form"), "Yakin ingin menghapus fasilitas ini?")']) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
