<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MedicalVisitController;
use App\Http\Controllers\PetController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
})->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::resource('pets', PetController::class)->except(['show', 'destroy']);
    Route::resource('visits', MedicalVisitController::class)->except(['show', 'destroy']);
});

require __DIR__.'/auth.php';