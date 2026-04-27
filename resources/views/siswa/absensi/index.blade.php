@extends('layouts.app')

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-1"><i class="bi bi-calendar-check me-2"></i>Absensi Harian</h3>
    <p class="text-muted mb-0">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
</div>

<div class="alert alert-light border mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between gap-2">
        <div>
            <div class="fw-semibold">Periode absensi minggu ini</div>
            <div class="text-muted small">{{ $mingguIniMulai->translatedFormat('d M Y') }} - {{ $mingguIniSelesai->translatedFormat('d M Y') }}</div>
        </div>
        <div class="text-md-end">
            <div class="fw-semibold">Jam kerja</div>
            <div class="text-muted small">Check-in 00:00 - 07:00 | Check-out 16:00 - 18:00</div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert-modern alert-success mb-4">
        <i class="bi bi-check-circle-fill fs-5"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

@if(session('error'))
    <div class="alert-modern alert-danger mb-4">
        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
        <span>{{ session('error') }}</span>
    </div>
@endif

@if(!$canCheckin)
    <div class="alert alert-warning border mb-4">
        <i class="bi bi-clock-history me-2"></i>Waktu check-in sudah lewat. Check-in hanya tersedia dari jam 00:00 sampai 07:00.
    </div>
@endif

{{-- ==================== BELUM ABSEN HARI INI ==================== --}}
@if(!$hariIni)
<div class="card card-modern mb-4">
    <div class="card-body p-4">
        <div class="text-center mb-4">
            <div class="stat-icon text-white mx-auto mb-3" style="background: linear-gradient(135deg, #FF8C42, #FF6B35); width:64px; height:64px; font-size:1.8rem;">
                <i class="bi bi-clock"></i>
            </div>
            <h5 class="fw-bold">Anda belum absen hari ini</h5>
            <p class="text-muted mb-0">Silakan pilih salah satu opsi di bawah</p>
        </div>

        <div class="row g-3 justify-content-center">
            {{-- Tombol Check-in + Foto --}}
            <div class="col-md-4">
                <form action="{{ route('siswa.absensi.checkin') }}" method="POST" enctype="multipart/form-data" id="checkinForm">
                    @csrf
                    <button type="button" class="btn btn-lg w-100 text-white py-3" style="background: linear-gradient(135deg, #10b981, #34d399); border:none; border-radius:16px; {{ !$canCheckin ? 'opacity:.6; cursor:not-allowed;' : '' }}" onclick="{{ $canCheckin ? 'prepareCheckin()' : 'alert(\'Check-in ditutup setelah jam 07:00.\')' }}" {{ !$canCheckin ? 'disabled' : '' }}>
                        <i class="bi bi-camera-fill fs-3 d-block mb-1"></i>
                        <span class="fw-bold">Check In + Foto</span>
                        <small class="d-block opacity-75">Hadir &middot; Upload bukti di lokasi</small>
                    </button>
                    <input type="hidden" name="lokasi" id="lokasiCheckin" required>
                    <input type="hidden" name="latitude" id="latitudeCheckin">
                    <input type="hidden" name="longitude" id="longitudeCheckin">
                    <input type="file" name="foto" id="fotoCheckin" accept="image/jpeg,image/png" capture="environment" class="d-none" required onchange="document.getElementById('checkinForm').submit()">
                    @error('foto')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    @error('lokasi')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    <div class="text-muted small mt-2" id="lokasiInfo">{{ $canCheckin ? 'Lokasi belum diambil.' : 'Check-in ditutup setelah jam 07:00.' }}</div>
                </form>
            </div>

            {{-- Tombol Izin --}}
            <div class="col-md-4">
                <form action="{{ route('siswa.absensi.izin') }}" method="POST">
                    @csrf
                    <input type="hidden" name="status" value="izin">
                    <button type="submit" class="btn btn-lg w-100 text-white py-3" style="background: linear-gradient(135deg, #f59e0b, #fbbf24); border:none; border-radius:16px;" onclick="return confirm('Yakin ingin mengajukan izin hari ini?')">
                        <i class="bi bi-envelope-paper fs-3 d-block mb-1"></i>
                        <span class="fw-bold">Izin</span>
                        <small class="d-block opacity-75">Tidak hadir karena izin</small>
                    </button>
                </form>
            </div>

            {{-- Tombol Sakit --}}
            <div class="col-md-4">
                <form action="{{ route('siswa.absensi.izin') }}" method="POST">
                    @csrf
                    <input type="hidden" name="status" value="sakit">
                    <button type="submit" class="btn btn-lg w-100 text-white py-3" style="background: linear-gradient(135deg, #06b6d4, #22d3ee); border:none; border-radius:16px;" onclick="return confirm('Yakin ingin mengajukan sakit hari ini?')">
                        <i class="bi bi-thermometer-half fs-3 d-block mb-1"></i>
                        <span class="fw-bold">Sakit</span>
                        <small class="d-block opacity-75">Tidak hadir karena sakit</small>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ==================== SUDAH CHECK-IN, BELUM CHECK-OUT ==================== --}}
