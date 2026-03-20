<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

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

use App\Http\Controllers\BookingController;
use App\Http\Controllers\CrewController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\HotelController;

Route::get('/', function () {
    return redirect()->route('bookings.index');
});

use App\Http\Controllers\UserManagementController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('bookings.index');
    })->name('dashboard');

    Route::resource('bookings', BookingController::class);
    Route::resource('crews', CrewController::class);
    Route::resource('companies', CompanyController::class);
    Route::resource('hotels', HotelController::class);
    Route::post('bookings/{booking}/toggle', [BookingController::class, 'toggle'])->name('bookings.toggle');
    Route::delete('bookings/status-logs/{log}', [BookingController::class, 'deleteStatusLog'])->name('bookings.delete-status');

    // Admin only routes
    Route::middleware('can:admin')->group(function () {
        Route::resource('users', UserManagementController::class);
        Route::get('activity-log', [\App\Http\Controllers\ActivityLogController::class, 'index'])->name('admin.activity-log');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
