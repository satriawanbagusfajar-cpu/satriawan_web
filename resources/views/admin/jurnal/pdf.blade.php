<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Jurnal Kegiatan</title>
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
            background: #ecfdf5;
            padding: 15px;
            border-radius: 8px;
            border: 2px solid #10b981;
        }

        .stats-title {
            font-size: 14px;
            font-weight: bold;
            color: #059669;
            margin-bottom: 10px;
        }

        .stats-value {
            font-size: 28px;
            font-weight: bold;
            color: #10b981;
        }

        .jurnal-section {
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

        .jurnal-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        .jurnal-table thead {
            background: #FF8C42;
            color: white;
        }

        .jurnal-table th {
            padding: 10px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #ddd;
        }

        .jurnal-table td {
            padding: 10px;
            border: 1px solid #ddd;
            vertical-align: top;
        }

        .jurnal-table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }

        .no-column {
            width: 5%;
            text-align: center;
        }

        .tanggal-column {
            width: 12%;
        }

        .kegiatan-column {
            width: 30%;
        }

        .keterangan-column {
            width: 53%;
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

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>📔 LAPORAN JURNAL KEGIATAN</h1>
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

        <!-- Statistik -->
        <div class="stats-section">
            <div class="stats-title">📊 TOTAL KEGIATAN TERCATAT</div>
            <div class="stats-value">{{ $jurnal->count() }} Kegiatan</div>
        </div>

        <!-- Detail Jurnal -->
        <div class="jurnal-section">
            <div class="section-title">📅 DAFTAR KEGIATAN HARIAN</div>
            
            @if($jurnal->count() > 0)
                <table class="jurnal-table">
                    <thead>
                        <tr>
                            <th class="no-column">No</th>
                            <th class="tanggal-column">Tanggal</th>
                            <th class="kegiatan-column">Kegiatan</th>
                            <th class="keterangan-column">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($jurnal as $index => $item)
                            <tr>
                                <td class="no-column">{{ $index + 1 }}</td>
                                <td class="tanggal-column">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                                <td class="kegiatan-column"><strong>{{ $item->kegiatan }}</strong></td>
                                <td class="keterangan-column">{{ $item->keterangan }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    Tidak ada catatan jurnal untuk periode {{ $bulan }} {{ $tahun }}
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
