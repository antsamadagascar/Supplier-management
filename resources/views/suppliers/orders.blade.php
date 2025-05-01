@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Commandes</h1>
            <h3>Fournisseur: {{ $supplier['name'] }}
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
    
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">2. Liste des commandes</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Date</th>
                            <th>Montant total</th>
                            <th>Statut</th>
                            <th>Statut de réception</th>
                            <th>Statut de paiement</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td>{{ $order['name'] ?? 'N/A' }}</td>
                                <td>{{ isset($order['transaction_date']) ? \Carbon\Carbon::parse($order['transaction_date'])->format('d/m/Y') : 'N/A' }}</td>
                                <td>{{ $order['grand_total'] ?? '0.00' }} {{ $order['currency'] ?? 'EUR' }}</td>
                                <td>
                                    <span class="badge {{ isset($order['status']) ? ($order['status'] == 'To Receive and Bill' ? 'badge-warning' : ($order['status'] == 'Completed' ? 'badge-success' : 'badge-secondary')) : 'badge-secondary' }}">
                                        {{ $order['status'] ?? 'Unknown' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ isset($order['per_received']) && $order['per_received'] == 100 ? 'badge-success' : 'badge-warning' }}">
                                        @if(isset($order['per_received']) && $order['per_received'] == 100)
                                            2.1.1 Reçu (100%)
                                        @else
                                            En attente ({{ $order['per_received'] ?? '0' }}%)
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ isset($order['per_billed']) && $order['per_billed'] == 100 ? 'badge-success' : 'badge-warning' }}">
                                        @if(isset($order['per_billed']) && $order['per_billed'] == 100 && isset($order['payment_status']) && $order['payment_status'] == 'Paid')
                                            2.1.2 Payé (100%)
                                        @elseif(isset($order['per_billed']) && $order['per_billed'] == 100)
                                            Facturé (100%)
                                        @else
                                            Non facturé ({{ $order['per_billed'] ?? '0' }}%)
                                        @endif
                                    </span>
                                </td>
                                <td>
                                 
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Aucune commande trouvée pour ce fournisseur</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection