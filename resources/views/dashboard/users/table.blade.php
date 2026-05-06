    <table class="table" id="users-table">
        <thead>
        <tr>
            <th>Nama</th>
            <th>Email</th>
            <th>Role</th>
            <th>Tanggal Daftar</th>
            <th>Aksi</th>
        </tr>
        </thead>
        <tbody>
        @foreach($users as $user)
            @php
                // Generate inisial untuk avatar
                $nameParts = preg_split('/\s+/', trim($user->name));
                if (count($nameParts) >= 2) {
                    $initials = strtoupper(mb_substr($nameParts[0], 0, 1) . mb_substr($nameParts[1], 0, 1));
                } else {
                    $initials = strtoupper(mb_substr($user->name, 0, 2));
                }
                // Hash nama untuk pilih warna avatar
                $avatarColors = [
                    ['#3498db', '#2980b9'],
                    ['#27ae60', '#1e8449'],
                    ['#e67e22', '#d35400'],
                    ['#9b59b6', '#7d3c98'],
                    ['#1abc9c', '#16a085'],
                    ['#e74c3c', '#c0392b'],
                ];
                $colorIdx = abs(crc32($user->name)) % count($avatarColors);
                $avatarGradient = 'linear-gradient(135deg, ' . $avatarColors[$colorIdx][0] . ', ' . $avatarColors[$colorIdx][1] . ')';
                $isSelf = $user->id === Auth::id();
            @endphp
            <tr>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="user-avatar-table mr-2" style="background: {{ $avatarGradient }};">
                            {{ $initials }}
                        </div>
                        <div>
                            <strong>{{ $user->name }}</strong>
                            @if($isSelf)
                                <span class="badge badge-info ml-1">Anda</span>
                            @endif
                        </div>
                    </div>
                </td>
                <td>{{ $user->email }}</td>
                <td>
                    @if($user->isAdmin())
                        <span class="badge badge-danger">
                            <i class="fas fa-user-shield mr-1"></i>Admin
                        </span>
                    @else
                        <span class="badge badge-info">
                            <i class="fas fa-user mr-1"></i>User
                        </span>
                    @endif
                </td>
                <td>
                    <small>{{ $user->created_at ? $user->created_at->isoFormat('D MMM YYYY') : '-' }}</small>
                </td>
                <td width="120">
                    {!! Form::open(['route' => ['users.destroy', $user->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('users.edit', [$user->id]) }}"
                           class='btn btn-default btn-sm' title="Edit User">
                            <i class="far fa-edit"></i>
                        </a>
                        @if(!$isSelf)
                            {!! Form::button('<i class="far fa-trash-alt"></i>', [
                                'type'    => 'button',
                                'class'   => 'btn btn-danger btn-sm',
                                'title'   => 'Hapus User',
                                'onclick' => 'confirmDelete(this.closest("form"), "Yakin ingin menghapus user ' . addslashes($user->name) . '?")'
                            ]) !!}
                        @else
                            <button type="button" class="btn btn-default btn-sm" disabled title="Tidak bisa hapus akun sendiri">
                                <i class="far fa-trash-alt"></i>
                            </button>
                        @endif
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
