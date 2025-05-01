@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Paiement de facture</h1>
            <h3>Facture: {{ $invoice['name'] }}</h3>
        </div>
        <div class="col-md-4 text-right">
            <a href="{{ route('supplier.accounting', ['supplier_id' => $supplier_id]) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour au mode comptable
            </a>
        </div>
    </div>
    
    @if(session('error'))