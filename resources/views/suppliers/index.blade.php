    @extends('layouts.app')

    @section('content')
    <div class="container">
        <h1 class="mb-4">Choisir un fournisseur</h1>
        
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        
        <div class="card">
            <div class="card-header bg-primary text-white">
                Liste des fournisseurs
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Fournisseur</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($suppliers as $supplier)
                                <tr>
                                    <td>{{ $supplier['name'] }}</td>
                                    <td>
                                        <div class="btn-group">
                                        <a href="{{ route('supplier.dashboard', ['supplier_id' => urlencode($supplier['name'])]) }}" class="btn btn-sm btn-primary">
                                            Sélectionner
                                        </a>

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Aucun fournisseur trouvé</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endsection