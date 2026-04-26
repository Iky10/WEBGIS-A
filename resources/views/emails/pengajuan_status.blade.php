<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Pengajuan</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f9; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 30px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .header { background: {{ $pengajuan->status === 'disetujui' ? '#28a745' : '#dc3545' }}; color: #fff; padding: 24px 30px; }
        .header h1 { margin: 0; font-size: 22px; }
        .header p { margin: 6px 0 0; opacity: 0.9; font-size: 14px; }
        .body { padding: 30px; color: #333; line-height: 1.6; }
        .badge { display: inline-block; padding: 4px 14px; border-radius: 20px; font-size: 13px; font-weight: 600; color: #fff; }
        .badge-success { background: #28a745; }
        .badge-danger { background: #dc3545; }
        table { width: 100%; border-collapse: collapse; margin: 16px 0; }
        table th { text-align: left; padding: 8px 12px; background: #f8f9fa; width: 40%; font-size: 14px; color: #666; }
        table td { padding: 8px 12px; font-size: 14px; }
        .catatan { background: #fff3cd; border-left: 4px solid #ffc107; padding: 12px 16px; margin: 16px 0; border-radius: 4px; }
        .footer { background: #f8f9fa; padding: 16px 30px; text-align: center; font-size: 12px; color: #999; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Pengajuan {{ $statusLabel }}</h1>
            <p>{{ $pengajuan->kode_pengajuan }}</p>
        </div>
        <div class="body">
            <p>Yth. <strong>{{ $pengajuan->nama_pemohon }}</strong>,</p>
            <p>Pengajuan penggunaan gedung Anda telah diproses dengan status:</p>
            <p>
                @if($pengajuan->status === 'disetujui')
                    <span class="badge badge-success">✔ DISETUJUI</span>
                @else
                    <span class="badge badge-danger">✘ DITOLAK</span>
                @endif
            </p>

            <table>
                <tr><th>Kode Pengajuan</th><td>{{ $pengajuan->kode_pengajuan }}</td></tr>
                <tr><th>Gedung</th><td>{{ $pengajuan->gedung->nama_gedung ?? '-' }}</td></tr>
                <tr><th>Kegiatan</th><td>{{ $pengajuan->nama_kegiatan }}</td></tr>
                <tr><th>Tanggal</th>
                    <td>
                        {{ $pengajuan->tanggal_mulai->format('d M Y') }}
                        @if($pengajuan->tanggal_mulai != $pengajuan->tanggal_selesai)
                            — {{ $pengajuan->tanggal_selesai->format('d M Y') }}
                        @endif
                    </td>
                </tr>
                <tr><th>Jam</th><td>{{ $pengajuan->jam_mulai }} — {{ $pengajuan->jam_selesai }}</td></tr>
            </table>

            @if($pengajuan->catatan_admin)
                <div class="catatan">
                    <strong>Catatan Admin:</strong><br>
                    {{ $pengajuan->catatan_admin }}
                </div>
            @endif

            <p style="margin-top: 20px; color: #666; font-size: 13px;">
                Email ini dikirim otomatis oleh sistem WebGIS Gedung.
                Silakan hubungi admin jika ada pertanyaan.
            </p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} WebGIS Gedung — Jurusan Rekayasa Komputer
        </div>
    </div>
</body>
</html>