@elseif(in_array($hariIni->status, ['hadir', 'terlambat']) && $hariIni->jam_masuk && !$hariIni->jam_keluar)
<div class="card card-modern mb-4">
    <div class="card-body p-4">
        <div class="text-center mb-4">
            <div class="stat-icon text-white mx-auto mb-3" style="background: linear-gradient(135deg, {{ $hariIni->isTerlambat() ? '#ef4444' : '#10b981' }}, {{ $hariIni->isTerlambat() ? '#f87171' : '#34d399' }}); width:64px; height:64px; font-size:1.8rem;">
                <i class="bi bi-check-lg"></i>
            </div>
            <h5 class="fw-bold {{ $hariIni->isTerlambat() ? 'text-danger' : 'text-success' }}">{{ $hariIni->isTerlambat() ? 'Anda Telat Check-in' : 'Anda Tepat Waktu Check-in' }}</h5>
            <p class="text-muted mb-0">Jam masuk tercatat pada pukul</p>
            <div class="fs-2 fw-bold mt-1 {{ $hariIni->isTerlambat() ? 'text-danger' : 'text-success' }}">{{ $hariIni->jam_masuk }}</div>
            <div class="mt-2">
                <span class="badge bg-{{ $hariIni->badge_waktu }} px-3 py-2">{{ $hariIni->keterangan_waktu }}</span>
            </div>
            @if($hariIni->foto)
                <div class="mt-3">
                    <img src="{{ route('media.public', ['path' => $hariIni->foto], false) }}" class="rounded-3 shadow-sm" style="max-height:150px; cursor:pointer;" alt="Foto Check-in" data-bs-toggle="modal" data-bs-target="#fotoHariIni">
                    <div class="text-muted small mt-1"><i class="bi bi-camera me-1"></i>Foto dokumentasi hari ini</div>
                </div>
            @endif
        </div>

        <div class="row justify-content-center">
            <div class="col-md-5">
                <form action="{{ route('siswa.absensi.checkout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-lg w-100 text-white py-3" style="background: linear-gradient(135deg, #ef4444, #f87171); border:none; border-radius:16px; {{ !$canCheckout ? 'opacity:.6; cursor:not-allowed;' : '' }}" onclick="{{ $canCheckout ? 'return confirm(\'Yakin ingin check-out sekarang?\')' : 'alert(\'Check-out hanya bisa dilakukan dari jam 16:00 sampai 18:00.\'); return false;' }}" {{ !$canCheckout ? 'disabled' : '' }}>
                        <i class="bi bi-box-arrow-right fs-3 d-block mb-1"></i>
                        <span class="fw-bold">Check Out</span>
                        <small class="d-block opacity-75">Catat Jam Keluar</small>
                    </button>
                </form>
                @if(!$canCheckout)
                    <div class="text-muted small mt-2 text-center">Check-out hanya tersedia dari jam 16:00 sampai 18:00.</div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ==================== SUDAH SELESAI (Check-in & Check-out) ==================== --}}
