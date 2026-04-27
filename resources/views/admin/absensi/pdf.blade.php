<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Absensi</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            background: white;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #FF8C42;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .header h1 {
            margin: 0;
            color: #FF8C42;
            font-size: 24px;
        }

        .header p {
            margin: 5px 0 0 0;
            color: #666;
            font-size: 12px;
        }

        .info-section {
            margin-bottom: 30px;
            background: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #FF8C42;
        }

        .info-row {
            display: flex;
            margin-bottom: 8px;
            font-size: 13px;
        }

        .info-label {
            width: 150px;
            font-weight: bold;
            color: #333;
        }

        .info-value {
            flex: 1;
            color: #666;
        }

        .stats-section {
            margin-bottom: 30px;
        }

        .stats-title {
            font-size: 14px;
            font-weight: bold;
            color: #FF8C42;
            margin-bottom: 15px;
            border-bottom: 2px solid #FF8C42;
            padding-bottom: 8px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: white;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
        }

        .stat-label {
            font-size: 11px;
            color: #999;
            text-transform: uppercase;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .stat-value {
            font-size: 28px;
            font-weight: bold;
            color: #333;
        }

        .stat-card.hadir {
            border-color: #10b981;
            background: #f0fdf4;
        }

        .stat-card.hadir .stat-value {
            color: #10b981;
        }

        .stat-card.izin {
            border-color: #f59e0b;
            background: #fffbeb;
        }

        .stat-card.izin .stat-value {
            color: #f59e0b;
        }

        .stat-card.sakit {
            border-color: #06b6d4;
            background: #ecfeff;
        }

        .stat-card.sakit .stat-value {
            color: #06b6d4;
        }

        .stat-card.alpha {
            border-color: #ef4444;
            background: #fef2f2;
        }

        .stat-card.alpha .stat-value {
            color: #ef4444;
        }

        .stat-card.terlambat {
            border-color: #f97316;
            background: #fff7ed;
        }

        .stat-card.terlambat .stat-value {
            color: #f97316;
        }

        .attendance-section {
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #FF8C42;
            margin-bottom: 12px;
            border-bottom: 2px solid #FF8C42;
            padding-bottom: 8px;
        }

        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        .attendance-table thead {
            background: #FF8C42;
            color: white;
        }

        .attendance-table th {
            padding: 10px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #ddd;
        }

        .attendance-table td {
            padding: 10px;
            border: 1px solid #ddd;
        }

        .attendance-table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
        }

        .status-hadir {
            background: #d1fae5;
            color: #065f46;
        }

        .status-izin {
            background: #fef3c7;
            color: #92400e;
        }

        .status-sakit {
            background: #cffafe;
            color: #164e63;
        }

        .status-alpha {
            background: #fee2e2;
            color: #7f1d1d;
        }

        .status-terlambat {
            background: #ffedd5;
            color: #9a3412;
        }

        .status-telat {
            background: #fee2e2;
            color: #7f1d1d;
        }

        .empty-state {
            text-align: center;
            padding: 30px;
            color: #999;
            font-style: italic;
        }

        .footer {
            margin-top: 40px;
            border-top: 1px solid #ddd;
            padding-top: 15px;
            text-align: right;
            font-size: 11px;
            color: #666;
        }

        .footer-date {
            margin-bottom: 30px;
        }

        .signature {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 40px;
            margin-top: 40px;
        }

        .signature-box {
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-bottom: 8px;
            padding-top: 40px;
        }

        .signature-name {
            font-size: 11px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>📋 LAPORAN ABSENSI SISWA</h1>
            <p>Periode {{ $bulan }} {{ $tahun }} | PKL SMK Fatahillah</p>
        </div>

        <!-- Info Siswa -->
        <div class="info-section">
            <div class="info-row">
                <span class="info-label">Nama Siswa</span>
                <span class="info-value">{{ $siswa->nama }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">NIS</span>
                <span class="info-value">{{ $siswa->nis }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Kelas / Jurusan</span>
                <span class="info-value">{{ $siswa->kelas }} — {{ $siswa->jurusan }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Perusahaan</span>
                <span class="info-value">{{ $siswa->perusahaan?->nama_perusahaan ?? 'Belum ditentukan' }}</span>
            </div>
        </div>

        <!-- Statistik Ringkas -->
        <div class="stats-section">
            <div class="stats-title">📊 RINGKASAN STATISTIK</div>
            <div class="stats-grid">
                <div class="stat-card hadir">
                    <div class="stat-label">Hadir</div>
                    <div class="stat-value">{{ $totals['hadir'] }}</div>
                </div>
                <div class="stat-card izin">
                    <div class="stat-label">Izin</div>
                    <div class="stat-value">{{ $totals['izin'] }}</div>
                </div>
                <div class="stat-card sakit">
                    <div class="stat-label">Sakit</div>
                    <div class="stat-value">{{ $totals['sakit'] }}</div>
                </div>
                <div class="stat-card terlambat">
                    <div class="stat-label">Terlambat</div>
                    <div class="stat-value">{{ $totals['terlambat'] }}</div>
                </div>
                <div class="stat-card alpha">
                    <div class="stat-label">Alfa</div>
                    <div class="stat-value">{{ $totals['alpha'] }}</div>
                </div>
            </div>
        </div>

        <!-- Detail Absensi -->
        <div class="attendance-section">
            <div class="section-title">📅 DETAIL ABSENSI HARIAN</div>
            
            @if($absensi->count() > 0)
                <table class="attendance-table">
                    <thead>
                        <tr>
                            <th style="width: 12%;">Tanggal</th>
                            <th style="width: 12%;">Hari</th>
                            <th style="width: 15%;">Status</th>
                            <th style="width: 15%;">Jam Masuk</th>
                            <th style="width: 15%;">Jam Keluar</th>
                            <th style="width: 31%;">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($absensi as $item)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->tanggal)->locale('id')->isoFormat('dddd') }}</td>
                                <td>
                                    <span class="status-badge status-{{ $item->status }}">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </td>
                                <td>{{ $item->jam_masuk ?? '-' }}</td>
                                <td>{{ $item->jam_keluar ?? '-' }}</td>
                                <td>
                                    @if(in_array($item->status, ['hadir', 'terlambat'], true))
                                        @if($item->isTerlambat())
                                            <span class="status-badge status-telat">Telat</span>
                                        @else
                                            <span class="status-badge status-hadir">Tepat Waktu</span>
                                        @endif
                                    @else
                                        —
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    Tidak ada data absensi untuk periode {{ $bulan }} {{ $tahun }}
                </div>
            @endif
        </div>

        <!-- Footer & Signature -->
        <div class="footer">
            <div class="footer-date">
                {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
            </div>
            <div class="signature">
                <div class="signature-box">
                    <div>Mengetahui,</div>
                    <div style="margin-top: 10px; font-size: 12px; color: #666;">Admin</div>
                    <div class="signature-line"></div>
                    <div class="signature-name">Admin</div>
                </div>
                <div class="signature-box">
                    <div>Siswa,</div>
                    <div style="margin-top: 10px; font-size: 12px; color: #666;">{{ $siswa->nama }}</div>
                    <div class="signature-line"></div>
                    <div class="signature-name">{{ $siswa->nama }}</div>
                </div>
            </div>
        </div>

        <div style="text-align: center; margin-top: 30px; font-size: 10px; color: #999;">
            <p>Dokumen ini digenerate secara otomatis oleh sistem PKL SMK Fatahillah</p>
        </div>
    </div>
</body>
</html>
