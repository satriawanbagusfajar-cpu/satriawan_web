@extends('layouts.app')

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-center">
    <div>
        <h3 class="fw-bold mb-1"><i class="bi bi-journal-text me-2"></i>Monitoring Jurnal Siswa</h3>
        <p class="text-muted mb-0">Pantau pengisian jurnal siswa bimbingan Anda</p>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white border-bottom py-3">
        <h5 class="mb-0"><i class="bi bi-funnel me-2"></i>Filter Data</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('pembimbing.jurnal.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <label for="siswa" class="form-label">Nama Siswa</label>
                <select class="form-select" id="siswa" name="siswa">
                    <option value="">-- Semua Siswa --</option>
                    @foreach($siswaBimbingan as $siswa)
                        <option value="{{ $siswa->id }}" {{ $filterSiswa == $siswa->id ? 'selected' : '' }}>
                            {{ $siswa->nama }} ({{ $siswa->nis }})
                            @if(isset($jurnalStatistics[$siswa->id]))
                                - {{ $jurnalStatistics[$siswa->id] }} jurnal
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="tanggal" class="form-label">Tanggal</label>
                <input type="date" class="form-control" id="tanggal" name="tanggal" value="{{ $filterTanggal }}">
            </div>
            <div class="col-md-3">
                <label for="approval_status" class="form-label">Approval</label>
                <select class="form-select" id="approval_status" name="approval_status">
                    <option value="">-- Semua --</option>
                    <option value="pending" {{ $filterApproval === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ $filterApproval === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ $filterApproval === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="col-md-5 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="bi bi-search me-1"></i>Cari
                </button>
                <a href="{{ route('pembimbing.jurnal.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
                @if($canApprove)
                    <a href="{{ route('pembimbing.jurnal.downloadPdf', request()->query()) }}" class="btn btn-outline-success" title="Download PDF">
                        <i class="bi bi-file-pdf"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white border-bottom py-3">
        <h5 class="mb-0">Daftar Jurnal</h5>
    </div>
    <div class="card-body">
        @if($jurnal->isEmpty())
            <div class="alert alert-info mb-0">
                <i class="bi bi-info-circle me-2"></i>Tidak ada data jurnal sesuai filter.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr style="background-color: #f8f9fa;">
                            <th>Tanggal</th>
                            <th>Nama Siswa</th>
                            <th>Kegiatan</th>
                            <th>Keterangan</th>
                            <th>Approval</th>
                            @if($canApprove)
                                <th>Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($jurnal as $item)
                            <tr>
                                <td>{{ $item->tanggal->format('d M Y') }}</td>
                                <td class="fw-semibold">{{ $item->siswa->nama }}</td>
                                <td>{{ $item->kegiatan }}</td>
                                <td>
                                    <small>{{ \Illuminate\Support\Str::limit($item->keterangan, 60) }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $item->approval_badge_class }}">{{ ucfirst($item->approval_status ?? 'pending') }}</span>
                                </td>
                                @if($canApprove)
                                    <td>
                                        @if(($item->approval_status ?? 'pending') === 'pending')
                                            <form action="{{ route('pembimbing.jurnal.approve', $item) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                            </form>
                                            <form action="{{ route('pembimbing.jurnal.reject', $item) }}" method="POST" class="d-inline" onsubmit="return submitRejectReason(this)">
                                                @csrf
                                                <input type="hidden" name="approval_notes">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Tolak</button>
                                            </form>
                                        @else
                                            <small class="text-muted">{{ $item->approval_notes ?: '-' }}</small>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $jurnal->links() }}
            </div>
        @endif
    </div>
</div>

<div class="row g-3 mt-3">
    @foreach($siswaBimbingan as $siswa)
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h6 class="fw-bold mb-0">{{ $siswa->nama }}</h6>
                            <small class="text-muted">{{ $siswa->nis }}</small>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-top">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Total Jurnal:</span>
                            <span class="badge bg-info">{{ $jurnalStatistics[$siswa->id] ?? 0 }} entri</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<script>
function submitRejectReason(form) {
    const reason = prompt('Masukkan alasan penolakan jurnal:');
    if (!reason) {
        return false;
    }

    form.querySelector('input[name="approval_notes"]').value = reason;
    return true;
}
</script>
@endsection
