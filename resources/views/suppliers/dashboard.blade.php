@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Header Section -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h1 class="mb-2">Tableau de bord fournisseur</h1>
            <h3 class="text-muted">{{ $supplier['name'] }}</h3>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour à la liste
            </a>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Summary Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-file-alt fa-2x text-primary me-3"></i>
                        <div>
                            <h5 class="card-title mb-1">Demandes de devis</h5>
                            <h3 class="mb-0">{{ $stats['quotations_count'] }}</h3>
                            <p class="text-muted mb-0">Total: {{ number_format($stats['quotations_total'], 2) }} {{ $supplier['default_currency'] ?? '' }}</p>
                        </div>
                    </div>
                    <a href="{{ route('supplier.quotations', ['supplier_id' => $supplier['name']]) }}" class="btn btn-primary mt-3 w-100">
                        Gérer les devis
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-shopping-cart fa-2x text-primary me-3"></i>
                        <div>
                            <h5 class="card-title mb-1">Commandes</h5>
                            <h3 class="mb-0">{{ $stats['orders_count'] }}</h3>
                            <p class="text-muted mb-0">Total: {{ number_format($stats['orders_total'], 2) }} {{ $supplier['default_currency'] ?? '' }}</p>
                        </div>
                    </div>
                    <a href="{{ route('supplier.orders', ['supplier_id' => $supplier['name']]) }}" class="btn btn-primary mt-3 w-100">
                        Gérer les commandes
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-file-invoice fa-2x text-primary me-3"></i>
                        <div>
                            <h5 class="card-title mb-1">Factures</h5>
                            <h3 class="mb-0">{{ $stats['invoices_count'] }}</h3>
                            <p class="text-muted mb-0">Total: {{ number_format($stats['invoices_total'], 2) }} {{ $supplier['default_currency'] ?? '' }}</p>
                        </div>
                    </div>
                    <a href="{{ route('supplier.accounting', ['supplier_id' => $supplier['name']]) }}" class="btn btn-primary mt-3 w-100">
                        Mode comptable
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Details Section -->
    <div class="row g-4">
        <!-- Supplier Information -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Informations du fournisseur</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Nom:</dt>
                        <dd class="col-sm-8">{{ $supplier['name'] }}</dd>
                        <dt class="col-sm-4">Type:</dt>
                        <dd class="col-sm-8">{{ $supplier['supplier_type'] ?? 'Non défini' }}</dd>
                        <dt class="col-sm-4">Groupe:</dt>
                        <dd class="col-sm-8">{{ $supplier['supplier_group'] ?? 'Non défini' }}</dd>
                        <dt class="col-sm-4">Pays:</dt>
                        <dd class="col-sm-8">{{ $supplier['country'] ?? 'Non défini' }}</dd>
                        <dt class="col-sm-4">Devise:</dt>
                        <dd class="col-sm-8">{{ $supplier['default_currency'] ?? 'Non défini' }}</dd>
                        <dt class="col-sm-4">Email:</dt>
                        <dd class="col-sm-8">{{ $supplier['email'] ?? 'Non défini' }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Financial Summary -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Résumé financier</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4 mb-3">
                            <h5 class="text-muted">Devis en attente</h5>
                            <h4>{{ $stats['pending_quotations'] ?? 0 }}</h4>
                        </div>
                        <div class="col-md-4 mb-3">
                            <h5 class="text-muted">Commandes en cours</h5>
                            <h4>{{ $stats['pending_orders'] ?? 0 }}</h4>
                        </div>
                        <div class="col-md-4 mb-3">
                            <h5 class="text-muted">Factures impayées</h5>
                            <h4>{{ $stats['unpaid_invoices'] ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 