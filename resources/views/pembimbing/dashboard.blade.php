@extends('layouts.app')

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-center">
    <div>
        <h3 class="fw-bold mb-1"><i class="bi bi-graph-up me-2"></i>Dashboard Pembimbing</h3>
        <p class="text-muted mb-0">Pantau siswa yang Anda bimbing</p>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card stat-card card-modern fade-in fade-in-delay-1">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div class="stat-icon text-white" style="background: linear-gradient(135deg, #8b5cf6, #a78bfa);"><i class="bi bi-person-check-fill"></i></div>
                <div>
                    <div class="text-muted small fw-semibold">Jumlah Siswa</div>
                    <div class="fs-4 fw-bold" style="color:#7c3aed;">{{ $totalSiswa }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card stat-card card-modern fade-in fade-in-delay-2">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div class="stat-icon text-white" style="background: linear-gradient(135deg, #06b6d4, #22d3ee);"><i class="bi bi-clock-history"></i></div>
                <div>
                    <div class="text-muted small fw-semibold">Total Absensi</div>
                    <div class="fs-4 fw-bold" style="color:#0891b2;">{{ $totalAbsensi }}</div>
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
                    <div class="fs-4 fw-bold" style="color:#db2777;">{{ $totalJurnal }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card stat-card card-modern fade-in fade-in-delay-4">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div class="stat-icon text-white" style="background: linear-gradient(135deg, #10b981, #34d399);"><i class="bi bi-file-earmark-text"></i></div>
                <div>
                    <div class="text-muted small fw-semibold">Rata-rata per Siswa</div>
                    <div class="fs-4 fw-bold" style="color:#059669;">{{ $totalSiswa > 0 ? round($totalJurnal / $totalSiswa, 1) : 0 }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white border-bottom py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-list-check me-2"></i>Daftar Siswa Bimbingan</h5>
            <div>
                <a href="{{ route('pembimbing.absensi.index') }}" class="btn btn-sm btn-outline-primary me-2">
                    <i class="bi bi-calendar-check me-1"></i>Monitoring Absensi
                </a>
                <a href="{{ route('pembimbing.jurnal.index') }}" class="btn btn-sm btn-outline-info">
                    <i class="bi bi-journal me-1"></i>Monitoring Jurnal
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        @if($siswaBimbingan->isEmpty())
            <div class="alert alert-info mb-0">
                <i class="bi bi-info-circle me-2"></i>Tidak ada siswa yang Anda bimbing saat ini.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr style="background-color: #f8f9fa;">
                            <th>Nama Siswa</th>
                            <th>NIS</th>
                            <th>Kelas</th>
                            <th>Perusahaan</th>
                            <th>Absensi</th>
                            <th>Jurnal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($siswaBimbingan as $siswa)
                            @php
                                $absensiCount = $siswa->absensi()->count();
                                $jurnalCount = $siswa->jurnal()->count();
                            @endphp
                            <tr>
                                <td class="fw-semibold">{{ $siswa->nama }}</td>
                                <td>{{ $siswa->nis }}</td>
                                <td>{{ $siswa->kelas }}</td>
                                <td>{{ $siswa->perusahaan?->nama_perusahaan ?? '-' }}</td>
                                <td>
                                    @if($absensiCount > 0)
                                        <span class="badge bg-success">{{ $absensiCount }} data</span>
                                    @else
                                        <span class="badge bg-secondary">Belum ada</span>
                                    @endif
                                </td>
                                <td>
                                    @if($jurnalCount > 0)
                                        <span class="badge bg-success">{{ $jurnalCount }} data</span>
                                    @else
                                        <span class="badge bg-secondary">Belum ada</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('pembimbing.absensi.index', ['siswa' => $siswa->id]) }}" class="btn btn-sm btn-outline-primary" title="Lihat Absensi">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('pembimbing.jurnal.index', ['siswa' => $siswa->id]) }}" class="btn btn-sm btn-outline-info" title="Lihat Jurnal">
                                        <i class="bi bi-file-text"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
