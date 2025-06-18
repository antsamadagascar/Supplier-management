@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Commandes</h1>
            <h3>Fournisseur: {{ $supplier['name'] }}
                @if(isset($supplier['supplier_name']))
                    ({{ $supplier['supplier_name'] }})
                @endif
            </h3>
        </div>
        <div class="col-md-4 text-right">
            <a href="{{ route('supplier.dashboard', ['supplier_id' => $supplier['name'] ]) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour au tableau de bord
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
    
    <!-- Résumé des commandes -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total commandes</h5>
                    <h3>{{ count($orders) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Commandes complètes</h5>
                    <h3>{{ count($completedOrders) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">Commandes en attente</h5>
                    <h3>{{ count($pendingOrders) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Montant total</h5>
                    <h3>
                        @php
                            $totalAmount = 0;
                            foreach($orders as $order) {
                                $totalAmount += ($order['grand_total'] ?? 0);
                            }
                            echo number_format($totalAmount, 2);
                        @endphp
                        {{ isset($orders[0]['currency']) ? $orders[0]['currency'] : '' }}
                    </h3>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Onglets pour séparer les commandes complètes et en attente -->
    <ul class="nav nav-tabs mb-3" id="orderTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="all-tab" data-toggle="tab" href="#all" role="tab">
                Toutes les commandes ({{ count($orders) }})
            </a>
        </li>
        <!-- <li class="nav-item">
            <a class="nav-link" id="completed-tab" data-toggle="tab" href="#completed" role="tab">
                Complètes ({{ count($completedOrders) }})
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="pending-tab" data-toggle="tab" href="#pending" role="tab">
                En attente ({{ count($pendingOrders) }})
            </a>
        </li> -->
    </ul>
        
        <div class="tab-content" id="orderTabsContent">
            <!-- Toutes les commandes -->
            <div class="tab-pane fade show active" id="all" role="tabpanel">
                @include('suppliers.orders_table', ['orders' => $orders])
            </div>
            
            <!-- Commandes complètes -->
            <div class="tab-pane fade" id="completed" role="tabpanel">
                @include('suppliers.orders_table', ['orders' => $completedOrders])
            </div>
            
            <!-- Commandes en attente -->
            <div class="tab-pane fade" id="pending" role="tabpanel">
                @include('suppliers.orders_table', ['orders' => $pendingOrders])
            </div>
    </div>
</div>
@endsection