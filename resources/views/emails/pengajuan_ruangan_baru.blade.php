<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengajuan Ruangan Baru</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f9; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 30px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .header { background: #007bff; color: #fff; padding: 24px 30px; }
        .header h1 { margin: 0; font-size: 22px; }
        .header p { margin: 6px 0 0; opacity: 0.9; font-size: 14px; }
        .body { padding: 30px; color: #333; line-height: 1.6; }
        table { width: 100%; border-collapse: collapse; margin: 16px 0; }
        table th { text-align: left; padding: 8px 12px; background: #f8f9fa; width: 40%; font-size: 14px; color: #666; }
        table td { padding: 8px 12px; font-size: 14px; }
        .btn { display: inline-block; background: #007bff; color: #fff; padding: 10px 24px; text-decoration: none; border-radius: 6px; font-size: 14px; margin-top: 10px; }
        .footer { background: #f8f9fa; padding: 16px 30px; text-align: center; font-size: 12px; color: #999; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📋 Pengajuan Ruangan Baru Masuk</h1>
            <p>{{ $pengajuan->kode_pengajuan }}</p>
        </div>
        <div class="body">
            <p>Ada pengajuan penggunaan ruangan baru yang memerlukan persetujuan Anda.</p>

            <table>
                <tr><th>Kode</th><td><strong>{{ $pengajuan->kode_pengajuan }}</strong></td></tr>
                <tr><th>Pemohon</th><td>{{ $pengajuan->nama_pemohon }}</td></tr>
                <tr><th>Email</th><td>{{ $pengajuan->email_pemohon }}</td></tr>
                <tr><th>Instansi</th><td>{{ $pengajuan->asal_instansi }}</td></tr>
                <tr><th>Ruangan</th>
                    <td>
                        <strong>{{ $pengajuan->ruangan->nama_fasilitas ?? '-' }}</strong>
                        @if($pengajuan->ruangan && $pengajuan->ruangan->gedung)
                            <br><small style="color:#666;">Gedung: {{ $pengajuan->ruangan->gedung->nama_gedung }}</small>
                        @endif
                    </td>
                </tr>
                <tr><th>Kegiatan</th><td>{{ $pengajuan->nama_kegiatan }} ({{ $pengajuan->jenis_kegiatan }})</td></tr>
                <tr><th>Tanggal</th>
                    <td>
                        {{ $pengajuan->tanggal_mulai->format('d M Y') }}
                        @if($pengajuan->tanggal_mulai != $pengajuan->tanggal_selesai)
                            — {{ $pengajuan->tanggal_selesai->format('d M Y') }}
                        @endif
                    </td>
                </tr>
                <tr><th>Jam</th><td>{{ $pengajuan->jam_mulai }} — {{ $pengajuan->jam_selesai }}</td></tr>
                <tr><th>Peserta</th><td>{{ $pengajuan->jumlah_peserta ?? '-' }} orang</td></tr>
            </table>

            @if($pengajuan->keperluan)
                <p><strong>Keperluan:</strong><br>{{ $pengajuan->keperluan }}</p>
            @endif

            <a href="{{ route('pengajuan_ruangans.show', $pengajuan->id) }}" class="btn">
                Lihat Detail & Tindakan
            </a>

            <p style="margin-top: 20px; color: #666; font-size: 13px;">
                Email ini dikirim otomatis oleh sistem WebGIS Gedung.
            </p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} WebGIS Gedung — Jurusan Rekayasa Komputer
        </div>
    </div>
</body>
</html>
