<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Route;

echo "\n=== ROUTE VERIFICATION TEST ===\n\n";

// Get all routes
$routes = Route::getRoutes();
$groupedRoutes = [
    'auth' => [],
    'admin' => [],
    'siswa' => [],
    'pembimbing' => [],
    'other' => [],
];

foreach ($routes as $route) {
    $name = $route->getName();
    
    if (strpos($name, 'admin') === 0) {
        $groupedRoutes['admin'][] = $name;
    } elseif (strpos($name, 'siswa') === 0) {
        $groupedRoutes['siswa'][] = $name;
    } elseif (strpos($name, 'pembimbing') === 0) {
        $groupedRoutes['pembimbing'][] = $name;
    } elseif (in_array($name, ['login', 'login.attempt', 'register', 'register.store', 'logout'])) {
        $groupedRoutes['auth'][] = $name;
    } elseif ($name) {
        $groupedRoutes['other'][] = $name;
    }
}

echo "✅ AUTH ROUTES:\n";
foreach ($groupedRoutes['auth'] as $route) {
    echo "   - {$route}\n";
}
echo "\n";

echo "✅ ADMIN ROUTES:\n";
$adminForSort = $groupedRoutes['admin'];
sort($adminForSort);
foreach ($adminForSort as $route) {
    echo "   - {$route}\n";
}
echo "\n";

echo "✅ SISWA ROUTES:\n";
$siswaForSort = $groupedRoutes['siswa'];
sort($siswaForSort);
foreach ($siswaForSort as $route) {
    echo "   - {$route}\n";
}
echo "\n";

echo "✅ PEMBIMBING ROUTES:\n";
$pembimbingForSort = $groupedRoutes['pembimbing'];
sort($pembimbingForSort);
foreach ($pembimbingForSort as $route) {
    echo "   - {$route}\n";
}
echo "\n";

// Verify critical routes exist
echo "=== CRITICAL ROUTES CHECK ===\n\n";

$criticalRoutes = [
    'login' => 'Auth Login',
    'register' => 'Auth Register',
    'logout' => 'Auth Logout',
    'admin.dashboard' => 'Admin Dashboard',
    'admin.siswa.index' => 'Admin Siswa List',
    'admin.perusahaan.index' => 'Admin Perusahaan List',
    'admin.absensi.index' => 'Admin Absensi',
    'siswa.dashboard' => 'Siswa Dashboard',
    'siswa.absensi.index' => 'Siswa Absensi',
    'pembimbing.dashboard' => 'Pembimbing Dashboard',
    'pembimbing.absensi.index' => 'Pembimbing Absensi Index',
    'pembimbing.absensi.downloadPdf' => 'Pembimbing Absensi Download PDF',
    'pembimbing.jurnal.index' => 'Pembimbing Jurnal Index',
    'pembimbing.jurnal.downloadPdf' => 'Pembimbing Jurnal Download PDF',
];

$allExist = true;
foreach ($criticalRoutes as $routeName => $description) {
    $exists = Route::has($routeName);
    $status = $exists ? "✓" : "✗";
    echo "{$status} {$description}: {$routeName}\n";
    if (!$exists) {
        $allExist = false;
    }
}

echo "\n";
if ($allExist) {
    echo "✅ ALL CRITICAL ROUTES EXIST!\n";
} else {
    echo "❌ SOME CRITICAL ROUTES MISSING!\n";
}
echo "\n";
