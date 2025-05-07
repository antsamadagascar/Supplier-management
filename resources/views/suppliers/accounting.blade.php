@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Mode Comptable</h1>
            <h3>Fournisseur: {{ $supplier['supplier_name'] ?? 'Auccun'}} ({{ $supplier['name'] ?? 'Auccun'}})</h3>
            <p>Groupe: {{ $supplier['supplier_group'] ?? 'Auccun'}} | Type: {{ $supplier['supplier_type'] }} | Pays: {{ $supplier['country'] ?? 'Auccun'}}</p>
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
                        <!-- <th>Montant payé</th> -->
                        <th>Devise</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @php
                $totalMontant = 0;
                $totauxParStatut = [
                    'Paid' => 0,
                    'Unpaid' => 0,
                    'Partially Paid' => 0
                ];
            @endphp

            @forelse($invoices as $invoice)
                @php
                    $montant = $invoice['amount'] ?? 0;
                    $statut = $invoice['status'] ?? 'Unpaid';
                    $totalMontant += $montant;
                    if (isset($totauxParStatut[$statut])) {
                        $totauxParStatut[$statut] += $montant;
                    } else {
                        $totauxParStatut[$statut] = $montant;
                    }
                @endphp
                <tr>
                    <td>{{ $invoice['name'] ?? 'N/A' }}</td>
                    <td>{{ isset($invoice['posting_date']) ? \Carbon\Carbon::parse($invoice['posting_date'])->format('d/m/Y') : 'N/A' }}</td>
                    <td>{{ $invoice['item_code'] ?? 'N/A' }}</td>
                    <td>{{ number_format($invoice['qty'] ?? 0, 2, ',', ' ') }}</td>
                    <td>{{ number_format($invoice['rate'] ?? 0, 2, ',', ' ') }}</td>
                    <td>{{ number_format($montant, 2, ',', ' ') }}</td>
                    <td>{{ $invoice['purchase_order'] ?? '—' }}</td>
                    <td>
                        <span class="badge 
                            {{ $statut === 'Paid' ? 'bg-success' : 
                            ($statut === 'Unpaid' ? 'bg-danger' : 'bg-warning') }}">
                            {{ $statut }}
                        </span>
                    </td>
                    <td>{{ $invoice['currency'] ?? '—' }}</td>
                    <td>
                        @if($statut !== 'Paid')
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
                <tfoot>
                <tr class="bg-light fw-bold">
                    <td colspan="5" class="text-end">Total global</td>
                    <td>{{ number_format($totalMontant, 2, ',', ' ') }}</td>
                    <td colspan="4"></td>
                </tr>
                <tr>
                    <td colspan="5" class="text-end text-success">Total payé</td>
                    <td>{{ number_format($totauxParStatut['Paid'], 2, ',', ' ') }}</td>
                    <td colspan="4"></td>
                </tr>
                <tr>
                    <td colspan="5" class="text-end text-danger">Total non payé</td>
                    <td>{{ number_format($totauxParStatut['Unpaid'], 2, ',', ' ') }}</td>
                    <td colspan="4"></td>
                </tr>
                <tr>
                    <td colspan="5" class="text-end text-warning">Total partiellement payé</td>
                    <td>{{ number_format($totauxParStatut['Partially Paid'], 2, ',', ' ') }}</td>
                    <td colspan="4"></td>
                </tr>
            </tfoot>

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
                            <th>Reference Factures</th>
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
                                    @if(!empty($payment['references']) && isset($payment['references'][0]['reference_name']))
                                        {{ $payment['references'][0]['reference_name'] }}
                                    @else
                                        {{ $payment['reference_name'] ?? '—' }}
                                    @endif
                                </td>

                                <td>
                                <span class="badge {{ $payment['docstatus'] == 1 ? 'bg-success' : 'bg-secondary' }}">
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
