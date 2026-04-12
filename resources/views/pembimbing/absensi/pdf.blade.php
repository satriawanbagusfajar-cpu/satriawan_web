<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Absensi</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .header h2 {
            font-size: 18px;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 11px;
            margin: 2px 0;
        }

        .filter-info {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f5f5f5;
            border-left: 3px solid #007bff;
            font-size: 11px;
        }

        .filter-info p {
            margin: 2px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        thead {
            background-color: #f0f0f0;
        }

        th {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
        }

        td {
            border: 1px solid #ccc;
            padding: 8px;
            font-size: 11px;
        }

        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .badge {
            display: inline-block;
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            text-align: center;
        }

        .badge-success {
            background-color: #28a745;
            color: white;
        }

        .badge-warning {
            background-color: #ffc107;
            color: black;
        }

        .badge-info {
            background-color: #17a2b8;
            color: white;
        }

        .badge-danger {
            background-color: #dc3545;
            color: white;
        }

        .summary {
            margin-top: 20px;
            padding: 10px;
            background-color: #f0f0f0;
            border: 1px solid #ddd;
        }

        .summary h4 {
            font-size: 12px;
            margin-bottom: 8px;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
        }

        .summary-item {
            padding: 8px;
            background-color: white;
            border: 1px solid #ddd;
            text-align: center;
        }

        .summary-item label {
            display: block;
            font-size: 10px;
            color: #666;
        }

        .summary-item .value {
            display: block;
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }

        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 11px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN ABSENSI SISWA</h2>
        <p>SMK Fatahillah</p>
    </div>

    @if($filterStatus || $filterSiswa || $filterTanggal)
        <div class="filter-info">
            <strong>Filter yang Diterapkan:</strong>
            @if($filterSiswa)
                <p>- Siswa: {{ $absensi->first()?->siswa->nama ?? 'N/A' }}</p>
            @endif
            @if($filterStatus)
                <p>- Status: {{ ucfirst($filterStatus) }}</p>
            @endif
            @if($filterTanggal)
                <p>- Tanggal: {{ \Carbon\Carbon::parse($filterTanggal)->format('d M Y') }}</p>
            @endif
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Nama Siswa</th>
                <th>NIS</th>
                <th>Jam Masuk</th>
                <th>Jam Keluar</th>
                <th>Status</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($absensi as $item)
                <tr>
                    <td>{{ $item->tanggal->format('d/m/Y') }}</td>
                    <td>{{ $item->siswa->nama }}</td>
                    <td>{{ $item->siswa->nis }}</td>
                    <td>{{ $item->jam_masuk ?? '-' }}</td>
                    <td>{{ $item->jam_keluar ?? '-' }}</td>
                    <td>
                        @if($item->status === 'hadir')
                            <span class="badge badge-success">HADIR</span>
                        @elseif($item->status === 'sakit')
                            <span class="badge badge-warning">SAKIT</span>
                        @elseif($item->status === 'izin')
                            <span class="badge badge-info">IZIN</span>
                        @else
                            <span class="badge badge-danger">ALFA</span>
                        @endif
                    </td>
                    <td>
                        @if($item->status === 'hadir')
                            {{ $item->isTerlambat() ? 'Telat' : 'Tepat Waktu' }}
                        @else
                            {{ ucfirst($item->status) }}
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center;">Tidak ada data absensi</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary">
        <h4>RINGKASAN ABSENSI</h4>
        <div class="summary-grid">
            <div class="summary-item">
                <label>Hadir</label>
                <span class="value" style="color: #28a745;">{{ $totals['hadir'] }}</span>
            </div>
            <div class="summary-item">
                <label>Sakit</label>
                <span class="value" style="color: #ffc107;">{{ $totals['sakit'] }}</span>
            </div>
            <div class="summary-item">
                <label>Izin</label>
                <span class="value" style="color: #17a2b8;">{{ $totals['izin'] }}</span>
            </div>
            <div class="summary-item">
                <label>Alpha</label>
                <span class="value" style="color: #dc3545;">{{ $totals['alpha'] }}</span>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
