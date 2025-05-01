@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Tableau de bord fournisseur</h1>
            <h3>{{ $supplier['name'] }}</h3>
        </div>
        <div class="col-md-4 text-right">
            <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
        </div>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">1. Demandes de devis</h5>
                </div>
                <div class="card-body">
                    <p>Gérer les demandes de devis pour ce fournisseur</p>
                    <a href="{{ route('supplier.quotations', ['supplier_id' => $supplier['name']]) }}" class="btn btn-primary">
                        Voir les demandes de devis
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">2. Commandes</h5>
                </div>
                <div class="card-body">
                    <p>Consulter et gérer les commandes</p>
                    <a href="{{ route('supplier.orders', ['supplier_id' => $supplier['name']]) }}" class="btn btn-primary">
                        Voir les commandes
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white"> 
                    <h5 class="mb-0">3. Mode comptable</h5>
                </div>
                <div class="card-body">
                    <p>Gérer les factures et les paiements</p>
                    <a href="{{ route('supplier.accounting', ['supplier_id' => $supplier['name']]) }}" class="btn btn-primary">
                        Accéder au mode comptable
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info text-white">
                    Informations du fournisseur
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Nom:</dt>
                        <dd class="col-sm-8">{{ $supplier['name'] }}</dd>
                        
                        <dt class="col-sm-4">Type:</dt>
                        <dd class="col-sm-8">{{ $supplier['supplier_type'] ?? 'Non défini' }}</dd>
                        
                        <dt class="col-sm-4">Groupe:</dt>
                        <dd class="col-sm-8">{{ $supplier['supplier_group'] ?? 'Non défini' }}</dd>
                        
                        <dt class="col-sm-4">Pays:</dt>
                        <dd class="col-sm-8">{{ $supplier['country'] ?? 'Non défini' }}</dd>
                        
                        <dt class="col-sm-4">Devise par défaut:</dt>
                        <dd class="col-sm-8">{{ $supplier['default_currency'] ?? 'Non défini' }}</dd>
                    </dl>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info text-white">
                    Statistiques
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4 mb-3">
                            <h4>{{ $stats['quotations_count'] ?? 0 }}</h4>
                            <p>Demandes de devis</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <h4>{{ $stats['orders_count'] ?? 0 }}</h4>
                            <p>Commandes</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <h4>{{ $stats['invoices_count'] ?? 0 }}</h4>
                            <p>Factures</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection