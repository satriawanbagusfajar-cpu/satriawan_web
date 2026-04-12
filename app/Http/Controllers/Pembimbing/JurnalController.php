<?php

namespace App\Http\Controllers\Pembimbing;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Jurnal;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class JurnalController extends Controller
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
        $filterSiswa = request()->get('siswa');
        $filterTanggal = request()->get('tanggal');
        $filterBulan = request()->get('bulan');

        $query = Jurnal::whereIn('siswa_id', $siswaBimbingan->pluck('id'))
            ->with('siswa');

        // Filter berdasarkan siswa
        if ($filterSiswa) {
            $query->where('siswa_id', $filterSiswa);
        }

        // Filter berdasarkan tanggal
        if ($filterTanggal) {
            $query->whereDate('tanggal', $filterTanggal);
        }

        // Filter berdasarkan bulan
        if ($filterBulan) {
            $query->whereMonth('tanggal', date('m', strtotime($filterBulan)))
                  ->whereYear('tanggal', date('Y', strtotime($filterBulan)));
        }

        $jurnal = $query->orderBy('tanggal', 'desc')
            ->paginate(15)
            ->appends(request()->query());

        // Hitung statistik jurnal per siswa
        $jurnalStatistics = Jurnal::whereIn('siswa_id', $siswaBimbingan->pluck('id'))
            ->selectRaw('siswa_id, COUNT(*) as count')
            ->groupBy('siswa_id')
            ->pluck('count', 'siswa_id');

        return view('pembimbing.jurnal.index', [
            'jurnal' => $jurnal,
            'siswaBimbingan' => $siswaBimbingan,
            'jurnalStatistics' => $jurnalStatistics,
            'filterSiswa' => $filterSiswa,
            'filterTanggal' => $filterTanggal,
            'filterBulan' => $filterBulan,
        ]);
    }

    public function downloadPdf()
    {
        $user = Auth::user();
        $filterSiswa = request()->get('siswa');
        $filterTanggal = request()->get('tanggal');
        $filterBulan = request()->get('bulan');

        // Ambil siswa yang dibimbing sesuai role
        if ($user->role === 'guru_pembimbing') {
            $siswaBimbingan = $user->siswaBimbingan()->pluck('id');
        } else {
            $perusahaanIds = $user->perusahaanBimbingan()->pluck('id');
            $siswaBimbingan = Siswa::whereIn('perusahaan_id', $perusahaanIds)->pluck('id');
        }

        $query = Jurnal::whereIn('siswa_id', $siswaBimbingan)
            ->with('siswa');

        // Apply filters
        if ($filterSiswa) {
            $query->where('siswa_id', $filterSiswa);
        }

        if ($filterTanggal) {
            $query->whereDate('tanggal', $filterTanggal);
        }

        if ($filterBulan) {
            $query->whereMonth('tanggal', date('m', strtotime($filterBulan)))
                  ->whereYear('tanggal', date('Y', strtotime($filterBulan)));
        }

        $jurnal = $query->orderBy('tanggal', 'asc')->get();

        $namaBulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        $pdf = Pdf::loadView('pembimbing.jurnal.pdf', [
            'jurnal' => $jurnal,
            'filterSiswa' => $filterSiswa,
            'filterTanggal' => $filterTanggal,
            'filterBulan' => $filterBulan,
            'namaBulan' => $namaBulan,
        ])
        ->setPaper('A4', 'portrait')
        ->setOption('isHtml5ParserEnabled', true)
        ->setOption('isRemoteEnabled', true);

        $filename = 'jurnal-pembimbing-' . now()->format('Y-m-d-His') . '.pdf';

        return $pdf->download($filename);
    }
}
