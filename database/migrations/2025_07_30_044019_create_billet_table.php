<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('billets', function (Blueprint $table) {
            $table->id();
            $table->string("nom_complet_client");
            $table->string("numero_client")->nullable();
            $table->string("numero_billet")->nullable();
            $table->string("code_bilet");
            $table->integer("occurance_billet");
            $table->integer("nombre_reel");
            $table->enum("moyen_achat", ['en_ligne', 'guichet'])->default('guichet');
            $table->enum("statut_billet", ['valide', 'utiliser'])->default('valide');

            
            $table->foreignId('tarif_id')->constrained('tarifs')->onDelete('restrict');
            $table->foreignId('type_billet_id')->constrained('type_billets')->onDelete('restrict');
        
        
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billet');
    }
};
