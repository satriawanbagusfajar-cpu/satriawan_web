@extends('layouts.app')

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-center">
    <div>
        <h3 class="fw-bold mb-1"><i class="bi bi-people-fill me-2"></i>Data Siswa</h3>
        <p class="text-muted mb-0">Kelola data siswa PKL</p>
    </div>
    <div class="d-flex gap-2 mt-2 mt-md-0">
        <a href="{{ route('admin.siswa.import') }}" class="btn btn-outline-primary"><i class="bi bi-upload me-1"></i>Import CSV</a>
        <a href="{{ route('admin.siswa.create') }}" class="btn btn-gradient"><i class="bi bi-plus-lg me-1"></i>Tambah Siswa</a>
    </div>
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
                    <th>Nama</th>
                    <th>NIS</th>
                    <th>Kelas</th>
                    <th>Jurusan</th>
                    <th>Perusahaan</th>
                    <th width="130">Aksi</th>
                </tr>
                </thead>
                <tbody>
                @forelse($siswa as $item)
                    <tr>
                        <td class="fw-semibold">{{ $item->nama }}</td>
                        <td><span class="badge-status badge-secondary">{{ $item->nis }}</span></td>
                        <td>{{ $item->kelas }}</td>
                        <td>{{ $item->jurusan }}</td>
                        <td>{{ $item->perusahaan?->nama_perusahaan ?? '<span class="text-muted">-</span>' }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.siswa.edit', $item) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                <form action="{{ route('admin.siswa.destroy', $item) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus data siswa ini?')" title="Hapus"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty-state"><i class="bi bi-inbox"></i><p>Belum ada data siswa.</p></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="table-pagination mt-3">
    <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-2">
        <small class="text-muted">
            Menampilkan {{ $siswa->firstItem() ?? 0 }}-{{ $siswa->lastItem() ?? 0 }} dari {{ $siswa->total() }} data siswa
        </small>
        {{ $siswa->links('vendor.pagination.clean') }}
    </div>
</div>
@endsection
