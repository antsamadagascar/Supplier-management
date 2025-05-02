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
            <p><strong>Fournisseur :</strong> {{ $invoice['supplier'] }}</p>
            <p><strong>Compagnie :</strong> {{ $invoice['company'] }}</p>
            <p><strong>Montant à payer :</strong> {{ number_format($invoice['outstanding_amount'] ?? $invoice['grand_total'], 2, ',', ' ') }} {{ $invoice['currency'] ?? 'EUR' }}</p>
        </div>
    </div>
    <form action="{{ route('invoices.pay') }}" method="POST">
        @csrf
        <input type="hidden" name="invoice_id" value="{{ $invoice['name'] }}">
        <input type="hidden" name="supplier" value="{{ $invoice['supplier'] }}">
        
        <div class="form-group mb-3">
            <label for="paid_amount">Montant payé</label>
            <input type="number" step="0.01" name="paid_amount" id="paid_amount" class="form-control" 
                value="{{ $invoice['outstanding_amount'] ?? $invoice['grand_total'] }}" required>
        </div>
        
        <div class="form-group mb-3">
            <label for="payment_mode">Mode de paiement</label>
            <select name="payment_mode" id="payment_mode" class="form-control" required>
                @foreach($paymentModes as $mode)
                    <option value="{{ $mode['name'] }}">{{ $mode['mode_name'] }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="form-group mb-3">
            <label for="payment_date">Date du paiement</label>
            <input type="date" name="payment_date" id="payment_date" class="form-control" 
                value="{{ now()->format('Y-m-d') }}" required>
        </div>
        
        <button type="submit" class="btn btn-success">Confirmer le paiement</button>
    </form>
</div>
@endsection