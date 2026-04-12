<?php

namespace App\Http\Controllers\Pembimbing;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Absensi;
use App\Models\Jurnal;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        
        // Get siswa yang dibimbing sesuai dengan role
        if ($user->role === 'guru_pembimbing') {
            // Guru pembimbing - ambil siswa dari siswaBimbingan
            $siswaBimbingan = $user->siswaBimbingan()->with('perusahaan')->get();
        } else {
            // Pembimbing perusahaan - ambil siswa dari perusahaan yang dibimbing
            $perusahaanIds = $user->perusahaanBimbingan()->pluck('id');
            $siswaBimbingan = Siswa::whereIn('perusahaan_id', $perusahaanIds)
                ->with('perusahaan')
                ->get();
        }

        $totalSiswa = $siswaBimbingan->count();
        $totalAbsensi = Absensi::whereIn('siswa_id', $siswaBimbingan->pluck('id'))->count();
        $totalJurnal = Jurnal::whereIn('siswa_id', $siswaBimbingan->pluck('id'))->count();

        return view('pembimbing.dashboard', [
            'siswaBimbingan' => $siswaBimbingan,
            'totalSiswa' => $totalSiswa,
            'totalAbsensi' => $totalAbsensi,
            'totalJurnal' => $totalJurnal,
        ]);
    }
}
