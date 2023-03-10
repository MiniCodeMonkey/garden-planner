<?php

use App\Http\Controllers\CalendarController;
use App\Http\Controllers\GardensController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SeedsController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/seeds', [SeedsController::class, 'index'])->name('seeds.index');
    Route::get('/seeds/{seed}', [SeedsController::class, 'show'])->name('seeds.show');
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
    Route::get('/garden', [GardensController::class, 'index'])->name('gardens.index');
});

require __DIR__ . '/auth.php';
