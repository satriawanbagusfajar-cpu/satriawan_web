@extends('layouts.app')

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-1"><i class="bi bi-pencil-square me-2"></i>Edit Siswa</h3>
    <p class="text-muted mb-0">Perbarui data siswa: {{ $siswa->nama }}</p>
</div>

<div class="card card-modern">
    <div class="card-body p-4">
        <form action="{{ route('admin.siswa.update', $siswa) }}" method="POST">
            @csrf
            @method('PUT')
            <h6 class="fw-bold mb-3" style="color:#FF8C42;"><i class="bi bi-person me-1"></i>Data Siswa</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nama Lengkap</label>
                    <input class="form-control" name="nama" value="{{ old('nama', $siswa->nama) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">NIS</label>
                    <input class="form-control" name="nis" value="{{ old('nis', $siswa->nis) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Kelas</label>
                    <input class="form-control" name="kelas" value="{{ old('kelas', $siswa->kelas) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Jurusan</label>
                    <input class="form-control" name="jurusan" value="{{ old('jurusan', $siswa->jurusan) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Perusahaan</label>
                    <select class="form-select" name="perusahaan_id">
                        <option value="">- Pilih Perusahaan -</option>
                        @foreach($perusahaan as $item)
                            <option value="{{ $item->id }}" @selected(old('perusahaan_id', $siswa->perusahaan_id) == $item->id)>{{ $item->nama_perusahaan }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <hr class="my-3">
            <h6 class="fw-bold mb-3" style="color:#FF8C42;"><i class="bi bi-key me-1"></i>Akun Login</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Email Login</label>
                    <input type="email" class="form-control" name="email" value="{{ old('email', $siswa->user?->email) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Password Baru <small class="text-muted">(Opsional)</small></label>
                    <input type="password" class="form-control" name="password" placeholder="Kosongkan jika tidak diubah">
                </div>
            </div>

            <div class="d-flex gap-2">
                <button class="btn btn-gradient"><i class="bi bi-check-lg me-1"></i>Update</button>
                <a href="{{ route('admin.siswa.index') }}" class="btn btn-light border"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
            </div>
        </form>
    </div>
</div>
@endsection
