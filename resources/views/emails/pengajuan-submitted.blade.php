<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f9; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #1a3c5e, #2d6a9f); color: #fff; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 22px; }
        .body { padding: 30px; }
        .kode-box { background: #e8f5e9; border: 2px dashed #4caf50; border-radius: 8px; padding: 20px; text-align: center; margin: 20px 0; }
        .kode-box .label { font-size: 13px; color: #666; margin-bottom: 5px; }
        .kode-box .kode { font-size: 28px; font-weight: bold; color: #2e7d32; letter-spacing: 2px; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        table td { padding: 8px 12px; border-bottom: 1px solid #eee; }
        table td:first-child { color: #888; width: 40%; }
        .footer { background: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #999; }
        .btn { display: inline-block; background: #2d6a9f; color: #fff; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ Pengajuan Berhasil Dikirim</h1>
        </div>
        <div class="body">
            <p>Halo <strong>{{ $pengajuan->nama_pemohon }}</strong>,</p>
            <p>Pengajuan penggunaan gedung Anda telah berhasil kami terima. Berikut detail pengajuan Anda:</p>

            <div class="kode-box">
                <div class="label">KODE PENGAJUAN ANDA</div>
                <div class="kode">{{ $pengajuan->kode_pengajuan }}</div>
                <div class="label" style="margin-top:8px;">Simpan kode ini untuk mengecek status pengajuan</div>
            </div>

            <table>
                <tr>
                    <td>Gedung</td>
                    <td><strong>{{ optional($pengajuan->gedung)->nama_gedung ?? '-' }}</strong></td>
                </tr>
                <tr>
                    <td>Jenis Kegiatan</td>
                    <td>{{ $pengajuan->jenis_kegiatan }}</td>
                </tr>
                <tr>
                    <td>Nama Kegiatan</td>
                    <td>{{ $pengajuan->nama_kegiatan }}</td>
                </tr>
                <tr>
                    <td>Tanggal</td>
                    <td>{{ $pengajuan->tanggal_mulai->format('d/m/Y') }} - {{ $pengajuan->tanggal_selesai->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <td>Jam</td>
                    <td>{{ $pengajuan->jam_mulai }} - {{ $pengajuan->jam_selesai }}</td>
                </tr>
                <tr>
                    <td>Status</td>
                    <td><strong style="color:#ff9800;">Diproses</strong></td>
                </tr>
            </table>

            <p>Anda dapat mengecek status pengajuan kapan saja melalui halaman berikut:</p>
            <p style="text-align:center;">
                <a href="{{ url('/pengajuan/cek-status') }}" class="btn">Cek Status Pengajuan</a>
            </p>

            <p style="color:#888; font-size:13px;">Kami akan mengirimkan email lagi saat status pengajuan Anda diperbarui oleh admin.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} WebGIS Gedung. Email ini dikirim otomatis, mohon tidak membalas.
        </div>
    </div>
</body>
</html>
