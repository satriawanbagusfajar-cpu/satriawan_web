# Documentation - Guru Pembimbing & Pembimbing Perusahaan Setup

## 🎓 Sistem Pembimbing PKL

Sistem ini mendukung dua jenis pembimbing:
1. **Guru Pembimbing (Sekolah)** - Membimbing siswa dari sekolah
2. **Pembimbing Perusahaan** - Membimbing siswa di perusahaan tempat PKL

---

## 📋 DAFTAR AKUN TEST

### Guru Pembimbing (Sekolah)

| Nama | Email | Password | ID Siswa yang Dibimbing |
|------|-------|----------|------------------------|
| Bu Siti (Guru Pembimbing) | `guru.pembimbing1@pkl.test` | `guru123` | Siti (NIS003), Bagus (NIS002) |
| Pak Ahmad (Guru Pembimbing) | `guru.pembimbing2@pkl.test` | `guru456` | Siswa PKL (NIS001) |

### Pembimbing Perusahaan

| Nama | Email | Password | Perusahaan | ID Siswa |
|------|-------|----------|-----------|----------|
| Ir. Bambang Sakha | `pembimbing.sakha@pkl.test` | `sakha789` | Sakha Internasional | (akan terlihat di sistem) |
| Hendra Samick | `pembimbing.samick@pkl.test` | `samick789` | Samick | Siti (NIS003), Bagus (NIS002) |
| Dewi Indofood | `pembimbing.indofood@pkl.test` | `indofood789` | Indofood | Siswa PKL (NIS001) |

---

## 🎯 Fitur Dashboard Pembimbing

### Dashboard Utama (`/pembimbing/dashboard`)
Menampilkan:
- ✅ **Statistik Jumlah Siswa** - Total siswa yang dibimbing
- ✅ **Total Absensi** - Total data absensi semua siswa
- ✅ **Total Jurnal** - Total data jurnal semua siswa
- ✅ **Rata-rata Jurnal per Siswa** - Metrik produktivitas
- ✅ **Tabel Siswa Bimbingan** dengan status:
  - Status Absensi (berapa data ada / belum ada)
  - Status Jurnal (berapa data ada / belum ada)
  - Action buttons untuk lihat absensi/jurnal

---

## 📊 Monitoring Absensi (`/pembimbing/absensi`)

### Fitur:
- ✅ **Filter berdasarkan:**
  - Nama Siswa
  - Status (Hadir, Sakit, Izin, Alfa)
  - Tanggal spesifik
  
- ✅ **Tabel Absensi** menampilkan:
  - Tanggal
  - Nama Siswa
  - Jam Masuk & Jam Keluar
  - Status Kehadiran
  - Keterangan Waktu (Tepat Waktu / Telat)

- ✅ **Download PDF** - Export laporan absensi dengan filter yang diaplikasi
  - Format: A4 Landscape
  - Includes: Statistik (Hadir, Sakit, Izin, Alfa)

---

## 📖 Monitoring Jurnal (`/pembimbing/jurnal`)

### Fitur:
- ✅ **Filter berdasarkan:**
  - Nama Siswa
  - Tanggal spesifik
  - Bulan/Tahun

- ✅ **Tabel Jurnal** menampilkan:
  - Tanggal Jurnal
  - Nama Siswa
  - Isi Jurnal
  - Statistik count per siswa

- ✅ **Download PDF** - Export laporan jurnal dengan filter
  - Format: A4 Portrait
  - Includes: Semua data jurnal sesuai filter dengan formatting rapi

---

## 🔄 Perbedaan Guru Pembimbing vs Pembimbing Perusahaan

### Guru Pembimbing
```
Role: guru_pembimbing
Filter Siswa: Berdasarkan field guru_pembimbing_id di tabel siswa
Relasi: User -> siswaBimbingan() [guru_pembimbing_id]
```

### Pembimbing Perusahaan
```
Role: pembimbing_perusahaan
Filter Siswa: Berdasarkan perusahaan yang dibimbing
- User -> perusahaanBimbingan() [pembimbing_id di tabel perusahaan]
- Perusahaan -> siswa() [perusahaan_id di tabel siswa]
Relasi: User -> perusahaanBimbingan() -> siswa()
```

---

## 🗄️ Database Schema Updates

