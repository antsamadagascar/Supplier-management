<?php

namespace App\Http\Controllers;

use App\Services\ErpApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Exception;

class ErpController extends Controller
{
    private ErpApiService $erpApiService;

    public function __construct(ErpApiService $erpApiService)
    {
        $this->erpApiService = $erpApiService;
    }

    public function suppliers()
    {
        try {
            $suppliers = $this->erpApiService->getResource('Supplier');
            return view('suppliers.index', compact('suppliers'));
        } catch (Exception $e) {
            Log::error('Erreur API Suppliers: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de la récupération des fournisseurs.');
        }
    }

    public function showSupplierDashboard(string $supplier_id)
    {
        $supplier_id = urldecode($supplier_id);
        
        try {
            $supplier = $this->erpApiService->getResource("Supplier/{$supplier_id}");
            
            $stats = $this->calculateSupplierStats($supplier_id);
            
            return view('suppliers.dashboard', compact('supplier', 'stats'));
        } catch (Exception $e) {
            Log::error('Erreur dashboard fournisseur', [
                'message' => $e->getMessage(),
                'supplier_id' => $supplier_id
            ]);
            return back()->with('error', 'Impossible de charger les informations du fournisseur.');
        }
    }

    private function calculateSupplierStats(string $supplier_id): array
    {
        $stats = [
            'quotations_count' => 0,
            'quotations_total' => 0,
            'pending_quotations' => 0,
            'orders_count' => 0,
            'orders_total' => 0,
            'pending_orders' => 0,
            'invoices_count' => 0,
            'invoices_total' => 0,
            'unpaid_invoices' => 0,
        ];

        try {
            $quotations = $this->erpApiService->getResource('Supplier Quotation', [
                'filters' => [['supplier', '=', $supplier_id]],
                'fields' => ['name', 'grand_total', 'net_total', 'status'],
                'limit_page_length' => 100,

                'fields' => [
                    'name', 'transaction_date', 'grand_total', 'net_total', 'currency',
                    'status', 'valid_till', 'company', 'contact_person',
                    'items.item_code', 'items.item_name', 'items.qty', 'items.rate',
                    'items.amount', 'taxes.description', 'taxes.tax_amount'
                ]

            ]);
        //    dd($quotations);
            Log::info('Quotations response', ['supplier_id' => $supplier_id, 'count' => count($quotations), 'data' => $quotations]);

            $stats['quotations_count'] = count($quotations);
            $stats['quotations_total'] = array_sum(array_column($quotations, 'net_total') ?: [0]);
            $stats['pending_quotations'] = count(array_filter($quotations, 
                fn($q) => in_array($q['status'] ?? '', ['Draft', 'Submitted'])));

            // Fetch orders
            $orders = $this->fetchSupplierOrders($supplier_id);
            Log::info('Orders response', ['supplier_id' => $supplier_id, 'count' => count($orders), 'data' => $orders]);

            $stats['orders_count'] = count($orders);
            $stats['orders_total'] = array_sum(array_column($orders, 'net_total') ?: [0]);
            $stats['pending_orders'] = count(array_filter($orders, 
                fn($o) => in_array($o['status'] ?? '', ['Draft', 'To Receive and Bill'])));

            // Fetch invoices
            $invoices = $this->erpApiService->getResource('Purchase Invoice', [
                'supplier' => $supplier_id,
                'fields' => ['name', 'grand_total', 'net_total', 'status'],
                'limit_page_length' => 100
            ]);
            Log::info('Invoices response', ['supplier_id' => $supplier_id, 'count' => count($invoices), 'data' => $invoices]);

            $stats['invoices_count'] = count($invoices);
            $stats['invoices_total'] = array_sum(array_column($invoices, 'net_total') ?: [0]);
            $stats['unpaid_invoices'] = count(array_filter($invoices, 
                fn($i) => in_array($i['status'] ?? '', ['Unpaid', 'Overdue'])));

        } catch (Exception $e) {
            Log::error('Erreur calcul stats fournisseur', [
                'message' => $e->getMessage(),
                'supplier_id' => $supplier_id,
                'trace' => $e->getTraceAsString()
            ]);
        }

        return $stats;
    }

