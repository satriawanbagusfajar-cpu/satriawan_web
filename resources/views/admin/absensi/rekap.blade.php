@extends('layouts.app')

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-2">
    <div>
        <h3 class="fw-bold mb-1"><i class="bi bi-bar-chart-line me-2"></i>Rekap Absensi</h3>
        <p class="text-muted mb-0">Rekap kehadiran per hari, per bulan, per siswa</p>
    </div>
    <div class="d-flex gap-2 flex-wrap justify-content-end">
        <a href="{{ route('admin.absensi.rekap.download', request()->query()) }}" class="btn btn-gradient"><i class="bi bi-download me-1"></i>Unduh CSV</a>
        <a href="{{ route('admin.absensi.index') }}" class="btn btn-outline-primary"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
    </div>
</div>

{{-- Filter --}}
<div class="card card-modern mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-semibold"><i class="bi bi-calendar3 me-1"></i>Bulan</label>
                <select class="form-select" name="bulan">
                    @php
                        $namaBulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                    @endphp
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" @selected($bulan == $m)>{{ $namaBulan[$m] }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold"><i class="bi bi-calendar-event me-1"></i>Tahun</label>
                <select class="form-select" name="tahun">
                    @for($y = now()->year; $y >= now()->year - 3; $y--)
                        <option value="{{ $y }}" @selected($tahun == $y)>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold"><i class="bi bi-person me-1"></i>Siswa</label>
                <select class="form-select" name="siswa_id">
                    <option value="">Semua Siswa</option>
                    @foreach($allSiswa as $s)
                        <option value="{{ $s->id }}" @selected($siswaFilter == $s->id)>{{ $s->nama }} ({{ $s->nis }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-gradient w-100"><i class="bi bi-funnel me-1"></i>Filter</button>
            </div>
        </form>
    </div>
</div>

{{-- Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card stat-card card-modern fade-in fade-in-delay-1">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div class="stat-icon text-white" style="background: linear-gradient(135deg, #10b981, #34d399);"><i class="bi bi-check-circle-fill"></i></div>
                <div>
                    <div class="text-muted small fw-semibold">Hadir</div>
                    <div class="fs-4 fw-bold" style="color:#059669;">{{ $grandTotal['hadir'] }}</div>
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
                    <div class="fs-4 fw-bold" style="color:#d97706;">{{ $grandTotal['izin'] }}</div>
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
                    <div class="fs-4 fw-bold" style="color:#0891b2;">{{ $grandTotal['sakit'] }}</div>
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
                    <div class="fs-4 fw-bold" style="color:#dc2626;">{{ $grandTotal['alpha'] }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Rekap Table --}}
<div class="card card-modern">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-modern mb-0" style="font-size: .82rem;">
                <thead>
                    <tr>
                        <th class="sticky-col" style="min-width:160px; position:sticky; left:0; z-index:2; background:linear-gradient(135deg, #FF8C42 0%, #FF6B35 100%);">Nama Siswa</th>
                        @foreach($dates as $date)
                            @php $d = \Carbon\Carbon::parse($date); @endphp
                            <th class="text-center" style="min-width:36px;">
                                <div>{{ $d->format('d') }}</div>
                                <div class="fw-normal" style="font-size:.7rem; text-transform:none;">{{ $d->locale('id')->shortDayName }}</div>
                            </th>
                        @endforeach
                        <th class="text-center" style="min-width:40px; background:#059669;">H</th>
                        <th class="text-center" style="min-width:40px; background:#d97706;">I</th>
                        <th class="text-center" style="min-width:40px; background:#0891b2;">S</th>
                        <th class="text-center" style="min-width:40px; background:#dc2626;">A</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rekap as $r)
                        <tr>
                            <td class="fw-semibold" style="position:sticky; left:0; z-index:1; background:#fff;">{{ $r['siswa']->nama }}</td>
                            @foreach($dates as $date)
                                @php
                                    $status = $r['harian'][$date];
                                    $cellClass = match($status) {
                                        'hadir' => 'bg-success bg-opacity-25 text-success',
                                        'izin' => 'bg-warning bg-opacity-25 text-warning',
                                        'sakit' => 'bg-info bg-opacity-25 text-info',
                                        'alpha' => 'bg-danger bg-opacity-25 text-danger',
                                        default => '',
                                    };
                                    $cellText = match($status) {
                                        'hadir' => 'H',
                                        'izin' => 'I',
                                        'sakit' => 'S',
                                        'alpha' => 'A',
                                        default => '-',
                                    };
                                @endphp
                                <td class="text-center {{ $cellClass }}" style="font-weight:600;">{{ $cellText }}</td>
                            @endforeach
                            <td class="text-center fw-bold" style="color:#059669;">{{ $r['totals']['hadir'] }}</td>
                            <td class="text-center fw-bold" style="color:#d97706;">{{ $r['totals']['izin'] }}</td>
                            <td class="text-center fw-bold" style="color:#0891b2;">{{ $r['totals']['sakit'] }}</td>
                            <td class="text-center fw-bold" style="color:#dc2626;">{{ $r['totals']['alpha'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($dates) + 5 }}" class="empty-state">
                                <i class="bi bi-inbox"></i><p>Belum ada data siswa.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="table-pagination mt-3 d-flex justify-content-center">
    {{ $rekap->links('vendor.pagination.clean') }}
</div>

{{-- Legend --}}
<div class="mt-3 d-flex gap-3 flex-wrap">
    <span class="d-flex align-items-center gap-1"><span class="badge bg-success">&nbsp;</span> <small>H = Hadir</small></span>
    <span class="d-flex align-items-center gap-1"><span class="badge bg-warning">&nbsp;</span> <small>I = Izin</small></span>
    <span class="d-flex align-items-center gap-1"><span class="badge bg-info">&nbsp;</span> <small>S = Sakit</small></span>
    <span class="d-flex align-items-center gap-1"><span class="badge bg-danger">&nbsp;</span> <small>A = Alpha</small></span>
    <span class="d-flex align-items-center gap-1"><span class="text-muted">-</span> <small>= Belum ada data</small></span>
</div>
@endsection
