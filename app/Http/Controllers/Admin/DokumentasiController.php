<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dokumentasi;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DokumentasiController extends Controller
{
    public function index(Request $request): View
    {
        $query = Dokumentasi::with('siswa')->latest('tanggal');

        if ($request->filled('siswa_id')) {
            $query->where('siswa_id', $request->integer('siswa_id'));
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->input('tanggal'));
        }

        $dokumentasi = $query->paginate(12)->withQueryString();
        $siswa = Siswa::orderBy('nama')->get();

        return view('admin.dokumentasi.index', compact('dokumentasi', 'siswa'));
    }
}
