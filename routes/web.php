<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\OwnerAdminController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Health check: /up -> 200 kalau app + database sehat, 503 kalau tidak.
Route::get('/up', function () {
    $checks = [
        'app' => true,
        'php' => PHP_VERSION,
        'laravel' => app()->version(),
    ];

    try {
        DB::connection()->getPdo();
        $checks['database'] = true;

        $tables = DB::select('SHOW TABLES');
        $checks['tables'] = count($tables);

        $checks['migrations_run'] = Schema::hasTable('migrations')
            ? DB::table('migrations')->count()
            : 0;

        if (Schema::hasTable('users')) {
            $checks['users'] = DB::table('users')->count();
        }
    } catch (\Throwable $e) {
        $checks['database'] = false;
        $checks['error'] = $e->getMessage();
    }

    $healthy = ($checks['database'] ?? false) === true;

    return response()->json([
        'status' => $healthy ? 'ok' : 'degraded',
        'checks' => $checks,
        'time' => now()->toIso8601String(),
    ], $healthy ? 200 : 503);
})->name('health');

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/owner/dashboard', [OwnerController::class, 'index'])
    ->middleware(['auth', 'verified', 'role:owner'])
    ->name('owner.dashboard');

Route::get('/owner/laporan', [OwnerController::class, 'laporan'])
    ->middleware(['auth', 'verified', 'role:owner'])
    ->name('owner.laporan');

    Route::get('/owner/monitoring', function () {
    return view('owner.monitoring');
})
    ->middleware(['auth', 'verified', 'role:owner'])
    ->name('owner.monitoring');

    Route::get('/owner/admin', [OwnerAdminController::class, 'index'])
    ->middleware(['auth', 'verified', 'role:owner'])
    ->name('owner.admin.index');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('customers', CustomerController::class);

    Route::resource('packages', PackageController::class);

    Route::post('/payments/generate-monthly', [PaymentController::class, 'generateMonthly'])
    ->name('payments.generate-monthly');
    
    Route::resource('payments', PaymentController::class);
});

require __DIR__.'/auth.php';
