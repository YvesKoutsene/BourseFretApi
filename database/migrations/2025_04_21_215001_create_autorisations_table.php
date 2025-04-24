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
        Schema::create('autorisation', function (Blueprint $table) {
            $table->string('keyautorisation')->unique();
            $table->integer('statut')->default(10);
            $table->unsignedBigInteger('idprofil');
            $table->unsignedBigInteger('idfonctionnalite');
            $table->unsignedBigInteger('createdby')->nullable();
            $table->unsignedBigInteger('updatedby')->nullable();
            $table->foreign('idprofil')->references('id')->on('profil')->onDelete('restrict');
            $table->foreign('idfonctionnalite')->references('id')->on('fonctionnalite')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('autorisation');
    }
};
