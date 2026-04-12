@extends('layouts.app')

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-center">
    <div>
        <h3 class="fw-bold mb-1"><i class="bi bi-grid-1x2-fill me-2"></i>Dashboard Admin</h3>
        <p class="text-muted mb-0">Ringkasan data PKL SMK Fatahillah</p>
    </div>
    <div class="d-flex gap-2 mt-2 mt-md-0">
        <a href="{{ route('admin.absensi.rekap') }}" class="btn btn-gradient btn-sm"><i class="bi bi-bar-chart-line me-1"></i>Rekap Absensi</a>
        <a href="{{ route('admin.chart') }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-graph-up me-1"></i>Grafik</a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card stat-card card-modern fade-in fade-in-delay-1">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div class="stat-icon text-white" style="background: linear-gradient(135deg, #FF8C42, #FF6B35);"><i class="bi bi-people-fill"></i></div>
                <div>
                    <div class="text-muted small fw-semibold">Total Siswa</div>
                    <div class="fs-4 fw-bold" style="color:#FF8C42;">{{ $stats['total_siswa'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card stat-card card-modern fade-in fade-in-delay-2">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div class="stat-icon text-white" style="background: linear-gradient(135deg, #06b6d4, #22d3ee);"><i class="bi bi-building"></i></div>
                <div>
                    <div class="text-muted small fw-semibold">Perusahaan</div>
                    <div class="fs-4 fw-bold" style="color:#0891b2;">{{ $stats['total_perusahaan'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card stat-card card-modern fade-in fade-in-delay-3">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div class="stat-icon text-white" style="background: linear-gradient(135deg, #ec4899, #f472b6);"><i class="bi bi-journal-text"></i></div>
                <div>
                    <div class="text-muted small fw-semibold">Total Jurnal</div>
                    <div class="fs-4 fw-bold" style="color:#db2777;">{{ $stats['total_jurnal'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card stat-card card-modern fade-in fade-in-delay-4">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div class="stat-icon text-white" style="background: linear-gradient(135deg, #10b981, #34d399);"><i class="bi bi-check-circle-fill"></i></div>
                <div>
                    <div class="text-muted small fw-semibold">Sudah Absen Hari Ini</div>
                    <div class="fs-4 fw-bold" style="color:#059669;">{{ $stats['hadir'] }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<h6 class="fw-bold text-muted mb-3"><i class="bi bi-clipboard-data me-1"></i>Status Kehadiran</h6>
<div class="row g-3">
    <div class="col-md-4 col-12">
        <div class="card stat-card card-modern fade-in fade-in-delay-2">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div class="stat-icon text-white" style="background: linear-gradient(135deg, #f59e0b, #fbbf24);"><i class="bi bi-envelope-fill"></i></div>
                <div>
                    <div class="text-muted small fw-semibold">Izin</div>
                    <div class="fs-4 fw-bold" style="color:#d97706;">{{ $stats['izin'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-12">
        <div class="card stat-card card-modern fade-in fade-in-delay-3">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div class="stat-icon text-white" style="background: linear-gradient(135deg, #06b6d4, #22d3ee);"><i class="bi bi-thermometer-half"></i></div>
                <div>
                    <div class="text-muted small fw-semibold">Sakit</div>
                    <div class="fs-4 fw-bold" style="color:#0891b2;">{{ $stats['sakit'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-12">
        <div class="card stat-card card-modern fade-in fade-in-delay-4">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div class="stat-icon text-white" style="background: linear-gradient(135deg, #ef4444, #f87171);"><i class="bi bi-x-circle-fill"></i></div>
                <div>
                    <div class="text-muted small fw-semibold">Alpha</div>
                    <div class="fs-4 fw-bold" style="color:#dc2626;">{{ $stats['alpha'] }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card card-modern mt-4">
    <div class="card-header bg-white border-0 pt-3 px-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h6 class="fw-bold mb-0"><i class="bi bi-clipboard-check me-1"></i>Absensi Hari Ini</h6>
            <small class="text-muted">{{ now()->translatedFormat('l, d F Y') }}</small>
        </div>
        <span class="badge text-bg-dark">{{ $stats['total_hari_ini'] }} siswa sudah absen</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive" style="max-height: 420px; overflow:auto;">
            <table class="table table-modern mb-0">
                <thead style="position: sticky; top: 0; z-index: 2;">
                    <tr>
                        <th>Nama Siswa</th>
                        <th>Perusahaan</th>
                        <th>Status</th>
                        <th>Jam Masuk</th>
                        <th>Jam Keluar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($todayAbsensi as $item)
                        @php
                            $statusColors = ['hadir' => 'success', 'izin' => 'warning', 'sakit' => 'info', 'alpha' => 'danger'];
                        @endphp
                        <tr>
                            <td class="fw-semibold">{{ $item->siswa?->nama ?? '-' }}</td>
                            <td>{{ $item->siswa?->perusahaan?->nama_perusahaan ?? '-' }}</td>
                            <td>
                                <span class="badge-status badge-{{ $statusColors[$item->status] ?? 'secondary' }}">{{ ucfirst($item->status) }}</span>
                            </td>
                            <td>{{ $item->jam_masuk ?? '-' }}</td>
                            <td>{{ $item->jam_keluar ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty-state">
                                <i class="bi bi-inbox"></i><p>Belum ada siswa yang absen hari ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="table-pagination px-4 pb-4 d-flex justify-content-center">
            {{ $todayAbsensi->links('vendor.pagination.clean') }}
        </div>
    </div>
</div>
@endsection
