@extends('layouts.app')

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
    <div>
        <h3 class="fw-bold mb-1"><i class="bi bi-journal-text me-2"></i>Jurnal Harian Siswa</h3>
        <p class="text-muted mb-0">Catatan kegiatan harian seluruh siswa PKL</p>
    </div>
</div>

<!-- Export PDF Section -->
<div class="card card-modern mb-4" style="background: linear-gradient(135deg, #fef3c7 0%, #fef08a 100%); border: 2px solid #f59e0b;">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <h6 class="fw-bold mb-1"><i class="bi bi-file-pdf me-2" style="color:#d97706;"></i>Export Jurnal ke PDF</h6>
                <p class="text-muted small mb-2">Unduh laporan jurnal kegiatan siswa per bulan dalam format PDF</p>
            </div>
            <form action="{{ route('admin.jurnal.exportPdf') }}" method="GET" class="row g-2 align-items-end w-100">
                <div class="col-12 col-md-6 col-lg">
                    <label class="form-label fw-semibold small mb-1">Pilih Siswa</label>
                    <select class="form-select form-select-sm" name="siswa_id" required>
                        <option value="">-- Pilih Siswa --</option>
                        @foreach($siswa as $item)
                            <option value="{{ $item->id }}">{{ $item->nama }} ({{ $item->nis }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label fw-semibold small mb-1">Bulan</label>
                    <select class="form-select form-select-sm" name="bulan">
                        @php
                            $namaBulan = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                            $bulanSekarang = now()->month;
                        @endphp
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" @selected($i === $bulanSekarang)>{{ $namaBulan[$i] }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label fw-semibold small mb-1">Tahun</label>
                    <select class="form-select form-select-sm" name="tahun">
                        @for($i = now()->year; $i >= now()->year - 5; $i--)
                            <option value="{{ $i }}" @selected($i === now()->year)>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-12 col-lg-auto d-grid">
                    <button type="submit" class="btn btn-warning btn-sm"><i class="bi bi-download me-1"></i>Download PDF</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="card card-modern mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-8">
                <label class="form-label fw-semibold"><i class="bi bi-person me-1"></i>Pilih Siswa</label>
                <select class="form-select" name="siswa_id">
                    <option value="">Semua Siswa</option>
                    @foreach($siswa as $item)
                        <option value="{{ $item->id }}" @selected(request('siswa_id') == $item->id)>
                            {{ $item->nama }} - {{ $item->nis }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button class="btn btn-gradient w-100"><i class="bi bi-funnel me-1"></i>Filter</button>
                <a href="{{ route('admin.jurnal.index') }}" class="btn btn-outline-primary w-100"><i class="bi bi-arrow-counterclockwise me-1"></i>Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="row g-3">
    @forelse($jurnal as $item)
        <div class="col-md-6 col-xl-4">
            <div class="card card-modern h-100 fade-in">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge text-bg-dark">{{ $item->siswa?->nis ?? '-' }}</span>
                                <span class="badge text-bg-light text-dark">{{ $item->siswa?->kelas ?? '-' }}</span>
                            </div>
                            <h5 class="fw-bold mb-1">{{ $item->siswa?->nama ?? '-' }}</h5>
                            <div class="text-muted small">{{ $item->siswa?->perusahaan?->nama_perusahaan ?? '-' }}</div>
                        </div>
                        <div class="text-end">
                            <div class="text-muted small">Tanggal</div>
                            <div class="fw-semibold">{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="text-muted small fw-semibold mb-1">Kegiatan</div>
                        <div class="p-3 rounded-3" style="background:#f8fafc; border:1px solid var(--border);">
                            {{ $item->kegiatan }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="text-muted small fw-semibold mb-1">Approval</div>
                        <div>
                            <span class="badge bg-{{ $item->approval_badge_class }}">{{ ucfirst($item->approval_status ?? 'pending') }}</span>
                            @if($item->approval_notes)
                                <small class="d-block text-muted mt-1">{{ $item->approval_notes }}</small>
                            @endif
                        </div>
                    </div>

                    <div>
                        <div class="text-muted small fw-semibold mb-1">Keterangan</div>
                        <div class="text-muted">
                            {{ $item->keterangan ?? '-' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card card-modern">
                <div class="card-body empty-state">
                    <i class="bi bi-inbox"></i><p>Belum ada data jurnal.</p>
                </div>
            </div>
        </div>
    @endforelse
</div>

<div class="table-pagination mt-3 d-flex justify-content-center">
    {{ $jurnal->links('pagination::bootstrap-5') }}
    </div>
@endsection
