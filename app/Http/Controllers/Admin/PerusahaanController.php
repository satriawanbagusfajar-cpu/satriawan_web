<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Perusahaan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PerusahaanController extends Controller
{
    public function index(Request $request): View
    {
        $perPage = (int) $request->get('per_page', 10);
        $perusahaan = Perusahaan::latest()->paginate($perPage)->withQueryString();

        return view('admin.perusahaan.index', compact('perusahaan'));
    }

    public function create(): View
    {
        $pembimbingList = User::where('role', 'pembimbing_perusahaan')->get();
        return view('admin.perusahaan.create', compact('pembimbingList'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_perusahaan' => ['required', 'string', 'max:255'],
            'alamat' => ['required', 'string'],
            'pembimbing_id' => ['nullable', 'exists:users,id'],
        ]);

        Perusahaan::create($validated);

        return redirect()->route('admin.perusahaan.index')->with('success', 'Data perusahaan berhasil ditambahkan.');
    }

    public function edit(Perusahaan $perusahaan): View
    {
        $pembimbingList = User::where('role', 'pembimbing_perusahaan')->get();
        return view('admin.perusahaan.edit', compact('perusahaan', 'pembimbingList'));
    }

    public function update(Request $request, Perusahaan $perusahaan): RedirectResponse
    {
        $validated = $request->validate([
            'nama_perusahaan' => ['required', 'string', 'max:255'],
            'alamat' => ['required', 'string'],
            'pembimbing_id' => ['nullable', 'exists:users,id'],
        ]);

        $perusahaan->update($validated);

        return redirect()->route('admin.perusahaan.index')->with('success', 'Data perusahaan berhasil diperbarui.');
    }

    public function destroy(Perusahaan $perusahaan): RedirectResponse
    {
        $perusahaan->delete();

        return redirect()->route('admin.perusahaan.index')->with('success', 'Data perusahaan berhasil dihapus.');
    }
}