@elseif(in_array($hariIni->status, ['hadir', 'terlambat']) && $hariIni->jam_masuk && $hariIni->jam_keluar)
<div class="card card-modern mb-4">
    <div class="card-body p-4 text-center">
        <div class="stat-icon text-white mx-auto mb-3" style="background: linear-gradient(135deg, #10b981, #34d399); width:64px; height:64px; font-size:1.8rem;">
            <i class="bi bi-patch-check-fill"></i>
        </div>
        <h5 class="fw-bold text-success">Absensi Hari Ini Selesai</h5>
        <p class="text-muted mb-2">Terima kasih, kehadiran Anda sudah tercatat</p>
        <div class="mb-3">
            <span class="badge bg-{{ $hariIni->badge_waktu }} px-3 py-2">{{ $hariIni->keterangan_waktu }}</span>
        </div>
        @if($hariIni->foto)
            <div class="mb-3">
                <img src="{{ route('media.public', ['path' => $hariIni->foto], false) }}" class="rounded-3 shadow-sm" style="max-height:150px; cursor:pointer;" alt="Foto Check-in" data-bs-toggle="modal" data-bs-target="#fotoHariIni">
            </div>
        @endif

        <div class="row justify-content-center g-3 mt-2">
            <div class="col-auto">
                <div class="card border-0 px-4 py-3" style="background: rgba(16,185,129,0.1); border-radius:12px;">
                    <div class="text-muted small fw-semibold">Jam Masuk</div>
                    <div class="fs-4 fw-bold" style="color:#059669;">{{ $hariIni->jam_masuk }}</div>
                </div>
            </div>
            <div class="col-auto">
                <div class="card border-0 px-4 py-3" style="background: rgba(239,68,68,0.1); border-radius:12px;">
                    <div class="text-muted small fw-semibold">Jam Keluar</div>
                    <div class="fs-4 fw-bold" style="color:#dc2626;">{{ $hariIni->jam_keluar }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ==================== SUDAH IZIN / SAKIT ==================== --}}
@else
<div class="card card-modern mb-4">
    <div class="card-body p-4 text-center">
        @php $statusIcons = ['izin' => 'envelope-paper', 'sakit' => 'thermometer-half', 'alpha' => 'x-circle', 'terlambat' => 'alarm']; @endphp
        @php $statusGrad = ['izin' => 'linear-gradient(135deg, #f59e0b, #fbbf24)', 'sakit' => 'linear-gradient(135deg, #06b6d4, #22d3ee)', 'alpha' => 'linear-gradient(135deg, #ef4444, #f87171)', 'terlambat' => 'linear-gradient(135deg, #f97316, #fb923c)']; @endphp
        <div class="stat-icon text-white mx-auto mb-3" style="background: {{ $statusGrad[$hariIni->status] ?? 'linear-gradient(135deg, #FF8C42, #FF6B35)' }}; width:64px; height:64px; font-size:1.8rem;">
            <i class="bi bi-{{ $statusIcons[$hariIni->status] ?? 'calendar-x' }}"></i>
        </div>
        <h5 class="fw-bold">Absensi Hari Ini: {{ ucfirst($hariIni->status) }}</h5>
        <p class="text-muted mb-0">Status Anda untuk hari ini sudah tercatat</p>
    </div>
</div>
@endif

{{-- ==================== RIWAYAT ==================== --}}
<div class="card card-modern mb-3">
    <div class="card-body py-2 px-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <small class="text-muted mb-0">Atur jumlah data per halaman</small>
        <form method="GET" class="d-flex align-items-center gap-2">
            <label for="per_page" class="small text-muted mb-0">Tampilkan</label>
            <select class="form-select form-select-sm" id="per_page" name="per_page" onchange="this.form.submit()">
                @foreach([5,10,20,50] as $size)
                    <option value="{{ $size }}" @selected((int) request('per_page', 5) === $size)>{{ $size }}</option>
                @endforeach
            </select>
        </form>
    </div>
</div>

