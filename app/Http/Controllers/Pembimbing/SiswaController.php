<?php

namespace App\Http\Controllers\Pembimbing;

use App\Http\Controllers\Controller;
use App\Models\Perusahaan;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class SiswaController extends Controller
{
    public function create(Request $request): View
    {
        $user = $request->user();
        abort_unless($user && $user->role === 'pembimbing_perusahaan', 403);

        $perusahaan = $user->perusahaanBimbingan()
            ->orderBy('nama_perusahaan')
            ->get();

        return view('pembimbing.siswa.create', compact('perusahaan'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user && $user->role === 'pembimbing_perusahaan', 403);

        $allowedPerusahaanIds = $user->perusahaanBimbingan()->pluck('id')->all();

        $students = collect($request->input('students', []))
            ->map(function ($row): array {
                return [
                    'nama' => trim((string) ($row['nama'] ?? '')),
                    'nis' => trim((string) ($row['nis'] ?? '')),
                    'kelas' => trim((string) ($row['kelas'] ?? '')),
                    'jurusan' => trim((string) ($row['jurusan'] ?? '')),
                    'perusahaan_id' => $row['perusahaan_id'] ?? null,
                    'email' => trim((string) ($row['email'] ?? '')),
                    'password' => (string) ($row['password'] ?? ''),
                ];
            })
            ->filter(function ($row): bool {
                return collect($row)->contains(fn ($val) => $val !== '' && $val !== null);
            })
            ->values()
            ->all();

        $validator = Validator::make([
            'students' => $students,
        ], [
            'students' => ['required', 'array', 'min:1'],
            'students.*.nama' => ['required', 'string', 'max:255'],
            'students.*.nis' => ['required', 'string', 'max:100', 'distinct', 'unique:siswa,nis'],
            'students.*.kelas' => ['required', 'string', 'max:100'],
            'students.*.jurusan' => ['required', 'string', 'max:100'],
            'students.*.perusahaan_id' => ['required', 'integer'],
            'students.*.email' => ['required', 'email', 'max:255', 'distinct', 'unique:users,email'],
            'students.*.password' => ['required', 'string', 'min:6'],
        ], [
            'students.required' => 'Data siswa belum diisi.',
            'students.min' => 'Minimal isi 1 siswa.',
            'students.*.nama.required' => 'Nama wajib diisi.',
            'students.*.nis.required' => 'NIS wajib diisi.',
            'students.*.nis.unique' => 'NIS sudah terdaftar.',
            'students.*.nis.distinct' => 'NIS pada baris tidak boleh sama.',
            'students.*.kelas.required' => 'Kelas wajib diisi.',
            'students.*.jurusan.required' => 'Jurusan wajib diisi.',
            'students.*.perusahaan_id.required' => 'Perusahaan wajib dipilih.',
            'students.*.email.required' => 'Email wajib diisi.',
            'students.*.email.email' => 'Format email tidak valid.',
            'students.*.email.unique' => 'Email sudah terdaftar.',
            'students.*.email.distinct' => 'Email pada baris tidak boleh sama.',
            'students.*.password.required' => 'Password wajib diisi.',
            'students.*.password.min' => 'Password minimal 6 karakter.',
        ]);

        $validator->after(function ($validator) use ($students, $allowedPerusahaanIds): void {
            foreach ($students as $idx => $student) {
                $perusahaanId = (int) ($student['perusahaan_id'] ?? 0);
                if (! in_array($perusahaanId, $allowedPerusahaanIds, true)) {
                    $validator->errors()->add("students.$idx.perusahaan_id", 'Perusahaan tidak valid untuk akun pembimbing ini.');
                }
            }
        });

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::transaction(function () use ($students): void {
            foreach ($students as $student) {
                $newUser = User::create([
                    'name' => $student['nama'],
                    'email' => $student['email'],
                    'password' => Hash::make($student['password']),
                    'role' => 'siswa',
                ]);

                Siswa::create([
                    'nama' => $student['nama'],
                    'nis' => $student['nis'],
                    'kelas' => $student['kelas'],
                    'jurusan' => $student['jurusan'],
                    'perusahaan_id' => (int) $student['perusahaan_id'],
                    'user_id' => $newUser->id,
                ]);
            }
        });

        return redirect()->route('pembimbing.dashboard')
            ->with('success', count($students) . ' data siswa berhasil ditambahkan oleh pembimbing perusahaan.');
    }
}
