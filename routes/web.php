<?php

use App\Http\Controllers\MedicalVisitController;
use App\Http\Controllers\PetController;
use Illuminate\Support\Facades\Route;

// Add this line to fix the 404 on the homepage:
Route::get('/', function () {
    return redirect('/pets');
});

Route::resource('pets', PetController::class)->except(['show', 'edit', 'destroy']);
Route::resource('visits', MedicalVisitController::class)->except(['show', 'edit', 'destroy']);