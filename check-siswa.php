<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Siswa;

echo "=== DAFTAR SISWA DI DATABASE ===\n\n";

$allSiswa = Siswa::with('guruPembimbing')->get();
echo "Total siswa: " . $allSiswa->count() . "\n\n";

if ($allSiswa->count() > 0) {
    foreach ($allSiswa as $siswa) {
        echo "ID: {$siswa->id}\n";
        echo "  NIS: {$siswa->nis}\n";
        echo "  Nama: {$siswa->nama}\n";
        echo "  Guru Pembimbing: " . ($siswa->guruPembimbing ? $siswa->guruPembimbing->name : "Belum ditentukan") . "\n";
        echo "  Perusahaan: {$siswa->perusahaan_id}\n\n";
    }
} else {
    echo "❌ Belum ada data siswa di database\n";
}