    public function supplierQuotations(string $supplier_id)
    {
        try {
            $supplier = $this->erpApiService->getResource("Supplier/{$supplier_id}");
            $quotations = $this->erpApiService->getResource('Supplier Quotation', [
                'filters' => [['supplier', '=', $supplier_id]],
                'fields' => [
                    'name', 'transaction_date', 'grand_total', 'net_total', 'currency',
                    'status', 'valid_till', 'company', 'contact_person',
                    'items.item_code', 'items.item_name', 'items.qty', 'items.rate',
                    'items.amount', 'taxes.description', 'taxes.tax_amount'
                ]
            ]);

            $quotations = $this->normalizeQuotationItems($quotations);
            
            return view('suppliers.quotations', compact('quotations', 'supplier'));
        } catch (Exception $e) {
            Log::error('Erreur API Supplier Quotations: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de la récupération des demandes de devis.');
        }
    }

    private function normalizeQuotationItems(array $quotations): array
    {
        return array_map(function ($quotation) {
            if (empty($quotation['items']) && isset($quotation['item_code'])) {
                $quotation['items'] = [[
                    'item_code' => $quotation['item_code'] ?? 'N/A',
                    'item_name' => $quotation['item_name'] ?? 'N/A',
                    'qty' => $quotation['qty'] ?? 0,
                    'rate' => $quotation['rate'] ?? 0,
                    'amount' => $quotation['amount'] ?? 0
                ]];
            }
            
            $quotation['items'] = $quotation['items'] ?? [];
            $quotation['taxes'] = $quotation['taxes'] ?? [];
            
            return $quotation;
        }, $quotations);
    }
    public function quotations(string $supplier_id)
{
    try {
        $supplier = $this->erpApiService->getResource("Supplier/{$supplier_id}");
        $quotations = $this->erpApiService->getList("Supplier Quotation", ["supplier" => $supplier_id]);
        
        // Loguer les données brutes
        Log::info('Données des devis pour fournisseur ' . $supplier_id, ['quotations' => $quotations]);

        // Vérifier si $quotations est bien formé
        if (!isset($quotations['data']) || !is_array($quotations['data'])) {
            Log::warning('Données des devis mal formées', ['quotations' => $quotations]);
            $quotations = ['data' => []];
        }

        // Vérifier les doublons
        $quotationNames = array_column($quotations['data'], 'name');
        if (count($quotationNames) !== count(array_unique($quotationNames))) {
            Log::warning('Doublons détectés dans les devis', ['names' => $quotationNames]);
        }

        return view('suppliers.quotations', [
            'supplier' => $supplier,
            'quotations' => $quotations['data'],
        ]);
    } catch (Exception $e) {
        Log::error('Erreur API Quotations: ' . $e->getMessage(), ['supplier_id' => $supplier_id]);
        return back()->with('error', 'Erreur lors de la récupération des devis.');
    }
}
    public function quotationItems(string $supplier_id, string $quotation_id)
    {
        try {
            $supplier = $this->erpApiService->getResource("Supplier/{$supplier_id}");
            $quotation = $this->erpApiService->getResource("Supplier Quotation/{$quotation_id}");
            
            $items = $quotation['items'] ?? [];
            $quotation_currency = $quotation['currency'] ?? 'XOF';

            return view('suppliers.quotation_items', compact(
                'supplier', 'quotation', 'quotation_id', 'supplier_id', 
                'items', 'quotation_currency'
            ));
        } catch (Exception $e) {
            Log::error('Erreur API Quotation Items: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de la récupération des éléments du devis.');
        }
    }

