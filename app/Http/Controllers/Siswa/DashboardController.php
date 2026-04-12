<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Jurnal;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $siswa = $request->user()->siswa;

        abort_unless($siswa, 403, 'Akun siswa belum terhubung ke data siswa.');

        $stats = [
            'hadir' => Absensi::where('siswa_id', $siswa->id)->where('status', 'hadir')->count(),
            'izin' => Absensi::where('siswa_id', $siswa->id)->where('status', 'izin')->count(),
            'sakit' => Absensi::where('siswa_id', $siswa->id)->where('status', 'sakit')->count(),
            'alpha' => Absensi::where('siswa_id', $siswa->id)->where('status', 'alpha')->count(),
            'jurnal' => Jurnal::where('siswa_id', $siswa->id)->count(),
        ];

        return view('siswa.dashboard', compact('siswa', 'stats'));
    }
}
