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
        $response = $this->client->get("{$this->apiUrl}/api/resource/Purchase Invoice/{$invoice_id}", [
            'headers' => $this->headers,
        ]);
        $invoice = json_decode($response->getBody(), true)['data'];
        
        $paymentModes = [
            ['name' => 'Virement', 'mode_name' => 'Virement bancaire'],
            ['name' => 'Chèque', 'mode_name' => 'Chèque'],
            ['name' => 'Espèces', 'mode_name' => 'Espèces'],
        ];
        
        return view('invoices.pay', [
            'invoice' => $invoice,
            'supplier_id' => $invoice['supplier'] ?? null,
            'paymentModes' => $paymentModes,
        ]);
    } catch (\Exception $e) {
        return back()->with('error', 'Erreur API Payment: ' . $e->getMessage());
    }
}

public function payInvoice(Request $request)
{
    $request->validate([
        'invoice_id' => 'required',
        'supplier' => 'required',
        'paid_amount' => 'required|numeric|min:0',
        'payment_mode' => 'required',
        'payment_date' => 'required|date',
    ]);

    try {
        $invoiceResponse = $this->client->get("{$this->apiUrl}/api/resource/Purchase Invoice/{$request->invoice_id}", [
            'headers' => $this->headers,
        ]);
        $invoiceData = json_decode($invoiceResponse->getBody(), true)['data'];

        if (empty($invoiceData['company'])) {
            throw new \Exception("La facture ne contient pas d'information sur la compagnie.");
        }

        if (empty($invoiceData['credit_to'])) {
            throw new \Exception("Le compte fournisseur (credit_to) est manquant dans la facture.");
        }

        $paymentData = [
            'doctype' => 'Payment Entry',
            'payment_type' => 'Pay',
            'party_type' => 'Supplier',
            'party' => $request->supplier,
            'paid_amount' => $request->paid_amount,
            'received_amount' => $request->paid_amount,
            'source_exchange_rate' => 1,
            'target_exchange_rate' => 1,
            'posting_date' => $request->payment_date,
            'company' => $invoiceData['company'],
            'mode_of_payment' => $request->payment_mode,
            'paid_to' => $invoiceData['credit_to'],
            'references' => [
                [
                    'reference_doctype' => 'Purchase Invoice',
                    'reference_name' => $request->invoice_id,
                    'allocated_amount' => $request->paid_amount
                ]
            ]
        ];

        Log::info('Données de paiement envoyées: ' . json_encode($paymentData));

        $response = $this->client->post("{$this->apiUrl}/api/resource/Payment Entry", [
            'headers' => $this->headers,
            'json' => $paymentData
        ]);

        Log::info('Réponse API: ' . $response->getBody());

        return redirect()->route('invoices.index')->with('success', 'Paiement enregistré avec succès.');
    } catch (\Exception $e) {
        Log::error('Erreur API Payment: ' . $e->getMessage());

        if (method_exists($e, 'getResponse') && $e->getResponse()) {
            Log::error('Réponse d\'erreur: ' . $e->getResponse()->getBody());
        }

        return back()->with('error', 'Erreur lors de l\'enregistrement du paiement: ' . $e->getMessage());
    }
}

}
