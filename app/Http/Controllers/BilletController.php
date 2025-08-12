<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use Str;
use App\Models\Billet;
use App\Models\Tarif;
use App\Models\Type_billet;


use Illuminate\Http\Request;

class BilletController extends Controller
{

    public function index()
    {
     
        $billets = Billet::with(['typeBillet', 'tarif'])->get();
    
        $totalBillets = $billets->sum('nombre_reel');
    
        // Initialisation des stats
        $montantLigneUSD = $montantLigneCDF = 0;
        $montantGuichetUSD = $montantGuichetCDF = 0;
    
        $billetsParType = []; 
    
        foreach ($billets as $billet) {
            $occurence = $billet->nombre_reel;
            $montant = $occurence * ($billet->tarif->prix ?? 0);
            $devise = $billet->tarif->devise ?? null;
            $typeNom = $billet->typeBillet->nom_type_billet ?? 'Inconnu';
            $moyen = $billet->moyen_achat;
    
            // Init le type si pas encore fait
            if (!isset($billetsParType[$typeNom])) {
                $billetsParType[$typeNom] = [
                    'total' => 0,
                    'en_ligne' => 0,
                    'guichet' => 0,
                    'usd' => 0,
                    'cdf' => 0,
                ];
            }
    
            // Ajout du nombre de billets
            $billetsParType[$typeNom]['total'] += $occurence;
            $billetsParType[$typeNom][$moyen] += $occurence;
    
            // Ajout du montant
            if ($devise === 'usd') {
                if ($moyen === 'en_ligne') {
                    $montantLigneUSD += $montant;
                } else {
                    $montantGuichetUSD += $montant;
                }
                $billetsParType[$typeNom]['usd'] += $montant;
            }
    
            if ($devise === 'cdf') {
                if ($moyen === 'en_ligne') {
                    $montantLigneCDF += $montant;
                } else {
                    $montantGuichetCDF += $montant;
                }
                $billetsParType[$typeNom]['cdf'] += $montant;
            }
        }
    
        return view('ticket.index', [
            'billets'            => $billets,
            'totalBillets'       => $totalBillets,
            'montantLigneUSD'    => $montantLigneUSD,
            'montantLigneCDF'    => $montantLigneCDF,
            'montantGuichetUSD'  => $montantGuichetUSD,
            'montantGuichetCDF'  => $montantGuichetCDF,
            'totalUSD'           => $montantLigneUSD + $montantGuichetUSD,
            'totalCDF'           => $montantLigneCDF + $montantGuichetCDF,
            'billetsParType'     => $billetsParType,
        ]);
    }
    
    
    //
    public function store(Request $request)
    {
        // Étape 1 : validation des données
        $validated = $request->validate([
            'nom_complet_client' => 'required|string|max:255',
            'numero_client'      => 'required|string|max:20',
            'numero_billet'      => 'string',
            'type_billet'        => 'required|string',
            'devise'             => 'required|string|in:usd,cdf', 
            'nombre_reel'        => 'required|integer|min:1',
        ],
         [
            
            'numero_billet.unique'        => 'Ce numéro de billet est déjà utilisé.',
         
        ]);
    
        // Étape 2 : recherche des modèles associés
        $type_billet = Type_billet::where('nom_type_billet', $validated['type_billet'])->first();
        if (!$type_billet) {
            return redirect()->back()->with('error', 'Type de billet introuvable.');
        }
        
        if ($type_billet->quantite_disponible < $request->nombre_reel) {
            return redirect()->back()->with('error', 'Quantité insuffisante de billets disponibles.');
        }

        $tarif = Tarif::where('type_billet_id', $type_billet->id)
                      ->where('devise', $validated['devise'])
                      ->first();
    
        if (!$tarif) {
            return redirect()->back()->with('error', 'Tarif introuvable.');
        }

        try {
            // Génération du code unique
            $now = time(); 
            $start1978 = strtotime('1978-01-01 00:00:00'); 
            $secondsSince1978 = $now - $start1978;
            $code = 'Ticket-' . $validated['nom_complet_client'] . '-' . $secondsSince1978 . '-' . uniqid();
            $code = Hash::make($code);
    
            // Création du billet
            $ticket = Billet::create([
                'nom_complet_client' => $validated['nom_complet_client'],
                'numero_client'      => $validated['numero_client'],
                'numero_billet'      => $validated['numero_billet'],
                'code_bilet'         => "",
                'occurance_billet'   => $validated['nombre_reel'],
                'nombre_reel'        => $validated['nombre_reel'],
                'type_billet_id'     => $type_billet->id,
                'tarif_id'           => $tarif->id,
            ]);
            $type_billet->quantite_disponible -= $request->nombre_reel;
            $type_billet->save();
    
            return redirect()->route('billet.index')->with('success', 'Billet enregistré avec succès.');

    
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    

    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string'
        ]);
    
        // Recherche billet valide avec code
        $billet = Billet::where('code_bilet', $request->code)
                        ->where('statut_billet', 'valide')
                        ->first();
    
        if (!$billet) {
            return response()->json([
                'valid' => false,
                'message' => 'Code invalide'
            ], 404);
        }
    
        // Utilisation d'occurance_billet pour décrémenter
        if ($billet->occurance_billet > 1) {
            $billet->decrement('occurance_billet');
            $message = 'Billet validé';
        } else {
            $billet->update([
                'statut_billet' => 'utiliser',
                'occurance_billet' => 0
            ]);
            $message = 'Dernier billet utilisé';
        }
    
        return response()->json([
            'valid' => true,
            'nom' => $billet->nom_complet_client,
            'contact' => $billet->numero_client,
            'occurance_billet' => $billet->occurance_billet,
            'type_billet' => $billet->typeBillet->nom_type_billet ?? 'N/A',
            'message' => $message
        ]);
    }
    public function scanne(){
        return View("ticket.scanner");
     }

}
