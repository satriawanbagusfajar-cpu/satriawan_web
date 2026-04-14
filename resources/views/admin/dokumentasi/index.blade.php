@extends('layouts.app')

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-1"><i class="bi bi-camera-fill me-2"></i>Dokumentasi Kegiatan PKL</h3>
    <p class="text-muted mb-0">Pantau foto kegiatan siswa di lokasi PKL</p>
</div>

{{-- Filter --}}
<div class="card card-modern mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-semibold"><i class="bi bi-person me-1"></i>Filter Siswa</label>
                <select class="form-select" name="siswa_id">
                    <option value="">Semua Siswa</option>
                    @foreach($siswa as $s)
                        <option value="{{ $s->id }}" @selected(request('siswa_id') == $s->id)>{{ $s->nama }} ({{ $s->nis }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold"><i class="bi bi-calendar3 me-1"></i>Tanggal</label>
                <input type="date" name="tanggal" class="form-control" value="{{ request('tanggal') }}">
            </div>
            <div class="col-md-3">
                <button class="btn btn-gradient"><i class="bi bi-funnel me-1"></i>Filter</button>
                @if(request()->hasAny(['siswa_id', 'tanggal']))
                    <a href="{{ route('admin.dokumentasi.index') }}" class="btn btn-outline-secondary ms-1"><i class="bi bi-x-lg me-1"></i>Reset</a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Gallery --}}
<div class="card card-modern">
    <div class="card-body p-4">
        @if($dokumentasi->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-camera fs-1 d-block mb-2"></i>
                <p>Belum ada dokumentasi dari siswa.</p>
            </div>
        @else
            <div class="row g-3">
                @foreach($dokumentasi as $dok)
                    <div class="col-md-3 col-6">
                        <div class="card border-0 shadow-sm rounded-3 overflow-hidden h-100">
                            <img src="{{ route('media.public', ['path' => $dok->foto]) }}" class="card-img-top" style="height:180px; object-fit:cover; cursor:pointer;" alt="Dokumentasi" data-bs-toggle="modal" data-bs-target="#fotoModal{{ $dok->id }}">
                            <div class="card-body p-3">
                                <h6 class="fw-bold mb-1" style="font-size:.85rem;">{{ $dok->siswa?->nama ?? '-' }}</h6>
                                <small class="text-muted"><i class="bi bi-calendar3 me-1"></i>{{ \Carbon\Carbon::parse($dok->tanggal)->format('d M Y') }}</small>
                                @if($dok->keterangan)
                                    <p class="mb-0 mt-1 small text-muted">{{ Str::limit($dok->keterangan, 60) }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Modal Zoom --}}
                    <div class="modal fade" id="fotoModal{{ $dok->id }}" tabindex="-1">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content border-0 rounded-4 overflow-hidden">
                                <div class="modal-header border-0 pb-0">
                                    <div>
                                        <h6 class="modal-title fw-bold mb-0"><i class="bi bi-person me-1"></i>{{ $dok->siswa?->nama ?? '-' }}</h6>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($dok->tanggal)->format('d M Y') }}</small>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body text-center p-2">
                                    <img src="{{ route('media.public', ['path' => $dok->foto]) }}" class="img-fluid rounded-3" style="max-height:70vh;" alt="Dokumentasi">
                                    @if($dok->keterangan)
                                        <p class="mt-3 text-muted">{{ $dok->keterangan }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="table-pagination mt-3 d-flex justify-content-center">
                {{ $dokumentasi->links('vendor.pagination.clean') }}
            </div>
        @endif
    </div>
</div>
@endsection
