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
            <div class="col-md-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">-- Semua Status --</option>
                    <option value="hadir" {{ $filterStatus === 'hadir' ? 'selected' : '' }}>Hadir</option>
                    <option value="sakit" {{ $filterStatus === 'sakit' ? 'selected' : '' }}>Sakit</option>
                    <option value="izin" {{ $filterStatus === 'izin' ? 'selected' : '' }}>Izin</option>
                    <option value="alpha" {{ $filterStatus === 'alpha' ? 'selected' : '' }}>Alpha</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="tanggal" class="form-label">Tanggal</label>
                <input type="date" class="form-control" id="tanggal" name="tanggal" value="{{ $filterTanggal }}">
            </div>
            <div class="col-md-3 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="bi bi-search me-1"></i>Cari
                </button>
                <a href="{{ route('pembimbing.absensi.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
                <a href="{{ route('pembimbing.absensi.downloadPdf', request()->query()) }}" class="btn btn-outline-success" title="Download PDF">
                    <i class="bi bi-file-pdf"></i>
                </a>
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
                            <th>Status</th>
                            <th>Keterangan Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($absensi as $item)
                            <tr>
                                <td>{{ $item->tanggal->format('d M Y') }}</td>
                                <td class="fw-semibold">{{ $item->siswa->nama }}</td>
                                <td>{{ $item->jam_masuk ?? '-' }}</td>
                                <td>{{ $item->jam_keluar ?? '-' }}</td>
                                <td>
                                    <span class="badge {{ $item->status === 'hadir' ? 'bg-success' : ($item->status === 'sakit' ? 'bg-warning' : ($item->status === 'izin' ? 'bg-info' : 'bg-danger')) }}">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($item->status === 'hadir')
                                        <span class="badge {{ $item->isTerlambat() ? 'bg-warning' : 'bg-success' }}">
                                            {{ $item->isTerlambat() ? 'Telat' : 'Tepat Waktu' }}
                                        </span>
                                    @else
                                        <span class="text-muted">{{ ucfirst($item->status) }}</span>
                                    @endif
                                </td>
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
@endsection
