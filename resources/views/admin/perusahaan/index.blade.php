@extends('layouts.app')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h3 class="fw-bold mb-1"><i class="bi bi-building me-2"></i>Data Perusahaan</h3>
        <p class="text-muted mb-0">Kelola data perusahaan mitra PKL</p>
    </div>
    <a href="{{ route('admin.perusahaan.create') }}" class="btn btn-gradient"><i class="bi bi-plus-lg me-1"></i>Tambah Perusahaan</a>
</div>

<div class="card card-modern mb-3">
    <div class="card-body py-2 px-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <small class="text-muted mb-0">Atur jumlah data per halaman</small>
        <form method="GET" class="d-flex align-items-center gap-2">
            <label for="per_page" class="small text-muted mb-0">Tampilkan</label>
            <select class="form-select form-select-sm" id="per_page" name="per_page" onchange="this.form.submit()">
                @foreach([5,10,25,50] as $size)
                    <option value="{{ $size }}" @selected((int) request('per_page', 10) === $size)>{{ $size }}</option>
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
                    <th>Nama Perusahaan</th>
                    <th>Alamat</th>
                    <th>Pembimbing</th>
                    <th width="150">Aksi</th>
                </tr>
                </thead>
                <tbody>
                @forelse($perusahaan as $item)
                    <tr>
                        <td class="fw-semibold">{{ $item->nama_perusahaan }}</td>
                        <td>{{ $item->alamat }}</td>
                        <td>
                            @if($item->pembimbingPerusahaan)
                                <span class="badge bg-info">{{ $item->pembimbingPerusahaan->name }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.perusahaan.edit', $item) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                <form action="{{ route('admin.perusahaan.destroy', $item) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus data perusahaan ini?')" title="Hapus"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="empty-state"><i class="bi bi-inbox"></i><p>Belum ada data perusahaan.</p></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="table-pagination mt-3">
    <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-2">
        <small class="text-muted">
            Menampilkan {{ $perusahaan->firstItem() ?? 0 }}-{{ $perusahaan->lastItem() ?? 0 }} dari {{ $perusahaan->total() }} data perusahaan
        </small>
        {{ $perusahaan->links('vendor.pagination.clean') }}
    </div>
</div>
@endsection
