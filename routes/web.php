<?php

use App\Http\Controllers\PerformanceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShowController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\VenueController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});
// TODO simplify this to remove the laravel starting page entirely
Route::redirect('/dashboard', '/shows')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('shows', ShowController::class)->except(['index', 'show']);

    Route::get('tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::post('performances/{performance}/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::delete('tickets/{ticket}', [TicketController::class, 'destroy'])->name('tickets.destroy');
});
Route::resource('shows', ShowController::class)->only(['index', 'show']);
Route::get('venues', [VenueController::class, 'index'])->name('venues.index');
Route::get('performances', [PerformanceController::class, 'index'])->name('performances.index');

require __DIR__.'/auth.php';
