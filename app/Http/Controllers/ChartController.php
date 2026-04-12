<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Siswa;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChartController extends Controller
{
    public function admin(): View
    {
        $data = $this->buildChartData();
        $monthly = $this->buildMonthlyData();

        return view('charts.admin', compact('data', 'monthly'));
    }

    public function siswa(Request $request): View
    {
        $siswa = $request->user()->siswa;
        abort_unless($siswa, 403, 'Akun siswa belum terhubung ke data siswa.');

        $data = $this->buildChartData($siswa->id);
        $monthly = $this->buildMonthlyData($siswa->id);

        return view('charts.siswa', compact('data', 'siswa', 'monthly'));
    }

    private function buildChartData(?int $siswaId = null): array
    {
        $query = Absensi::query();

        if ($siswaId) {
            $query->where('siswa_id', $siswaId);
        }

        $rekap = $query->selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');

        return [
            'labels' => ['Hadir', 'Izin', 'Sakit', 'Alpha'],
            'values' => [
                (int) $rekap->get('hadir', 0),
                (int) $rekap->get('izin', 0),
                (int) $rekap->get('sakit', 0),
                (int) $rekap->get('alpha', 0),
            ],
            'total_siswa' => Siswa::count(),
        ];
    }

    private function buildMonthlyData(?int $siswaId = null): array
    {
        $tahun = (int) now()->year;

        $query = Absensi::query()->whereYear('tanggal', $tahun);

        if ($siswaId) {
            $query->where('siswa_id', $siswaId);
        }

        $rows = $query->get(['tanggal', 'status']);

        $namaBulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $hadir = array_fill(0, 12, 0);
        $izin = array_fill(0, 12, 0);
        $sakit = array_fill(0, 12, 0);
        $alpha = array_fill(0, 12, 0);

        foreach ($rows as $row) {
            $idx = Carbon::parse($row->tanggal)->month - 1;
            match ($row->status) {
                'hadir' => $hadir[$idx]++,
                'izin' => $izin[$idx]++,
                'sakit' => $sakit[$idx]++,
                'alpha' => $alpha[$idx]++,
                default => null,
            };
        }

        return [
            'labels' => $namaBulan,
            'tahun' => $tahun,
            'hadir' => $hadir,
            'izin' => $izin,
            'sakit' => $sakit,
            'alpha' => $alpha,
        ];
    }
}
