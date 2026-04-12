@extends('layouts.app')

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-1"><i class="bi bi-people-fill me-2"></i>Tambah Banyak Siswa</h3>
    <p class="text-muted mb-0">Input beberapa siswa sekaligus dalam satu kali simpan</p>
</div>

<div class="card card-modern">
    <div class="card-body p-4">
        @if($errors->any())
            <div class="alert alert-danger">
                <div class="fw-semibold mb-1">Periksa kembali data yang diinput:</div>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.siswa.store') }}" method="POST">
            @csrf
            <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0" style="color:#FF8C42;"><i class="bi bi-table me-1"></i>Data Siswa & Akun Login</h6>
                <button type="button" class="btn btn-outline-primary" id="btnTambahBaris">
                    <i class="bi bi-plus-circle me-1"></i>Tambah Baris
                </button>
            </div>

            <div class="table-responsive">
                <table class="table align-middle" id="bulkTable">
                    <thead class="table-light">
                        <tr>
                            <th>Nama</th>
                            <th>NIS</th>
                            <th>Kelas</th>
                            <th>Jurusan</th>
                            <th>Perusahaan</th>
                            <th>Email</th>
                            <th>Password</th>
                            <th style="width: 70px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $oldStudents = old('students', [
                                [
                                    'nama' => '',
                                    'nis' => '',
                                    'kelas' => '',
                                    'jurusan' => '',
                                    'perusahaan_id' => '',
                                    'email' => '',
                                    'password' => '',
                                ],
                            ]);
                        @endphp

                        @foreach($oldStudents as $i => $student)
                            <tr>
                                <td><input class="form-control" name="students[{{ $i }}][nama]" value="{{ $student['nama'] ?? '' }}" placeholder="Nama lengkap"></td>
                                <td><input class="form-control" name="students[{{ $i }}][nis]" value="{{ $student['nis'] ?? '' }}" placeholder="NIS"></td>
                                <td><input class="form-control" name="students[{{ $i }}][kelas]" value="{{ $student['kelas'] ?? '' }}" placeholder="XII RPL 1"></td>
                                <td><input class="form-control" name="students[{{ $i }}][jurusan]" value="{{ $student['jurusan'] ?? '' }}" placeholder="RPL"></td>
                                <td>
                                    <select class="form-select" name="students[{{ $i }}][perusahaan_id]">
                                        <option value="">- Pilih -</option>
                                        @foreach($perusahaan as $item)
                                            <option value="{{ $item->id }}" @selected(($student['perusahaan_id'] ?? '') == $item->id)>{{ $item->nama_perusahaan }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input type="email" class="form-control" name="students[{{ $i }}][email]" value="{{ $student['email'] ?? '' }}" placeholder="email@contoh.com"></td>
                                <td><input type="text" class="form-control" name="students[{{ $i }}][password]" value="{{ $student['password'] ?? '' }}" placeholder="Min 6 karakter"></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-danger btnHapusBaris" title="Hapus baris">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="small text-muted mb-4">
                Tips: klik "Tambah Baris" untuk menambah banyak siswa, lalu klik Simpan sekali untuk memasukkan semua data.
            </div>

            <template id="rowTemplate">
                <tr>
                    <td><input class="form-control" name="students[__INDEX__][nama]" placeholder="Nama lengkap"></td>
                    <td><input class="form-control" name="students[__INDEX__][nis]" placeholder="NIS"></td>
                    <td><input class="form-control" name="students[__INDEX__][kelas]" placeholder="XII RPL 1"></td>
                    <td><input class="form-control" name="students[__INDEX__][jurusan]" placeholder="RPL"></td>
                    <td>
                        <select class="form-select" name="students[__INDEX__][perusahaan_id]">
                            <option value="">- Pilih -</option>
                            @foreach($perusahaan as $item)
                                <option value="{{ $item->id }}">{{ $item->nama_perusahaan }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="email" class="form-control" name="students[__INDEX__][email]" placeholder="email@contoh.com"></td>
                    <td><input type="text" class="form-control" name="students[__INDEX__][password]" placeholder="Min 6 karakter"></td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-danger btnHapusBaris" title="Hapus baris">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            </template>

            <div class="d-flex gap-2">
                <button class="btn btn-gradient"><i class="bi bi-check-lg me-1"></i>Simpan Semua</button>
                <a href="{{ route('admin.siswa.index') }}" class="btn btn-light border"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
            </div>
        </form>
    </div>
</div>

<script>
    (function () {
        const tableBody = document.querySelector('#bulkTable tbody');
        const template = document.querySelector('#rowTemplate').innerHTML;
        const addBtn = document.getElementById('btnTambahBaris');

        function nextIndex() {
            return tableBody.querySelectorAll('tr').length;
        }

        function addRow() {
            const idx = nextIndex();
            const html = template.replaceAll('__INDEX__', String(idx));
            tableBody.insertAdjacentHTML('beforeend', html);
        }

        addBtn.addEventListener('click', addRow);

        tableBody.addEventListener('click', function (e) {
            const btn = e.target.closest('.btnHapusBaris');
            if (!btn) return;

            if (tableBody.querySelectorAll('tr').length === 1) {
                return;
            }

            btn.closest('tr').remove();
        });
    })();
</script>
@endsection
