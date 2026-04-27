@extends('layouts.app')

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-center">
    <div>
        <h3 class="fw-bold mb-1"><i class="bi bi-calendar-check me-2"></i>Monitoring Absensi Siswa</h3>
        <p class="text-muted mb-0">Pantau kehadiran siswa bimbingan Anda</p>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white border-bottom py-3">
        <h5 class="mb-0"><i class="bi bi-funnel me-2"></i>Filter Data</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('pembimbing.absensi.index') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label for="siswa" class="form-label">Nama Siswa</label>
                <select class="form-select" id="siswa" name="siswa">
                    <option value="">-- Semua Siswa --</option>
                    @foreach($siswaBimbingan as $siswa)
                        <option value="{{ $siswa->id }}" {{ $filterSiswa == $siswa->id ? 'selected' : '' }}>
                            {{ $siswa->nama }} ({{ $siswa->nis }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">-- Semua Status --</option>
                    <option value="hadir" {{ $filterStatus === 'hadir' ? 'selected' : '' }}>Hadir</option>
                    <option value="terlambat" {{ $filterStatus === 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                    <option value="sakit" {{ $filterStatus === 'sakit' ? 'selected' : '' }}>Sakit</option>
                    <option value="izin" {{ $filterStatus === 'izin' ? 'selected' : '' }}>Izin</option>
                    <option value="alpha" {{ $filterStatus === 'alpha' ? 'selected' : '' }}>Alpha</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="tanggal" class="form-label">Tanggal</label>
                <input type="date" class="form-control" id="tanggal" name="tanggal" value="{{ $filterTanggal }}">
            </div>
            <div class="col-md-2">
                <label for="approval_status" class="form-label">Approval</label>
                <select class="form-select" id="approval_status" name="approval_status">
                    <option value="">-- Semua --</option>
                    <option value="pending" {{ $filterApproval === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ $filterApproval === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ $filterApproval === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="bi bi-search me-1"></i>Cari
                </button>
                <a href="{{ route('pembimbing.absensi.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
                @if($canApprove)
                    <a href="{{ route('pembimbing.absensi.downloadPdf', request()->query()) }}" class="btn btn-outline-success" title="Download PDF">
                        <i class="bi bi-file-pdf"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white border-bottom py-3">
        <h5 class="mb-0">Daftar Absensi</h5>
    </div>
    <div class="card-body">
        @if($absensi->isEmpty())
            <div class="alert alert-info mb-0">
                <i class="bi bi-info-circle me-2"></i>Tidak ada data absensi sesuai filter.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr style="background-color: #f8f9fa;">
                            <th>Tanggal</th>
                            <th>Nama Siswa</th>
                            <th>Jam Masuk</th>
                            <th>Jam Keluar</th>
                            <th>Lokasi</th>
                            <th>Foto</th>
                            <th>Status</th>
                            <th>Approval</th>
                            @if($canApprove)
                                <th>Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($absensi as $item)
                            <tr>
                                <td>{{ $item->tanggal->format('d M Y') }}</td>
                                <td class="fw-semibold">{{ $item->siswa->nama }}</td>
                                <td>{{ $item->jam_masuk ?? '-' }}</td>
                                <td>{{ $item->jam_keluar ?? '-' }}</td>
                                <td><small>{{ $item->lokasi ?? '-' }}</small></td>
                                <td>
                                    @if($item->foto)
                                        <img src="{{ route('media.public', ['path' => $item->foto], false) }}" alt="Foto Absensi" style="height:40px; width:40px; object-fit:cover; border-radius:6px;">
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ in_array($item->status, ['hadir', 'terlambat']) ? ($item->isTerlambat() ? 'bg-warning' : 'bg-success') : ($item->status === 'sakit' ? 'bg-info' : ($item->status === 'izin' ? 'bg-primary' : 'bg-danger')) }}">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </td>
                                <td><span class="badge bg-{{ $item->approval_badge_class }}">{{ ucfirst($item->approval_status ?? 'pending') }}</span></td>
                                @if($canApprove)
                                    <td>
                                        @if(($item->approval_status ?? 'pending') === 'pending')
                                            <form action="{{ route('pembimbing.absensi.approve', $item) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                            </form>
                                            <form action="{{ route('pembimbing.absensi.reject', $item) }}" method="POST" class="d-inline" onsubmit="return submitRejectReason(this)">
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
                {{ $absensi->links() }}
            </div>
        @endif
    </div>
</div>

<script>
function submitRejectReason(form) {
    const reason = prompt('Masukkan alasan penolakan absensi:');
    if (!reason) {
        return false;
    }

    form.querySelector('input[name="approval_notes"]').value = reason;
    return true;
}
</script>
@endsection
