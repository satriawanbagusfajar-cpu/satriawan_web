<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

echo "\n=== DAFTAR USER PEMBIMBING ===\n\n";

$pembimbing = User::whereIn('role', ['guru_pembimbing', 'pembimbing_perusahaan'])
    ->get(['id', 'email', 'name', 'role']);

foreach ($pembimbing as $user) {
    echo "ID: {$user->id} | Email: {$user->email} | Nama: {$user->name} | Role: {$user->role}\n";
}

echo "\n=== TOTAL: " . $pembimbing->count() . " user pembimbing ===\n";
echo "\n";
