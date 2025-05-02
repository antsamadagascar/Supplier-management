@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Mode Comptable</h1>
            <h3>Fournisseur: {{ $supplier['supplier_name'] }} ({{ $supplier['name'] }})</h3>
            <p>Groupe: {{ $supplier['supplier_group'] }} | Type: {{ $supplier['supplier_type'] }} | Pays: {{ $supplier['country'] }}</p>
        </div>
        <div class="col-md-4 text-right">
            <a href="{{ route('supplier.dashboard', ['supplier_id' => $supplier['name'] ]) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour au tableau de bord
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">3.1 Factures</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Référence</th>
                            <th>Date</th>
                            <th>Article</th>
                            <th>Quantité</th>
                            <th>PU</th>
                            <th>Montant</th>
                            <th>Commande</th>
                            <th>Statut</th>
                            <th>Montant payé</th>
                            <th>Devise</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $invoice)
                            <tr>
                                <td>{{ $invoice['name'] ?? 'N/A' }}</td>
                                <td>{{ isset($invoice['posting_date']) ? \Carbon\Carbon::parse($invoice['posting_date'])->format('d/m/Y') : 'N/A' }}</td>
                                <td>{{ $invoice['item_code'] ?? 'N/A' }}</td>
                                <td>{{ number_format($invoice['qty'] ?? 0, 2, ',', ' ') }}</td>
                                <td>{{ number_format($invoice['rate'] ?? 0, 2, ',', ' ') }}</td>
                                <td>{{ number_format($invoice['amount'] ?? 0, 2, ',', ' ') }}</td>
                                <td>{{ $invoice['purchase_order'] ?? '—' }}</td>
                                <td>
                                    <span class="badge {{ $invoice['status'] === 'Paid' ? 'badge-success' : ($invoice['status'] === 'Unpaid' ? 'badge-danger' : 'badge-warning') }}">
                                        {{ $invoice['status'] ?? '—' }}
                                    </span>
                                </td>
                                <td>{{ number_format($invoice['paid_amount'] ?? 0, 2, ',', ' ') }}</td>
                                <td>{{ $invoice['currency'] ?? '—' }}</td>
                                <td>
                                @if($invoice['status'] !== 'Paid')
                                    <a href="{{ route('invoices.showPayForm', ['invoice_id' => $invoice['name']]) }}" class="btn btn-sm btn-outline-primary">Régler</a>
                                @else
                                    <span class="text-muted">Payée</span>
                                @endif

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center">Aucune facture trouvée pour ce fournisseur</td>
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
                    <thead class="thead-light">
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
                                <td>{{ $payment['references'][0]['reference_name'] ?? '—' }}</td>
                                <td>{{ number_format($payment['paid_amount'] ?? 0, 2, ',', ' ') }} {{ $payment['paid_from_account_currency']  }}</td>
                                <td>{{ $payment['mode_of_payment'] ?? '—' }}</td>
                                <td>
                                    <span class="badge {{ $payment['docstatus'] == 1 ? 'badge-success' : 'badge-secondary' }}">
                                        {{ $payment['docstatus'] == 1 ? 'Validé' : 'Brouillon' }}
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
