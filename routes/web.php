<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\OwnerAdminController;

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
