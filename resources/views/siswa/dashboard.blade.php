@extends('layouts.app')

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-1"><i class="bi bi-speedometer2 me-2"></i>Dashboard Siswa</h3>
    <p class="text-muted mb-0">Selamat datang, {{ $siswa->nama }}</p>
</div>

<div class="card card-modern mb-4 fade-in">
    <div class="card-body p-4">
        <h5 class="fw-bold mb-3"><i class="bi bi-person-badge me-2" style="color:#FF8C42;"></i>Profil Siswa</h5>
        <div class="row g-3">
            <div class="col-md-3 col-6">
                <div class="text-muted small fw-semibold">Nama Lengkap</div>
                <div class="fw-bold">{{ $siswa->nama }}</div>
            </div>
            <div class="col-md-3 col-6">
                <div class="text-muted small fw-semibold">NIS</div>
                <div class="fw-bold">{{ $siswa->nis }}</div>
            </div>
            <div class="col-md-3 col-6">
                <div class="text-muted small fw-semibold">Kelas / Jurusan</div>
                <div class="fw-bold">{{ $siswa->kelas }} &mdash; {{ $siswa->jurusan }}</div>
            </div>
            <div class="col-md-3 col-6">
                <div class="text-muted small fw-semibold">Perusahaan</div>
                <div class="fw-bold">{{ $siswa->perusahaan?->nama_perusahaan ?? '-' }}</div>
            </div>
        </div>
    </div>
</div>

<h6 class="fw-bold text-muted mb-3"><i class="bi bi-clipboard-data me-1"></i>Statistik Kehadiran</h6>
<div class="row g-3">
    <div class="col-md-3 col-6">
        <div class="card stat-card card-modern fade-in fade-in-delay-1">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div class="stat-icon text-white" style="background: linear-gradient(135deg, #10b981, #34d399);"><i class="bi bi-check-circle-fill"></i></div>
                <div>
                    <div class="text-muted small fw-semibold">Hadir</div>
                    <div class="fs-4 fw-bold" style="color:#059669;">{{ $stats['hadir'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
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
    <div class="col-md-3 col-6">
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
    <div class="col-md-3 col-6">
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
@endsection
