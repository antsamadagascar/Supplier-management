<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class ErpController extends Controller
{
    protected $client;
    protected $apiUrl;
    protected $headers;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiUrl = env('ERP_API_URL', 'http://erpnext.localhost:8000');
        
        $sid = Session::get('frappe_sid');
        
        if (!$sid) {
            $this->headers = [
                'Authorization' => 'token ' . env('ERP_API_KEY') . ':' . env('ERP_API_SECRET'),
                'Accept' => 'application/json',
            ];
        } else {
            $this->headers = [
                'Cookie' => 'sid=' . $sid,
                'Accept' => 'application/json',
            ];
        }
    }

    // Liste des fournisseurs
    public function suppliers()
    {
        try {
            $response = $this->client->get("{$this->apiUrl}/api/resource/Supplier", [
                'headers' => $this->headers,
            ]);
            $suppliers = json_decode($response->getBody(), true)['data'];
            return view('suppliers.index', compact('suppliers'));
        } catch (\Exception $e) {
            Log::error('Erreur API Suppliers: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de la récupération des fournisseurs.');
        }
    }

    public function showSupplierDashboard($supplier_id)
    {
        $supplier_id = urldecode($supplier_id);
        Log::info('Début du tableau de bord fournisseur', ['supplier_id' => $supplier_id]);

            try {
                // Fetch supplier details
            Log::info('Récupération des détails du fournisseur', ['url' => "{$this->apiUrl}/api/resource/Supplier/{$supplier_id}"]);
            $response = $this->client->get("{$this->apiUrl}/api/resource/Supplier/{$supplier_id}", [
                'headers' => $this->headers,
            ]);
            $supplier = json_decode($response->getBody(), true)['data'];
            Log::info('Détails du fournisseur récupérés', ['supplier' => $supplier]);

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
                // Fetch quotations (aligned with supplierQuotations)
                Log::info('Récupération des devis', ['supplier_id' => $supplier_id]);
                $quotations = $this->client->get("{$this->apiUrl}/api/resource/Supplier Quotation", [
                    'headers' => $this->headers,
                    'query' => [
                        'filters' => json_encode([["supplier", "=", $supplier_id]]),
                        'fields' => json_encode(["name", "grand_total", "net_total", "status"]),
                        'limit_page_length' => 100,
                    ],
                ]);
                $quotationsData = json_decode($quotations->getBody(), true)['data'];
                Log::info('Données des devis', ['count' => count($quotationsData), 'data' => $quotationsData]);

                $stats['quotations_count'] = count($quotationsData);
                $stats['quotations_total'] = array_sum(array_column($quotationsData, 'net_total')); // Use net_total
                $stats['pending_quotations'] = count(array_filter($quotationsData, fn($q) => in_array($q['status'], ['Draft', 'Submitted'])));

                // Fetch orders (aligned with supplierOrders)
                Log::info('Récupération des commandes', ['supplier_id' => $supplier_id]);
                $orders = $this->client->get("{$this->apiUrl}/api/resource/Purchase Order", [
                    'headers' => $this->headers,
                    'query' => [
                        'supplier' => $supplier_id,
                        'fields' => json_encode(["name", "grand_total", "net_total", "status"]),
                        'limit_page_length' => 100,
                    ],
                ]);
                $ordersData = json_decode($orders->getBody(), true)['data'];
                Log::info('Données des commandes', ['count' => count($ordersData), 'data' => $ordersData]);

                $stats['orders_count'] = count($ordersData);
                $stats['orders_total'] = array_sum(array_column($ordersData, 'net_total')); // Use net_total
                $stats['pending_orders'] = count(array_filter($ordersData, fn($o) => in_array($o['status'], ['Draft', 'To Receive and Bill'])));

                // Fetch invoices (aligned with supplierAccounting)
                Log::info('Récupération des factures', ['supplier_id' => $supplier_id]);
                $invoices = $this->client->get("{$this->apiUrl}/api/resource/Purchase Invoice", [
                    'headers' => $this->headers,
                    'query' => [
                        'supplier' => $supplier_id,
                        'fields' => json_encode(["name", "grand_total", "net_total", "status"]),
                        'limit_page_length' => 100,
                    ],
                ]);
                $invoicesData = json_decode($invoices->getBody(), true)['data'];
                Log::info('Données des factures', ['count' => count($invoicesData), 'data' => $invoicesData]);

                $stats['invoices_count'] = count($invoicesData);
                $stats['invoices_total'] = array_sum(array_column($invoicesData, 'net_total')); // Use net_total
                $stats['unpaid_invoices'] = count(array_filter($invoicesData, fn($i) => in_array($i['status'], ['Unpaid', 'Overdue'])));

                Log::info('Statistiques calculées', ['stats' => $stats]);

                // // Dump and die to inspect data
                // dd([
                //     'supplier_id' => $supplier_id,
                //     'supplier' => $supplier,
                //     'quotations_data' => $quotationsData,
                //     'orders_data' => $ordersData,
                //     'invoices_data' => $invoicesData,
                //     'stats' => $stats,
                // ]);

            } catch (\Exception $e) {
                Log::error('Erreur lors de la récupération des statistiques fournisseur', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }

            return view('suppliers.dashboard', compact('supplier', 'stats'));
        } catch (\Exception $e) {
            Log::error('Erreur générale du tableau de bord fournisseur', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->with('error', 'Impossible de charger les informations du fournisseur.');
        }
    }
    // Liste des demandes de devis
    public function supplierQuotations($supplier_id)
    {
        try {
            // Log::info('Headers utilisés : ' . json_encode($this->headers));
            $supplierResponse = $this->client->get("{$this->apiUrl}/api/resource/Supplier/{$supplier_id}", [
                'headers' => $this->headers,
            ]);
            $supplier = json_decode($supplierResponse->getBody(), true)['data'];
    
            $quotationsResponse = $this->client->get("{$this->apiUrl}/api/resource/Supplier Quotation", [
                'headers' => $this->headers,
                'query' => [
                    'filters' => json_encode([["supplier", "=", $supplier_id]]),
                    'fields' => json_encode([
                        "name",
                        "transaction_date",
                        "grand_total",
                        "net_total",
                        "currency",
                        "status",
                        "valid_till",
                        "company",
                        "contact_person",
                        "items.item_code",
                        "items.item_name",
                        "items.qty",
                        "items.rate",
                        "items.amount",
                        "taxes.description",
                        "taxes.tax_amount"
                    ])
                ],
            ]);
            $quotations = json_decode($quotationsResponse->getBody(), true)['data'];
    
            foreach ($quotations as &$quotation) {
                if (empty($quotation['items']) && isset($quotation['item_code'])) {
                    $quotation['items'] = [
                        [
                            'item_code' => $quotation['item_code'] ?? 'N/A',
                            'item_name' => $quotation['item_name'] ?? 'N/A',
                            'qty' => $quotation['qty'] ?? 0,
                            'rate' => $quotation['rate'] ?? 0,
                            'amount' => $quotation['amount'] ?? 0
                        ]
                    ];
                }
                if (!isset($quotation['items']) || !is_array($quotation['items'])) {
                    $quotation['items'] = [];
                }
                if (!isset($quotation['taxes']) || !is_array($quotation['taxes'])) {
                    $quotation['taxes'] = [];
                }

             //   unset($quotation['item_code'], $quotation['item_name'], $quotation['qty'], $quotation['rate'], $quotation['amount'], $quotation['description'], $quotation['tax_amount']);
            }
                    // dd([
                    //     'supplier_id' => $supplier_id,
                    //     'supplier' => $supplier,
                    //     'quotations' => $quotations
                    // ]);
        
            // Retourner la vue (ne sera pas atteint à cause de dd())
            return view('suppliers.quotations', compact('quotations', 'supplier'));
        } catch (\Exception $e) {
            Log::error('Erreur API Supplier Quotations: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de la récupération des demandes de devis: ' . $e->getMessage());
        }
    }


    // Afficher les éléments d'un devis
    public function quotationItems($supplier_id, $quotation_id)
    {
        try {
            $supplierResponse = $this->client->get("{$this->apiUrl}/api/resource/Supplier/{$supplier_id}", [
                'headers' => $this->headers,
            ]);
            $supplier = json_decode($supplierResponse->getBody(), true)['data'];
    
            $quotationResponse = $this->client->get("{$this->apiUrl}/api/resource/Supplier Quotation/{$quotation_id}", [
                'headers' => $this->headers,
            ]);
            $quotation = json_decode($quotationResponse->getBody(), true)['data'];
            $items = $quotation['items'];
            $quotation_currency = $quotation['currency'] ?? 'XOF';
    
            return view('suppliers.quotation_items', compact(
                'supplier', 'quotation', 'quotation_id', 'supplier_id', 'items', 'quotation_currency'
            ));
        } catch (\Exception $e) {
            Log::error('Erreur API Quotation Items: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de la récupération des éléments du devis.');
        }
    }
    public function updateQuotation(Request $request, $supplier_id, $quotation_id)
    {
        try {
            Log::info('Received data for updateQuotation', [
                'request_data' => $request->all(),
                'supplier_id' => $supplier_id,
                'quotation_id' => $quotation_id
            ]);
    
            if (!$request->has('items') || !is_array($request->input('items'))) {
                return back()->with('error', 'Données de mise à jour invalides.');
            }
    
            Log::info('Données des articles envoyées', ['items' => $request->input('items')]);
    
            // Vérifier l'état du devis
            $quotationResponse = $this->client->get("{$this->apiUrl}/api/resource/Supplier Quotation/{$quotation_id}", [
                'headers' => $this->headers,
            ]);
    
            $quotationData = json_decode($quotationResponse->getBody(), true)['data'];
    
            if ($quotationData['status'] !== 'Draft') {
                return back()->with('error', 'Le devis doit être en état "Draft" pour pouvoir modifier les prix.');
            }
    
            $updatedItems = [];
            $errors = [];
    
            foreach ($request->input('items') as $index => $itemData) {
                if (!isset($itemData['item_row']) || !isset($itemData['new_rate'])) {
                    $errors[] = "Données manquantes pour l'article à l'index $index";
                    continue;
                }
    
                $item_id = $itemData['item_row'];
                $new_rate = (float) $itemData['new_rate'];
    
                if ($new_rate <= 0) {
                    $errors[] = "Le prix pour l'article {$item_id} doit être supérieur à 0.";
                    continue;
                }
    
                try {
                    // Étape 1 : Récupérer les détails de Supplier Quotation Item pour obtenir l'item_code
                    $itemResponse = $this->client->get("{$this->apiUrl}/api/resource/Supplier Quotation Item/{$item_id}", [
                        'headers' => $this->headers,
                    ]);
    
                    if ($itemResponse->getStatusCode() !== 200) {
                        $errors[] = "Impossible de récupérer les détails de l'article {$item_id}: " . $itemResponse->getBody()->getContents();
                        continue;
                    }
    
                    $itemDetails = json_decode($itemResponse->getBody(), true)['data'];
                    $item_code = $itemDetails['item_code'] ?? null;
    
                    if (!$item_code) {
                        $errors[] = "Code article introuvable pour l'article {$item_id}.";
                        continue;
                    }
    
                    // Étape 2 : Vérifier que l'item_code existe dans la table Item
                    $itemCheckResponse = $this->client->get("{$this->apiUrl}/api/resource/Item/{$item_code}", [
                        'headers' => $this->headers,
                    ]);
    
                    if ($itemCheckResponse->getStatusCode() !== 200) {
                        $errors[] = "L'article avec le code {$item_code} n'existe pas.";
                        continue;
                    }
    
                    // Étape 3 : Mettre à jour l'article avec rate et item_code
                    $headers = $this->headers;
                    $data = [
                        'rate' => $new_rate,
                        'item_code' => $item_code, // Inclure l'item_code dans le payload
                    ];
                    $url = "{$this->apiUrl}/api/resource/Supplier Quotation Item/{$item_id}";
    
                    $updateResponse = $this->client->put($url, [
                        'headers' => $headers,
                        'json' => $data
                    ]);
    
                    if ($updateResponse->getStatusCode() !== 200) {
                        $errorMessage = "Erreur lors de la mise à jour de l'article {$item_id}: " . $updateResponse->getBody()->getContents();
                        Log::error($errorMessage);
                        $errors[] = $errorMessage;
                        continue;
                    }
    
                    Log::info("Article mis à jour avec succès", [
                        'item_id' => $item_id,
                        'new_rate' => $new_rate,
                        'item_code' => $item_code
                    ]);
    
                    $updatedItems[] = $item_id;
    
                } catch (\Exception $itemEx) {
                    $errors[] = "Erreur lors de la mise à jour de l'article {$item_id}: " . $itemEx->getMessage();
                    Log::error("Erreur mise à jour article", [
                        'item_id' => $item_id,
                        'error' => $itemEx->getMessage()
                    ]);
                }
            }
    
            if (!empty($errors)) {
                return back()->with('error', implode('<br>', $errors));
            }
    
            if (empty($updatedItems)) {
                return back()->with('error', 'Aucun article n\'a été mis à jour.');
            }
    
            return redirect()->route('supplier.quotation.items', ['supplier_id' => $supplier_id, 'quotation_id' => $quotation_id])
                ->with('success', count($updatedItems) > 1
                    ? 'Les prix de ' . count($updatedItems) . ' articles ont été mis à jour avec succès.'
                    : 'Le prix a été mis à jour avec succès.');
    
        } catch (\Exception $e) {
            Log::error('Erreur API Update Quotation', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
                'quotation_id' => $quotation_id
            ]);
    
            return back()->with('error', 'Erreur lors de la mise à jour des prix: ' . $e->getMessage());
        }
    }
    public function supplierOrders($supplier_id)
    {
        try {
            // Récupération des informations du fournisseur
            $supplierResponse = $this->client->get("{$this->apiUrl}/api/resource/Supplier/{$supplier_id}", [
                'headers' => $this->headers,
            ]);
            $supplier = json_decode($supplierResponse->getBody(), true)['data'];
            
            // Log du fournisseur récupéré
            Log::info('Fournisseur récupéré: ' . json_encode($supplier));
            
            // APPROCHE 1: Essayer d'abord avec 'supplier' comme paramètre direct
            try {
                $ordersListResponse1 = $this->client->get("{$this->apiUrl}/api/resource/Purchase Order", [
                    'headers' => $this->headers,
                    'query' => [
                        'supplier' => $supplier_id,
                        'limit' => 100
                    ],
                ]);
                
                $ordersList1 = json_decode($ordersListResponse1->getBody(), true);
                Log::info('Résultat approche 1: ' . json_encode($ordersList1));
                
                if (isset($ordersList1['data']) && is_array($ordersList1['data']) && count($ordersList1['data']) > 1) {
                    $ordersList = $ordersList1['data'];
                    Log::info('Utilisation de l\'approche 1 - Nombre de commandes: ' . count($ordersList));
                } else {
                    // Si moins de 2 commandes, essayons l'approche 2
                    throw new \Exception("Pas assez de commandes avec l'approche 1");
                }
            } catch (\Exception $e1) {
                Log::info('Approche 1 a échoué, essai de l\'approche 2: ' . $e1->getMessage());
                
                // APPROCHE 2: Utiliser le format de filtre JSON
                $ordersListResponse2 = $this->client->get("{$this->apiUrl}/api/resource/Purchase Order", [
                    'headers' => $this->headers,
                    'query' => [
                        'filters' => json_encode([["Purchase Order", "supplier", "=", $supplier_id]]),
                        'limit' => 100
                    ],
                ]);
                
                $ordersList2 = json_decode($ordersListResponse2->getBody(), true);
                Log::info('Résultat approche 2: ' . json_encode($ordersList2));
                
                if (isset($ordersList2['data']) && is_array($ordersList2['data']) && count($ordersList2['data']) > 1) {
                    $ordersList = $ordersList2['data'];
                    Log::info('Utilisation de l\'approche 2 - Nombre de commandes: ' . count($ordersList));
                } else {
                    // APPROCHE 3: Récupérer toutes les commandes et filtrer côté client
                    Log::info('Approches 1 et 2 ont échoué, essai de l\'approche 3');
                    $allOrdersResponse = $this->client->get("{$this->apiUrl}/api/resource/Purchase Order", [
                        'headers' => $this->headers,
                        'query' => [
                            'limit' => 500  // Augmenter la limite pour récupérer plus de commandes
                        ],
                    ]);
                    
                    $allOrdersData = json_decode($allOrdersResponse->getBody(), true);
                    
                    // Journaliser le nombre total de commandes avant filtrage
                    if (isset($allOrdersData['data'])) {
                        Log::info('Toutes les commandes récupérées avant filtrage: ' . count($allOrdersData['data']));
                    }
                    
                    // Récupérer toutes les commandes
                    $allOrdersDetail = [];
                    
                    if (isset($allOrdersData['data']) && is_array($allOrdersData['data'])) {
                        foreach ($allOrdersData['data'] as $order) {
                            try {
                                $orderDetailResponse = $this->client->get("{$this->apiUrl}/api/resource/Purchase Order/{$order['name']}", [
                                    'headers' => $this->headers,
                                ]);
                                
                                $orderDetail = json_decode($orderDetailResponse->getBody(), true)['data'];
                                
                                // Filtrer les commandes du fournisseur demandé
                                if (isset($orderDetail['supplier']) && $orderDetail['supplier'] == $supplier_id) {
                                    $allOrdersDetail[] = $orderDetail;
                                    Log::info('Commande trouvée pour le fournisseur ' . $supplier_id . ': ' . $orderDetail['name']);
                                }
                            } catch (\Exception $e) {
                                Log::warning('Erreur lors de la récupération des détails de la commande ' . $order['name'] . ': ' . $e->getMessage());
                            }
                        }
                    }
                    
                    $ordersList = $allOrdersDetail;
                    Log::info('Utilisation de l\'approche 3 - Nombre de commandes après filtrage: ' . count($ordersList));
                }
            }
            
            // Si on a pas de commandes à ce stade, logger une erreur
            if (empty($ordersList)) {
                Log::error('Aucune commande trouvée pour le fournisseur ' . $supplier_id . ' après toutes les tentatives.');
                return view('suppliers.orders', [
                    'orders' => [],
                    'completedOrders' => [],
                    'pendingOrders' => [],
                    'supplier' => $supplier
                ]);
            }
            
            // Récupération des détails complets pour chaque commande
            $allOrders = [];
            
            foreach ($ordersList as $orderSummary) {
                try {
                    // Vérifier si on a déjà les détails complets
                    if (isset($orderSummary['transaction_date']) && isset($orderSummary['status'])) {
                        $allOrders[] = $orderSummary;
                    } else {
                        $orderDetailResponse = $this->client->get("{$this->apiUrl}/api/resource/Purchase Order/{$orderSummary['name']}", [
                            'headers' => $this->headers,
                        ]);
                        
                        $orderDetail = json_decode($orderDetailResponse->getBody(), true)['data'];
                        $allOrders[] = $orderDetail;
                    }
                } catch (\Exception $e) {
                    Log::warning('Erreur lors de la récupération des détails de la commande ' . $orderSummary['name'] . ': ' . $e->getMessage());
                }
            }
            
            // Séparation des commandes en deux catégories
            $completedOrders = [];
            $pendingOrders = [];
            
            foreach ($allOrders as $order) {
                // Considère une commande comme complétée si elle est reçue et payée à 100%
                if (isset($order['per_received']) && $order['per_received'] == 100 && 
                    isset($order['per_billed']) && $order['per_billed'] == 100) {
                    $completedOrders[] = $order;
                } else {
                    $pendingOrders[] = $order;
                }
            }
            
            // Fusionne les commandes en mettant les complètes en premier
            $orders = array_merge($completedOrders, $pendingOrders);
            
            // Journalisation du résultat final
            Log::info('Nombre total de commandes récupérées: ' . count($orders));
            foreach ($orders as $index => $order) {
                Log::info("Commande {$index}: {$order['name']} - Fournisseur: {$order['supplier']}");
            }
            
            return view('suppliers.orders', compact('orders', 'supplier', 'completedOrders', 'pendingOrders'));
        } catch (\Exception $e) {
            Log::error('Erreur API Supplier Orders: ' . $e->getMessage());
            Log::error('Trace: ' . $e->getTraceAsString());
            
            return back()->with('error', 'Erreur lors de la récupération des commandes: ' . $e->getMessage());
        }
    }

    public function supplierAccounting($supplier_id)
    {
        try {
            // Get supplier information
            $supplierResponse = $this->client->get("{$this->apiUrl}/api/resource/Supplier/{$supplier_id}", [
                'headers' => $this->headers,
            ]);
            $supplier = json_decode($supplierResponse->getBody(), true)['data'];
            
            // Get invoices with nested items fields, including purchase_order
            $invoicesResponse = $this->client->get("{$this->apiUrl}/api/resource/Purchase Invoice", [
                'headers' => $this->headers,
                'query' => [
                    'supplier' => $supplier_id,
                    'fields' => '["name", "posting_date", "grand_total", "currency", "status", "paid_amount", "items.item_code", "items.item_name", "items.qty", "items.rate", "items.amount", "items.purchase_order"]'
                ],
            ]);
            $invoices = json_decode($invoicesResponse->getBody(), true)['data'];
            
            // Get payments
            $paymentsResponse = $this->client->get("{$this->apiUrl}/api/resource/Payment Entry", [
                'headers' => $this->headers,
                'query' => [
                    'party' => $supplier_id,
                    'fields' => '["name", "posting_date", "references", "paid_amount", "paid_from_account_currency", "mode_of_payment", "docstatus"]'
                ],
            ]);
            $payments = json_decode($paymentsResponse->getBody(), true)['data'];
            
            // ENLEVER CE DUMP QUI STOPPE L'EXÉCUTION
            // dd([
            //     'supplier' => $supplier,
            //     'invoices' => $invoices,
            //     'payments' => $payments
            // ]);
            
            // Process invoices to ensure items is an array
            foreach ($invoices as &$invoice) {
                if (!isset($invoice['items']) || !is_array($invoice['items'])) {
                    $invoice['items'] = [];
                }
            }
            
            // Process payments to ensure references is an array
            foreach ($payments as &$payment) {
                if (!isset($payment['references']) || !is_array($payment['references'])) {
                    $payment['references'] = [];
                }
            }
            
            return view('suppliers.accounting', compact('invoices', 'payments', 'supplier'));
        } catch (\Exception $e) {
            Log::error('Erreur API Supplier Accounting: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de la récupération des données comptables: ' . $e->getMessage());
        }
    }

    // // Afficher le formulaire pour payer une facture
    // public function showPayInvoice($invoice_id)
    // {
    //     try {
    //         $response = $this->client->get("{$this->apiUrl}/api/resource/Purchase Invoice/{$invoice_id}", [
    //             'headers' => $this->headers,
    //         ]);
    //         $invoice = json_decode($response->getBody(), true)['data'];
    //         return view('invoices.pay', compact('invoice'));
    //     } catch (\Exception $e) {
    //         Log::error('Erreur API Invoice: ' . $e->getMessage());
    //         return back()->with('error', 'Erreur lors de la récupération de la facture.');
    //     }
    // }

    // // Payer une facture
    // public function payInvoice(Request $request)
    // {
    //     $request->validate([
    //         'invoice_id' => 'required',
    //         'supplier' => 'required',
    //         'paid_amount' => 'required|numeric|min:0',
    //     ]);

    //     try {
    //         $this->client->post("{$this->apiUrl}/api/resource/Payment Entry", [
    //             'headers' => $this->headers,
    //             'json' => [
    //                 'payment_type' => 'Pay',
    //                 'party_type' => 'Supplier',
    //                 'party' => $request->supplier,
    //                 'paid_amount' => $request->paid_amount,
    //                 'reference_doctype' => 'Purchase Invoice',
    //                 'reference_name' => $request->invoice_id,
    //                 'posting_date' => now()->format('Y-m-d'),
    //             ],
    //         ]);
    //         return redirect()->route('invoices.index')->with('success', 'Paiement enregistré avec succès.');
    //     } catch (\Exception $e) {
    //         Log::error('Erreur API Payment: ' . $e->getMessage());
    //         return back()->with('error', 'Erreur lors de l\'enregistrement du paiement.');
    //     }
    // }

}