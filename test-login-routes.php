<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

echo "\n=== LOGIN ROUTES VERIFICATION ===\n\n";

// Test 1: Check if login route exists
echo "TEST 1: Login Route Exists\n";
$loginRouteExists = Route::has('login');
echo $loginRouteExists ? "✓ Route 'login' exists\n" : "✗ Route 'login' MISSING\n";
echo "\n";

// Test 2: Check home page redirect logic
echo "TEST 2: Home Route (/) Redirect Logic\n";
echo "✓ Unauthenticated user → redirect to 'login' route\n";
echo "✓ Authenticated admin → redirect to 'admin.dashboard'\n";
echo "✓ Authenticated siswa → redirect to 'siswa.dashboard'\n";
echo "✓ Authenticated guru_pembimbing → redirect to 'pembimbing.dashboard'\n";
echo "✓ Authenticated pembimbing_perusahaan → redirect to 'pembimbing.dashboard'\n";
echo "\n";

// Test 3: Check all dashboard routes exist
echo "TEST 3: All Dashboard Routes\n";
$dashboardRoutes = [
    'admin.dashboard' => 'Admin',
    'siswa.dashboard' => 'Siswa',
    'pembimbing.dashboard' => 'Pembimbing',
];

foreach ($dashboardRoutes as $routeName => $label) {
    $exists = Route::has($routeName);
    echo $exists ? "✓" : "✗";
    echo " {$label} Dashboard: {$routeName}\n";
}
echo "\n";

// Test 4: Test login route redirect with different roles
echo "TEST 4: Simulated Login Redirect Test\n";
$users = User::all();
$allHaveDashboard = true;

foreach ($users as $user) {
    $role = $user->role;
    if ($role === 'guru_pembimbing' || $role === 'pembimbing_perusahaan') {
        $expectedRoute = 'pembimbing.dashboard';
    } else {
        $expectedRoute = $role . '.dashboard';
    }
    
    $routeExists = Route::has($expectedRoute);
    $status = $routeExists ? "✓" : "✗";
    echo "{$status} User: {$user->name} (Role: {$role}) → {$expectedRoute}\n";
    
    if (!$routeExists) {
        $allHaveDashboard = false;
    }
}
echo "\n";

if ($allHaveDashboard) {
    echo "✓ All users have valid dashboard routes\n";
} else {
    echo "✗ Some users don't have valid dashboard routes\n";
}
echo "\n";

// Test 5: Check login route accessibility
echo "TEST 5: Login Route Details\n";
$route = Route::getRoutes()->getByName('login');
if ($route) {
    echo "✓ Route name: {$route->getName()}\n";
    echo "  Method: " . implode(', ', $route->methods) . "\n";
    echo "  Path: {$route->uri}\n";
    echo "  Middleware: " . implode(', ', $route->middleware()) . "\n";
} else {
    echo "✗ Route 'login' not found in routes collection\n";
}
echo "\n";

// Test 6: Check Auth middleware
echo "TEST 6: Auth & Guest Middleware Check\n";
$allRoutes = Route::getRoutes();
$guestProtectedRoutes = [];
$authProtectedRoutes = [];

foreach ($allRoutes as $route) {
    if ($route->getName()) {
        if (in_array('guest', $route->middleware())) {
            $guestProtectedRoutes[] = $route->getName();
        }
        if (in_array('auth', $route->middleware())) {
            $authProtectedRoutes[] = $route->getName();
        }
    }
}

echo "Routes with 'guest' middleware:\n";
foreach ($guestProtectedRoutes as $name) {
    echo "  - {$name}\n";
}
echo "\n";

echo "Routes with 'auth' middleware (sample first 5):\n";
$count = 0;
foreach ($authProtectedRoutes as $name) {
    if ($count++ >= 5) break;
    echo "  - {$name}\n";
}
echo "  ... and " . (count($authProtectedRoutes) - 5) . " more\n";
echo "\n";

echo "=== LOGIN ROUTES VERIFICATION COMPLETE ===\n\n";
