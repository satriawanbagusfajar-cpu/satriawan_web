<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Jurnal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JurnalController extends Controller
{
    public function index(Request $request): View
    {
        $siswa = $request->user()->siswa;
        abort_unless($siswa, 403, 'Akun siswa belum terhubung ke data siswa.');

        $perPage = (int) $request->get('per_page', 10);
        $riwayat = Jurnal::where('siswa_id', $siswa->id)->latest('tanggal')->paginate($perPage)->withQueryString();

        return view('siswa.jurnal.index', compact('riwayat'));
    }

    public function store(Request $request): RedirectResponse
    {
        $siswa = $request->user()->siswa;
        abort_unless($siswa, 403, 'Akun siswa belum terhubung ke data siswa.');

        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'kegiatan' => ['required', 'string'],
            'keterangan' => ['nullable', 'string'],
        ]);

        Jurnal::updateOrCreate(
            ['siswa_id' => $siswa->id, 'tanggal' => $validated['tanggal']],
            [
                'kegiatan' => $validated['kegiatan'],
                'keterangan' => $validated['keterangan'] ?? null,
                'approval_status' => 'pending',
                'approved_by' => null,
                'approved_at' => null,
                'approval_notes' => null,
            ],
        );

        return back()->with('success', 'Jurnal harian berhasil disimpan.');
    }
}
