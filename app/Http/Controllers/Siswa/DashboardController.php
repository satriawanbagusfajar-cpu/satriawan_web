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

        $siswa->loadMissing('perusahaan.pembimbingPerusahaan');

        $stats = [
            'hadir' => Absensi::where('siswa_id', $siswa->id)->where('status', 'hadir')->count(),
            'izin' => Absensi::where('siswa_id', $siswa->id)->where('status', 'izin')->count(),
            'sakit' => Absensi::where('siswa_id', $siswa->id)->where('status', 'sakit')->count(),
            'alpha' => Absensi::where('siswa_id', $siswa->id)->where('status', 'alpha')->count(),
            'jurnal' => Jurnal::where('siswa_id', $siswa->id)->count(),
        ];

        $today = now()->toDateString();
        $todayAbsensi = Absensi::where('siswa_id', $siswa->id)
            ->whereDate('tanggal', $today)
            ->first();

        $todayJurnal = Jurnal::where('siswa_id', $siswa->id)
            ->whereDate('tanggal', $today)
            ->first();

        $notifications = [];
        $now = now();
        $checkinEnd = $now->copy()->setTime(7, 0, 0);
        $checkoutStart = $now->copy()->setTime(16, 0, 0);
        $accessEnd = $now->copy()->setTime(18, 0, 0);

        if ($now->lte($checkinEnd) && ! $todayAbsensi) {
            $notifications[] = [
                'type' => 'warning',
                'icon' => 'alarm',
                'message' => 'Ingat: lakukan absensi sebelum jam 07:00 agar tidak tercatat alpha.',
            ];
        }

        if ($now->gt($checkinEnd) && ! $todayAbsensi) {
            $notifications[] = [
                'type' => 'danger',
                'icon' => 'x-octagon',
                'message' => 'Batas check-in sudah lewat. Jika belum absen, status akan tercatat alpha.',
            ];
        }

        if ($todayAbsensi && ($todayAbsensi->approval_status ?? null) === 'pending') {
            $notifications[] = [
                'type' => 'info',
                'icon' => 'hourglass-split',
                'message' => 'Absensi hari ini masih menunggu persetujuan pembimbing perusahaan.',
            ];
        }

        if ($todayAbsensi && in_array($todayAbsensi->status, ['hadir', 'terlambat'], true) && ! $todayAbsensi->jam_keluar && $now->betweenIncluded($checkoutStart, $accessEnd)) {
            $notifications[] = [
                'type' => 'warning',
                'icon' => 'box-arrow-right',
                'message' => 'Jangan lupa check-out sebelum jam 18:00.',
            ];
        }

        if ($now->betweenIncluded($checkoutStart, $accessEnd) && ! $todayJurnal) {
            $notifications[] = [
                'type' => 'primary',
                'icon' => 'journal-plus',
                'message' => 'Isi jurnal harian hari ini sebelum jam 18:00.',
            ];
        }

        return view('siswa.dashboard', compact('siswa', 'stats', 'notifications'));
    }
}
