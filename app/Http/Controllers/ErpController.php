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
            \Log::info('Received data:', $request->all());
    
            $quotationResponse = $this->client->get("{$this->apiUrl}/api/resource/Supplier Quotation/{$quotation_id}", [
                'headers' => $this->headers,
            ]);
            $quotation = json_decode($quotationResponse->getBody(), true)['data'];
            $items = $quotation['items'];
            
            $item_row = $request->input('item_row');
            $new_rate = (float) $request->input('new_rate');
            
            $found = false;
            foreach ($items as &$item) {
                if ($item['name'] === $item_row) {
                    $item['rate'] = $new_rate;
                    $item['amount'] = $new_rate * $item['qty'];
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                \Log::error('Item not found: ' . $item_row);
                return back()->with('error', 'Article non trouvé dans ce devis.');
            }
            
            $total = array_sum(array_column($items, 'amount'));
            
            $this->client->put("{$this->apiUrl}/api/resource/Supplier Quotation/{$quotation_id}", [
                'headers' => $this->headers,
                'json' => [
                    'items' => $items,
                    'total' => $total,
                    'net_total' => $total,
                    'base_total' => $total,
                    'base_net_total' => $total,
                    'grand_total' => $total,
                    'base_grand_total' => $total
                ]
            ]);
            
            return redirect()->route('supplier.quotation.items', ['supplier_id' => $supplier_id, 'quotation_id' => $quotation_id])
                ->with('success', 'Le prix a été mis à jour avec succès.');
        } catch (\Exception $e) {
            \Log::error('Erreur API Update Quotation: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de la mise à jour des prix: ' . $e->getMessage());
        }
    }
    public function supplierOrders($supplier_id)
    {
        try {
            $supplierResponse = $this->client->get("{$this->apiUrl}/api/resource/Supplier/{$supplier_id}", [
                'headers' => $this->headers,
            ]);
            $supplier = json_decode($supplierResponse->getBody(), true)['data'];

            $ordersResponse = $this->client->get("{$this->apiUrl}/api/resource/Purchase Order", [
                'headers' => $this->headers,
                'query' => ['supplier' => $supplier_id],  
            ]);
            $orders = json_decode($ordersResponse->getBody(), true)['data'];

            return view('suppliers.orders', compact('orders', 'supplier'));

        } catch (\Exception $e) {
            Log::error('Erreur API Supplier Orders: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de la récupération des commandes.');
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
            
            // Get invoices with nested items fields
            $invoicesResponse = $this->client->get("{$this->apiUrl}/api/resource/Purchase Invoice", [
                'headers' => $this->headers,
                'query' => [
                    'supplier' => $supplier_id,
                    'fields' => '["name", "posting_date", "grand_total", "currency", "status", "paid_amount", "items.item_code", "items.item_name", "items.qty", "items.rate", "items.amount"]'
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
    // // Afficher le formulaire pour mettre à jour le prix
    // public function showUpdatePrice($item_price_id)
    // {
    //     try {
    //         $response = $this->client->get("{$this->apiUrl}/api/resource/Item Price/{$item_price_id}", [
    //             'headers' => $this->headers,
    //         ]);
    //         $item_price = json_decode($response->getBody(), true)['data'];
    //         return view('prices.update', compact('item_price'));
    //     } catch (\Exception $e) {
    //         Log::error('Erreur API Item Price: ' . $e->getMessage());
    //         return back()->with('error', 'Erreur lors de la récupération du prix.');
    //     }
    // }

    // // Mettre à jour le prix
    // public function updatePrice(Request $request)
    // {
    //     $request->validate([
    //         'item_price_id' => 'required',
    //         'price_list_rate' => 'required|numeric|min:0',
    //     ]);

    //     try {
    //         $this->client->put("{$this->apiUrl}/api/resource/Item Price/{$request->item_price_id}", [
    //             'headers' => $this->headers,
    //             'json' => [
    //                 'price_list_rate' => $request->price_list_rate,
    //             ],
    //         ]);
    //         return redirect()->route('quotations.index')->with('success', 'Prix mis à jour avec succès.');
    //     } catch (\Exception $e) {
    //         Log::error('Erreur API Update Price: ' . $e->getMessage());
    //         return back()->with('error', 'Erreur lors de la mise à jour du prix.');
    //     }
    // }

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