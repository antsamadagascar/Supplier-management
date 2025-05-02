<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ErpController;

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
    
    // Liste des fournisseurs (correction du doublon)
    Route::get('/suppliers', [ErpController::class, 'suppliers'])->name('suppliers.index');
    
    // Routes pour les fournisseurs
    Route::get('/supplier/{supplier_id}/dashboard', [ErpController::class, 'showSupplierDashboard'])->name('supplier.dashboard');
    Route::get('/supplier/{supplier_id}/quotations', [ErpController::class, 'supplierQuotations'])->name('supplier.quotations');
    Route::get('/supplier/{supplier_id}/orders', [ErpController::class, 'supplierOrders'])->name('supplier.orders');
    Route::get('/supplier/{supplier_id}/accounting', [ErpController::class, 'supplierAccounting'])->name('supplier.accounting');
    
    // Ajouter la route manquante pour les éléments de devis
    Route::get('/supplier/{supplier_id}/quotations/{quotation_id}/items', [ErpController::class, 'quotationItems'])->name('supplier.quotation.items');

    // Route pour mettre à jour les prix des éléments de devis
    Route::post('/supplier/{supplier_id}/quotations/{quotation_id}/update', [ErpController::class, 'updateQuotation'])->name('supplier.quotation.update');

    // Routes pour le module fournisseur et devis
// Routes pour le module fournisseur et devis
Route::get('/supplier/{supplier_id}/quotations', [ErpController::class, 'supplierQuotations'])->name('supplier.quotations');
Route::get('/supplier/{supplier_id}/quotations/{quotation_id}/items', [ErpController::class, 'quotationItems'])->name('supplier.quotation.items');
Route::post('/supplier/{supplier_id}/quotations/{quotation_id}/update', [ErpController::class, 'updateQuotation'])->name('supplier.quotation.update');

});