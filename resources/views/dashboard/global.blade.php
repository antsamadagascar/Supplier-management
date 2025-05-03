@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Dashboard  Fournisseurs</h1>
    
    <!-- Filtres -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-filter me-1"></i>
                Filtres
            </div>
            <div class="card-body">
                <form action="{{ route('dashboard') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Type de filtre</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="filter_type" id="year_filter" value="year" {{ $filterType == 'year' ? 'checked' : '' }}>
                            <label class="form-check-label" for="year_filter">
                                Par année
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="filter_type" id="custom_filter" value="custom" {{ $filterType == 'custom' ? 'checked' : '' }}>
                            <label class="form-check-label" for="custom_filter">
                                Période personnalisée
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-3" id="year_selector" {{ $filterType == 'custom' ? 'style=display:none' : '' }}>
                        <label for="year" class="form-label">Année</label>
                        <select class="form-select" id="year" name="year">
                            @foreach($years as $y)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-3" id="date_range" {{ $filterType == 'year' ? 'style=display:none' : '' }}>
                        <label for="start_date" class="form-label">Date de début</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate ?? '' }}">
                    </div>
                    
                    <div class="col-md-3" id="date_range_end" {{ $filterType == 'year' ? 'style=display:none' : '' }}>
                        <label for="end_date" class="form-label">Date de fin</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate ?? '' }}">
                    </div>
                    
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Appliquer les filtres</button>
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary">Réinitialiser</a>
                    </div>
                </form>
            </div>
        </div>
    
    <!-- KPIs -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">Total Commandes</h5>
                            <h2 class="mb-0">{{ number_format($dashboardData['total_orders'], 0, ',', ' ') }}</h2>
                        </div>
                        <div class="text-white">
                            <i class="fas fa-shopping-cart fa-3x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">Total Factures</h5>
                            <h2 class="mb-0">{{ number_format($dashboardData['total_invoices'], 0, ',', ' ') }}</h2>
                        </div>
                        <div class="text-white">
                            <i class="fas fa-file-invoice fa-3x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">Total Devis</h5>
                            <h2 class="mb-0">{{ number_format($dashboardData['total_quotations'], 0, ',', ' ') }}</h2>
                        </div>
                        <div class="text-white">
                            <i class="fas fa-file-signature fa-3x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">Factures impayées</h5>
                            <h2 class="mb-0">{{ $dashboardData['unpaid_invoices'] }}</h2>
                        </div>
                        <div class="text-white">
                            <i class="fas fa-exclamation-circle fa-3x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Graphiques -->
    <div class="row mb-4">
        <div class="col-xl-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-chart-bar me-1"></i>
                    Montants par fournisseur (Top 10)
                </div>
                <div class="card-body">
                    <canvas id="supplierBarChart" height="300"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <i class="fas fa-chart-pie me-1"></i>
                    Statut des factures
                </div>
                <div class="card-body">
                    <canvas id="invoicePieChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tableau des fournisseurs -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-table me-1"></i>
            Liste des fournisseurs
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="suppliersTable">
                    <thead>
                        <tr>
                            <th>Fournisseur</th>
                            <th>Total Commandes</th>
                            <th>Total Factures</th>
                            <th>Factures payées</th>
                            <th>Factures impayées</th>
                            <th>Total Devis</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dashboardData['suppliers_data'] as $supplier)
                        <tr>
                            <td>{{ $supplier['name'] }}</td>
                            <td>{{ number_format($supplier['orders_total'], 0, ',', ' ') }}</td>
                            <td>{{ number_format($supplier['invoices_total'], 0, ',', ' ') }}</td>
                            <td>{{ $supplier['paid_invoices'] }}</td>
                            <td>
                                @if($supplier['unpaid_invoices'] > 0)
                                    <span class="badge bg-danger">{{ $supplier['unpaid_invoices'] }}</span>
                                @else
                                    <span class="badge bg-success">0</span>
                                @endif
                            </td>
                            <td>{{ number_format($supplier['quotations_total'], 0, ',', ' ') }}</td>
                            <td>
                                <a href="{{ route('supplier.dashboard', ['supplier_id' => $supplier['id']]) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-eye"></i> Détails
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Toggle des filtres
    document.getElementById('year_filter').addEventListener('change', function() {
        document.getElementById('year_selector').style.display = 'block';
        document.getElementById('date_range').style.display = 'none';
        document.getElementById('date_range_end').style.display = 'none';
    });
    
    document.getElementById('custom_filter').addEventListener('change', function() {
        document.getElementById('year_selector').style.display = 'none';
        document.getElementById('date_range').style.display = 'block';
        document.getElementById('date_range_end').style.display = 'block';
    });
    
    // Graphique à barres pour les montants par fournisseur
    const supplierData = @json($dashboardData['chart_data']['orders_by_supplier'] ?? []);
    
    const barChart = new Chart(document.getElementById('supplierBarChart'), {
        type: 'bar',
        data: {
            labels: supplierData.map(item => item.name),
            datasets: [
                {
                    label: 'Commandes',
                    data: supplierData.map(item => item.commandes ?? 0),
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Factures',
                    data: supplierData.map(item => item.factures ?? 0),
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Devis',
                    data: supplierData.map(item => item.devis ?? 0),
                    backgroundColor: 'rgba(255, 206, 86, 0.6)',
                    borderColor: 'rgba(255, 206, 86, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Montant (€)'
                    }
                }
            }
        }
    });
    
    // Graphique camembert pour le statut des factures
    const invoiceData = @json($dashboardData['chart_data']['invoice_status'] ?? []);
    
    const pieChart = new Chart(document.getElementById('invoicePieChart'), {
        type: 'pie',
        data: {
            labels: invoiceData.map(item => item.name),
            datasets: [{
                data: invoiceData.map(item => item.value),
                backgroundColor: [
                    'rgba(75, 192, 192, 0.6)',
                    'rgba(255, 99, 132, 0.6)'
                ],
                borderColor: [
                    'rgba(75, 192, 192, 1)',
                    'rgba(255, 99, 132, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
    
    // DataTable pour le tableau des fournisseurs
    $(document).ready(function() {
        $('#suppliersTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/fr-FR.json'
            },
            order: [[1, 'desc']] // Tri par défaut sur le montant total des commandes
        });
    });
</script>
@endsection