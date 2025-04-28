<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;

// Routes publiques
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Routes protégées par l'authentification Frappe
Route::middleware([\App\Http\Middleware\FrappeAuthMiddleware::class])->group(function () {
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/user', [AuthController::class, 'getLoggedUser'])->name('user.info');
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::get('/calendar', function() {
        return view('calendar.index');
    })->name('calendar');
    Route::get('/tableau', [DashboardController::class, 'index'])->name('tableau');
    Route::delete('/tickets/{id}', function ($id) {
        return redirect()->route('dashboard')->with('success', 'Ticket supprimé avec succès.');
    })->name('tickets.destroy');
    Route::get('formulaire', function() {
        return view('formulaire.index');
    })->name('formulaire');
});
