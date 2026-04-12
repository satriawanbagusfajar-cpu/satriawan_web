<?php
require 'vendor/autoload.php';

// Load Laravel app
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

// Test login dengan pembimbing account
$pembimbing = User::where('email', 'guru.pembimbing1@pkl.test')->first();

if (!$pembimbing) {
    echo "❌ Pembimbing user tidak ditemukan\n";
    exit(1);
}

echo "✅ User pembimbing ditemukan:\n";
echo "  - Email: {$pembimbing->email}\n";
echo "  - Nama: {$pembimbing->name}\n";
echo "  - Role: {$pembimbing->role}\n\n";

// Check siswa bimbingan
$siswaBimbingan = $pembimbing->siswaBimbingan()->get();
echo "✅ Jumlah siswa bimbingan: " . $siswaBimbingan->count() . "\n";

if ($siswaBimbingan->count() > 0) {
    echo "   Daftar siswa:\n";
    foreach ($siswaBimbingan as $siswa) {
        echo "   - {$siswa->nama_siswa} ({$siswa->nisn})\n";
    }
} else {
    echo "⚠️  Belum ada siswa yang dibimbing guru ini\n";
}

// Check absensi data
$absensiCount = \App\Models\Absensi::whereIn('siswa_id', $siswaBimbingan->pluck('id'))->count();
echo "\n✅ Total absensi siswa bimbingan: {$absensiCount}\n";

// Check jurnal data
$jurnalCount = \App\Models\Jurnal::whereIn('siswa_id', $siswaBimbingan->pluck('id'))->count();
echo "✅ Total jurnal siswa bimbingan: {$jurnalCount}\n";

// Check role middleware permission
echo "\n✅ Role user: {$pembimbing->role}\n";
$allowedRoles = ['guru_pembimbing', 'pembimbing_perusahaan'];
if (in_array($pembimbing->role, $allowedRoles)) {
    echo "✅ Role pembimbing VALID untuk akses pembimbing module\n";
} else {
    echo "❌ Role pembimbing TIDAK VALID\n";
}

echo "\n=== PEMBIMBING VERIFICATION COMPLETE ===\n";
