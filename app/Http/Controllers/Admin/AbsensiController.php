<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Perusahaan;
use App\Models\Siswa;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AbsensiController extends Controller
{
    private function buildRekapData(Request $request): array
    {
        $bulan = $request->integer('bulan', (int) now()->month);
        $tahun = $request->integer('tahun', (int) now()->year);
        $siswaFilter = $request->integer('siswa_id');

        $startDate = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        $daysInMonth = $startDate->daysInMonth;

        $dates = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $dates[] = Carbon::create($tahun, $bulan, $d)->toDateString();
        }

        $siswaQuery = Siswa::orderBy('nama');
        if ($siswaFilter) {
            $siswaQuery->where('id', $siswaFilter);
        }
        $siswaList = $siswaQuery->get();

        $absensiQuery = Absensi::whereBetween('tanggal', [$startDate->toDateString(), $endDate->toDateString()]);
        if ($siswaFilter) {
            $absensiQuery->where('siswa_id', $siswaFilter);
        }
        $absensiData = $absensiQuery->get()->groupBy('siswa_id');

        $rekapRows = [];
        foreach ($siswaList as $s) {
            $siswaAbsensi = $absensiData->get($s->id, collect());
            $byDate = $siswaAbsensi->keyBy(fn ($a) => Carbon::parse($a->tanggal)->toDateString());

            $harian = [];
            $totals = ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'terlambat' => 0, 'alpha' => 0];
            foreach ($dates as $date) {
                $status = $byDate->has($date) ? $byDate[$date]->status : null;
                $harian[$date] = $status;
                if ($status && isset($totals[$status])) {
                    $totals[$status]++;
                }
            }

            $rekapRows[] = [
                'siswa' => $s,
                'harian' => $harian,
                'totals' => $totals,
            ];
        }

        $grandTotal = ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'terlambat' => 0, 'alpha' => 0];
        foreach ($rekapRows as $r) {
            foreach ($grandTotal as $key => &$val) {
                $val += $r['totals'][$key];
            }
        }

        return compact('bulan', 'tahun', 'siswaFilter', 'dates', 'daysInMonth', 'rekapRows', 'grandTotal');
    }

    public function index(Request $request): View
    {
        Absensi::ensureAutoAlphaForToday();

        $query = Absensi::with('siswa.perusahaan')->latest('tanggal');

        if ($request->filled('siswa_id')) {
            $query->where('siswa_id', $request->integer('siswa_id'));
        }

        if ($request->filled('perusahaan_id')) {
            $perusahaanId = $request->integer('perusahaan_id');
            $query->whereHas('siswa', function ($q) use ($perusahaanId): void {
                $q->where('perusahaan_id', $perusahaanId);
            });
        }

        $perPage = (int) $request->get('per_page', 15);
        $absensi = $query->paginate($perPage)->withQueryString();
        $siswa = Siswa::orderBy('nama')->get();
        $perusahaan = Perusahaan::orderBy('nama_perusahaan')->get();

        return view('admin.absensi.index', compact('absensi', 'siswa', 'perusahaan'));
    }

    public function rekap(Request $request): View
    {
        $data = $this->buildRekapData($request);
        $bulan = $data['bulan'];
        $tahun = $data['tahun'];
        $siswaFilter = $data['siswaFilter'];
        $dates = $data['dates'];
        $daysInMonth = $data['daysInMonth'];
        $grandTotal = $data['grandTotal'];
        $rekapRows = $data['rekapRows'];

        $perPage = 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = collect($rekapRows)->forPage($currentPage, $perPage)->values();

        $rekap = new LengthAwarePaginator(
            $currentItems,
            count($rekapRows),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        $allSiswa = Siswa::orderBy('nama')->get();

        return view('admin.absensi.rekap', compact(
            'rekap', 'dates', 'bulan', 'tahun', 'grandTotal', 'allSiswa', 'siswaFilter', 'daysInMonth'
        ));
    }

    public function downloadRekap(Request $request)
    {
        $data = $this->buildRekapData($request);
        $bulan = $data['bulan'];
        $tahun = $data['tahun'];
        $dates = $data['dates'];
        $rekapRows = $data['rekapRows'];
        $siswaFilter = $data['siswaFilter'];

        $namaBulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $filename = 'rekap-absensi-' . strtolower($namaBulan[$bulan]) . '-' . $tahun;
        if ($siswaFilter) {
            $filename .= '-siswa-' . $siswaFilter;
        }
        $filename .= '.csv';

        return response()->streamDownload(function () use ($dates, $rekapRows): void {
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

            $header = ['Nama Siswa', 'NIS', 'Kelas', 'Jurusan'];
            foreach ($dates as $date) {
                $header[] = Carbon::parse($date)->format('d/m');
            }
            $header[] = 'Total Hadir';
            $header[] = 'Total Izin';
            $header[] = 'Total Sakit';
            $header[] = 'Total Terlambat';
            $header[] = 'Total Alpha';
            fputcsv($output, $header);

            foreach ($rekapRows as $row) {
                $csvRow = [
                    $row['siswa']->nama,
                    $row['siswa']->nis,
                    $row['siswa']->kelas,
                    $row['siswa']->jurusan,
                ];

                foreach ($dates as $date) {
                    $status = $row['harian'][$date];
                    $csvRow[] = match ($status) {
                        'hadir' => 'H',
                        'izin' => 'I',
                        'sakit' => 'S',
                        'terlambat' => 'T',
                        'alpha' => 'A',
                        default => '-',
                    };
                }

                $csvRow[] = $row['totals']['hadir'];
                $csvRow[] = $row['totals']['izin'];
                $csvRow[] = $row['totals']['sakit'];
                $csvRow[] = $row['totals']['terlambat'];
                $csvRow[] = $row['totals']['alpha'];
                fputcsv($output, $csvRow);
            }

            fclose($output);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exportPdf(Request $request)
    {
        $siswaId = $request->integer('siswa_id');
        $bulan = $request->integer('bulan', (int) now()->month);
        $tahun = $request->integer('tahun', (int) now()->year);

        $siswa = Siswa::findOrFail($siswaId);

        $startDate = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $absensi = Absensi::where('siswa_id', $siswaId)
            ->whereBetween('tanggal', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderBy('tanggal')
            ->get();

        $totals = ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'terlambat' => 0, 'alpha' => 0];
        foreach ($absensi as $a) {
            if (isset($totals[$a->status])) {
                $totals[$a->status]++;
            }
        }

        $namaBulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        $pdf = Pdf::loadView('admin.absensi.pdf', [
            'siswa' => $siswa,
            'absensi' => $absensi,
            'totals' => $totals,
            'bulan' => $namaBulan[$bulan],
            'tahun' => $tahun,
        ])
        ->setPaper('A4', 'portrait')
        ->setOption('isHtml5ParserEnabled', true)
        ->setOption('isRemoteEnabled', true);

        $filename = 'absensi-' . Str::slug($siswa->nama) . '-' . strtolower($namaBulan[$bulan]) . '-' . $tahun . '.pdf';

        return $pdf->download($filename);
    }
}
