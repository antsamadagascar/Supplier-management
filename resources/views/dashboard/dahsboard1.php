@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Filtres -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Filtres</h5>
        </div>
        <div class="card-body">
            <form id="filter-form" class="row g-3">
                <div class="col-md-3">
                    <label for="date-debut" class="form-label">Date début</label>
                    <input type="date" class="form-control" id="date-debut" name="date_debut" value="2025-01-01">
                </div>
                <div class="col-md-3">
                    <label for="date-fin" class="form-label">Date fin</label>
                    <input type="date" class="form-control" id="date-fin" name="date_fin" value="2025-03-30">
                </div>
                <div class="col-md-3">
                    <label for="annee" class="form-label">Année</label>
                    <select class="form-select" id="annee" name="annee">
                        <option value="">Toutes les années</option>
                        <option value="2025" selected>2025</option>
                        <option value="2024">2024</option>
                        <option value="2023">2023</option>
                        <option value="2022">2022</option>
                        <option value="2021">2021</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="categorie" class="form-label">Catégorie</label>
                    <select class="form-select" id="categorie" name="categorie">
                        <option value="">Toutes les catégories</option>
                        <option value="1" selected>Développement</option>
                        <option value="2">Design</option>
                        <option value="3">Marketing</option>
                    </select>
                </div>
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-primary">Appliquer</button>
                    <button type="reset" class="btn btn-outline-secondary">Réinitialiser</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Chiffres clés -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-count">150</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Revenus</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="revenue-count">12,500.00 €</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-euro-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Taux de complétion</div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800" id="completion-rate">75%</div>
                                </div>
                                <div class="col">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-info" role="progressbar" id="completion-bar"
                                            style="width: 75%" aria-valuenow="75" aria-valuemin="0"
                                            aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                En attente</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="pending-count">12</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="row mb-4">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Aperçu des revenus</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Options:</div>
                            <a class="dropdown-item" href="#" id="monthly-view">Vue mensuelle</a>
                            <a class="dropdown-item" href="#" id="quarterly-view">Vue trimestrielle</a>
                            <a class="dropdown-item" href="#" id="yearly-view">Vue annuelle</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="revenuesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Répartition par source</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="sourcesChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small" id="chart-legend">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau de données -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Données détaillées</h6>
            <div>
                <button class="btn btn-sm btn-outline-primary" id="export-excel">
                    <i class="fas fa-file-excel me-1"></i>Exporter Excel
                </button>
                <button class="btn btn-sm btn-outline-danger" id="export-pdf">
                    <i class="fas fa-file-pdf me-1"></i>Exporter PDF
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Titre</th>
                            <th>Catégorie</th>
                            <th>Montant</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>25/03/2025</td>
                            <td>Projet A</td>
                            <td>Développement</td>
                            <td>5,000.00 €</td>
                            <td><span class="badge bg-success">Terminé</span></td>
                            <td>
                                <a href="#" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                <a href="#" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>28/03/2025</td>
                            <td>Projet B</td>
                            <td>Design</td>
                            <td>3,500.00 €</td>
                            <td><span class="badge bg-warning">En cours</span></td>
                            <td>
                                <a href="#" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                <a href="#" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                            </td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>15/02/2025</td>
                            <td>Campagne X</td>
                            <td>Marketing</td>
                            <td>4,000.00 €</td>
                            <td><span class="badge bg-danger">En attente</span></td>
                            <td>
                                <a href="#" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                <a href="#" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.1/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Données statiques pour les graphiques
    const revenuesData = {
        labels: ['Janvier', 'Février', 'Mars'],
        data: [4000, 4500, 5000]
    };

    const sourcesData = {
        labels: ['Développement', 'Design', 'Marketing'],
        data: [5000, 3500, 4000]
    };

    // Configuration du graphique des revenus
    var ctx = document.getElementById('revenuesChart').getContext('2d');
    var revenuesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: revenuesData.labels,
            datasets: [{
                label: 'Revenus',
                lineTension: 0.3,
                backgroundColor: 'rgba(78, 115, 223, 0.05)',
                borderColor: 'rgba(78, 115, 223, 1)',
                pointRadius: 3,
                pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                pointBorderColor: 'rgba(78, 115, 223, 1)',
                pointHoverRadius: 5,
                pointHoverBackgroundColor: 'rgba(78, 115, 223, 1)',
                pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
                pointHitRadius: 10,
                pointBorderWidth: 2,
                data: revenuesData.data,
            }],
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Configuration du graphique en camembert
    var ctx2 = document.getElementById('sourcesChart').getContext('2d');
    var sourcesColors = ['#4e73df', '#1cc88a', '#36b9cc'];

    var sourcesChart = new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: sourcesData.labels,
            datasets: [{
                data: sourcesData.data,
                backgroundColor: sourcesColors,
                hoverBackgroundColor: sourcesColors,
                hoverBorderColor: 'white',
            }],
        },
        options: {
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Génération de la légende personnalisée
    var legendHtml = '';
    for (var i = 0; i < sourcesData.labels.length; i++) {
        legendHtml += '<span class="mr-2">';
        legendHtml += '<i class="fas fa-circle" style="color: ' + sourcesColors[i] + '"></i> ';
        legendHtml += sourcesData.labels[i];
        legendHtml += '</span>';
    }
    document.getElementById('chart-legend').innerHTML = legendHtml;

    // Initialisation du tableau de données
    $('#dataTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json'
        },
        pageLength: 10,
        responsive: true
    });

    // Gestion des événements sur les filtres (simulation statique)
    document.getElementById('filter-form').addEventListener('submit', function(e) {
        e.preventDefault();
        alert('Filtre appliqué (version statique - aucune mise à jour réelle)');
    });

    // Gestion des exports (simulation statique)
    document.getElementById('export-excel').addEventListener('click', function() {
        alert('Export Excel (version statique - pas de fichier généré)');
    });
    
    document.getElementById('export-pdf').addEventListener('click', function() {
        alert('Export PDF (version statique - pas de fichier généré)');
    });

    // Gestion des vues de graphique (simulation statique)
    document.getElementById('monthly-view').addEventListener('click', function(e) {
        e.preventDefault();
        revenuesChart.data.labels = ['Janvier', 'Février', 'Mars'];
        revenuesChart.data.datasets[0].data = [4000, 4500, 5000];
        revenuesChart.update();
    });
    
    document.getElementById('quarterly-view').addEventListener('click', function(e) {
        e.preventDefault();
        revenuesChart.data.labels = ['T1', 'T2'];
        revenuesChart.data.datasets[0].data = [8500, 5000];
        revenuesChart.update();
    });
    
    document.getElementById('yearly-view').addEventListener('click', function(e) {
        e.preventDefault();
        revenuesChart.data.labels = ['2025'];
        revenuesChart.data.datasets[0].data = [12500];
        revenuesChart.update();
    });
});
</script>
@endsection