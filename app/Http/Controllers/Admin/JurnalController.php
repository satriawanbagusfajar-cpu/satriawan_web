<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jurnal;
use App\Models\Siswa;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class JurnalController extends Controller
{
    public function index(Request $request): View
    {
        $query = Jurnal::with('siswa.perusahaan')->latest('tanggal');

        if ($request->filled('siswa_id')) {
            $query->where('siswa_id', $request->integer('siswa_id'));
        }

        $jurnal = $query->paginate(12)->withQueryString();
        $siswa = Siswa::orderBy('nama')->get();

        return view('admin.jurnal.index', compact('jurnal', 'siswa'));
    }

    public function exportPdf(Request $request)
    {
        $siswaId = $request->integer('siswa_id');
        $bulan = $request->integer('bulan', (int) now()->month);
        $tahun = $request->integer('tahun', (int) now()->year);

        $siswa = Siswa::findOrFail($siswaId);

        $startDate = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $jurnal = Jurnal::where('siswa_id', $siswaId)
            ->whereBetween('tanggal', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderBy('tanggal')
            ->get();

        $namaBulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        $pdf = Pdf::loadView('admin.jurnal.pdf', [
            'siswa' => $siswa,
            'jurnal' => $jurnal,
            'bulan' => $namaBulan[$bulan],
            'tahun' => $tahun,
        ])
        ->setPaper('A4', 'portrait')
        ->setOption('isHtml5ParserEnabled', true)
        ->setOption('isRemoteEnabled', true);

        $filename = 'jurnal-' . Str::slug($siswa->nama) . '-' . strtolower($namaBulan[$bulan]) . '-' . $tahun . '.pdf';

        return $pdf->download($filename);
    }
}