<div class="card card-modern">
    <div class="card-header bg-white border-0 pt-3 px-4">
        <h6 class="fw-bold mb-0"><i class="bi bi-clock-history me-1"></i>Riwayat Absensi</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Hari</th>
                    <th>Jam Masuk</th>
                    <th>Jam Keluar</th>
                    <th>Foto</th>
                    <th>Lokasi</th>
                    <th>Status</th>
                    <th>Approval</th>
                </tr>
                </thead>
                <tbody>
                @php $fotoItems = []; @endphp
                @forelse($riwayat as $item)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->locale('id')->isoFormat('dddd') }}</td>
                        <td>
                            <span class="fw-semibold {{ $item->status === 'hadir' ? ($item->isTerlambat() ? 'text-danger' : 'text-success') : '' }}">
                                {{ $item->jam_masuk ?? '-' }}
                            </span>
                        </td>
                        <td>{{ $item->jam_keluar ?? '-' }}</td>
                        <td>
                            @if($item->foto)
                                @php $fotoItems[] = $item; @endphp
                                <img src="{{ route('media.public', ['path' => $item->foto], false) }}" class="rounded" style="height:40px; width:40px; object-fit:cover; cursor:pointer;" alt="Foto" data-bs-toggle="modal" data-bs-target="#fotoModal{{ $item->id }}">
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($item->lokasi)
                                <span class="small">{{ $item->lokasi }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge-status badge-{{ $item->badge_waktu }}">{{ $item->keterangan_waktu }}</span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $item->approval_badge_class }}">{{ ucfirst($item->approval_status ?? 'pending') }}</span>
                        </td>
                    </tr>

                @empty
                    <tr><td colspan="8" class="empty-state"><i class="bi bi-inbox"></i><p>Belum ada riwayat absensi.</p></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="table-pagination px-3 py-3">
            <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-2">
                <small class="text-muted">
                    Menampilkan {{ $riwayat->firstItem() ?? 0 }}-{{ $riwayat->lastItem() ?? 0 }} dari {{ $riwayat->total() }} riwayat
                </small>
                {{ $riwayat->links('vendor.pagination.clean') }}
            </div>
        </div>

        @foreach($fotoItems as $item)
            <div class="modal fade" id="fotoModal{{ $item->id }}" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-0 rounded-4 overflow-hidden">
                        <div class="modal-header border-0 pb-0">
                            <h6 class="modal-title fw-bold"><i class="bi bi-camera me-1"></i>{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</h6>
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

@if($hariIni && $hariIni->foto)
<div class="modal fade" id="fotoHariIni" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 overflow-hidden">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold"><i class="bi bi-camera me-1"></i>Foto Dokumentasi Hari Ini</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-2">
                <img src="{{ route('media.public', ['path' => $hariIni->foto], false) }}" class="img-fluid rounded-3" style="max-height:70vh;" alt="Foto Check-in">
            </div>
        </div>
    </div>
</div>
@endif

<script>
function prepareCheckin() {
    const info = document.getElementById('lokasiInfo');
    const lokasiInput = document.getElementById('lokasiCheckin');
    const latitudeInput = document.getElementById('latitudeCheckin');
    const longitudeInput = document.getElementById('longitudeCheckin');
    const fotoInput = document.getElementById('fotoCheckin');

    if (!navigator.geolocation) {
        alert('Perangkat tidak mendukung GPS. Aktifkan perangkat/location service lalu coba lagi.');
        return;
    }

    info.textContent = 'Mengambil lokasi...';

    navigator.geolocation.getCurrentPosition(function (position) {
        const lat = Number(position.coords.latitude).toFixed(6);
        const lng = Number(position.coords.longitude).toFixed(6);

        latitudeInput.value = lat;
        longitudeInput.value = lng;
        lokasiInput.value = lat + ', ' + lng;
        info.textContent = 'Lokasi siap: ' + lokasiInput.value;

        fotoInput.click();
    }, function () {
        info.textContent = 'Lokasi gagal diambil.';
        alert('Lokasi wajib untuk absensi. Izinkan akses lokasi kemudian coba lagi.');
    }, {
        enableHighAccuracy: true,
        timeout: 10000,
        maximumAge: 0
    });
}
</script>
@endsection
