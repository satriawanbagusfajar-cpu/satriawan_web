@extends('layouts.app')

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-1"><i class="bi bi-pencil-square me-2"></i>Edit Perusahaan</h3>
    <p class="text-muted mb-0">Perbarui data: {{ $perusahaan->nama_perusahaan }}</p>
</div>

<div class="card card-modern">
    <div class="card-body p-4">
        <form action="{{ route('admin.perusahaan.update', $perusahaan) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <label class="form-label fw-semibold">Nama Perusahaan</label>
                    <input class="form-control" name="nama_perusahaan" value="{{ old('nama_perusahaan', $perusahaan->nama_perusahaan) }}" required>
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-semibold">Alamat</label>
                    <textarea class="form-control" name="alamat" rows="3" required>{{ old('alamat', $perusahaan->alamat) }}</textarea>
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-semibold">Pembimbing Perusahaan</label>
                    <select class="form-select" name="pembimbing_id">
                        <option value="">-- Pilih Pembimbing Perusahaan --</option>
                        @foreach($pembimbingList as $pembimbing)
                            <option value="{{ $pembimbing->id }}" {{ old('pembimbing_id', $perusahaan->pembimbing_id) == $pembimbing->id ? 'selected' : '' }}>
                                {{ $pembimbing->name }} ({{ $pembimbing->email }})
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Pilih pembimbing dari perusahaan atau kosongkan jika belum ditentukan</small>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-gradient"><i class="bi bi-check-lg me-1"></i>Update</button>
                <a href="{{ route('admin.perusahaan.index') }}" class="btn btn-light border"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
            </div>
        </form>
    </div>
</div>
@endsection
