<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Jurnal;
use App\Models\Perusahaan;
use App\Models\Siswa;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = Carbon::today()->toDateString();

        $todayAbsensiQuery = Absensi::with('siswa.perusahaan')
            ->whereDate('tanggal', $today)
            ->orderBy('status')
            ->orderBy('jam_masuk')
            ->orderBy('created_at');

        $todayStats = [
            'hadir' => (clone $todayAbsensiQuery)->where('status', 'hadir')->count(),
            'izin' => (clone $todayAbsensiQuery)->where('status', 'izin')->count(),
            'sakit' => (clone $todayAbsensiQuery)->where('status', 'sakit')->count(),
            'alpha' => (clone $todayAbsensiQuery)->where('status', 'alpha')->count(),
        ];

        $todayAbsensi = $todayAbsensiQuery->paginate(8)->withQueryString();

        $stats = [
            'total_siswa' => Siswa::count(),
            'total_perusahaan' => Perusahaan::count(),
            'total_jurnal' => Jurnal::count(),
            'hadir' => $todayStats['hadir'],
            'izin' => $todayStats['izin'],
            'sakit' => $todayStats['sakit'],
            'alpha' => $todayStats['alpha'],
            'total_hari_ini' => array_sum($todayStats),
        ];

        return view('admin.dashboard', compact('stats', 'todayAbsensi'));
    }
}
