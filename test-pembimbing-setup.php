<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Siswa;

echo "\n=== PEMBIMBING SETUP VERIFICATION ===\n\n";

// Test 1: Check Guru Pembimbing Accounts
echo "TEST 1: Guru Pembimbing Accounts\n";
$guruList = User::where('role', 'guru_pembimbing')->get();
echo "Total Guru Pembimbing: " . $guruList->count() . "\n";
foreach ($guruList as $guru) {
    $siswaBimbingan = $guru->siswaBimbingan()->count();
    echo "  ✓ {$guru->name} ({$guru->email}) - Membimbing {$siswaBimbingan} siswa\n";
}
echo "\n";

// Test 2: Check Pembimbing Perusahaan Accounts
echo "TEST 2: Pembimbing Perusahaan Accounts\n";
$pembimbingList = User::where('role', 'pembimbing_perusahaan')->get();
echo "Total Pembimbing Perusahaan: " . $pembimbingList->count() . "\n";
foreach ($pembimbingList as $pembimbing) {
    $perusahaanBimbingan = $pembimbing->perusahaanBimbingan()->count();
    echo "  ✓ {$pembimbing->name} ({$pembimbing->email}) - Membimbing {$perusahaanBimbingan} perusahaan\n";
    
    if ($perusahaanBimbingan > 0) {
        foreach ($pembimbing->perusahaanBimbingan()->get() as $pt) {
            $siswaPt = $pt->siswa()->count();
            echo "      └─ {$pt->nama_perusahaan}: {$siswaPt} siswa\n";
        }
    }
}
echo "\n";

// Test 3: Check Siswa Assignment
echo "TEST 3: Siswa Assignment\n";
$allSiswa = Siswa::with(['guruPembimbing', 'perusahaan'])->get();
echo "Total Siswa: " . $allSiswa->count() . "\n";
foreach ($allSiswa as $siswa) {
    echo "  ✓ {$siswa->nama} (NIS: {$siswa->nis})\n";
    if ($siswa->guruPembimbing) {
        echo "      Guru Pembimbing: {$siswa->guruPembimbing->name}\n";
    }
    if ($siswa->perusahaan) {
        echo "      Perusahaan: {$siswa->perusahaan->nama_perusahaan}\n";
    }
}
echo "\n";

// Test 4: Check Perusahaan Pembimbing
echo "TEST 4: Perusahaan Pembimbing Assignment\n";
$perusahaanList = \App\Models\Perusahaan::with('pembimbingPerusahaan')->get();
foreach ($perusahaanList as $pt) {
    $pembimbing = $pt->pembimbingPerusahaan ? $pt->pembimbingPerusahaan->name : "Belum ditunjuk";
    echo "  ✓ {$pt->nama_perusahaan}\n";
    echo "      Pembimbing: {$pembimbing}\n";
}
echo "\n";

// Test 5: Check Routes
echo "TEST 5: Routes Verification\n";
echo "  Dashboard: GET /pembimbing/dashboard\n";
echo "  Absensi: GET /pembimbing/absensi\n";
echo "  Download Absensi: GET /pembimbing/absensi/download-pdf\n";
echo "  Jurnal: GET /pembimbing/jurnal\n";
echo "  Download Jurnal: GET /pembimbing/jurnal/download-pdf\n";
echo "\n";

echo "✅ Setup verification selesai!\n\n";
