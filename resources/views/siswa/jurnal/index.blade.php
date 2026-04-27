@extends('layouts.app')

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-1"><i class="bi bi-journal-text me-2"></i>Jurnal Harian PKL</h3>
    <p class="text-muted mb-0">Catat kegiatan harian selama PKL</p>
</div>

<div class="card card-modern mb-4">
    <div class="card-body p-4">
        <h6 class="fw-bold mb-3"><i class="bi bi-plus-circle me-1" style="color:#FF8C42;"></i>Tambah Jurnal</h6>
        <form action="{{ route('siswa.jurnal.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Tanggal</label>
                    <input type="date" class="form-control" name="tanggal" value="{{ now()->toDateString() }}" required>
                </div>
                <div class="col-md-9">
                    <label class="form-label fw-semibold">Kegiatan</label>
                    <input type="text" class="form-control" name="kegiatan" placeholder="Deskripsi kegiatan hari ini" required>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Keterangan <small class="text-muted">(Opsional)</small></label>
                    <textarea class="form-control" name="keterangan" rows="3" placeholder="Catatan tambahan..."></textarea>
                </div>
            </div>
            <button class="btn btn-gradient mt-3"><i class="bi bi-check-lg me-1"></i>Simpan Jurnal</button>
        </form>
    </div>
</div>

<div class="card card-modern mb-3">
    <div class="card-body py-2 px-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <small class="text-muted mb-0">Atur jumlah data per halaman</small>
        <form method="GET" class="d-flex align-items-center gap-2">
            <label for="per_page" class="small text-muted mb-0">Tampilkan</label>
            <select class="form-select form-select-sm" id="per_page" name="per_page" onchange="this.form.submit()">
                @foreach([5,10,20,50] as $size)
                    <option value="{{ $size }}" @selected((int) request('per_page', 10) === $size)>{{ $size }}</option>
                @endforeach
            </select>
        </form>
    </div>
</div>

<div class="card card-modern">
    <div class="card-header bg-white border-0 pt-3 px-4">
        <h6 class="fw-bold mb-0"><i class="bi bi-clock-history me-1"></i>Riwayat Jurnal</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead>
                <tr>
                    <th width="130">Tanggal</th>
                    <th>Kegiatan</th>
                    <th>Keterangan</th>
                    <th>Approval</th>
                </tr>
                </thead>
                <tbody>
                @forelse($riwayat as $item)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</td>
                        <td>{{ $item->kegiatan }}</td>
                        <td>{{ $item->keterangan ?? '-' }}</td>
                        <td>
                            <span class="badge bg-{{ $item->approval_badge_class }}">{{ ucfirst($item->approval_status ?? 'pending') }}</span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="empty-state"><i class="bi bi-inbox"></i><p>Belum ada jurnal.</p></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="table-pagination mt-3">
    <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-2">
        <small class="text-muted">
            Menampilkan {{ $riwayat->firstItem() ?? 0 }}-{{ $riwayat->lastItem() ?? 0 }} dari {{ $riwayat->total() }} jurnal
        </small>
        {{ $riwayat->links('vendor.pagination.clean') }}
    </div>
</div>
@endsection
