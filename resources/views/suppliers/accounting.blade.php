@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Mode Comptable</h1>
            <h3>Fournisseur: {{ $supplier['name'] }}</h3>
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
    
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">3.1 Factures</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Date</th>
                            <th>Commande associée</th>
                            <th>Montant total</th>
                            <th>Statut</th>
                            <th>Montant payé</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $invoice)
                            <tr>
                                <td>{{ $invoice['name'] ?? 'N/A' }}</td>
                                <td>{{ isset($invoice['posting_date']) ? \Carbon\Carbon::parse($invoice['posting_date'])->format('d/m/Y') : 'N/A' }}</td>
                                <td>{{ $invoice['items'][0]['purchase_order'] ?? 'N/A' }}</td>
                                <td>{{ $invoice['grand_total'] ?? '0.00' }} {{ $invoice['currency'] ?? 'EUR' }}</td>
                                <td>
                                    <span class="badge {{ isset($invoice['status']) ? ($invoice['status'] == 'Paid' ? 'badge-success' : ($invoice['status'] == 'Unpaid' ? 'badge-danger' : 'badge-warning')) : 'badge-secondary' }}">
                                        {{ $invoice['status'] ?? 'Unknown' }}
                                    </span>
                                </td>
                                <td>{{ $invoice['paid_amount'] ?? '0.00' }} {{ $invoice['currency'] ?? 'EUR' }}</td>
                                <td>
                                    @if(isset($invoice['status']) && $invoice['status'] != 'Paid')
                                    
                                    @else
                                        <button class="btn btn-sm btn-secondary" disabled>Payée</button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Aucune facture trouvée pour ce fournisseur</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">Historique des paiements</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Date</th>
                            <th>Facture</th>
                            <th>Montant</th>
                            <th>Mode de paiement</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                            <tr>
                                <td>{{ $payment['name'] ?? 'N/A' }}</td>
                                <td>{{ isset($payment['posting_date']) ? \Carbon\Carbon::parse($payment['posting_date'])->format('d/m/Y') : 'N/A' }}</td>
                                <td>{{ isset($payment['references']) && !empty($payment['references']) ? ($payment['references'][0]['reference_name'] ?? 'N/A') : 'N/A' }}</td>
                                <td>{{ $payment['paid_amount'] ?? '0.00' }} {{ $payment['paid_from_account_currency'] ?? 'EUR' }}</td>
                                <td>{{ $payment['mode_of_payment'] ?? 'Virement' }}</td>
                                <td>
                                    <span class="badge {{ isset($payment['docstatus']) && $payment['docstatus'] == 1 ? 'badge-success' : 'badge-secondary' }}">
                                        {{ isset($payment['docstatus']) && $payment['docstatus'] == 1 ? 'Validé' : 'Brouillon' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Aucun paiement trouvé pour ce fournisseur</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection