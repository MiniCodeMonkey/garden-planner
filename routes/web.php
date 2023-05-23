<?php

use App\Http\Controllers\CalendarController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GardensController;
use App\Http\Controllers\PlantsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SeedsController;
use App\Http\Controllers\SeedTraysController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
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


Route::get('/', function (Request $request) {
    if ($request->user()) {
        return Redirect::route('dashboard');
    }

    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
});

Route::middleware('auth')->group(function () {
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::resource('seeds', SeedsController::class);
    Route::resource('gardens', GardensController::class)->except(['create', 'edit']);
    Route::get('gardens.geojson', [GardensController::class, 'geojson']);
    Route::resource('plants', PlantsController::class);
    Route::resource('trays', SeedTraysController::class);

    Route::get('calendar', [CalendarController::class, 'index'])->name('calendar.index');
});

require __DIR__ . '/auth.php';
