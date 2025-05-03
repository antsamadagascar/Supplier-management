<?php
namespace App\Http\Controllers;

use App\Services\ErpApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    private ErpApiService $erpApiService;

    public function __construct(ErpApiService $erpApiService)
    {
        $this->erpApiService = $erpApiService;
    }

    public function index(Request $request)
    {
        try {
            // Gestion des filtres de date
            $filterType = $request->input('filter_type', 'year');
            $year = $request->input('year', date('Y'));

            if ($filterType === 'year') {
                $startDate = "{$year}-01-01";
                $endDate = "{$year}-12-31";
            } else {
                $startDate = $request->input('start_date');
                $endDate = $request->input('end_date');

                if (!$startDate || !$endDate) {
                    $startDate = date('Y-m-01');
                    $endDate = date('Y-m-t');
                }
            }
            
            $dashboardData = $this->initializeDashboardData();

            $stats = $this->getGlobalStats($startDate, $endDate);
            
            //dd($stats);
            
            // Mise à jour des données du dashboard
            $dashboardData['total_quotations'] = $stats['quotations_count'];
            $dashboardData['total_quotations_amount'] = $stats['quotations_total'];
            $dashboardData['total_orders'] = $stats['orders_count'];
            $dashboardData['total_orders_amount'] = $stats['orders_total'];
            $dashboardData['total_completed_orders'] = $stats['completed_orders'];
            $dashboardData['total_invoices'] = $stats['invoices_count'];
            $dashboardData['total_invoices_amount'] = $stats['invoices_total'];
            $dashboardData['unpaid_invoices'] = $stats['unpaid_invoices'];
            $dashboardData['paid_invoices'] = $stats['invoices_count'] - $stats['unpaid_invoices'];

        //    dd($dashboardData['total_quotations']);
            

            // Récupération des données par fournisseur
            $suppliers = $this->erpApiService->getResource('Supplier');
            if (!empty($suppliers)) {
                foreach ($suppliers as $supplier) {
                    $supplier_id = $supplier['name'];
                    $supplierStats = $this->getSupplierStats($supplier_id, $startDate, $endDate);
                    DD($supplierStats);
                    $dashboardData['suppliers_data'][] = [
                        'name' => $supplier['supplier_name'] ?? $supplier_id,
                        'id' => $supplier_id,
                        'quotations_count' => $supplierStats['quotations_count'],
                        'quotations_total' => $supplierStats['quotations_total'],
                        'orders_count' => $supplierStats['orders_count'],
                        'orders_total' => $supplierStats['orders_total'],
                        'completed_orders' => $supplierStats['completed_orders'],
                        'invoices_count' => $supplierStats['invoices_count'],
                        'invoices_total' => $supplierStats['invoices_total'],
                        'unpaid_invoices' => $supplierStats['unpaid_invoices'],
                        'paid_invoices' => $supplierStats['invoices_count'] - $supplierStats['unpaid_invoices']
                    ];
                }
         //       DD($dashboardData['suppliers_data']);
        //        // Tri par total des commandes
                usort($dashboardData['suppliers_data'], fn($a, $b) => $b['orders_total'] <=> $a['orders_total']);
            }

            // Préparation des données pour les graphiques
            $dashboardData['chart_data'] = $this->prepareChartData($dashboardData['suppliers_data']);

            // Liste des années pour le filtre
            $years = range(date('Y') - 5, date('Y'));

            return view('dashboard.global', compact('dashboardData', 'years', 'year', 'startDate', 'endDate', 'filterType'));
        } catch (\Exception $e) {
            Log::error('Erreur dashboard global', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return view('dashboard.global', [
                'dashboardData' => $this->initializeDashboardData(),
                'years' => range(date('Y') - 5, date('Y')),
                'year' => $year,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'filterType' => $filterType,
                'error' => 'Impossible de charger le dashboard global.'
            ]);
        }
    }

    private function initializeDashboardData()
    {
        return [
            'total_quotations' => 0,
            'total_quotations_amount' => 0,
            'total_orders' => 0,
            'total_orders_amount' => 0,
            'total_completed_orders' => 0,
            'total_invoices' => 0,
            'total_invoices_amount' => 0,
            'paid_invoices' => 0,
            'unpaid_invoices' => 0,
            'suppliers_data' => [],
            'chart_data' => []
        ];
    }

    private function getGlobalStats($startDate = null, $endDate = null)
    {
        $stats = [
            'quotations_count' => 0,
            'quotations_total' => 0,
            'pending_quotations' => 0,
            'orders_count' => 0,
            'orders_total' => 0,
            'pending_orders' => 0,
            'completed_orders' => 0,
            'invoices_count' => 0,
            'invoices_total' => 0,
            'unpaid_invoices' => 0,
        ];

        try {
            // Filtres de date
            $dateFilters = [];
            if ($startDate && $endDate) {
                $dateFilters = [
                    ['transaction_date', '>=', $startDate],
                    ['transaction_date', '<=', $endDate]
                ];
            }


            $quotations = $this->erpApiService->getResource('Supplier Quotation', [
                'filters' => $dateFilters,
                'fields' => ['name', 'net_total', 'grand_total', 'base_net_total', 'base_grand_total', 'status', 'transaction_date'],
                'limit_page_length' => 1000
            ]);

            Log::info('Raw global quotations data', [
                'count' => count($quotations),
                'data' => $quotations,
                'sample' => !empty($quotations) ? $quotations[0] : 'Aucun devis'
            ]);

            if (is_array($quotations)) {
                $stats['quotations_count'] = count($quotations);
                
                // Correction ici : s'assurer que les valeurs sont bien converties en nombres
                $stats['quotations_total'] = 0;
                foreach ($quotations as $q) {
                    // Essayer différents champs dans l'ordre de préférence et convertir explicitement en float
                    $amount = 0;
                    if (!empty($q['net_total']) && is_numeric($q['net_total'])) {
                        $amount = (float)$q['net_total'];
                    } elseif (!empty($q['base_net_total']) && is_numeric($q['base_net_total'])) {
                        $amount = (float)$q['base_net_total'];
                    } elseif (!empty($q['grand_total']) && is_numeric($q['grand_total'])) {
                        $amount = (float)$q['grand_total'];
                    } elseif (!empty($q['base_grand_total']) && is_numeric($q['base_grand_total'])) {
                        $amount = (float)$q['base_grand_total'];
                    }
                    $stats['quotations_total'] += $amount;
                }
                
                $stats['pending_quotations'] = count(array_filter($quotations, fn($q) => in_array($q['status'] ?? '', ['Draft', 'Submitted'])));
            } else {
                Log::warning('Aucun devis renvoyé ou format inattendu', ['quotations' => $quotations]);
            }

            // Commandes
            $orders = $this->erpApiService->getResource('Purchase Order', [
                'filters' => $dateFilters,
                'fields' => ['name', 'net_total', 'grand_total', 'base_net_total', 'base_grand_total', 'status', 'transaction_date', 'per_received', 'per_billed'],
                'limit_page_length' => 1000
            ]);

            Log::info('Raw global orders data', [
                'count' => count($orders),
                'data' => $orders,
                'sample' => !empty($orders) ? $orders[0] : 'Aucune commande'
            ]);

            if (is_array($orders)) {
                $stats['orders_count'] = count($orders);
                
                // Correction ici également
                $stats['orders_total'] = 0;
                foreach ($orders as $o) {
                    $amount = 0;
                    if (!empty($o['net_total']) && is_numeric($o['net_total'])) {
                        $amount = (float)$o['net_total'];
                    } elseif (!empty($o['base_net_total']) && is_numeric($o['base_net_total'])) {
                        $amount = (float)$o['base_net_total'];
                    } elseif (!empty($o['grand_total']) && is_numeric($o['grand_total'])) {
                        $amount = (float)$o['grand_total'];
                    } elseif (!empty($o['base_grand_total']) && is_numeric($o['base_grand_total'])) {
                        $amount = (float)$o['base_grand_total'];
                    }
                    $stats['orders_total'] += $amount;
                }
                
                $stats['pending_orders'] = count(array_filter($orders, fn($o) => in_array($o['status'] ?? '', ['Draft', 'To Receive and Bill'])));
                $stats['completed_orders'] = count(array_filter($orders, fn($o) => ($o['per_received'] ?? 0) == 100 && ($o['per_billed'] ?? 0) == 100));
            } else {
                Log::warning('Aucune commande renvoyée ou format inattendu', ['orders' => $orders]);
            }

            // Factures
            $invoiceFilters = [];
            if ($startDate && $endDate) {
                $invoiceFilters = [
                    ['posting_date', '>=', $startDate],
                    ['posting_date', '<=', $endDate]
                ];
            }

            $invoices = $this->erpApiService->getResource('Purchase Invoice', [
                'filters' => $invoiceFilters,
                'fields' => ['name', 'net_total', 'grand_total', 'base_net_total', 'base_grand_total', 'status', 'posting_date'],
                'limit_page_length' => 1000
            ]);

            Log::info('Raw global invoices data', [
                'count' => count($invoices),
                'data' => $invoices,
                'sample' => !empty($invoices) ? $invoices[0] : 'Aucune facture'
            ]);

            if (is_array($invoices)) {
                $stats['invoices_count'] = count($invoices);
                
                // Même correction pour les factures
                $stats['invoices_total'] = 0;
                foreach ($invoices as $i) {
                    $amount = 0;
                    if (!empty($i['net_total']) && is_numeric($i['net_total'])) {
                        $amount = (float)$i['net_total'];
                    } elseif (!empty($i['base_net_total']) && is_numeric($i['base_net_total'])) {
                        $amount = (float)$i['base_net_total'];
                    } elseif (!empty($i['grand_total']) && is_numeric($i['grand_total'])) {
                        $amount = (float)$i['grand_total'];
                    } elseif (!empty($i['base_grand_total']) && is_numeric($i['base_grand_total'])) {
                        $amount = (float)$i['base_grand_total'];
                    }
                    $stats['invoices_total'] += $amount;
                }
                
                $stats['unpaid_invoices'] = count(array_filter($invoices, fn($i) => in_array($i['status'] ?? '', ['Unpaid', 'Overdue'])));
            } else {
                Log::warning('Aucune facture renvoyée ou format inattendu', ['invoices' => $invoices]);
            }

            Log::info('Global stats', ['stats' => $stats]);

        } catch (\Exception $e) {
            Log::error('Erreur calcul stats globaux', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        return $stats;
    }

    private function getSupplierStats($supplier_id, $startDate = null, $endDate = null)
    {
        $stats = [
            'quotations_count' => 0,
            'quotations_total' => 0,
            'pending_quotations' => 0,
            'orders_count' => 0,
            'orders_total' => 0,
            'pending_orders' => 0,
            'completed_orders' => 0,
            'invoices_count' => 0,
            'invoices_total' => 0,
            'unpaid_invoices' => 0,
        ];

        try {
            // Filtres de date
            $dateFilters = [];
            if ($startDate && $endDate) {
                $dateFilters = [
                    ['transaction_date', '>=', $startDate],
                    ['transaction_date', '<=', $endDate]
                ];
            }

            // Devis
            $quotationFilters = [['supplier', '=', $supplier_id]];
            if (!empty($dateFilters)) {
                $quotationFilters = array_merge($quotationFilters, $dateFilters);
            }

            $quotations = $this->erpApiService->getResource('Supplier Quotation', [
                'filters' => $quotationFilters,
                'fields' => ['name', 'grand_total', 'net_total', 'total', 'status', 'transaction_date'],
                'limit_page_length' => 1000
            ]);

            Log::info('Raw supplier quotations data', [
                'supplier_id' => $supplier_id,
                'count' => count($quotations),
                'data' => $quotations
            ]);

            if (is_array($quotations)) {
                $stats['quotations_count'] = count($quotations);
                
                // Correction similaire pour les devis fournisseurs
                $stats['quotations_total'] = 0;
                foreach ($quotations as $q) {
                    $amount = 0;
                    if (!   empty($q['net_total']) && is_numeric($q['net_total'])) {
                        $amount = (float)$q['net_total'];
                    } elseif (!empty($q['grand_total']) && is_numeric($q['grand_total'])) {
                        $amount = (float)$q['grand_total'];
                    } elseif (!empty($q['total']) && is_numeric($q['total'])) {
                        $amount = (float)$q['total'];
                    }
                    $stats['quotations_total'] += $amount;
                }
                
                $stats['pending_quotations'] = count(array_filter($quotations, fn($q) => in_array($q['status'] ?? '', ['Draft', 'Submitted'])));
            }

            // Commandes
            $orderFilters = [['supplier', '=', $supplier_id]];
            if (!empty($dateFilters)) {
                $orderFilters = array_merge($orderFilters, $dateFilters);
            }

            $orders = $this->erpApiService->getResource('Purchase Order', [
                'filters' => $orderFilters,
                'fields' => ['name', 'grand_total', 'net_total', 'total', 'status', 'transaction_date', 'per_received', 'per_billed'],
                'limit_page_length' => 1000
            ]);

            Log::info('Raw supplier orders data', [
                'supplier_id' => $supplier_id,
                'count' => count($orders),
                'data' => $orders
            ]);

            if (is_array($orders)) {
                $stats['orders_count'] = count($orders);
                
                // Correction pour les commandes fournisseurs
                $stats['orders_total'] = 0;
                foreach ($orders as $o) {
                    $amount = 0;
                    if (!empty($o['net_total']) && is_numeric($o['net_total'])) {
                        $amount = (float)$o['net_total'];
                    } elseif (!empty($o['grand_total']) && is_numeric($o['grand_total'])) {
                        $amount = (float)$o['grand_total'];
                    } elseif (!empty($o['total']) && is_numeric($o['total'])) {
                        $amount = (float)$o['total'];
                    }
                    $stats['orders_total'] += $amount;
                }
                
                $stats['pending_orders'] = count(array_filter($orders, fn($o) => in_array($o['status'] ?? '', ['Draft', 'To Receive and Bill'])));
                $stats['completed_orders'] = count(array_filter($orders, fn($o) => ($o['per_received'] ?? 0) == 100 && ($o['per_billed'] ?? 0) == 100));
            }

            // Factures
            $invoiceFilters = [['supplier', '=', $supplier_id]];
            if ($startDate && $endDate) {
                $invoiceFilters[] = ['posting_date', '>=', $startDate];
                $invoiceFilters[] = ['posting_date', '<=', $endDate];
            }

            $invoices = $this->erpApiService->getResource('Purchase Invoice', [
                'filters' => $invoiceFilters,
                'fields' => ['name', 'grand_total', 'net_total', 'total', 'status', 'posting_date'],
                'limit_page_length' => 1000
            ]);

            Log::info('Raw supplier invoices data', [
                'supplier_id' => $supplier_id,
                'count' => count($invoices),
                'data' => $invoices
            ]);

            if (is_array($invoices)) {
                $stats['invoices_count'] = count($invoices);
                
                // Correction pour les factures fournisseurs
                $stats['invoices_total'] = 0;
                foreach ($invoices as $i) {
                    $amount = 0;
                    if (!empty($i['net_total']) && is_numeric($i['net_total'])) {
                        $amount = (float)$i['net_total'];
                    } elseif (!empty($i['grand_total']) && is_numeric($i['grand_total'])) {
                        $amount = (float)$i['grand_total'];
                    } elseif (!empty($i['total']) && is_numeric($i['total'])) {
                        $amount = (float)$i['total'];
                    }
                    $stats['invoices_total'] += $amount;
                }
                
                $stats['unpaid_invoices'] = count(array_filter($invoices, fn($i) => in_array($i['status'] ?? '', ['Unpaid', 'Overdue'])));
            }

            Log::info('Supplier stats', [
                'supplier_id' => $supplier_id,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur calcul stats fournisseur', [
                'supplier_id' => $supplier_id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        return $stats;
    }

    private function prepareChartData($supplierData)
    {
        $top10Suppliers = array_slice($supplierData, 0, 10);

        $ordersBySupplier = array_map(fn($supplier) => [
            'name' => $supplier['name'] ?? 'Unknown Supplier',
            'devis' => (float) ($supplier['quotations_total'] ?? 0),
            'commandes' => (float) ($supplier['orders_total'] ?? 0),
            'commandes_completees' => (float) ($supplier['completed_orders'] ?? 0),
            'factures' => (float) ($supplier['invoices_total'] ?? 0)
        ], $top10Suppliers);

        $invoiceStatus = [
            ['name' => 'Payées', 'value' => (int) array_sum(array_column($supplierData, 'paid_invoices'))],
            ['name' => 'Impayées', 'value' => (int) array_sum(array_column($supplierData, 'unpaid_invoices'))]
        ];

        $ordersStatus = [
            ['name' => 'Complétées', 'value' => (int) array_sum(array_column($supplierData, 'completed_orders'))],
            ['name' => 'En cours', 'value' => (int) array_sum(array_column($supplierData, 'orders_count')) - (int) array_sum(array_column($supplierData, 'completed_orders'))]
        ];

        $chartData = [
            'orders_by_supplier' => $ordersBySupplier,
            'invoice_status' => $invoiceStatus,
            'orders_status' => $ordersStatus
        ];

        Log::info('Chart data prepared', ['chart_data' => $chartData]);

        return $chartData;
    }
}