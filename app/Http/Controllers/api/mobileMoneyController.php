<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

use App\Models\Billet;
use App\Models\Tarif;
use App\Models\Type_billet;

class mobileMoneyController extends Controller
{
    public function sendPayment(Request $request)
    {
        try {
            // Recherche du type de billet
            $type_billet = Type_billet::where('nom_type_billet', $request->type_billet)->first();
            if (!$type_billet) {
                return response()->json([
                    'status' => false,
                    'message' => 'Type de billet introuvable.'
                ], 404);
            }
            if ($type_billet->quantite_disponible < $request->nombre_reel) {
                return response()->json([
                    'status' => false,
                    'message' => 'Quantité insuffisante de billets disponibles.'
                ], 400);
            }
    
            // Recherche du tarif selon la devise
            $tarif = Tarif::where('type_billet_id', $type_billet->id)
                        ->where('devise', $request->devise)
                        ->first();
    
            if (!$tarif) {
                return response()->json([
                    'status' => false,
                    'message' => 'Tarif introuvable pour cette devise.'
                ], 404);
            }
    
            // Données de la transaction
            $data = [
                'transactionReference' => 'TX-' . date('YmdHis') . '-' . rand(1000, 9999),
                'amount'               => $tarif->prix * $request->nombre_reel,
                'currency'             => $request->devise,
                'customerFullName'     => $request->nom_complet_client,
                'customerEmailAdress'  => 'jean.dupont@example.com',
                'provider'             => $request->service,
                'walletID'             => $request->numero_client,
                'callbackUrl'          => 'https://tondomaine.com/mobile_callback',
            ];
    
            // Payload pour Maishapay
            $payload = [
                'transactionReference' => $data['transactionReference'],
                'gatewayMode' => "0",
                'publicApiKey' => env('MAISHAPAY_PUBLIC_KEY_TEST'),
                'secretApiKey' => env('MAISHAPAY_SECRET_KEY_TEST'),
                'order' => [
                    'amount' => $data['amount'],
                    'currency' => $data['currency'],
                    'customerFullName' => $data['customerFullName'],
                    'customerEmailAdress' => $data['customerEmailAdress'],
                ],
                'paymentChannel' => [
                    'channel' => 'MOBILEMONEY',
                    'provider' => $data['provider'],
                    'walletID' => $data['walletID'],
                    'callbackUrl' => $data['callbackUrl'],
                ],
            ];
    
            // Envoi de la requête
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->withOptions([
                'verify' => false,
            ])->post(env('MOBILE_MONEY_URL'), $payload);
    
            if ($response->successful()) {
                // Génération du code billet sécurisé
                $now = time();
                $start1978 = strtotime('1978-01-01 00:00:00');
                $secondsSince1978 = $now - $start1978;
                $raw_code = 'Ticket-' . $request->nom_complet_client . '-' . $secondsSince1978 . '-' . uniqid();
                $code = Hash::make($raw_code);
    
                // Création du billet
                $billet = Billet::create([
                    'nom_complet_client' => $request->nom_complet_client,
                    'numero_client'      => $request->numero_client,
                    'code_bilet'         => $code,
                    'occurance_billet'   => $request->nombre_reel,
                    'nombre_reel'        => $request->nombre_reel,
                    'type_billet_id'     => $type_billet->id,
                    'tarif_id'           => $tarif->id,
                    'moyen_achat'        => 'en_ligne'
                ]);
                $type_billet->quantite_disponible -= $request->nombre_reel;
                $type_billet->save();
    
                return response()->json([
                    'status' => true,
                    'message' => 'Paiement envoyé avec succès.',
                    'data' => $response->json(),
                    'billet' => $billet
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Erreur lors de l’appel à Maishapay.',
                    'error' => $response->body(),
                ], $response->status());
            }
        } catch (\Throwable $e) {
            Log::error("Erreur Maishapay: " . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Une erreur inattendue est survenue.',
                'exception' => $e->getMessage(),
            ], 500);
        }
    }
    
    
}
