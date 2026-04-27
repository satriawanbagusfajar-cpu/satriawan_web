<?php

namespace App\Http\Controllers\Pembimbing;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Jurnal;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class JurnalController extends Controller
{
    public function index(Request $request): View
    {
        /** @var User $user */
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
        $filterSiswa = $request->get('siswa');
        $filterTanggal = $request->get('tanggal');
        $filterBulan = $request->get('bulan');
        $filterApproval = $request->get('approval_status');

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

        if ($filterApproval) {
            $query->where('approval_status', $filterApproval);
        }

        $jurnal = $query->orderBy('tanggal', 'desc')
            ->paginate(15)
            ->appends($request->query());

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
            'filterApproval' => $filterApproval,
            'canApprove' => $user->role === 'pembimbing_perusahaan',
        ]);
    }

    public function approve(Request $request, Jurnal $jurnal): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user && $user->role === 'pembimbing_perusahaan', 403, 'Hanya pembimbing perusahaan yang dapat menyetujui jurnal.');

        if ($jurnal->approval_status === 'approved') {
            return back()->with('error', 'Jurnal ini sudah disetujui sebelumnya.');
        }

        $jurnal->update([
            'approval_status' => 'approved',
            'approved_by' => $user->id,
            'approved_at' => now(),
            'approval_notes' => $request->input('approval_notes'),
        ]);

        return back()->with('success', 'Jurnal berhasil disetujui.');
    }

    public function reject(Request $request, Jurnal $jurnal): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user && $user->role === 'pembimbing_perusahaan', 403, 'Hanya pembimbing perusahaan yang dapat menolak jurnal.');

        $validated = $request->validate([
            'approval_notes' => ['required', 'string', 'max:255'],
        ]);

        $jurnal->update([
            'approval_status' => 'rejected',
            'approved_by' => $user->id,
            'approved_at' => now(),
            'approval_notes' => $validated['approval_notes'],
        ]);

        return back()->with('success', 'Jurnal berhasil ditolak.');
    }

    public function downloadPdf(Request $request)
    {
        /** @var User|null $user */
        $user = Auth::user();
        abort_unless($user && $user->role === 'pembimbing_perusahaan', 403, 'Hanya pembimbing perusahaan yang dapat mengunduh laporan PDF.');

        $filterSiswa = $request->get('siswa');
        $filterTanggal = $request->get('tanggal');
        $filterBulan = $request->get('bulan');
        $filterApproval = $request->get('approval_status');

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

        if ($filterApproval) {
            $query->where('approval_status', $filterApproval);
        }

        $jurnal = $query->orderBy('tanggal', 'asc')->get();

        $namaBulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        $pdf = Pdf::loadView('pembimbing.jurnal.pdf', [
            'jurnal' => $jurnal,
            'filterSiswa' => $filterSiswa,
            'filterTanggal' => $filterTanggal,
            'filterBulan' => $filterBulan,
            'filterApproval' => $filterApproval,
            'namaBulan' => $namaBulan,
        ])
        ->setPaper('A4', 'portrait')
        ->setOption('isHtml5ParserEnabled', true)
        ->setOption('isRemoteEnabled', true);

        $filename = 'jurnal-pembimbing-' . now()->format('Y-m-d-His') . '.pdf';

        return $pdf->download($filename);
    }
}
