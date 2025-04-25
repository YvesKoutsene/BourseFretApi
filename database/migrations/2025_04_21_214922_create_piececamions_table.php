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
        Schema::create('piececamion', function (Blueprint $table) {
           $table->id(); // Ajoute une clé primaire auto-incrémentée
           $table->string('keypiececamion')->unique();
           $table->string('piece');
           $table->integer('statut')->default(10);
           $table->unsignedBigInteger('idcamion');
           $table->unsignedBigInteger('idpieceafournir');
           $table->unsignedBigInteger('createdby')->nullable();
           $table->unsignedBigInteger('updatedby')->nullable();
           $table->timestamps();
           $table->foreign('idcamion')->references('id')->on('camion')->onDelete('restrict');
           $table->foreign('idpieceafournir')->references('id')->on('pieceafournir')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('piececamion');
    }
};
