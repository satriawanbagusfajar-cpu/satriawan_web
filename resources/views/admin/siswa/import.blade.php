@extends('layouts.app')

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-2">
    <div>
        <h3 class="fw-bold mb-1"><i class="bi bi-upload me-2"></i>Import Data Siswa</h3>
        <p class="text-muted mb-0">Upload file CSV untuk menambahkan banyak siswa sekaligus</p>
    </div>
    <a href="{{ route('admin.siswa.index') }}" class="btn btn-outline-primary"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
</div>

<div class="row g-4">
    <div class="col-md-7">
        <div class="card card-modern">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-file-earmark-arrow-up me-1" style="color:#FF8C42;"></i>Upload File CSV</h6>

                <form action="{{ route('admin.siswa.processImport') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label for="file" class="form-label fw-semibold">File CSV <span class="text-danger">*</span></label>
                        <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" name="file" accept=".csv,.txt" required>
                        @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Format: CSV (comma-separated), maksimal 2MB</div>
                    </div>

                    <button type="submit" class="btn btn-gradient"><i class="bi bi-upload me-1"></i>Import Siswa</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card card-modern">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-info-circle me-1" style="color:#10b981;"></i>Panduan Format CSV</h6>

                <p class="small text-muted mb-2">File CSV harus memiliki header (baris pertama) dengan kolom berikut:</p>

                <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-3" style="font-size:.85rem;">
                        <thead class="table-light">
                            <tr>
                                <th>Kolom</th>
                                <th>Wajib</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td><code>nama</code></td><td><span class="badge bg-danger">Ya</span></td><td>Nama lengkap siswa</td></tr>
                            <tr><td><code>nis</code></td><td><span class="badge bg-danger">Ya</span></td><td>NIS unik</td></tr>
                            <tr><td><code>kelas</code></td><td><span class="badge bg-danger">Ya</span></td><td>Contoh: XII RPL 1</td></tr>
                            <tr><td><code>jurusan</code></td><td><span class="badge bg-danger">Ya</span></td><td>Contoh: RPL</td></tr>
                            <tr><td><code>email</code></td><td><span class="badge bg-danger">Ya</span></td><td>Email untuk login</td></tr>
                            <tr><td><code>password</code></td><td><span class="badge bg-danger">Ya</span></td><td>Password login (min 6 karakter)</td></tr>
                            <tr><td><code>perusahaan</code></td><td><span class="badge bg-secondary">Tidak</span></td><td>Nama perusahaan (harus sudah ada di database)</td></tr>
                        </tbody>
                    </table>
                </div>

                <h6 class="fw-bold mb-2" style="font-size:.85rem;"><i class="bi bi-file-text me-1"></i>Contoh isi file CSV:</h6>
                <div class="bg-light p-3 rounded" style="font-size:.8rem; font-family:monospace; overflow-x:auto;">
                    nama,nis,kelas,jurusan,email,password,perusahaan<br>
                    Andi Pratama,NIS100,XII RPL 1,RPL,andi@pkl.test,andi123,Sakha Internasional<br>
                    Budi Santoso,NIS101,XII RPL 1,RPL,budi@pkl.test,budi123,<br>
                    Citra Dewi,NIS102,XII RPL 2,RPL,citra@pkl.test,citra123,Samick
                </div>

                <div class="mt-3">
                    <a href="{{ route('admin.siswa.import') }}?download_template=1" class="btn btn-sm btn-outline-success" id="downloadTemplate"><i class="bi bi-download me-1"></i>Download Template CSV</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('downloadTemplate').addEventListener('click', function(e) {
        e.preventDefault();
        const csv = 'nama,nis,kelas,jurusan,email,password,perusahaan\nContoh Siswa,NIS999,XII RPL 1,RPL,contoh@pkl.test,password123,';
        const blob = new Blob([csv], { type: 'text/csv' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'template_import_siswa.csv';
        a.click();
        URL.revokeObjectURL(url);
    });
</script>
@endpush
