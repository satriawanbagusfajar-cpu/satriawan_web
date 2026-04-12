<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Jurnal</title>
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

        .jurnal-entry {
            margin-bottom: 15px;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            page-break-inside: avoid;
        }

        .jurnal-entry-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 1px solid #eee;
        }

        .jurnal-date {
            background-color: #007bff;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
        }

        .jurnal-siswa {
            font-weight: bold;
            font-size: 12px;
        }

        .jurnal-kegiatan {
            font-weight: bold;
            color: #333;
            margin-bottom: 6px;
            font-size: 12px;
        }

        .jurnal-keterangan {
            color: #666;
            line-height: 1.5;
            font-size: 11px;
            text-align: justify;
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

        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary-table th,
        .summary-table td {
            border: 1px solid #ddd;
            padding: 6px;
            font-size: 11px;
        }

        .summary-table th {
            background-color: #e0e0e0;
            font-weight: bold;
        }

        .summary-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
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
        <h2>LAPORAN JURNAL KEGIATAN SISWA</h2>
        <p>SMK Fatahillah</p>
    </div>

    @if($filterSiswa || $filterTanggal || $filterBulan)
        <div class="filter-info">
            <strong>Filter yang Diterapkan:</strong>
            @if($filterSiswa)
                <p>- Siswa: {{ $jurnal->first()?->siswa->nama ?? 'N/A' }}</p>
            @endif
            @if($filterTanggal)
                <p>- Tanggal: {{ \Carbon\Carbon::parse($filterTanggal)->format('d M Y') }}</p>
            @endif
            @if($filterBulan)
                <p>- Bulan: {{ \Carbon\Carbon::parse($filterBulan)->format('M Y') }}</p>
            @endif
        </div>
    @endif

    @forelse($jurnal as $item)
        <div class="jurnal-entry">
            <div class="jurnal-entry-header">
                <span class="jurnal-date">{{ $item->tanggal->format('d/m/Y') }}</span>
                <span class="jurnal-siswa">{{ $item->siswa->nama }} ({{ $item->siswa->nis }})</span>
            </div>
            <div class="jurnal-kegiatan">{{ $item->kegiatan }}</div>
            <div class="jurnal-keterangan">{{ nl2br($item->keterangan) }}</div>
        </div>
    @empty
        <div style="text-align: center; padding: 20px; color: #999;">
            <p>Tidak ada data jurnal</p>
        </div>
    @endforelse

    @if($jurnal->count() > 0)
        <div class="summary">
            <h4>STATISTIK JURNAL</h4>
            <table class="summary-table">
                <thead>
                    <tr>
                        <th>Nama Siswa</th>
                        <th>NIS</th>
                        <th>Total Jurnal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jurnal->groupBy('siswa_id') as $siswaEntries)
                        <tr>
                            <td>{{ $siswaEntries->first()->siswa->nama }}</td>
                            <td>{{ $siswaEntries->first()->siswa->nis }}</td>
                            <td>{{ $siswaEntries->count() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
