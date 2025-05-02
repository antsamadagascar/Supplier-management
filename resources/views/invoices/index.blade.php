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
                                    @else
                                        <span class="text-muted">Payée</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Aucune facture disponible.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