    public function updateQuotation(Request $request, string $supplier_id, string $quotation_id)
    {
        try {
            $items= $request->input('items', []);
            if (empty($items) || !is_array($items)) {
                return back()->with('error', 'Données de mise à jour invalides.');
            }

            $quotation = $this->erpApiService->getResource("Supplier Quotation/{$quotation_id}");
            if ($quotation['status'] !== 'Draft') {
                return back()->with('error', 'Le devis doit être en état "Draft" pour pouvoir modifier les prix.');
            }

            $updatedItems = [];
            $errors = [];

            foreach ($items as $index => $itemData) {
                $item_id = $itemData['item_row'] ?? null;
                $new_rate = (float) ($itemData['new_rate'] ?? 0);

                if (!$item_id || $new_rate <= 0) {
                    $errors[] = $item_id ? 
                        "Le prix pour l'article {$item_id} doit être supérieur à 0." :
                        "Données manquantes pour l'article à l'index $index";
                    continue;
                }

                try {
                    $itemDetails = $this->erpApiService->getResource("Supplier Quotation Item/{$item_id}");
                    $item_code = $itemDetails['item_code'] ?? null;

                    if (!$item_code || !$this->erpApiService->resourceExists("Item/{$item_code}")) {
                        $errors[] = $item_code ? 
                            "L'article avec le code {$item_code} n'existe pas." :
                            "Code article introuvable pour l'article {$item_id}.";
                        continue;
                    }

                    $this->erpApiService->updateResource("Supplier Quotation Item/{$item_id}", [
                        'rate' => $new_rate,
                        'item_code' => $item_code
                    ]);

                    $updatedItems[] = $item_id;
                } catch (Exception $e) {
                    $errors[] = "Erreur lors de la mise à jour de l'article {$item_id}: " . $e->getMessage();
                    Log::error("Erreur mise à jour article", [
                        'item_id' => $item_id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            if (!empty($errors)) {
                return back()->with('error', implode('<br>', $errors));
            }

            if (empty($updatedItems)) {
                return back()->with('error', 'Aucun article n\'a été mis à jour.');
            }

            return redirect()
                ->route('supplier.quotation.items', ['supplier_id' => $supplier_id, 'quotation_id' => $quotation_id])
                ->with('success', count($updatedItems) > 1
                    ? 'Les prix de ' . count($updatedItems) . ' articles ont été mis à jour avec succès.'
                    : 'Le prix a été mis à jour avec succès.');

        } catch (Exception $e) {
            Log::error('Erreur API Update Quotation: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de la mise à jour des prix.');
        }
    }
    

    public function supplierOrders(string $supplier_id)
    {
        try {
            $supplier = $this->erpApiService->getResource("Supplier/{$supplier_id}");
            $orders = $this->fetchSupplierOrders($supplier_id);

            if (empty($orders)) {
                Log::warning('Aucune commande trouvée pour le fournisseur', ['supplier_id' => $supplier_id]);
                return view('suppliers.orders', [
                    'orders' => [],
                    'completedOrders' => [],
                    'pendingOrders' => [],
                    'supplier' => $supplier,
                    'message' => 'Aucune commande trouvée pour ce fournisseur.'
                ]);
            }

            $completedOrders = array_filter($orders, 
                fn($order) => ($order['per_received'] ?? 0) == 100 && ($order['per_billed'] ?? 0) == 100);
            $pendingOrders = array_filter($orders, 
                fn($order) => !isset($order['per_received']) || $order['per_received'] != 100 || 
                              !isset($order['per_billed']) || $order['per_billed'] != 100);
            $orders = array_merge($completedOrders, $pendingOrders);

            Log::info('Commandes traitées', [
                'supplier_id' => $supplier_id,
                'total' => count($orders),
                'completed' => count($completedOrders),
                'pending' => count($pendingOrders),
                'order_names' => array_column($orders, 'name')
            ]);

            return view('suppliers.orders', compact('orders', 'supplier', 'completedOrders', 'pendingOrders'));
        } catch (Exception $e) {
            Log::error('Erreur API Supplier Orders', [
                'message' => $e->getMessage(),
                'supplier_id' => $supplier_id,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Erreur lors de la récupération des commandes: ' . $e->getMessage());
        }
    }

    private function fetchSupplierOrders(string $supplier_id): array
    {
        $strategies = [
            // Strategy 1: Direct supplier parameter
            function () use ($supplier_id) {
                $orders = $this->erpApiService->getResource('Purchase Order', [
                    'supplier' => $supplier_id,
                    'fields' => ['name', 'transaction_date', 'grand_total', 'net_total', 'status', 'per_received', 'per_billed', 'supplier'],
                    'limit_page_length' => 100
                ]);
                Log::info('Strategy 1 response', ['count' => count($orders), 'data' => $orders]);
                return $orders;
            },
            // Strategy 2: JSON filter
            function () use ($supplier_id) {
                $orders = $this->erpApiService->getResource('Purchase Order', [
                    'filters' => [['Purchase Order', 'supplier', '=', $supplier_id]],
                    'fields' => ['name', 'transaction_date', 'grand_total', 'net_total', 'status', 'per_received', 'per_billed', 'supplier'],
                    'limit_page_length' => 100
                ]);
                Log::info('Strategy 2 response', ['count' => count($orders), 'data' => $orders]);
                return $orders;
            },
            // Strategy 3: Fetch all and filter client-side
            function () use ($supplier_id) {
                $allOrders = $this->erpApiService->getResource('Purchase Order', [
                    'fields' => ['name', 'transaction_date', 'grand_total', 'net_total', 'status', 'per_received', 'per_billed', 'supplier'],
                    'limit_page_length' => 500
                ]);
                $filteredOrders = array_filter($allOrders, 
                    fn($order) => ($order['supplier'] ?? '') === $supplier_id);
                Log::info('Strategy 3 response', ['total' => count($allOrders), 'filtered' => count($filteredOrders), 'data' => $filteredOrders]);
                return $filteredOrders;
            }
        ];

        $allOrders = [];
        foreach ($strategies as $index => $strategy) {
            try {
                $orders = $strategy();
                foreach ($orders as $order) {
                    if (isset($order['name'])) {
                        // Log potential duplicates but include all records
                        if (isset($allOrders[$order['name']])) {
                            Log::warning('Potential duplicate order detected', [
                                'name' => $order['name'],
                                'existing' => $allOrders[$order['name']],
                                'new' => $order
                            ]);
                        }
                        $allOrders[$order['name']] = $order;
                    } else {
                        Log::warning('Skipping order with missing name', ['order' => $order]);
                    }
                }
                Log::info("Strategy {$index} succeeded", ['count' => count($orders)]);
            } catch (Exception $e) {
                Log::warning("Strategy {$index} failed", [
                    'message' => $e->getMessage(),
                    'supplier_id' => $supplier_id
                ]);
            }
        }

        // Convert to indexed array
        $uniqueOrders = array_values($allOrders);

        // Fetch detailed data for all orders to ensure completeness
        $detailedOrders = [];
        foreach ($uniqueOrders as $order) {
            try {
                $detailedOrder = $this->erpApiService->getResource("Purchase Order/{$order['name']}");
                Log::info('Fetched detailed order', ['name' => $order['name'], 'data' => $detailedOrder]);
                $detailedOrders[] = $detailedOrder;
            } catch (Exception $e) {
                Log::warning("Failed to fetch order details for {$order['name']}", ['message' => $e->getMessage()]);
                $detailedOrders[] = $order; 
            }
        }

        Log::info('Final orders', [
            'count' => count($detailedOrders),
            'names' => array_column($detailedOrders, 'name')
        ]);
        return $detailedOrders;
    }

    public function supplierAccounting(string $supplier_id)
    {
        try {
            $supplier = $this->erpApiService->getResource("Supplier/{$supplier_id}");
            
            $invoices = $this->erpApiService->getResource('Purchase Invoice', [
                'supplier' => $supplier_id,
                'fields' => [
                    'name', 'posting_date', 'grand_total', 'net_total', 'currency', 'status',
                    'paid_amount', 'items.item_code', 'items.item_name', 'items.qty',
                    'items.rate', 'items.amount', 'items.purchase_order'
                ],
                'limit_page_length' => 100
            ]);


          //       dd($invoices);
            $payments = $this->erpApiService->getResource('Payment Entry', [
                'party' => $supplier_id,
                'fields' => [
                    'name', 'posting_date', 'paid_amount', 'paid_from_account_currency',
                    'mode_of_payment', 'docstatus', 'references.reference_doctype',
                    'references.reference_name', 'references.allocated_amount'
                ],
                'limit_page_length' => 100
            ]);

            Log::info('Accounting data', [
                'supplier_id' => $supplier_id,
                'invoices_count' => count($invoices),
                'invoices' => array_column($invoices, 'name'),
                'payments_count' => count($payments),
                'payments' => array_column($payments, 'name')
            ]);

            if (empty($invoices) && empty($payments)) {
                return view('suppliers.accounting', [
                    'invoices' => [],
                    'payments' => [],
                    'supplier' => $supplier,
                    'message' => 'Aucune donnée comptable trouvée pour ce fournisseur.'
                ]);
            }

            $invoices = array_map(function ($invoice) {
                $invoice['items'] = $invoice['items'] ?? [];
                $invoice['net_total'] = $invoice['net_total'] ?? $invoice['grand_total'] ?? 0;
                $invoice['status'] = $invoice['status'] ?? 'Unknown';
                return $invoice;
            }, $invoices);

            $payments = array_map(function ($payment) {
                $payment['references'] = $payment['references'] ?? [];
                $payment['paid_amount'] = $payment['paid_amount'] ?? 0;
                return $payment;
            }, $payments);
         //   dd($payments);
            return view('suppliers.accounting', compact('invoices', 'payments', 'supplier'));
        } catch (Exception $e) {
            Log::error('Erreur API Supplier Accounting', [
                'message' => $e->getMessage(),
                'supplier_id' => $supplier_id,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Erreur lors de la récupération des données comptables: ' . $e->getMessage());
        }
    }
}