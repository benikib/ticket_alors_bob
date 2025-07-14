<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MpesaService
{
    public function getSession()
    {
        $apiKey = env('MPESA_API_KEY'); // Exemple : KkTdgdQYsVghe3sH2H30WCsNZzJa926F
        $publicKeyPem = "-----BEGIN PUBLIC KEY-----\n" 
            . chunk_split(env('MPESA_PUBLIC_KEY'), 64, "\n") 
            . "-----END PUBLIC KEY-----";

        $publicKey = openssl_pkey_get_public($publicKeyPem);

        if (!$publicKey) {
            throw new \Exception("Impossible de charger la clé publique");
        }

        if (!openssl_public_encrypt($apiKey, $encryptedApiKey, $publicKey, OPENSSL_PKCS1_OAEP_PADDING)) {
            throw new \Exception("Le chiffrement de la clé API a échoué");
        }

        $bearerToken = base64_encode($encryptedApiKey);

        $url = env('MPESA_BASE_URL') . '/sandbox/ipg/v2/' . env('MPESA_MARKET') . '/getSession/';

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $bearerToken,
            'Origin' => '*'
        ])->withoutVerifying()->get($url);

        if (!$response->successful()) {
            throw new \Exception("Erreur M-Pesa : " . $response->body());
        }

        return json_decode($response->body(), true);
    }
}
