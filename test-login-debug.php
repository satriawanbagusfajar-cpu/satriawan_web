<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

echo "=== LOGIN TEST PEMBIMBING ===\n\n";

// Find pembimbing user
$pembimbing = User::where('email', 'guru.pembimbing1@pkl.test')->first();

if (!$pembimbing) {
    echo "❌ User tidak ditemukan\n";
    exit(1);
}

echo "✅ User ditemukan:\n";
echo "   Email: {$pembimbing->email}\n";
echo "   Nama: {$pembimbing->name}\n";
echo "   Role: {$pembimbing->role}\n";
echo "   Role Type: " . var_export($pembimbing->role, true) . "\n";
echo "   Role === 'guru_pembimbing': " . ($pembimbing->role === 'guru_pembimbing' ? 'true' : 'false') . "\n";
echo "\n";

// Test password
$testPassword = 'guru123';
$passwordMatch = Hash::check($testPassword, $pembimbing->password);
echo "Password Check ($testPassword):\n";
echo "   Match: " . ($passwordMatch ? 'YES ✅' : 'NO ❌') . "\n";
echo "\n";

// Test middleware role check
$roles = ['guru_pembimbing', 'pembimbing_perusahaan'];
$hasRole = in_array($pembimbing->role, $roles, true);
echo "Role Middleware Check:\n";
echo "   Allowed roles: " . json_encode($roles) . "\n";
echo "   User role: '{$pembimbing->role}'\n";
echo "   Has access: " . ($hasRole ? 'YES ✅' : 'NO ❌') . "\n";
echo "\n";

// Simulate login
Auth::login($pembimbing);
echo "Login Simulation:\n";
echo "   Auth::check(): " . (Auth::check() ? 'true ✅' : 'false ❌') . "\n";
echo "   Auth::user()->role: " . (Auth::user() ? Auth::user()->role : 'null') . "\n";
echo "\n=== TEST COMPLETE ===\n";
