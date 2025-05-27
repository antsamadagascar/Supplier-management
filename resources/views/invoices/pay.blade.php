@extends('layouts.app')
@section('content')
<div class="container">
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

    <h2>Paiement de la facture : {{ $invoice['name'] }}</h2>
    
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Fournisseur :</strong> {{ $invoice['supplier'] }}</p>
                    <p><strong>Compagnie :</strong> {{ $invoice['company'] }}</p>
                    <p><strong>Montant total :</strong> {{ number_format($invoice['grand_total'], 2, ',', ' ') }} {{ $invoice['currency'] ?? 'EUR' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Déjà payé :</strong> {{ number_format($totalPaid, 2, ',', ' ') }} {{ $invoice['currency'] ?? 'EUR' }}</p>
                    <p><strong>Reste à payer :</strong> <span class="text-{{ $remainingAmount > 0 ? 'danger' : 'success' }}">{{ number_format($remainingAmount, 2, ',', ' ') }} {{ $invoice['currency'] ?? 'EUR' }}</span></p>
                    <p><strong>Statut :</strong> 
                        @if($remainingAmount <= 0)
                            <span class="badge bg-success">Entièrement payée</span>
                        @elseif($totalPaid > 0)
                            <span class="badge bg-warning">Partiellement payée</span>
                        @else
                            <span class="badge bg-danger">Non payée</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    @if($remainingAmount > 0)
    <div class="card mb-4">
        <div class="card-header">
            <h4>Nouveau paiement</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('invoices.pay') }}" method="POST">
                @csrf
                <input type="hidden" name="invoice_id" value="{{ $invoice['name'] }}">
                <input type="hidden" name="supplier" value="{{ $invoice['supplier'] }}">
                
                <div class="form-group mb-3">
                    <label for="paid_amount">Montant à payer</label>
                    <input type="number" step="0.01" name="paid_amount" id="paid_amount" class="form-control"
                        value="{{ $remainingAmount }}" max="{{ $remainingAmount }}" required>
                    <small class="form-text text-muted">Montant maximum: {{ number_format($remainingAmount, 2, ',', ' ') }} {{ $invoice['currency'] ?? 'EUR' }}</small>
                </div>
                
                <div class="form-group mb-3">
                    <label for="payment_mode">Mode de paiement</label>
                    <select name="payment_mode" id="payment_mode" class="form-control" required>
                        <option value="">-- Sélectionnez un mode de paiement --</option>
                        @foreach($paymentModes as $mode)
                        <option value="{{ $mode['name'] }}">{{ $mode['mode_name'] ?? $mode['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group mb-3">
                    <label for="payment_date">Date du paiement</label>
                    <input type="date" name="payment_date" id="payment_date" class="form-control"
                        value="{{ now()->format('Y-m-d') }}" required>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="reference_no">Numéro de référence <span class="text-danger">*</span></label>
                        <input type="text" name="reference_no" id="reference_no" class="form-control"
                            value="{{$invoice['name']}}" required
                            placeholder="Ex: Numéro de chèque, référence bancaire...">

                        <small class="form-text text-muted">Obligatoire pour les transactions bancaires</small>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="reference_date">Date de référence <span class="text-danger">*</span></label>
                        <input type="date" name="reference_date" id="reference_date" class="form-control"
                            value="{{ now()->format('Y-m-d') }}" required>
                        <small class="form-text text-muted">Obligatoire pour les transactions bancaires</small>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-success">Confirmer le paiement</button>
            </form>
        </div>
    </div>
    @endif

    @if(count($paymentHistory) > 0)
    <div class="card">
        <div class="card-header">
            <h4>Historique des paiements</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Mode de paiement</th>
                            <th>Référence</th>
                            <th>Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($paymentHistory as $payment)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($payment['posting_date'])->format('d/m/Y') }}</td>
                            <td>{{ $payment['mode_of_payment'] }}</td>
                            <td>
                                @if(!empty($payment['reference_no']))
                                    {{ $payment['reference_no'] }}
                                    @if(!empty($payment['reference_date']))
                                        ({{ \Carbon\Carbon::parse($payment['reference_date'])->format('d/m/Y') }})
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ number_format($payment['paid_amount'], 2, ',', ' ') }} {{ $invoice['currency'] ?? 'EUR' }}</td>
                        </tr>
                        @endforeach
                        <tr class="table-info">
                            <td colspan="3" class="text-end fw-bold">Total payé:</td>
                            <td class="fw-bold">{{ number_format($totalPaid, 2, ',', ' ') }} {{ $invoice['currency'] ?? 'EUR' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection