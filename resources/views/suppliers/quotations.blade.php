@extends('layouts.app')

@section('content')
<div class="container">
    <!-- En-tête -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h1>Demandes de devis</h1>
            <h3 class="text-muted">Fournisseur : {{ $supplier['supplier_name'] ?? 'N/A' }}</h3>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('supplier.dashboard', ['supplier_id' => $supplier['name']]) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Retour au tableau de bord
            </a>
        </div>
    </div>

    <!-- Liste des devis -->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Liste des demandes de devis</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Référence</th>
                            <th>Date</th>
                            <th>Valide jusqu'à</th>
                            <th>Montant net</th>
                            <th>Montant total</th>
                            <th>Statut</th>
                            <th>Articles</th>
                            <th>Détails</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($quotations as $quotationIndex => $quotation)
                            @php
                                $quotationUniqueId = "quotation-{$quotationIndex}";
                            @endphp
                            
                            <tr>
                                <td>{{ $quotation['name'] ?? 'N/A' }}</td>
                                <td>{{ \Carbon\Carbon::parse($quotation['transaction_date'])->format('d/m/Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($quotation['valid_till'])->format('d/m/Y') }}</td>
                                <td>{{ number_format($quotation['net_total'] ?? 0, 2) }} {{ $quotation['currency'] ?? 'USD' }}</td>
                                <td>{{ number_format($quotation['grand_total'] ?? 0, 2) }} {{ $quotation['currency'] ?? 'USD' }}</td>
                                <td>
                                    <span class="badge {{ $quotation['status'] == 'Submitted' ? 'bg-success' : 'bg-warning' }}">
                                        {{ $quotation['status'] ?? 'En attente' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="items-list">
                                        @foreach($quotation['items'] as $item)
                                            <div class="item-row">
                                                <strong>{{ $item['item_code'] }}</strong> - 
                                                {{ $item['qty'] }} × {{ number_format($item['rate'], 2) }}
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info" type="button" data-bs-toggle="collapse" data-bs-target="#details-{{ $quotationUniqueId }}" aria-expanded="false">
                                        <i class="fas fa-eye"></i> Détails
                                    </button>
                                </td>
                                <td>
                                <a href="{{ route('supplier.quotation.items', ['supplier_id' => $supplier['name'], 'quotation_id' => $quotation['name']]) }}" class="btn btn-sm btn-secondary">
        <i class="fas fa-pencil-alt"></i> Mettre à jour tous les prix
    </a>
                                </td>
                            </tr>

                            <!-- Section déroulante pour les détails -->
                            <tr>
                                <td colspan="9" class="p-0">
                                    <div class="collapse" id="details-{{ $quotationUniqueId }}">
                                        <div class="card card-body border-0">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h5>Détails du fournisseur</h5>
                                                    <p><strong>Nom:</strong> {{ $supplier['supplier_name'] ?? 'Non défini' }}</p>
                                                    <p><strong>Création:</strong> {{ !empty($supplier['creation']) ? \Carbon\Carbon::parse($supplier['creation'])->format('d/m/Y') : 'Aucune' }}</p>

                                                    <h5 class="mt-3">Détails des articles</h5>
                                                    <div class="table-responsive">
                                                        <table class="table table-sm">
                                                            <thead>
                                                                <tr>
                                                                    <th>Article</th>
                                                                    <th>Description</th>
                                                                    <th>Quantité</th>
                                                                    <th>Prix</th>
                                                                    <th>Total</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($quotation['items'] as $item)
                                                                    <tr>
                                                                        <td>{{ $item['item_code'] ?? 'N/A' }}</td>
                                                                        <td>{{ $item['item_name'] ?? 'N/A' }}</td>
                                                                        <td>{{ $item['qty'] ?? 0 }}</td>
                                                                        <td>{{ number_format($item['rate'] ?? 0, 2) }}</td>
                                                                        <td>{{ number_format($item['amount'] ?? 0, 2) }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-6">
                                                    @if(!empty($quotation['taxes']))
                                                        <h5>Taxes</h5>
                                                        <div class="table-responsive">
                                                            <table class="table table-sm">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Description</th>
                                                                        <th>Montant</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($quotation['taxes'] as $tax)
                                                                        <tr>
                                                                            <td>{{ $tax['description'] ?? 'N/A' }}</td>
                                                                            <td>{{ number_format($tax['tax_amount'] ?? 0, 2) }}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    Aucune demande de devis trouvée pour ce fournisseur
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    /* Style pour la liste des articles */
    .items-list {
        display: flex;
        flex-direction: column;
    }
    
    .item-row {
        margin-bottom: 0.25rem;
    }
    
    .item-row:last-child {
        margin-bottom: 0;
    }
</style>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Gestion des boutons de collapse
    document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(button => {
        button.addEventListener('click', () => {
            const target = document.querySelector(button.getAttribute('data-bs-target'));
            if (target) {
                target.classList.toggle('show');
            }
        });
    });
});
</script>
@endpush