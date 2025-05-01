@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Mise à jour des prix</h1>
            <h3>Demande de devis: {{ $quotation_id }}</h3>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('supplier.quotations', ['supplier_id' => $supplier_id]) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour aux demandes de devis
            </a>
        </div>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Mettre à jour les prix des articles</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('supplier.quotation.update', ['supplier_id' => $supplier_id, 'quotation_id' => $quotation_id]) }}" method="POST" id="update-prices-form">
                @csrf
                @forelse($items as $index => $item)
                    <div class="mb-3">
                        <label for="new_rate_{{ $index }}" class="form-label">
                            Nouveau prix pour {{ $item['item_name'] ?? ($item['description'] ?? 'Article ' . ($index + 1)) }} ({{ $quotation_currency }})
                        </label>
                        <input type="hidden" name="items[{{ $index }}][item_row]" value="{{ $item['name'] ?? '' }}">
                        <input 
                            type="number" 
                            name="items[{{ $index }}][new_rate]" 
                            id="new_rate_{{ $index }}" 
                            class="form-control" 
                            step="0.01" 
                            min="0" 
                            value="{{ isset($item['rate']) ? number_format($item['rate'], 2, '.', '') : '0.00' }}" 
                            required
                        >
                        @error("items.{$index}.new_rate")
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                @empty
                    <p class="text-center text-muted">Aucun article trouvé pour ce devis.</p>
                @endforelse
                
                @if(!empty($items))
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Mettre à jour tous les prix</button>
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('update-prices-form');
    const rateInputs = form.querySelectorAll('input[name$="[new_rate]"]');

    // Validation en temps réel pour chaque champ de prix
    rateInputs.forEach(input => {
        input.addEventListener('input', () => {
            const value = parseFloat(input.value);
            if (isNaN(value) || value < 0) {
                input.value = '';
            }
        });
    });

    // Validation lors de la soumission
    form.addEventListener('submit', (event) => {
        let valid = true;
        rateInputs.forEach(input => {
            const value = parseFloat(input.value);
            if (isNaN(value) || value < 0) {
                valid = false;
                input.classList.add('is-invalid');
                alert('Veuillez entrer un prix valide (nombre positif) pour tous les articles.');
            } else {
                input.classList.remove('is-invalid');
            }
        });

        if (!valid) {
            event.preventDefault();
            return;
        }

        // Confirmation
        if (!confirm('Confirmez-vous la mise à jour des prix pour tous les articles ?')) {
            event.preventDefault();
        }
    });
});
</script>
@endpush