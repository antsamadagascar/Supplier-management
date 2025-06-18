{{-- resources/views/dashboard/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Tableau de bord</h1>
    
    {{-- Filtres par date --}}
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-filter me-1"></i>
            Filtrer par période
        </div>
        <div class="card-body">
            <form action="{{ route('dashboard.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="start_date" class="form-label">Date de début</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label">Date de fin</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Filtrer</button>
                    <a href="{{ route('dashboard.index') }}" class="btn btn-secondary ms-2">Réinitialiser</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Cartes des statistiques principales --}}
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small">Total Commandes</div>
                            <div class="fs-4">{{ number_format($stats['orders_count'], 0, ',', ' ') }}</div>
                        </div>
                        <div>
                            <i class="fas fa-shopping-cart fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <div>{{ number_format($stats['orders_total'], 0, ',', ' ') }} XOF</div>
                    <a class="small text-white stretched-link" href="{{ route('dashboard.orders') }}">Détails</a>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small">Total Factures</div>
                            <div class="fs-4">{{ number_format($stats['invoices_count'], 0, ',', ' ') }}</div>
                        </div>
                        <div>
                            <i class="fas fa-file-invoice fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <div>{{ number_format($stats['invoices_total'], 0, ',', ' ') }} XOF</div>
                    <a class="small text-white stretched-link" href="{{ route('dashboard.invoices') }}">Détails</a>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small">Total Devis</div>
                            <div class="fs-4">{{ number_format($stats['quotations_count'], 0, ',', ' ') }}</div>
                        </div>
                        <div>
                            <i class="fas fa-quote-right fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <div>{{ number_format($stats['quotations_total'], 0, ',', ' ') }} XOF</div>
                    <a class="small text-white stretched-link" href="{{ route('dashboard.quotations') }}">Détails</a>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small">Fournisseurs Actifs</div>
                            <div class="fs-4">{{ number_format($stats['total_suppliers'], 0, ',', ' ') }}</div>
                        </div>
                        <div>
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <div>{{ number_format($stats['total_items'], 0, ',', ' ') }} Articles</div>
                    <a class="small text-white stretched-link" href="{{ route('dashboard.suppliers') }}">Détails</a>
                </div>
            </div>
        </div>
    </div>

    {{-- Cartes des statistiques secondaires --}}
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small">Factures Impayées</div>
                            <div class="fs-4">{{ number_format($stats['unpaid_invoices'], 0, ',', ' ') }}</div>
                        </div>
                        <div>
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <div>{{ ($stats['invoices_count'] > 0) ? number_format(($stats['unpaid_invoices'] / $stats['invoices_count']) * 100, 1) : 0 }}%</div>
                    <a class="small text-white stretched-link" href="{{ route('dashboard.invoices') }}?status=Unpaid">Détails</a>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-secondary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small">Commandes en attente</div>
                            <div class="fs-4">{{ number_format($stats['pending_orders'], 0, ',', ' ') }}</div>
                        </div>
                        <div>
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <div>{{ ($stats['orders_count'] > 0) ? number_format(($stats['pending_orders'] / $stats['orders_count']) * 100, 1) : 0 }}%</div>
                    <a class="small text-white stretched-link" href="{{ route('dashboard.orders') }}?status=To%20Receive%20and%20Bill">Détails</a>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-light text-dark mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small">Devis en attente</div>
                            <div class="fs-4">{{ number_format($stats['pending_quotations'], 0, ',', ' ') }}</div>
                        </div>
                        <div>
                            <i class="fas fa-hourglass-half fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <div>{{ ($stats['quotations_count'] > 0) ? number_format(($stats['pending_quotations'] / $stats['quotations_count']) * 100, 1) : 0 }}%</div>
                    <a class="small text-dark stretched-link" href="{{ route('dashboard.quotations') }}?status=Submitted">Détails</a>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card {{ $stats['revenue_trend_percentage'] >= 0 ? 'bg-success' : 'bg-danger' }} text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small">Revenus (Mois en cours)</div>
                            <div class="fs-4">{{ number_format($stats['revenue_current_month'], 0, ',', ' ') }}</div>
                        </div>
                        <div>
                            <i class="fas {{ $stats['revenue_trend_percentage'] >= 0 ? 'fa-chart-line' : 'fa-chart-line fa-rotate-180' }} fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <div>
                        {{ $stats['revenue_trend_percentage'] >= 0 ? '+' : '' }}{{ number_format($stats['revenue_trend_percentage'], 1) }}% vs mois précédent
                    </div>
                    <span class="small text-white">{{ number_format($stats['revenue_previous_month'], 0, ',', ' ') }} XOF</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Graphique d'évolution mensuelle --}}
        <div class="col-xl-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-area me-1"></i>
                    Évolution mensuelle
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart" width="100%" height="40"></canvas>
                </div>
            </div>
        </div>

        {{-- Top 5 fournisseurs --}}
        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-crown me-1"></i>
                    Top 5 Fournisseurs
                </div>
                <div class="card-body">
                    @if(count($topSuppliers) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Fournisseur</th>
                                        <th>Montant</th>
                                        <th>Commandes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topSuppliers as $supplier)
                                    <tr>
                                        <td>
                                            <a href="{{ route('supplier.dashboard', ['supplier_id' => $supplier['id']]) }}">
                                                {{ Str::limit($supplier['name'], 20) }}
                                            </a>
                                        </td>
                                        <td>{{ number_format($supplier['total_amount'], 0, ',', ' ') }}</td>
                                        <td>{{ $supplier['orders_count'] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            Aucun fournisseur avec des commandes sur la période sélectionnée.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Distribution des statuts --}}
        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-pie me-1"></i>
                    Distribution des statuts
                </div>
                <div class="card-body">
                    <canvas id="statusChart" width="100%" height="50"></canvas>
                </div>
            </div>
        </div>
        
        {{-- Répartition par fournisseur --}}
        <div class="col-xl-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-bar me-1"></i>
                    Répartition des commandes par fournisseur
                </div>
                <div class="card-body">
                    <canvas id="supplierChart" width="100%" height="50"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Données pour le graphique mensuel
    const monthlyData = @json($chartData['monthly_orders']);
    const monthlyInvoices = @json($chartData['monthly_invoices']);
    
    // Graphique d'évolution mensuelle
    const monthlyChartCtx = document.getElementById('monthlyChart').getContext('2d');
    new Chart(monthlyChartCtx, {
        type: 'line',
        data: {
            labels: monthlyData.map(item => item.month),
            datasets: [
                {
                    label: 'Commandes',
                    data: monthlyData.map(item => item.value),
                    backgroundColor: 'rgba(13, 110, 253, 0.2)',
                    borderColor: 'rgba(13, 110, 253, 1)',
                    borderWidth: 2,
                    tension: 0.4
                },
                {
                    label: 'Factures',
                    data: monthlyInvoices.map(item => item.value),
                    backgroundColor: 'rgba(25, 135, 84, 0.2)',
                    borderColor: 'rgba(25, 135, 84, 1)',
                    borderWidth: 2,
                    tension: 0.4
                }
            ]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString('fr-FR');
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += context.parsed.y.toLocaleString('fr-FR') + ' XOF';
                            return label;
                        }
                    }
                }
            }
        }
    });
    
    // Données pour le graphique des statuts
    const statusData = @json($chartData['status_distribution']);
    
    // Graphique de distribution des statuts
    const statusChartCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusChartCtx, {
        type: 'pie',
        data: {
            labels: statusData.map(item => item.status),
            datasets: [{
                data: statusData.map(item => item.count),
                backgroundColor: [
                    'rgba(13, 110, 253, 0.7)',
                    'rgba(25, 135, 84, 0.7)',
                    'rgba(255, 193, 7, 0.7)',
                    'rgba(220, 53, 69, 0.7)',
                    'rgba(108, 117, 125, 0.7)',
                    'rgba(0, 123, 255, 0.7)',
                    'rgba(40, 167, 69, 0.7)',
                    'rgba(111, 66, 193, 0.7)'
                ]
            }]
        },
        options: {
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((acc, curr) => acc + curr, 0);
                            const percentage = Math.round((value / total) * 100);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
    
    // Données pour le graphique par fournisseur
    const supplierData = @json($chartData['orders_by_supplier']);
    
    // Graphique de répartition par fournisseur
    const supplierChartCtx = document.getElementById('supplierChart').getContext('2d');
    new Chart(supplierChartCtx, {
        type: 'bar',
        data: {
            labels: supplierData.map(item => item.supplier),
            datasets: [{
                label: 'Montant total',
                data: supplierData.map(item => item.total),
                backgroundColor: 'rgba(13, 110, 253, 0.7)'
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString('fr-FR');
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += context.parsed.y.toLocaleString('fr-FR') + ' XOF';
                            return label;
                        }
                    }
                }
            }
        }
    });
});
</script>
@endsection