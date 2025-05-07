@extends('layouts.app')
@section('content')
<div class="container">
    <h2 class="mb-4">Liste des Factures</h2>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Totaux par statut -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info text-white">Résumé par statut</div>
                <div class="card-body">
                    @php
                        $totalPaid = $invoices->where('status', 'Paid')->sum('grand_total');
                        $totalUnpaid = $invoices->where('status', '!=', 'Paid')->sum('grand_total');
                        $countPaid = $invoices->where('status', 'Paid')->count();
                        $countUnpaid = $invoices->where('status', '!=', 'Paid')->count();
                    @endphp
                    <div class="row">
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between">
                                <span>Factures payées ({{ $countPaid }}):</span>
                                <span class="fw-bold text-success">{{ number_format($totalPaid, 2, ',', ' ') }} EUR</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between">
                                <span>Factures en attente ({{ $countUnpaid }}):</span>
                                <span class="fw-bold text-warning">{{ number_format($totalUnpaid, 2, ',', ' ') }} EUR</span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 d-flex justify-content-between">
                        <span>Montant total:</span>
                        <span class="fw-bold">{{ number_format($totalPaid + $totalUnpaid, 2, ',', ' ') }} EUR</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-primary text-white">Factures fournisseurs</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Fournisseur</th>
                            <th>Date</th>
                            <th>Montant</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $invoice)
                            <tr>
                                <td>{{ $invoice['name'] }}</td>
                                <td>{{ $invoice['supplier'] ?? 'N/A' }}</td>
                                <td>{{ isset($invoice['posting_date']) ? \Carbon\Carbon::parse($invoice['posting_date'])->format('d/m/Y') : 'N/A' }}</td>
                                <td>{{ number_format($invoice['grand_total'] ?? 0, 2, ',', ' ') }} {{ $invoice['currency'] ?? 'EUR' }}</td>
                                <td>
                                    <span class="badge {{ $invoice['status'] === 'Paid' ? 'bg-success' : 'bg-warning' }}">
                                        {{ $invoice['status'] }}
                                    </span>
                                </td>
                                <td>
                                    @if($invoice['status'] !== 'Paid')
                                        <a href="{{ route('invoices.showPayForm', ['invoice_id' => $invoice['name']]) }}" class="btn btn-sm btn-outline-primary">Régler</a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Aucune facture disponible.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="table-secondary">
                            <td colspan="3" class="text-end fw-bold">Total:</td>
                            <td colspan="3" class="fw-bold">{{ number_format($invoices->sum('grand_total'), 2, ',', ' ') }} EUR</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection