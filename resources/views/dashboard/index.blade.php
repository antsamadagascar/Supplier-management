@extends('layouts.app')

@section('content')
<!-- En-tête avec barre de navigation secondaire -->
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-tachometer-alt me-2"></i>Tableau de bord</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" id="export-pdf">
                <i class="fas fa-file-pdf"></i> Exporter PDF
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="export-excel">
                <i class="fas fa-file-excel"></i> Exporter Excel
            </button>
        </div>
        <div class="dropdown">
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="periodDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-calendar"></i> Cette semaine
            </button>
            <ul class="dropdown-menu" aria-labelledby="periodDropdown">
                <li><a class="dropdown-item period-select" href="#" data-period="day">Aujourd'hui</a></li>
                <li><a class="dropdown-item period-select" href="#" data-period="week">Cette semaine</a></li>
                <li><a class="dropdown-item period-select" href="#" data-period="month">Ce mois</a></li>
                <li><a class="dropdown-item period-select" href="#" data-period="quarter">Ce trimestre</a></li>
                <li><a class="dropdown-item period-select" href="#" data-period="year">Cette année</a></li>
            </ul>
        </div>
        <button type="button" class="btn btn-sm btn-primary ms-2" id="refresh-data">
            <i class="fas fa-sync-alt"></i> Rafraîchir
        </button>
    </div>
</div>

<!-- Alerte d'information -->
<div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
    <strong><i class="fas fa-info-circle me-2"></i>Astuce :</strong> Utilisez les filtres pour affiner les données par période.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>

<!-- Statistiques principales avec cartes animées -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2 dashboard-card">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Tickets</div>
                        <div class="h5 mb-0 font-weight-bold" id="total-tickets">152</div>
                        <div class="mt-2 text-xs"><span class="text-success me-2"><i class="fas fa-arrow-up"></i> 12%</span>depuis le dernier mois</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-ticket-alt fa-2x text-primary"></i>
                    </div>
                </div>
                <div class="progress progress-sm mt-3">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
            <div class="card-footer py-1 text-center">
                <a href="#" class="text-primary small stretched-link">Voir détails <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2 dashboard-card">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Leads</div>
                        <div class="h5 mb-0 font-weight-bold" id="total-leads">87</div>
                        <div class="mt-2 text-xs"><span class="text-success me-2"><i class="fas fa-arrow-up"></i> 8%</span>depuis le dernier mois</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-bullseye fa-2x text-success"></i>
                    </div>
                </div>
                <div class="progress progress-sm mt-3">
                    <div class="progress-bar bg-success" role="progressbar" style="width: 60%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
            <div class="card-footer py-1 text-center">
                <a href="#" class="text-success small stretched-link">Voir détails <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2 dashboard-card">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Clients</div>
                        <div class="h5 mb-0 font-weight-bold" id="total-customers">34</div>
                        <div class="mt-2 text-xs"><span class="text-info me-2"><i class="fas fa-equals"></i> 0%</span>depuis le dernier mois</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-info"></i>
                    </div>
                </div>
                <div class="progress progress-sm mt-3">
                    <div class="progress-bar bg-info" role="progressbar" style="width: 42%" aria-valuenow="42" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
            <div class="card-footer py-1 text-center">
                <a href="#" class="text-info small stretched-link">Voir détails <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2 dashboard-card">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Budget Total</div>
                        <div class="h5 mb-0 font-weight-bold" id="total-budget">75 400 €</div>
                        <div class="mt-2 text-xs"><span class="text-danger me-2"><i class="fas fa-arrow-down"></i> 3%</span>depuis le dernier mois</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-wallet fa-2x text-warning"></i>
                    </div>
                </div>
                <div class="progress progress-sm mt-3">
                    <div class="progress-bar bg-warning" role="progressbar" style="width: 85%" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
            <div class="card-footer py-1 text-center">
                <a href="#" class="text-warning small stretched-link">Voir détails <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </div>
</div>

