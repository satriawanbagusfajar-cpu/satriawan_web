<?php

namespace App\Http\Controllers\Pembimbing;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Absensi;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AbsensiController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        
        // Ambil siswa yang dibimbing sesuai role
        if ($user->role === 'guru_pembimbing') {
            $siswaBimbingan = $user->siswaBimbingan()->with('perusahaan')->get();
        } else {
            $perusahaanIds = $user->perusahaanBimbingan()->pluck('id');
            $siswaBimbingan = Siswa::whereIn('perusahaan_id', $perusahaanIds)
                ->with('perusahaan')
                ->get();
        }

        // Filter dan pencarian
        $filterStatus = request()->get('status');
        $filterSiswa = request()->get('siswa');
        $filterTanggal = request()->get('tanggal');

        $query = Absensi::whereIn('siswa_id', $siswaBimbingan->pluck('id'))
            ->with('siswa');

        // Filter berdasarkan status
        if ($filterStatus) {
            $query->where('status', $filterStatus);
        }

        // Filter berdasarkan siswa
        if ($filterSiswa) {
            $query->where('siswa_id', $filterSiswa);
        }

        // Filter berdasarkan tanggal
        if ($filterTanggal) {
            $query->whereDate('tanggal', $filterTanggal);
        }

        $absensi = $query->orderBy('tanggal', 'desc')
            ->paginate(15)
            ->appends(request()->query());

        return view('pembimbing.absensi.index', [
            'absensi' => $absensi,
            'siswaBimbingan' => $siswaBimbingan,
            'filterStatus' => $filterStatus,
            'filterSiswa' => $filterSiswa,
            'filterTanggal' => $filterTanggal,
        ]);
    }

    public function downloadPdf()
    {
        $user = Auth::user();
        $filterStatus = request()->get('status');
        $filterSiswa = request()->get('siswa');
        $filterTanggal = request()->get('tanggal');

        // Ambil siswa yang dibimbing sesuai role
        if ($user->role === 'guru_pembimbing') {
            $siswaBimbingan = $user->siswaBimbingan()->pluck('id');
        } else {
            $perusahaanIds = $user->perusahaanBimbingan()->pluck('id');
            $siswaBimbingan = Siswa::whereIn('perusahaan_id', $perusahaanIds)->pluck('id');
        }

        $query = Absensi::whereIn('siswa_id', $siswaBimbingan)
            ->with('siswa');

        // Apply filters
        if ($filterStatus) {
            $query->where('status', $filterStatus);
        }

        if ($filterSiswa) {
            $query->where('siswa_id', $filterSiswa);
        }

        if ($filterTanggal) {
            $query->whereDate('tanggal', $filterTanggal);
        }

        $absensi = $query->orderBy('tanggal', 'asc')->get();

        // Hitung statistik
        $totals = ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alpha' => 0];
        foreach ($absensi as $a) {
            if (isset($totals[$a->status])) {
                $totals[$a->status]++;
            }
        }

        $namaBulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        $pdf = Pdf::loadView('pembimbing.absensi.pdf', [
            'absensi' => $absensi,
            'totals' => $totals,
            'filterSiswa' => $filterSiswa,
            'filterStatus' => $filterStatus,
            'filterTanggal' => $filterTanggal,
            'namaBulan' => $namaBulan,
        ])
        ->setPaper('A4', 'landscape')
        ->setOption('isHtml5ParserEnabled', true)
        ->setOption('isRemoteEnabled', true);

        $filename = 'absensi-pembimbing-' . now()->format('Y-m-d-His') . '.pdf';

        return $pdf->download($filename);
    }
}
