<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Données statiques pour tester
        $allTickets = [
            ['id' => 1, 'title' => 'Problème de connexion', 'priority' => 'Haute', 'priority_color' => '#dc3545', 'status' => 'En cours', 'status_color' => '#ffc107'],
            ['id' => 2, 'title' => 'Mise à jour requise', 'priority' => 'Faible', 'priority_color' => '#28a745', 'status' => 'Résolu', 'status_color' => '#28a745'],
            ['id' => 3, 'title' => 'Erreur serveur', 'priority' => 'Moyenne', 'priority_color' => '#ffc107', 'status' => 'En attente', 'status_color' => '#6c757d'],
            ['id' => 4, 'title' => 'Bug interface', 'priority' => 'Haute', 'priority_color' => '#dc3545', 'status' => 'En cours', 'status_color' => '#ffc107'],
            ['id' => 5, 'title' => 'Demande de fonctionnalité', 'priority' => 'Faible', 'priority_color' => '#28a745', 'status' => 'Ouvert', 'status_color' => '#007bff'],
            ['id' => 6, 'title' => 'Problème de paiement', 'priority' => 'Haute', 'priority_color' => '#dc3545', 'status' => 'Résolu', 'status_color' => '#28a745'],
            ['id' => 7, 'title' => 'Test réseau', 'priority' => 'Moyenne', 'priority_color' => '#ffc107', 'status' => 'En attente', 'status_color' => '#6c757d'],
            ['id' => 8, 'title' => 'Panne matériel', 'priority' => 'Haute', 'priority_color' => '#dc3545', 'status' => 'Ouvert', 'status_color' => '#007bff'],
            ['id' => 9, 'title' => 'Support client', 'priority' => 'Faible', 'priority_color' => '#28a745', 'status' => 'Résolu', 'status_color' => '#28a745'],
            ['id' => 10, 'title' => 'Erreur de facturation', 'priority' => 'Moyenne', 'priority_color' => '#ffc107', 'status' => 'En cours', 'status_color' => '#ffc107'],
            ['id' => 11, 'title' => 'Problème de sécurité', 'priority' => 'Haute', 'priority_color' => '#dc3545', 'status' => 'En attente', 'status_color' => '#6c757d'],
            ['id' => 12, 'title' => 'Mise à jour logicielle', 'priority' => 'Faible', 'priority_color' => '#28a745', 'status' => 'Ouvert', 'status_color' => '#007bff'],
        ];

        // Pas besoin de pagination manuelle ici, DataTables gère ça côté client
        $tickets = $allTickets;

        return view('tableau.index', compact('tickets'));
    }
}   