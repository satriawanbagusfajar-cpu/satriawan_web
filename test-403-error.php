<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Auth;
use App\Models\User;

echo "\n=== 403 ERROR DIAGNOSIS ===\n\n";

// Test 1: Check if pembimbing accounts exist and have correct role
echo "TEST 1: Check Pembimbing Accounts\n";
$pembimbingUsers = User::whereIn('role', ['guru_pembimbing', 'pembimbing_perusahaan'])->get();

if ($pembimbingUsers->isEmpty()) {
    echo "❌ ERROR: Tidak ada akun pembimbing ditemukan!\n";
} else {
    echo "✓ Total pembimbing accounts: " . $pembimbingUsers->count() . "\n";
    foreach ($pembimbingUsers as $user) {
        echo "  - {$user->name} ({$user->email}) | Role: {$user->role}\n";
    }
}
echo "\n";

// Test 2: Try to login as guru_pembimbing
echo "TEST 2: Simulate Login as Guru Pembimbing\n";
$guruUser = User::where('email', 'guru.pembimbing1@pkl.test')->first();

if (!$guruUser) {
    echo "❌ ERROR: Akun guru.pembimbing1@pkl.test tidak ditemukan!\n";
} else {
    // Simulate login
    Auth::login($guruUser);
    
    if (Auth::check()) {
        $currentUser = Auth::user();
        echo "✓ Login berhasil\n";
        echo "  User: {$currentUser->name}\n";
        echo "  Role: {$currentUser->role}\n";
        
        // Check role validation
        $hasCorrectRole = in_array($currentUser->role, ['guru_pembimbing', 'pembimbing_perusahaan']);
        echo "  Has pembimbing role: " . ($hasCorrectRole ? "✓ YES" : "❌ NO") . "\n";
        
        // Check relasi
        $siswaBimbingan = $currentUser->siswaBimbingan()->count();
        echo "  Jumlah siswa dibimbing: {$siswaBimbingan}\n";
    } else {
        echo "❌ ERROR: Login gagal!\n";
    }
}
echo "\n";

// Test 3: Try to login as pembimbing_perusahaan
echo "TEST 3: Simulate Login as Pembimbing Perusahaan\n";
$ptUser = User::where('email', 'pembimbing.samick@pkl.test')->first();

if (!$ptUser) {
    echo "❌ ERROR: Akun pembimbing.samick@pkl.test tidak ditemukan!\n";
} else {
    Auth::logout();
    Auth::login($ptUser);
    
    if (Auth::check()) {
        $currentUser = Auth::user();
        echo "✓ Login berhasil\n";
        echo "  User: {$currentUser->name}\n";
        echo "  Role: {$currentUser->role}\n";
        
        // Check role validation
        $hasCorrectRole = in_array($currentUser->role, ['guru_pembimbing', 'pembimbing_perusahaan']);
        echo "  Has pembimbing role: " . ($hasCorrectRole ? "✓ YES" : "❌ NO") . "\n";
        
        // Check relasi perusahaan
        $perusahaanBimbingan = $currentUser->perusahaanBimbingan()->count();
        echo "  Jumlah perusahaan dibimbing: {$perusahaanBimbingan}\n";
    } else {
        echo "❌ ERROR: Login gagal!\n";
    }
}
echo "\n";

// Test 4: Check middleware logic
echo "TEST 4: Middleware Role Check Logic\n";
$siswaUser = User::where('role', 'siswa')->first();
if ($siswaUser) {
    Auth::logout();
    Auth::login($siswaUser);
    
    $currentUser = Auth::user();
    $roles = ['guru_pembimbing', 'pembimbing_perusahaan'];
    $hasAccess = in_array($currentUser->role, $roles, true);
    
    echo "User: {$currentUser->name} (Role: {$currentUser->role})\n";
    echo "Required roles: " . implode(', ', $roles) . "\n";
    echo "❌ Can access /pembimbing: " . ($hasAccess ? "YES" : "NO - Will get 403") . "\n";
}
echo "\n";

// Test 5: Check siswa without siswa record
echo "TEST 5: Check Siswa Account Status\n";
$testSiswa = User::where('email', 'siswa@pkl.test')->first();
if ($testSiswa) {
    echo "Siswa account: {$testSiswa->name}\n";
    $siswaNative = $testSiswa->siswa;
    if ($siswaNative) {
        echo "  ✓ Has siswa record: {$siswaNative->nama}\n";
    } else {
        echo "  ❌ NO siswa record - will get 403 on /siswa/dashboard!\n";
    }
}
echo "\n";

echo "=== DIAGNOSIS COMPLETE ===\n\n";
