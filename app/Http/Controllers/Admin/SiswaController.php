<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Perusahaan;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SiswaController extends Controller
{
    public function index(Request $request): View
    {
        $allowedPerPage = [5, 10, 25, 50];
        $perPage = $request->integer('per_page', 10);
        if (! in_array($perPage, $allowedPerPage, true)) {
            $perPage = 10;
        }

        $siswa = Siswa::with('perusahaan', 'user')
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.siswa.index', compact('siswa'));
    }

    public function create(): View
    {
        $perusahaan = Perusahaan::orderBy('nama_perusahaan')->get();

        return view('admin.siswa.create', compact('perusahaan'));
    }

    public function store(Request $request): RedirectResponse
    {
        if ($request->has('students')) {
            $students = collect($request->input('students', []))
                ->map(function ($row) {
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
                    return collect($row)->except('perusahaan_id')->contains(fn ($val) => $val !== '');
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
                'students.*.perusahaan_id' => ['nullable', 'exists:perusahaan,id'],
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
                'students.*.email.required' => 'Email wajib diisi.',
                'students.*.email.email' => 'Format email tidak valid.',
                'students.*.email.unique' => 'Email sudah terdaftar.',
                'students.*.email.distinct' => 'Email pada baris tidak boleh sama.',
                'students.*.password.required' => 'Password wajib diisi.',
                'students.*.password.min' => 'Password minimal 6 karakter.',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            DB::transaction(function () use ($students): void {
                foreach ($students as $student) {
                    $user = User::create([
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
                        'perusahaan_id' => $student['perusahaan_id'] ?: null,
                        'user_id' => $user->id,
                    ]);
                }
            });

            return redirect()->route('admin.siswa.index')->with('success', count($students) . ' data siswa berhasil ditambahkan.');
        }

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'nis' => ['required', 'string', 'max:100', 'unique:siswa,nis'],
            'kelas' => ['required', 'string', 'max:100'],
            'jurusan' => ['required', 'string', 'max:100'],
            'perusahaan_id' => ['nullable', 'exists:perusahaan,id'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $user = User::create([
            'name' => $validated['nama'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'siswa',
        ]);

        Siswa::create([
            'nama' => $validated['nama'],
            'nis' => $validated['nis'],
            'kelas' => $validated['kelas'],
            'jurusan' => $validated['jurusan'],
            'perusahaan_id' => $validated['perusahaan_id'] ?? null,
            'user_id' => $user->id,
        ]);

        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function edit(Siswa $siswa): View
    {
        $siswa->load('user');
        $perusahaan = Perusahaan::orderBy('nama_perusahaan')->get();

        return view('admin.siswa.edit', compact('siswa', 'perusahaan'));
    }

    public function update(Request $request, Siswa $siswa): RedirectResponse
    {
        $siswa->load('user');

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'nis' => ['required', 'string', 'max:100', Rule::unique('siswa', 'nis')->ignore($siswa->id)],
            'kelas' => ['required', 'string', 'max:100'],
            'jurusan' => ['required', 'string', 'max:100'],
            'perusahaan_id' => ['nullable', 'exists:perusahaan,id'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($siswa->user_id)],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        $siswa->update([
            'nama' => $validated['nama'],
            'nis' => $validated['nis'],
            'kelas' => $validated['kelas'],
            'jurusan' => $validated['jurusan'],
            'perusahaan_id' => $validated['perusahaan_id'] ?? null,
        ]);

        if ($siswa->user) {
            $siswa->user->name = $validated['nama'];
            $siswa->user->email = $validated['email'];
            if (! empty($validated['password'])) {
                $siswa->user->password = Hash::make($validated['password']);
            }
            $siswa->user->save();
        }

        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Siswa $siswa): RedirectResponse
    {
        $user = $siswa->user;
        $siswa->delete();
        if ($user) {
            $user->delete();
        }

        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa berhasil dihapus.');
    }

    public function import(): View
    {
        $perusahaan = Perusahaan::orderBy('nama_perusahaan')->get();

        return view('admin.siswa.import', compact('perusahaan'));
    }

    public function processImport(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');
        if (! $handle) {
            return back()->withErrors(['file' => 'Gagal membaca file.']);
        }

        // Read header row
        $header = fgetcsv($handle, 0, ',');
        if (! $header) {
            fclose($handle);
            return back()->withErrors(['file' => 'File CSV kosong atau format tidak valid.']);
        }

        // Normalize headers
        $header = array_map(fn ($h) => strtolower(trim($h)), $header);
        $required = ['nama', 'nis', 'kelas', 'jurusan', 'email', 'password'];
        $missing = array_diff($required, $header);
        if (! empty($missing)) {
            fclose($handle);
            return back()->withErrors(['file' => 'Kolom yang wajib ada: ' . implode(', ', $required) . '. Kolom tidak ditemukan: ' . implode(', ', $missing)]);
        }

        $success = 0;
        $errors = [];
        $line = 1;

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle, 0, ',')) !== false) {
                $line++;
                $data = array_combine($header, array_pad($row, count($header), ''));

                $nama = trim($data['nama'] ?? '');
                $nis = trim($data['nis'] ?? '');
                $kelas = trim($data['kelas'] ?? '');
                $jurusan = trim($data['jurusan'] ?? '');
                $email = trim($data['email'] ?? '');
                $password = trim($data['password'] ?? '');

                if (empty($nama) || empty($nis) || empty($email) || empty($password)) {
                    $errors[] = "Baris {$line}: Data tidak lengkap (nama/nis/email/password wajib diisi).";
                    continue;
                }

                if (User::where('email', $email)->exists()) {
                    $errors[] = "Baris {$line}: Email '{$email}' sudah terdaftar.";
                    continue;
                }

                if (Siswa::where('nis', $nis)->exists()) {
                    $errors[] = "Baris {$line}: NIS '{$nis}' sudah terdaftar.";
                    continue;
                }

                // Optional perusahaan column
                $perusahaanId = null;
                if (isset($data['perusahaan']) && trim($data['perusahaan']) !== '') {
                    $perusahaan = Perusahaan::where('nama_perusahaan', trim($data['perusahaan']))->first();
                    if ($perusahaan) {
                        $perusahaanId = $perusahaan->id;
                    }
                }

                $user = User::create([
                    'name' => $nama,
                    'email' => $email,
                    'password' => Hash::make($password),
                    'role' => 'siswa',
                ]);

                Siswa::create([
                    'nama' => $nama,
                    'nis' => $nis,
                    'kelas' => $kelas,
                    'jurusan' => $jurusan,
                    'perusahaan_id' => $perusahaanId,
                    'user_id' => $user->id,
                ]);

                $success++;
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            fclose($handle);
            return back()->withErrors(['file' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }

        fclose($handle);

        $message = "{$success} siswa berhasil diimport.";
        if (! empty($errors)) {
            $message .= ' ' . count($errors) . ' baris dilewati.';
            return redirect()->route('admin.siswa.index')
                ->with('success', $message)
                ->withErrors($errors);
        }

        return redirect()->route('admin.siswa.index')->with('success', $message);
    }
}
