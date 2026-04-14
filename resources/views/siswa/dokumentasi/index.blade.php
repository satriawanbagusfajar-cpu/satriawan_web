@extends('layouts.app')

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-1"><i class="bi bi-camera-fill me-2"></i>Dokumentasi Kegiatan PKL</h3>
    <p class="text-muted mb-0">Upload foto kegiatan untuk bukti kehadiran di lokasi PKL</p>
</div>

{{-- Upload Form --}}
<div class="card card-modern mb-4">
    <div class="card-body p-4">
        <h6 class="fw-bold mb-3"><i class="bi bi-cloud-arrow-up me-1" style="color:#667eea;"></i>Upload Dokumentasi</h6>
        <form action="{{ route('siswa.dokumentasi.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Tanggal</label>
                    <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', now()->toDateString()) }}" required>
                    @error('tanggal')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Foto Kegiatan <span class="text-danger">*</span></label>
                    <input type="file" name="foto" class="form-control @error('foto') is-invalid @enderror" accept="image/jpeg,image/png" required>
                    @error('foto')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-text">JPG/PNG, maks 2MB</div>
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-semibold">Keterangan</label>
                    <div class="input-group">
                        <input type="text" name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" placeholder="Contoh: Mengerjakan proyek di kantor" value="{{ old('keterangan') }}" maxlength="500">
                        <button class="btn btn-gradient" type="submit"><i class="bi bi-upload me-1"></i>Upload</button>
                    </div>
                    @error('keterangan')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card card-modern mb-3">
    <div class="card-body py-2 px-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <small class="text-muted mb-0">Atur jumlah data per halaman</small>
        <form method="GET" class="d-flex align-items-center gap-2">
            <label for="per_page" class="small text-muted mb-0">Tampilkan</label>
            <select class="form-select form-select-sm" id="per_page" name="per_page" onchange="this.form.submit()">
                @foreach([6,12,24,48] as $size)
                    <option value="{{ $size }}" @selected((int) request('per_page', 12) === $size)>{{ $size }}</option>
                @endforeach
            </select>
        </form>
    </div>
</div>

{{-- Gallery --}}
<div class="card card-modern">
    <div class="card-body p-4">
        <h6 class="fw-bold mb-3"><i class="bi bi-images me-1" style="color:#11998e;"></i>Riwayat Dokumentasi</h6>

        @if($riwayat->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-camera fs-1 d-block mb-2"></i>
                <p>Belum ada dokumentasi. Upload foto kegiatan PKL kamu!</p>
            </div>
        @else
            <div class="row g-3">
                @foreach($riwayat as $dok)
                    <div class="col-md-4 col-6">
                        <div class="card border-0 shadow-sm rounded-3 overflow-hidden h-100">
                            <img src="{{ route('media.public', ['path' => $dok->foto], false) }}" class="card-img-top" style="height:200px; object-fit:cover; cursor:pointer;" alt="Dokumentasi" data-bs-toggle="modal" data-bs-target="#fotoModal{{ $dok->id }}">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <small class="text-muted"><i class="bi bi-calendar3 me-1"></i>{{ \Carbon\Carbon::parse($dok->tanggal)->format('d M Y') }}</small>
                                        @if($dok->keterangan)
                                            <p class="mb-0 mt-1 small">{{ $dok->keterangan }}</p>
                                        @endif
                                    </div>
                                    <form action="{{ route('siswa.dokumentasi.destroy', $dok) }}" method="POST" class="ms-2">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus dokumentasi ini?')" title="Hapus"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Modal Zoom --}}
                    <div class="modal fade" id="fotoModal{{ $dok->id }}" tabindex="-1">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content border-0 rounded-4 overflow-hidden">
                                <div class="modal-header border-0 pb-0">
                                    <h6 class="modal-title fw-bold"><i class="bi bi-camera me-1"></i>{{ \Carbon\Carbon::parse($dok->tanggal)->format('d M Y') }}</h6>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body text-center p-2">
                                    <img src="{{ route('media.public', ['path' => $dok->foto], false) }}" class="img-fluid rounded-3" style="max-height:70vh;" alt="Dokumentasi">
                                    @if($dok->keterangan)
                                        <p class="mt-3 text-muted">{{ $dok->keterangan }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="table-pagination mt-4">
                <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-2">
                    <small class="text-muted">
                        Menampilkan {{ $riwayat->firstItem() ?? 0 }}-{{ $riwayat->lastItem() ?? 0 }} dari {{ $riwayat->total() }} dokumentasi
                    </small>
                    {{ $riwayat->links('vendor.pagination.clean') }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
