<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;


Route::get('/', function () {
    return view('dashboard.index');
})->name('dashboard');

Route::get('calendar', function() {
    return view('calendar.index');
})->name('calendar');
Route::get('/tableau', [DashboardController::class, 'index'])->name('tableau');

Route::delete('/tickets/{id}', function ($id) {
    return redirect()->route('dashboard')->with('success', 'Ticket supprimé avec succès.');
})->name('tickets.destroy');
/*
Route::get('/tableau', function () {
    return view('tableau.index');
})->name('tableau');
*/
Route::get('formulaire', function() {
    return view('formulaire.index');
})->name('formulaire');