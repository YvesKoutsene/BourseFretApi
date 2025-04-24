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
        Schema::create('utilisateur', function (Blueprint $table) {
            $table->id();
            $table->string('keyutilisateur')->unique();
            $table->string('nom');
            $table->string('prenom');
            $table->string('email')->unique();
            $table->string('telephone');
            $table->string('adresse');
            $table->string('motdepasse');
            $table->string('access_token');
            $table->unsignedBigInteger('idprofil');
            $table->unsignedBigInteger('idindicatif');
            $table->unsignedBigInteger('idpays');
            $table->unsignedBigInteger('idclient')->nullable();
            $table->unsignedBigInteger('idtransporteur')->nullable();
            $table->integer('statut')->default(10);
            $table->unsignedBigInteger('createdby')->nullable();
            $table->unsignedBigInteger('updatedby')->nullable();
            $table->timestamps();
            $table->foreign('idprofil')->references('id')->on('profil')->onDelete('restrict');
            $table->foreign('idindicatif')->references('id')->on('pays')->onDelete('restrict');
            $table->foreign('idpays')->references('id')->on('pays')->onDelete('restrict');
            $table->foreign('idclient')->references('id')->on('client')->onDelete('set null');
            $table->foreign('idtransporteur')->references('id')->on('transporteur')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('utilisateur');
    }
};
