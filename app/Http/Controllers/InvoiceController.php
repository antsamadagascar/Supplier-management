<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class InvoiceController extends Controller
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

    public function getPaymentModes()
    {
        try {
            $response = $this->client->get("{$this->apiUrl}/api/resource/Mode of Payment", [
                'headers' => $this->headers,
                'query' => [
                    'fields' => '["name", "mode_name", "type"]',  
                    'limit' => 50
                ]
            ]);
            
            $paymentModes = json_decode($response->getBody(), true)['data'];
            return $paymentModes;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des modes de paiement: ' . $e->getMessage());
            return [];
        }
    }

    public function showPayInvoice($invoice_id)
    {
        try {
            // Récupérer les détails de la facture
            $response = $this->client->get("{$this->apiUrl}/api/resource/Purchase Invoice/{$invoice_id}", [
                'headers' => $this->headers,
            ]);
            $invoice = json_decode($response->getBody(), true)['data'];
            
            // S'assurer que grand_total est un nombre
            $invoice['grand_total'] = (float) $invoice['grand_total'];
            
            // Récupérer les modes de paiement réels depuis l'API
            $paymentModes = $this->getPaymentModes();
            
            // Si aucun mode de paiement n'est récupéré, utilisez des valeurs par défaut
            if (empty($paymentModes)) {
                Log::warning('Aucun mode de paiement récupéré, utilisation des valeurs par défaut');
                $paymentModes = [
                    ['name' => 'Cash', 'mode_name' => 'Espèces'],
                    ['name' => 'Bank Draft', 'mode_name' => 'Virement bancaire'],
                    ['name' => 'Check', 'mode_name' => 'Chèque'],
                ];
            }
            
            // Vérifier si les modes de paiement sont bien formatés
            Log::info('Modes de paiement récupérés: ' . json_encode($paymentModes));
            
            // Récupérer l'historique des paiements pour cette facture
            $paymentHistory = $this->getInvoicePaymentHistory($invoice_id);
            $totalPaid = $this->calculateTotalPaid($paymentHistory);
            $remainingAmount = $invoice['grand_total'] - $totalPaid;
            
            return view('invoices.pay', [
                'invoice' => $invoice,
                'supplier_id' => $invoice['supplier'] ?? null,
                'paymentModes' => $paymentModes,
                'paymentHistory' => $paymentHistory,
                'totalPaid' => $totalPaid,
                'remainingAmount' => $remainingAmount
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'affichage du formulaire de paiement: ' . $e->getMessage());
            return back()->with('error', 'Erreur API Payment: ' . $e->getMessage());
        }
    }

    public function getInvoicePaymentHistory($invoice_id)
    {
        try {
            $response = $this->client->get("{$this->apiUrl}/api/resource/Payment Entry", [
                'headers' => $this->headers,
                'query' => [
                    'filters' => json_encode([
                        ["Payment Entry Reference", "reference_name", "=", $invoice_id]
                    ]),
                    'fields' => '["name", "posting_date", "paid_amount", "mode_of_payment", "reference_no", "reference_date"]'
                ]
            ]);
            
            return json_decode($response->getBody(), true)['data'] ?? [];
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération de l\'historique des paiements: ' . $e->getMessage());
            return [];
        }
    }

    public function calculateTotalPaid($paymentHistory)
    {
        $total = 0.0;
        foreach ($paymentHistory as $payment) {
            $amount = floatval($payment['paid_amount']);
            $total += $amount;
        }
        return $total;
    }

    public function payInvoice(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required',
            'supplier' => 'required',
            'paid_amount' => 'required|numeric|min:0',
            'payment_mode' => 'required',
            'payment_date' => 'required|date',
            'reference_no' => 'required',
            'reference_date' => 'required|date',
        ]);
    
        try {
            $invoiceResponse = $this->client->get("{$this->apiUrl}/api/resource/Purchase Invoice/{$request->invoice_id}", [
                'headers' => $this->headers,
            ]);
            $invoiceData = json_decode($invoiceResponse->getBody(), true)['data'];

            if ((int) $invoiceData['docstatus'] === 2) {
                return back()->with('error', 'Impossible de créer un paiement pour une facture annulée.');
            }

            if (empty($invoiceData['company'])) {
                throw new \Exception("La facture ne contient pas d'information sur la compagnie.");
            }

            if (empty($invoiceData['company'])) {
                throw new \Exception("La facture ne contient pas d'information sur la compagnie.");
            }
    
            if (empty($invoiceData['credit_to'])) {
                throw new \Exception("Le compte fournisseur (credit_to) est manquant dans la facture.");
            }
            

            $accountResponse = $this->client->get("{$this->apiUrl}/api/resource/Account", [
                'headers' => $this->headers,
                'query' => [
                    'filters' => json_encode([
                        ["account_type", "in", ["Bank", "Cash"]],
                        ["company", "=", $invoiceData['company']],
                        ["is_group", "=", 0]
                    ]),
                    'limit' => 1,
                    'fields' => '["name", "account_currency"]'
                ]
            ]);
            
            $accountData = json_decode($accountResponse->getBody(), true);
            
            if (empty($accountData['data'])) {
                throw new \Exception("Aucun compte bancaire ou de trésorerie trouvé pour la compagnie {$invoiceData['company']}.");
            }
            
            $bankAccount = $accountData['data'][0];
            $bankAccountName = $bankAccount['name'];
            $bankAccountCurrency = $bankAccount['account_currency'] ?? $invoiceData['currency'] ?? 'EUR';
    
            // Récupérer l'historique des paiements pour vérifier si le montant est valide
            $paymentHistory = $this->getInvoicePaymentHistory($request->invoice_id);
            $totalPaid = $this->calculateTotalPaid($paymentHistory);
            // S'assurer que les valeurs sont bien des nombres
            $grandTotal = (float) $invoiceData['grand_total'];
            $remainingAmount = $grandTotal - $totalPaid;
    
            if ($request->paid_amount > $remainingAmount) {
                return back()->with('error', 'Le montant payé ne peut pas dépasser le solde restant de ' . number_format($remainingAmount, 2, ',', ' ') . ' ' . ($invoiceData['currency'] ?? 'EUR'));
            }
            
            $paidAmount = (float) $request->paid_amount;
            
            // La devise de la facture
            $currency = $invoiceData['currency'] ?? 'EUR';
            
            $paymentData = [
                'doctype' => 'Payment Entry',
                'payment_type' => 'Pay',
                'party_type' => 'Supplier',
                'party' => $request->supplier,
                'paid_amount' => $paidAmount,
                'received_amount' => $paidAmount,
                'source_exchange_rate' => 1.0,
                'target_exchange_rate' => 1.0,
                'posting_date' => $request->payment_date,
                'company' => $invoiceData['company'],
                'mode_of_payment' => $request->payment_mode,
                'paid_from' => $bankAccountName, // Compte bancaire ou de trésorerie
                'paid_from_account_currency' => $bankAccountCurrency, // Devise du compte source
                'paid_to' => $invoiceData['credit_to'], // Compte fournisseur
                'paid_to_account_currency' => $currency, // Devise du compte fournisseur
                'references' => [
                    [
                        'reference_doctype' => 'Purchase Invoice',
                        'reference_name' => $request->invoice_id,
                        'allocated_amount' => $paidAmount
                    ]
                    ],
                // Ajouter le docstatus 1 pour soumettre directement
                'docstatus'=> 1
            ];
    
            // Ajouter des champs de référence si fournis
            if ($request->has('reference_no') && !empty($request->reference_no)) {
                $paymentData['reference_no'] = $request->reference_no;
            } else {
                // Générer un numéro de référence par défaut si non fourni (requis pour les transactions bancaires)
                $paymentData['reference_no'] = 'REF-' . date('YmdHis') . '-' . substr(uniqid(), -5);
            }
            
            if ($request->has('reference_date') && !empty($request->reference_date)) {
                $paymentData['reference_date'] = $request->reference_date;
            } else {
                $paymentData['reference_date'] = $request->payment_date;
            }
    
            Log::info('Données de paiement envoyées: ' . json_encode($paymentData));
    
            // Création du paiement avec docstatus 1 (directement soumis)
            $response = $this->client->post("{$this->apiUrl}/api/resource/Payment Entry", [
                'headers' => $this->headers,
                'json' => $paymentData
            ]);
    
            $paymentResult = json_decode($response->getBody(), true);
            $paymentName = $paymentResult['data']['name'] ?? null;
    
            if (!$paymentName) {
                throw new \Exception("Erreur lors de la création du paiement: Identifiant non trouvé dans la réponse.");
            }
    
            // Si l'approche avec docstatus 1 ne fonctionne pas, essayons une autre méthode de soumission
            if (!isset($paymentResult['data']['docstatus']) || $paymentResult['data']['docstatus'] != 1) {
                // Soumettre le paiement pour le valider
                $submitResponse = $this->client->post("{$this->apiUrl}/api/method/frappe.client.submit", [
                    'headers' => $this->headers,
                    'json' => [
                        'doc' => [
                            'doctype' => 'Payment Entry',
                            'name' => $paymentName
                        ]
                    ]
                ]);
                
                Log::info('Réponse API Soumission alternative: ' . $submitResponse->getBody());
            }
    
            $successMessage = 'Paiement de ' . number_format($request->paid_amount, 2, ',', ' ') . ' ' . ($invoiceData['currency'] ?? 'EUR') . ' enregistré et validé avec succès.';
            
            // Si la facture est entièrement payée
            if (abs($request->paid_amount - $remainingAmount) < 0.01) {
                $successMessage .= ' La facture est maintenant entièrement payée.';
            } else {
                $newRemainingAmount = $remainingAmount - $request->paid_amount;
                $successMessage .= ' Il reste ' . number_format($newRemainingAmount, 2, ',', ' ') . ' ' . ($invoiceData['currency'] ?? 'EUR') . ' à payer.';
            }
    
            return redirect()->route('invoices.index')->with('success', $successMessage);
        } catch (\Exception $e) {
            Log::error('Erreur API Payment: ' . $e->getMessage());
    
            if (method_exists($e, 'getResponse') && $e->getResponse()) {
                Log::error('Réponse d\'erreur: ' . $e->getResponse()->getBody());
            }
    
            return back()->with('error', 'Erreur lors de l\'enregistrement du paiement: ' . $e->getMessage());
        }
    }
}