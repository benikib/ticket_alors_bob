<?php
namespace App\Http\Controllers\api;
use App\Http\Controllers\Controller;    // <--- import Controller de base Laravel

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Str;
use App\Models\Billet;
use App\Models\Tarif;
use App\Models\Type_billet;

class BilletController extends Controller
{
    
      public function store(Request $request)
      {
                        // Étape 1 : récupération directe des données (attention, sans validation !)
                $nomComplet = $request->nom_complet_client;
                $numeroClient = $request->numero_client;
                $typeBilletNom = $request->type_billet;
                $devise = $request->devise;
                $nombreReel = $request->nombre_reel;

                // Étape 2 : recherche des modèles associés
                $type_billet = Type_billet::where('nom_type_billet', $typeBilletNom)->first();
                $tarif = Tarif::where('type_billet_id', $type_billet->id)
                            ->where('devise', $devise)
                            ->first();

                if (!$tarif) {
                    return response()->json(['error' => 'Tarif introuvable'], 404);
                }

                try {
                    // Génération du code unique
                    $now = time(); 
                    $start1978 = strtotime('1978-01-01 00:00:00'); 
                    $secondsSince1978 = $now - $start1978;
                    $code = 'Ticket-' . $nomComplet . '-' . $secondsSince1978 . '-' . uniqid();
                    $code = Hash::make($code);

                    // Création du billet
                    $billet = Billet::create([
                        'nom_complet_client' => $nomComplet,
                        'numero_client'      => $numeroClient,
                        'numero_billet'      => $numeroClient, // à vérifier : d'où vient ce champ ?
                        'code_bilet'         => $code,
                        'occurance_billet'   => $nombreReel,
                        'nombre_reel'        => $nombreReel,
                        'type_billet_id'     => $type_billet->id,
                        'tarif_id'           => $tarif->id,
                        'moyen_achat'        => 'en_ligne'
                    ]);

                    return response()->json([
                        'success' => true,
                        'message' => 'Billet enregistré avec succès.',
                        'data' => $billet,
                    ]);

                } catch (\Exception $e) {
                    return response()->json(['error' => $e->getMessage()], 500);
                }
      }
}