<!-- Graphiques en ligne -->
<div class="row mb-4">
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Aperçu des Revenus</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                        <div class="dropdown-header">Options:</div>
                        <a class="dropdown-item" href="#"><i class="fas fa-download fa-sm fa-fw me-2 text-gray-400"></i>Exporter</a>
                        <a class="dropdown-item" href="#"><i class="fas fa-print fa-sm fa-fw me-2 text-gray-400"></i>Imprimer</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#"><i class="fas fa-cogs fa-sm fa-fw me-2 text-gray-400"></i>Paramètres</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="revenueChart" style="height: 320px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Distribution des Tickets</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                        <div class="dropdown-header">Options:</div>
                        <a class="dropdown-item" href="#"><i class="fas fa-download fa-sm fa-fw me-2 text-gray-400"></i>Exporter</a>
                        <a class="dropdown-item" href="#"><i class="fas fa-print fa-sm fa-fw me-2 text-gray-400"></i>Imprimer</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="ticketsDistributionChart"></canvas>
                </div>
                <div class="mt-4 text-center small">
                    <span class="me-2">
                        <i class="fas fa-circle text-primary"></i> Ouverts
                    </span>
                    <span class="me-2">
                        <i class="fas fa-circle text-success"></i> Résolus
                    </span>
                    <span class="me-2">
                        <i class="fas fa-circle text-info"></i> En attente
                    </span>
                    <span>
                        <i class="fas fa-circle text-danger"></i> Fermés
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Budgets et Dépenses par Client avec fonctionnalités de recherche et de tri -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Budgets et Dépenses par Client</h6>
                <div class="input-group w-25">
                    <input type="text" class="form-control form-control-sm" placeholder="Rechercher un client..." id="client-search">
                    <button class="btn btn-outline-secondary btn-sm" type="button" id="search-btn">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-bordered" id="clientsTable">
                        <thead class="table-light">
                            <tr>
                                <th><a href="#" class="sort-link" data-sort="name">Client <i class="fas fa-sort"></i></a></th>
                                <th><a href="#" class="sort-link" data-sort="budget">Budget Total (€) <i class="fas fa-sort"></i></a></th>
                                <th><a href="#" class="sort-link" data-sort="lead-expense">Dépenses Leads (€) <i class="fas fa-sort"></i></a></th>
                                <th><a href="#" class="sort-link" data-sort="ticket-expense">Dépenses Tickets (€) <i class="fas fa-sort"></i></a></th>
                                <th><a href="#" class="sort-link" data-sort="total-expense">Dépenses Totales (€) <i class="fas fa-sort"></i></a></th>
                                <th>Solde</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Entreprise A</td>
                                <td>15 000 €</td>
                                <td>4 200 €</td>
                                <td>3 800 €</td>
                                <td>8 000 €</td>
                                <td><span class="badge bg-success">7 000 €</span></td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></button>
                                        <button type="button" class="btn btn-sm btn-outline-warning"><i class="fas fa-edit"></i></button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Entreprise B</td>
                                <td>22 000 €</td>
                                <td>8 300 €</td>
                                <td>9 450 €</td>
                                <td>17 750 €</td>
                                <td><span class="badge bg-success">4 250 €</span></td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></button>
                                        <button type="button" class="btn btn-sm btn-outline-warning"><i class="fas fa-edit"></i></button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Entreprise C</td>
                                <td>8 400 €</td>
                                <td>2 500 €</td>
                                <td>6 300 €</td>
                                <td>8 800 €</td>
                                <td><span class="badge bg-danger">-400 €</span></td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></button>
                                        <button type="button" class="btn btn-sm btn-outline-warning"><i class="fas fa-edit"></i></button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Entreprise D</td>
                                <td>30 000 €</td>
                                <td>10 500 €</td>
                                <td>8 950 €</td>
                                <td>19 450 €</td>
                                <td><span class="badge bg-success">10 550 €</span></td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></button>
                                        <button type="button" class="btn btn-sm btn-outline-warning"><i class="fas fa-edit"></i></button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="table-dark">
                            <tr>
                                <td><strong>TOTAL</strong></td>
                                <td><strong>75 400 €</strong></td>
                                <td><strong>25 500 €</strong></td>
                                <td><strong>28 500 €</strong></td>
                                <td><strong>54 000 €</strong></td>
                                <td><strong>21 400 €</strong></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="dataTables_info">Affichage de 1 à 4 sur 4 entrées</div>
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm justify-content-end">
                            <li class="page-item disabled"><a class="page-link" href="#">Précédent</a></li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item"><a class="page-link" href="#">Suivant</a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Graphiques supplémentaires -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Conversion des Leads</h6>
            </div>
            <div class="card-body">
                <canvas id="leadsConversionChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Tickets par Catégorie</h6>
            </div>
            <div class="card-body">
                <canvas id="ticketCategoriesChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Performance Mensuelle</h6>
            </div>
            <div class="card-body">
                <canvas id="monthlyPerformanceChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Activité récente et Tâches -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Activité Récente</h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-item-marker">
                            <div class="timeline-item-marker-text">30m</div>
                            <div class="timeline-item-marker-indicator bg-primary"></div>
                        </div>
                        <div class="timeline-item-content">
                            <a class="fw-bold text-dark" href="#">Nouveau ticket</a> créé par Entreprise B
                            <div class="text-muted small">Il y a 30 minutes</div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-item-marker">
                            <div class="timeline-item-marker-text">1h</div>
                            <div class="timeline-item-marker-indicator bg-success"></div>
                        </div>
                        <div class="timeline-item-content">
                            <a class="fw-bold text-dark" href="#">Ticket #12345</a> résolu par Jean Dupont
                            <div class="text-muted small">Il y a 1 heure</div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-item-marker">
                            <div class="timeline-item-marker-text">2h</div>
                            <div class="timeline-item-marker-indicator bg-warning"></div>
                        </div>
                        <div class="timeline-item-content">
                            <a class="fw-bold text-dark" href="#">Nouveau lead</a> qualifié pour Entreprise C
                            <div class="text-muted small">Il y a 2 heures</div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-item-marker">
                            <div class="timeline-item-marker-text">1j</div>
                            <div class="timeline-item-marker-indicator bg-info"></div>
                        </div>
                        <div class="timeline-item-content">
                            <a class="fw-bold text-dark" href="#">Budget actualisé</a> pour Entreprise A
                            <div class="text-muted small">Hier à 15:30</div>
                        </div>
                    </div>
                </div>
                <div class="text-center mt-3">
                    <a href="#" class="btn btn-sm btn-primary">Voir toutes les activités</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Tâches à Effectuer</h6>
                <button class="btn btn-sm btn-primary" id="add-task"><i class="fas fa-plus"></i> Ajouter</button>
            </div>
            <div class="card-body">
                <div class="list-group">
                    <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="task1">
                            <label class="form-check-label" for="task1">
                                Contacter Entreprise B pour le renouvellement
                            </label>
                            <div class="text-muted small">Échéance: 02/04/2025</div>
                        </div>
                        <span class="badge bg-warning">Moyenne</span>
                    </div>
                    <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="task2">
                            <label class="form-check-label" for="task2">
                                Préparer le rapport mensuel
                            </label>
                            <div class="text-muted small">Échéance: 05/04/2025</div>
                        </div>
                        <span class="badge bg-danger">Élevée</span>
                    </div>
                    <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="task3">
                            <label class="form-check-label" for="task3">
                                Suivre le ticket #12346 pour Entreprise D
                            </label>
                            <div class="text-muted small">Échéance: 01/04/2025</div>
                        </div>
                        <span class="badge bg-info">Faible</span>
                    </div>
                    <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="task4">
                            <label class="form-check-label" for="task4">
                                Mettre à jour les détails de contact pour Entreprise C
                            </label>
                            <div class="text-muted small">Échéance: 07/04/2025</div>
                        </div>
                        <span class="badge bg-success">Terminée</span>
                    </div>
                </div>
                <div class="text-center mt-3">
                    <a href="#" class="btn btn-sm btn-primary">Gérer toutes les tâches</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="text-muted small text-center mb-4">
    <i class="fas fa-clock me-1"></i> Dernière mise à jour : {{ date('d/m/Y H:i:s') }}
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configuration globale des graphiques
    Chart.defaults.font.family = '"Nunito", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif';
    Chart.defaults.color = '#858796';
    
    // Graphique des revenus
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'],
            datasets: [{
                label: 'Revenus',
                data: [15000, 18000, 17500, 19800, 21000, 23000, 22500, 24000, 25000, 27000, 28500, 30000],
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
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgb(255, 255, 255)',
                    bodyColor: '#858796',
                    titleMarginBottom: 10,
                    titleColor: '#6e707e',
                    titleFont: {
                        size: 14
                    },
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    padding: 15,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return 'Revenus: ' + context.raw + ' €';
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    }
                },
                y: {
                    ticks: {
                        callback: function(value) {
                            return value + ' €';
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)',borderDash: [2],
                        drawBorder: false
                    }
                }
            }
        }
    });
    
    // Graphique de distribution des tickets
    const ticketsDistributionCtx = document.getElementById('ticketsDistributionChart').getContext('2d');
    const ticketsDistributionChart = new Chart(ticketsDistributionCtx, {
        type: 'doughnut',
        data: {
            labels: ['Ouverts', 'Résolus', 'En attente', 'Fermés'],
            datasets: [{
                data: [35, 45, 15, 5],
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#e74a3b'],
                hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#e02d1b'],
                hoverBorderColor: 'rgba(234, 236, 244, 1)',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgb(255, 255, 255)',
                    bodyColor: '#858796',
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    padding: 15,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.raw + '%';
                        }
                    }
                }
            },
            cutout: '70%'
        }
    });
    
    // Graphique de conversion des leads
    const leadsConversionCtx = document.getElementById('leadsConversionChart').getContext('2d');
    const leadsConversionChart = new Chart(leadsConversionCtx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'],
            datasets: [
                {
                    label: 'Leads',
                    backgroundColor: 'rgba(78, 115, 223, 0.8)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    data: [28, 32, 25, 37, 41, 35],
                    maxBarThickness: 25
                },
                {
                    label: 'Convertis',
                    backgroundColor: 'rgba(28, 200, 138, 0.8)',
                    borderColor: 'rgba(28, 200, 138, 1)',
                    data: [12, 15, 10, 18, 22, 16],
                    maxBarThickness: 25
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    grid: {
                        borderDash: [2],
                        drawBorder: false
                    },
                    ticks: {
                        beginAtZero: true
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.raw;
                        }
                    }
                }
            }
        }
    });
    
    // Graphique des tickets par catégorie
    const ticketCategoriesCtx = document.getElementById('ticketCategoriesChart').getContext('2d');
    const ticketCategoriesChart = new Chart(ticketCategoriesCtx, {
        type: 'polarArea',
        data: {
            labels: ['Support', 'Facturation', 'Technique', 'Fonctionnalité', 'Bug'],
            datasets: [{
                data: [25, 15, 32, 18, 10],
                backgroundColor: [
                    'rgba(78, 115, 223, 0.7)', 
                    'rgba(28, 200, 138, 0.7)', 
                    'rgba(54, 185, 204, 0.7)', 
                    'rgba(246, 194, 62, 0.7)', 
                    'rgba(231, 74, 59, 0.7)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    align: 'start'
                }
            }
        }
    });
    
    // Graphique de performance mensuelle
    const monthlyPerformanceCtx = document.getElementById('monthlyPerformanceChart').getContext('2d');
    const monthlyPerformanceChart = new Chart(monthlyPerformanceCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'],
            datasets: [{
                label: 'Tickets traités',
                data: [65, 72, 78, 81, 90, 95],
                borderColor: 'rgba(78, 115, 223, 1)',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                tension: 0.3,
                fill: true
            }, {
                label: 'Temps moyen (h)',
                data: [8, 7.5, 6.8, 6.2, 5.5, 5],
                borderColor: 'rgba(28, 200, 138, 1)',
                backgroundColor: 'rgba(28, 200, 138, 0.1)',
                pointBackgroundColor: 'rgba(28, 200, 138, 1)',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    
    // Rafraîchir les données
    document.getElementById('refresh-data').addEventListener('click', function() {
        // Simuler un chargement de données
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Chargement...';
        this.disabled = true;
        
        setTimeout(() => {
            // Mise à jour des compteurs
            document.getElementById('total-tickets').textContent = Math.floor(Math.random() * 50) + 120;
            document.getElementById('total-leads').textContent = Math.floor(Math.random() * 30) + 70;
            document.getElementById('total-customers').textContent = Math.floor(Math.random() * 10) + 30;
            document.getElementById('total-budget').textContent = (Math.floor(Math.random() * 10000) + 70000) + ' €';
            
            // Mise à jour des graphiques
            revenueChart.data.datasets[0].data = revenueChart.data.datasets[0].data.map(
                value => value * (0.9 + Math.random() * 0.2)
            );
            revenueChart.update();
            
            ticketsDistributionChart.data.datasets[0].data = [
                Math.floor(Math.random() * 20) + 25,
                Math.floor(Math.random() * 20) + 35,
                Math.floor(Math.random() * 10) + 10,
                Math.floor(Math.random() * 5) + 5
            ];
            ticketsDistributionChart.update();
            
            // Rétablir le bouton
            this.innerHTML = '<i class="fas fa-sync-alt"></i> Rafraîchir';
            this.disabled = false;
            
            // Afficher une notification
            const now = new Date();
            const timeString = now.getHours().toString().padStart(2, '0') + ':' + 
                              now.getMinutes().toString().padStart(2, '0') + ':' + 
                              now.getSeconds().toString().padStart(2, '0');
            
            alert('Données mises à jour avec succès à ' + timeString);
        }, 1500);
    });
    
    // Filtres de période
    document.querySelectorAll('.period-select').forEach(item => {
        item.addEventListener('click', event => {
            event.preventDefault();
            const period = event.target.dataset.period;
            document.getElementById('periodDropdown').textContent = event.target.textContent;
            
            // Simuler un changement de données basé sur la période
            alert('Période modifiée: ' + event.target.textContent);
        });
    });
    
    // Recherche client
    document.getElementById('search-btn').addEventListener('click', function() {
        const searchTerm = document.getElementById('client-search').value.toLowerCase();
        const rows = document.querySelectorAll('#clientsTable tbody tr');
        
        rows.forEach(row => {
            const clientName = row.cells[0].textContent.toLowerCase();
            if (clientName.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
    
    // Tri des colonnes
    document.querySelectorAll('.sort-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            alert('Tri par ' + this.dataset.sort + ' activé');
        });
    });
    
    // Bouton pour ajouter une tâche
    document.getElementById('add-task').addEventListener('click', function() {
        const taskName = prompt('Entrez le nom de la nouvelle tâche:');
        if (taskName) {
            alert('Tâche ajoutée: ' + taskName);
        }
    });
    
    // Animation des cartes du dashboard
    document.querySelectorAll('.dashboard-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.classList.add('shadow-lg');
            this.style.transform = 'translateY(-5px)';
            this.style.transition = 'all 0.3s ease';
        });
        
        card.addEventListener('mouseleave', function() {
            this.classList.remove('shadow-lg');
            this.style.transform = 'translateY(0)';
        });
    });
    
    // Export PDF
    document.getElementById('export-pdf').addEventListener('click', function() {
        alert('Export PDF en cours...');
    });
    
    // Export Excel
    document.getElementById('export-excel').addEventListener('click', function() {
        alert('Export Excel en cours...');
    });
});
</script>
@endsection

@section('styles')
<style>
/* Styles supplémentaires pour le dashboard */
.dashboard-card {
    transition: all 0.3s ease;
}

.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.progress-sm {
    height: 0.5rem;
}

/* Timeline */
.timeline {
    position: relative;
    padding-left: 1rem;
}

.timeline::before {
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0.25rem;
    content: '';
    width: 1px;
    background-color: #e3e6ec;
}

.timeline-item {
    position: relative;
    padding-bottom: 1.5rem;
}

.timeline-item:last-child {
    padding-bottom: 0;
}

.timeline-item-marker {
    position: absolute;
    left: -1rem;
    width: 1rem;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.timeline-item-marker-text {
    font-size: 0.75rem;
    color: #a7aeb8;
    margin-bottom: 0.25rem;
}

.timeline-item-marker-indicator {
    height: 0.75rem;
    width: 0.75rem;
    border-radius: 100%;
    background-color: #fff;
    border: 1px solid #e3e6ec;
}

.timeline-item-content {
    padding-left: 0.75rem;
    padding-top: 0.15rem;
}

/* Task list */
.list-group-item .form-check {
    width: 85%;
}

.list-group-item .form-check-input:checked + .form-check-label {
    text-decoration: line-through;
    color: #6c757d;
}
</style>
@endsection