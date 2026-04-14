@extends('layouts.app')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h3 class="fw-bold mb-1"><i class="bi bi-calendar-check me-2"></i>Data Absensi Siswa</h3>
        <p class="text-muted mb-0">Pantau kehadiran seluruh siswa PKL</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.absensi.rekap') }}" class="btn btn-gradient"><i class="bi bi-bar-chart-line me-1"></i>Lihat Rekap</a>
    </div>
</div>

<!-- Export PDF Section -->
<div class="card card-modern mb-3" style="background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%); border: 2px solid #10b981;">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <h6 class="fw-bold mb-1"><i class="bi bi-file-pdf me-2" style="color:#059669;"></i>Export Absensi ke PDF</h6>
                <p class="text-muted small mb-2">Unduh laporan absensi siswa per bulan dalam format PDF</p>
            </div>
            <form action="{{ route('admin.absensi.exportPdf') }}" method="GET" class="d-flex gap-2 align-items-end" style="min-width: 500px;">
                <div class="flex-grow-1">
                    <label class="form-label fw-semibold small mb-1">Pilih Siswa</label>
                    <select class="form-select form-select-sm" name="siswa_id" required>
                        <option value="">-- Pilih Siswa --</option>
                        @foreach($siswa as $item)
                            <option value="{{ $item->id }}">{{ $item->nama }} ({{ $item->nis }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label fw-semibold small mb-1">Bulan</label>
                    <select class="form-select form-select-sm" name="bulan" style="width: 130px;">
                        @php
                            $namaBulan = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                            $bulanSekarang = now()->month;
                        @endphp
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" @selected($i === $bulanSekarang)>{{ $namaBulan[$i] }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="form-label fw-semibold small mb-1">Tahun</label>
                    <select class="form-select form-select-sm" name="tahun" style="width: 100px;">
                        @for($i = now()->year; $i >= now()->year - 5; $i--)
                            <option value="{{ $i }}" @selected($i === now()->year)>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-download me-1"></i>Download PDF</button>
            </form>
        </div>
    </div>
</div>

<div class="card card-modern mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-semibold"><i class="bi bi-funnel me-1"></i>Filter Siswa</label>
                <select class="form-select" name="siswa_id">
                    <option value="">Semua siswa</option>
                    @foreach($siswa as $item)
                        <option value="{{ $item->id }}" @selected(request('siswa_id') == $item->id)>{{ $item->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Filter Perusahaan</label>
                <select class="form-select" name="perusahaan_id">
                    <option value="">Semua perusahaan</option>
                    @foreach($perusahaan as $item)
                        <option value="{{ $item->id }}" @selected(request('perusahaan_id') == $item->id)>{{ $item->nama_perusahaan }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <button class="btn btn-outline-primary"><i class="bi bi-search me-1"></i>Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card card-modern mb-3">
    <div class="card-body py-2 px-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <small class="text-muted mb-0">Atur jumlah data per halaman</small>
        <form method="GET" class="d-flex align-items-center gap-2">
            @if(request('siswa_id'))<input type="hidden" name="siswa_id" value="{{ request('siswa_id') }}">@endif
            @if(request('perusahaan_id'))<input type="hidden" name="perusahaan_id" value="{{ request('perusahaan_id') }}">@endif
            <label for="per_page" class="small text-muted mb-0">Tampilkan</label>
            <select class="form-select form-select-sm" id="per_page" name="per_page" onchange="this.form.submit()">
                @foreach([10,15,25,50] as $size)
                    <option value="{{ $size }}" @selected((int) request('per_page', 15) === $size)>{{ $size }}</option>
                @endforeach
            </select>
        </form>
    </div>
</div>

<div class="card card-modern">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead>
                <tr>
                    <th>Nama Siswa</th>
                    <th>Perusahaan</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Keterangan</th>
                    <th>Jam Masuk</th>
                    <th>Jam Keluar</th>
                    <th>Foto</th>
                </tr>
                </thead>
                <tbody>
                @php
                    $absensiGroups = $absensi->getCollection()->groupBy(fn ($row) => $row->siswa?->perusahaan?->nama_perusahaan ?? 'Belum ada perusahaan');
                    $companyBadgeColors = ['primary', 'success', 'warning', 'info', 'danger', 'secondary'];
                    $statusColors = ['hadir' => 'success', 'izin' => 'warning', 'sakit' => 'info', 'alpha' => 'danger'];
                    $fotoItems = [];
                @endphp

                @forelse($absensiGroups as $companyName => $rows)
                    @php
                        $companyColor = $companyBadgeColors[crc32($companyName) % count($companyBadgeColors)];
                    @endphp
                    <tr class="table-light">
                        <td colspan="8" class="fw-bold">
                            <i class="bi bi-building me-1"></i>
                            {{ $companyName }}
                            <span class="badge bg-dark-subtle text-dark ms-2">{{ $rows->count() }} data</span>
                        </td>
                    </tr>

                    @foreach($rows as $item)
                        <tr>
                            <td class="fw-semibold">{{ $item->siswa?->nama ?? '-' }}</td>
                            <td>
                                <span class="badge text-bg-{{ $companyColor }}">{{ $item->siswa?->perusahaan?->nama_perusahaan ?? '-' }}</span>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</td>
                            <td>
                                <span class="badge-status badge-{{ $statusColors[$item->status] ?? 'secondary' }}">{{ ucfirst($item->status) }}</span>
                            </td>
                            <td>
                                @if($item->status === 'hadir')
                                    <span class="badge-status badge-{{ $item->badge_waktu }}">{{ $item->keterangan_waktu }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($item->status === 'hadir')
                                    <span class="fw-semibold {{ $item->isTerlambat() ? 'text-danger' : 'text-success' }}">{{ $item->jam_masuk ?? '-' }}</span>
                                @else
                                    {{ $item->jam_masuk ?? '-' }}
                                @endif
                            </td>
                            <td>{{ $item->jam_keluar ?? '-' }}</td>
                            <td>
                                @if($item->foto)
                                    @php $fotoItems[] = $item; @endphp
                                    <img src="{{ route('media.public', ['path' => $item->foto], false) }}" class="rounded" style="height:40px; width:40px; object-fit:cover; cursor:pointer;" alt="Foto" data-bs-toggle="modal" data-bs-target="#fotoAdm{{ $item->id }}">
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach

                @empty
                    <tr><td colspan="8" class="empty-state"><i class="bi bi-inbox"></i><p>Belum ada data absensi.</p></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @foreach($fotoItems as $item)
            <div class="modal fade" id="fotoAdm{{ $item->id }}" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-0 rounded-4 overflow-hidden">
                        <div class="modal-header border-0 pb-0">
                            <div>
                                <h6 class="modal-title fw-bold mb-0"><i class="bi bi-person me-1"></i>{{ $item->siswa?->nama ?? '-' }}</h6>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</small>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body text-center p-2">
                            <img src="{{ route('media.public', ['path' => $item->foto], false) }}" class="img-fluid rounded-3" style="max-height:70vh;" alt="Foto Absensi">
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<div class="table-pagination mt-3">
    <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-2">
        <small class="text-muted">
            Menampilkan {{ $absensi->firstItem() ?? 0 }}-{{ $absensi->lastItem() ?? 0 }} dari {{ $absensi->total() }} data absensi
        </small>
        {{ $absensi->links('vendor.pagination.clean') }}
    </div>
</div>
@endsection
