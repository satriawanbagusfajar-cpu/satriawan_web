<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AbsensiController extends Controller
{
    public function index(Request $request): View
    {
        $siswa = $request->user()->siswa;
        abort_unless($siswa, 403, 'Akun siswa belum terhubung ke data siswa.');

        $mingguIniMulai = now()->startOfWeek(Carbon::MONDAY)->startOfDay();
        $mingguIniSelesai = now()->endOfWeek(Carbon::SUNDAY)->endOfDay();

        // Auto checkout setelah jam 16:00
        $this->autoCheckout($siswa);

        $perPage = (int) $request->get('per_page', 5);
        $riwayat = Absensi::where('siswa_id', $siswa->id)
            ->orderByDesc('tanggal')
            ->paginate($perPage)
            ->withQueryString();

        $hariIni = Absensi::where('siswa_id', $siswa->id)->whereDate('tanggal', now()->toDateString())->first();

        return view('siswa.absensi.index', compact('riwayat', 'hariIni', 'mingguIniMulai', 'mingguIniSelesai'));
    }

    /**
     * Auto checkout setelah jam kerja (16:00)
     */
    private function autoCheckout($siswa): void
    {
        $today = now()->toDateString();
        $currentTime = now();
        $workEndTime = $currentTime->copy()->setTime(16, 0, 0);

        // Cek apakah sudah lewat jam 16:00
        if ($currentTime->greaterThanOrEqualTo($workEndTime)) {
            $absensi = Absensi::where('siswa_id', $siswa->id)
                ->whereDate('tanggal', $today)
                ->first();

            // Jika sudah checkin dan belum checkout, maka auto checkout
            if ($absensi && $absensi->status === 'hadir' && $absensi->jam_masuk && !$absensi->jam_keluar) {
                $absensi->update(['jam_keluar' => now()->format('H:i:s')]);
                session()->flash('auto_checkout', 'Check-out otomatis: Anda sudah melewati jam kerja (16:00), sistem melakukan check-out otomatis.');
            }
        }
    }

    /**
     * Check-in: siswa hadir, catat jam masuk.
     */
    public function checkin(Request $request): RedirectResponse
    {
        $siswa = $request->user()->siswa;
        abort_unless($siswa, 403);

        $request->validate([
            'foto' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $today = now()->toDateString();
        $nowTime = now()->format('H:i:s');

        $existing = Absensi::where('siswa_id', $siswa->id)->whereDate('tanggal', $today)->first();
        if ($existing) {
            return back()->with('error', 'Anda sudah melakukan absensi hari ini.');
        }

        $path = $request->file('foto')->store('absensi/' . $siswa->id, 'public');

        Absensi::create([
            'siswa_id' => $siswa->id,
            'tanggal' => $today,
            'status' => 'hadir',
            'jam_masuk' => $nowTime,
            'foto' => $path,
        ]);

        return back()->with('success', 'Check-in berhasil! Jam masuk: ' . $nowTime);
    }

    /**
     * Check-out: catat jam keluar.
     */
    public function checkout(Request $request): RedirectResponse
    {
        $siswa = $request->user()->siswa;
        abort_unless($siswa, 403);

        $today = now()->toDateString();
        $nowTime = now()->format('H:i:s');

        $absensi = Absensi::where('siswa_id', $siswa->id)->whereDate('tanggal', $today)->first();

        if (! $absensi || $absensi->status !== 'hadir') {
            return back()->with('error', 'Anda belum check-in hari ini.');
        }

        if ($absensi->jam_keluar) {
            return back()->with('error', 'Anda sudah check-out hari ini.');
        }

        $absensi->update(['jam_keluar' => $nowTime]);

        return back()->with('success', 'Check-out berhasil! Jam keluar: ' . $nowTime);
    }

    /**
     * Izin / Sakit: catat status tanpa jam masuk/keluar.
     */
    public function izin(Request $request): RedirectResponse
    {
        $siswa = $request->user()->siswa;
        abort_unless($siswa, 403);

        $validated = $request->validate([
            'status' => ['required', 'in:izin,sakit'],
        ]);

        $today = now()->toDateString();

        $existing = Absensi::where('siswa_id', $siswa->id)->whereDate('tanggal', $today)->first();
        if ($existing) {
            return back()->with('error', 'Anda sudah melakukan absensi hari ini.');
        }

        Absensi::create([
            'siswa_id' => $siswa->id,
            'tanggal' => $today,
            'status' => $validated['status'],
            'jam_masuk' => null,
            'jam_keluar' => null,
        ]);

        return back()->with('success', 'Status ' . $validated['status'] . ' berhasil dicatat untuk hari ini.');
    }
}