### Tabel `perusahaan`
Kolom baru ditambahkan pada migration:
```sql
ALTER TABLE perusahaan ADD COLUMN pembimbing_id BIGINT UNSIGNED NULLABLE;
ALTER TABLE perusahaan ADD FOREIGN KEY (pembimbing_id) REFERENCES users(id) ON DELETE SET NULL;
```

### Model Relationships
- **User.php**: 
  - `perusahaanBimbingan()` - HasMany relationship ke Perusahaan
  
- **Perusahaan.php**:
  - `pembimbingPerusahaan()` - BelongsTo relationship ke User

---

## 🧪 Testing Scenarios

### Scenario 1: Guru Pembimbing Login
1. Login dengan `guru.pembimbing1@pkl.test` / `guru123`
2. Dashboard menampilkan siswa: Siti, Bagus
3. Absensi & Jurnal hanya menampilkan data dari siswa tersebut
4. PDF download works correctly

### Scenario 2: Pembimbing Perusahaan Login
1. Login dengan `pembimbing.samick@pkl.test` / `samick789`
2. Dashboard menampilkan siswa dari perusahaan Samick: Siti, Bagus
3. Absensi & Jurnal hanya menampilkan data dari siswa tersebut
4. PDF download works correctly

### Scenario 3: Multi-filter & Download
1. Login sebagai pembimbing
2. Filter absensi berdasarkan siswa, status, tanggal
3. Click "Download PDF" button
4. Verify PDF berisi data yang sesuai filter

---

## ✨ Views Diupdate

1. **`resources/views/pembimbing/dashboard.blade.php`**
   - Added kolom status absensi & jurnal
   - Shows "X data" jika ada atau "Belum ada" jika kosong

2. **`resources/views/pembimbing/absensi/index.blade.php`**
   - Already has download PDF button
   - Supports both types of pembimbing

3. **`resources/views/pembimbing/jurnal/index.blade.php`**
   - Already has download PDF button  
   - Supports both types of pembimbing

4. **`resources/views/pembimbing/absensi/pdf.blade.php`**
   - PDF template for absensi

5. **`resources/views/pembimbing/jurnal/pdf.blade.php`**
   - PDF template for jurnal

---

## 🔐 Authentication & Authorization

Middleware configuration di `bootstrap/app.php`:
```php
'role' => RoleMiddleware::class
```

Routes protection:
```php
Route::middleware(['auth', 'role:guru_pembimbing|pembimbing_perusahaan'])->prefix('pembimbing')->name('pembimbing.')->group(...)
```

---

## 📝 Controllers Updated

1. **`app/Http/Controllers/Pembimbing/DashboardController.php`**
   - Supports both guru_pembimbing dan pembimbing_perusahaan
   - Dynamic query based on role

2. **`app/Http/Controllers/Pembimbing/AbsensiController.php`**
   - Updated index() & downloadPdf() untuk kedua role
   - Proper filtering

3. **`app/Http/Controllers/Pembimbing/JurnalController.php`**
   - Updated index() & downloadPdf() untuk kedua role
   - Proper filtering

---

## 🎯 Checklist Fitur

- ✅ Dashboard pembimbing dengan statistik
- ✅ Monitoring absensi siswa bimbingan
- ✅ Monitoring jurnal siswa bimbingan
- ✅ Filter data berdasarkan siswa, tanggal, status, bulan
- ✅ Download absensi PDF
- ✅ Download jurnal PDF
- ✅ Support guru_pembimbing
- ✅ Support pembimbing_perusahaan
- ✅ Show status (ada/belum ada) di dashboard untuk absensi & jurnal
- ✅ Test accounts for both types

---

## 🚀 Cara Menggunakan

### Untuk Testing:
1. Run: `php artisan migrate --step`
2. Run: `php artisan db:seed --class=PembimbingSeeder`
3. Login dengan salah satu akun test
4. Navigate ke `/pembimbing/dashboard`
5. Test semua fitur: filter, download PDF

### Untuk Produksi:
1. Sesuaikan data siswa dan perusahaan di database
2. Link pembimbing ke siswa (guru_pembimbing_id) atau perusahaan (pembimbing_id)
3. User dengan role `guru_pembimbing` atau `pembimbing_perusahaan` dapat login

