<?php

namespace App\Http\Controllers\Pembimbing;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Absensi;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AbsensiController extends Controller
{
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = Auth::user();
        Absensi::ensureAutoAlphaForToday();
        
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
        $filterStatus = $request->get('status');
        $filterSiswa = $request->get('siswa');
        $filterTanggal = $request->get('tanggal');
        $filterApproval = $request->get('approval_status');

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

        if ($filterApproval) {
            $query->where('approval_status', $filterApproval);
        }

        $absensi = $query->orderBy('tanggal', 'desc')
            ->paginate(15)
            ->appends($request->query());

        return view('pembimbing.absensi.index', [
            'absensi' => $absensi,
            'siswaBimbingan' => $siswaBimbingan,
            'filterStatus' => $filterStatus,
            'filterSiswa' => $filterSiswa,
            'filterTanggal' => $filterTanggal,
            'filterApproval' => $filterApproval,
            'canApprove' => $user->role === 'pembimbing_perusahaan',
        ]);
    }

    public function approve(Request $request, Absensi $absensi): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user && $user->role === 'pembimbing_perusahaan', 403, 'Hanya pembimbing perusahaan yang dapat menyetujui absensi.');

        if ($absensi->approval_status === 'approved') {
            return back()->with('error', 'Absensi ini sudah disetujui sebelumnya.');
        }

        $absensi->update([
            'approval_status' => 'approved',
            'approved_by' => $user->id,
            'approved_at' => now(),
            'approval_notes' => $request->input('approval_notes'),
        ]);

        return back()->with('success', 'Absensi berhasil disetujui.');
    }

    public function reject(Request $request, Absensi $absensi): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user && $user->role === 'pembimbing_perusahaan', 403, 'Hanya pembimbing perusahaan yang dapat menolak absensi.');

        $validated = $request->validate([
            'approval_notes' => ['required', 'string', 'max:255'],
        ]);

        $absensi->update([
            'approval_status' => 'rejected',
            'approved_by' => $user->id,
            'approved_at' => now(),
            'approval_notes' => $validated['approval_notes'],
        ]);

        return back()->with('success', 'Absensi berhasil ditolak.');
    }

    public function downloadPdf(Request $request)
    {
        /** @var User|null $user */
        $user = Auth::user();
        abort_unless($user && $user->role === 'pembimbing_perusahaan', 403, 'Hanya pembimbing perusahaan yang dapat mengunduh laporan PDF.');

        $filterStatus = $request->get('status');
        $filterSiswa = $request->get('siswa');
        $filterTanggal = $request->get('tanggal');
        $filterApproval = $request->get('approval_status');

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

        if ($filterApproval) {
            $query->where('approval_status', $filterApproval);
        }

        $absensi = $query->orderBy('tanggal', 'asc')->get();

        // Hitung statistik
        $totals = ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'terlambat' => 0, 'alpha' => 0];
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
            'filterApproval' => $filterApproval,
            'namaBulan' => $namaBulan,
        ])
        ->setPaper('A4', 'landscape')
        ->setOption('isHtml5ParserEnabled', true)
        ->setOption('isRemoteEnabled', true);

        $filename = 'absensi-pembimbing-' . now()->format('Y-m-d-His') . '.pdf';

        return $pdf->download($filename);
    }
}
