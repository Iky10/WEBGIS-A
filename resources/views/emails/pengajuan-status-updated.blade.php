<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f9; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header-disetujui { background: linear-gradient(135deg, #1b5e20, #4caf50); color: #fff; padding: 30px; text-align: center; }
        .header-ditolak { background: linear-gradient(135deg, #b71c1c, #e53935); color: #fff; padding: 30px; text-align: center; }
        .header-diproses { background: linear-gradient(135deg, #e65100, #ff9800); color: #fff; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 22px; }
        .body { padding: 30px; }
        .status-box { border-radius: 8px; padding: 15px 20px; text-align: center; margin: 20px 0; }
        .status-disetujui { background: #e8f5e9; border: 2px solid #4caf50; }
        .status-ditolak { background: #ffebee; border: 2px solid #e53935; }
        .status-diproses { background: #fff3e0; border: 2px solid #ff9800; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        table td { padding: 8px 12px; border-bottom: 1px solid #eee; }
        table td:first-child { color: #888; width: 40%; }
        .footer { background: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #999; }
        .btn { display: inline-block; background: #2d6a9f; color: #fff; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-{{ $pengajuan->status }}">
            <h1>
                @if($pengajuan->status == 'disetujui')
                    ✅ Pengajuan Disetujui
                @elseif($pengajuan->status == 'ditolak')
                    ❌ Pengajuan Ditolak
                @else
                    ⏳ Status Diperbarui
                @endif
            </h1>
        </div>
        <div class="body">
            <p>Halo <strong>{{ $pengajuan->nama_pemohon }}</strong>,</p>

            <p>Status pengajuan Anda dengan kode <strong>{{ $pengajuan->kode_pengajuan }}</strong> telah diperbarui:</p>

            <div class="status-box status-{{ $pengajuan->status }}">
                <div style="font-size: 24px; font-weight: bold;">
                    @if($pengajuan->status == 'disetujui')
                        ✅ DISETUJUI
                    @elseif($pengajuan->status == 'ditolak')
                        ❌ DITOLAK
                    @else
                        ⏳ DIPROSES
                    @endif
                </div>
            </div>

            @if($pengajuan->catatan_admin)
                <div style="background:#f5f5f5; border-left:4px solid #2d6a9f; padding:12px 16px; margin:15px 0; border-radius:0 4px 4px 0;">
                    <strong>Catatan dari Admin:</strong><br>
                    {{ $pengajuan->catatan_admin }}
                </div>
            @endif

            <table>
                <tr>
                    <td>Gedung</td>
                    <td><strong>{{ optional($pengajuan->gedung)->nama_gedung ?? '-' }}</strong></td>
                </tr>
                <tr>
                    <td>Kegiatan</td>
                    <td>{{ $pengajuan->nama_kegiatan }} ({{ $pengajuan->jenis_kegiatan }})</td>
                </tr>
                <tr>
                    <td>Tanggal</td>
                    <td>{{ $pengajuan->tanggal_mulai->format('d/m/Y') }} - {{ $pengajuan->tanggal_selesai->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <td>Jam</td>
                    <td>{{ $pengajuan->jam_mulai }} - {{ $pengajuan->jam_selesai }}</td>
                </tr>
            </table>

            @if($pengajuan->status == 'disetujui')
                <p style="color:#2e7d32;"><strong>Selamat!</strong> Silakan gunakan gedung sesuai jadwal yang diajukan.</p>
            @elseif($pengajuan->status == 'ditolak')
                <p style="color:#c62828;">Pengajuan Anda ditolak. Anda dapat mengajukan ulang dengan jadwal atau gedung yang berbeda.</p>
            @endif

            <p style="text-align:center; margin-top:20px;">
                <a href="{{ url('/pengajuan/cek-status') }}" class="btn">Lihat Detail Pengajuan</a>
            </p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} WebGIS Gedung. Email ini dikirim otomatis, mohon tidak membalas.
        </div>
    </div>
</body>
</html>
