<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Dokumentasi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DokumentasiController extends Controller
{
    public function index(Request $request): View
    {
        $siswa = $request->user()->siswa;
        abort_unless($siswa, 403, 'Akun siswa belum terhubung ke data siswa.');

        $perPage = (int) $request->get('per_page', 12);
        $riwayat = Dokumentasi::where('siswa_id', $siswa->id)->latest('tanggal')->paginate($perPage)->withQueryString();

        return view('siswa.dokumentasi.index', compact('riwayat'));
    }

    public function store(Request $request): RedirectResponse
    {
        $siswa = $request->user()->siswa;
        abort_unless($siswa, 403);

        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'foto' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'keterangan' => ['nullable', 'string', 'max:500'],
        ]);

        $relativeDir = 'dokumentasi/' . $siswa->id;
        $targetDir = public_path('media/' . $relativeDir);

        if (! is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $filename = Str::random(40) . '.' . $request->file('foto')->getClientOriginalExtension();
        $request->file('foto')->move($targetDir, $filename);
        $path = $relativeDir . '/' . $filename;

        Dokumentasi::create([
            'siswa_id' => $siswa->id,
            'tanggal' => $validated['tanggal'],
            'foto' => $path,
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        return back()->with('success', 'Dokumentasi berhasil diupload.');
    }

    public function destroy(Request $request, Dokumentasi $dokumentasi): RedirectResponse
    {
        $siswa = $request->user()->siswa;
        abort_unless($siswa && $dokumentasi->siswa_id === $siswa->id, 403);

        $dokumentasi->delete();

        return back()->with('success', 'Dokumentasi berhasil dihapus.');
    }
}
