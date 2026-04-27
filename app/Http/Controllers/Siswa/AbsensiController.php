<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AbsensiController extends Controller
{
    private const CHECKIN_START = '00:00:00';
    private const CHECKIN_END = '07:00:00';
    private const CHECKOUT_START = '16:00:00';
    private const ACCESS_END = '18:00:00';

    private function buildTodayTime(string $time): Carbon
    {
        return now()->copy()->setTimeFromTimeString($time);
    }

    private function canCheckinNow(): bool
    {
        $now = now();

        return $now->betweenIncluded(
            $this->buildTodayTime(self::CHECKIN_START),
            $this->buildTodayTime(self::CHECKIN_END),
        );
    }

    private function canCheckoutNow(): bool
    {
        $now = now();

        return $now->betweenIncluded(
            $this->buildTodayTime(self::CHECKOUT_START),
            $this->buildTodayTime(self::ACCESS_END),
        );
    }

    private function isAutoAlphaRecord(Absensi $absensi): bool
    {
        if (! Absensi::hasColumn('approval_notes')) {
            return $absensi->status === 'alpha' && ! $absensi->jam_masuk && ! $absensi->jam_keluar;
        }

        return $absensi->status === 'alpha'
            && ! $absensi->jam_masuk
            && ! $absensi->jam_keluar
            && str_contains((string) $absensi->approval_notes, 'alpha otomatis');
    }

    public function index(Request $request): View|RedirectResponse
    {
        $siswa = $request->user()->siswa;
        abort_unless($siswa, 403, 'Akun siswa belum terhubung ke data siswa.');

        if (now()->gt($this->buildTodayTime(self::ACCESS_END))) {
            return redirect()->route('siswa.dashboard')
                ->with('error', 'Halaman absensi siswa hanya bisa diakses sampai jam 18:00.');
        }

        Absensi::ensureAutoAlphaForToday();

        $mingguIniMulai = now()->startOfWeek(Carbon::MONDAY)->startOfDay();
        $mingguIniSelesai = now()->endOfWeek(Carbon::SUNDAY)->endOfDay();

        $perPage = (int) $request->get('per_page', 5);
        $riwayat = Absensi::where('siswa_id', $siswa->id)
            ->orderByDesc('tanggal')
            ->paginate($perPage)
            ->withQueryString();

        $hariIni = Absensi::where('siswa_id', $siswa->id)->whereDate('tanggal', now()->toDateString())->first();

        $canCheckin = $this->canCheckinNow();
        $canCheckout = $this->canCheckoutNow();

        return view('siswa.absensi.index', compact('riwayat', 'hariIni', 'mingguIniMulai', 'mingguIniSelesai', 'canCheckin', 'canCheckout'));
    }

    /**
     * Check-in: siswa hadir, catat jam masuk.
     */
    public function checkin(Request $request): RedirectResponse
    {
        $siswa = $request->user()->siswa;
        abort_unless($siswa, 403);

        if (now()->gt($this->buildTodayTime(self::ACCESS_END))) {
            return redirect()->route('siswa.dashboard')
                ->with('error', 'Akses absensi siswa sudah ditutup setelah jam 18:00.');
        }

        if (! $this->canCheckinNow()) {
            return back()->with('error', 'Check-in hanya dapat dilakukan dari jam 00:00 sampai 07:00.');
        }

        $request->validate([
            'foto' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'lokasi' => ['required', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        $today = now()->toDateString();
        $nowTime = now()->format('H:i:s');
        $status = 'hadir';

        $existing = Absensi::where('siswa_id', $siswa->id)->whereDate('tanggal', $today)->first();
        if ($existing) {
            if (! $this->isAutoAlphaRecord($existing)) {
                return back()->with('error', 'Anda sudah melakukan absensi hari ini.');
            }
        }

        $relativeDir = 'absensi/' . $siswa->id;
        $targetDir = public_path('media/' . $relativeDir);

        if (! is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $filename = Str::random(40) . '.' . $request->file('foto')->getClientOriginalExtension();
        $request->file('foto')->move($targetDir, $filename);
        $path = $relativeDir . '/' . $filename;

        $payload = [
            'siswa_id' => $siswa->id,
            'tanggal' => $today,
            'status' => $status,
            'jam_masuk' => $nowTime,
            'jam_keluar' => null,
            'foto' => $path,
            'lokasi' => $request->string('lokasi')->toString(),
            'latitude' => $request->filled('latitude') ? (float) $request->input('latitude') : null,
            'longitude' => $request->filled('longitude') ? (float) $request->input('longitude') : null,
            'approval_status' => 'pending',
            'approved_by' => null,
            'approved_at' => null,
            'approval_notes' => null,
        ];

        $payload = Absensi::keepExistingColumns($payload);

        if ($existing && $this->isAutoAlphaRecord($existing)) {
            $existing->update($payload);
        } else {
            Absensi::create($payload);
        }

        return back()->with('success', 'Check-in berhasil! Jam masuk: ' . $nowTime);
    }

    /**
     * Check-out: catat jam keluar.
     */
    public function checkout(Request $request): RedirectResponse
    {
        $siswa = $request->user()->siswa;
        abort_unless($siswa, 403);

        if (now()->gt($this->buildTodayTime(self::ACCESS_END))) {
            return redirect()->route('siswa.dashboard')
                ->with('error', 'Akses absensi siswa sudah ditutup setelah jam 18:00.');
        }

        if (now()->lt($this->buildTodayTime(self::CHECKOUT_START))) {
            return back()->with('error', 'Check-out hanya bisa dilakukan mulai jam 16:00.');
        }

        if (! $this->canCheckoutNow()) {
            return back()->with('error', 'Check-out ditutup setelah jam 18:00.');
        }

        $today = now()->toDateString();
        $nowTime = now()->format('H:i:s');

        $absensi = Absensi::where('siswa_id', $siswa->id)->whereDate('tanggal', $today)->first();

        if (! $absensi || ! in_array($absensi->status, ['hadir', 'terlambat'], true)) {
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

        if (now()->gt($this->buildTodayTime(self::ACCESS_END))) {
            return redirect()->route('siswa.dashboard')
                ->with('error', 'Akses absensi siswa sudah ditutup setelah jam 18:00.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:izin,sakit'],
        ]);

        $today = now()->toDateString();

        $existing = Absensi::where('siswa_id', $siswa->id)->whereDate('tanggal', $today)->first();
        if ($existing) {
            if (! $this->isAutoAlphaRecord($existing)) {
                return back()->with('error', 'Anda sudah melakukan absensi hari ini.');
            }
        }

        $payload = [
            'siswa_id' => $siswa->id,
            'tanggal' => $today,
            'status' => $validated['status'],
            'jam_masuk' => null,
            'jam_keluar' => null,
            'foto' => null,
            'lokasi' => null,
            'latitude' => null,
            'longitude' => null,
            'approval_status' => 'pending',
            'approved_by' => null,
            'approved_at' => null,
            'approval_notes' => null,
        ];

        $payload = Absensi::keepExistingColumns($payload);

        if ($existing && $this->isAutoAlphaRecord($existing)) {
            $existing->update($payload);
        } else {
            Absensi::create($payload);
        }

        return back()->with('success', 'Status ' . $validated['status'] . ' berhasil dicatat untuk hari ini.');
    }
}
