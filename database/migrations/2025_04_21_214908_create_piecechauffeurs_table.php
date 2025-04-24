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
        Schema::create('piecechauffeur', function (Blueprint $table) {
            $table->string('keypiecechauffeur')->unique();
            $table->string('piece');
            $table->integer('statut')->default(10);
            $table->unsignedBigInteger('idchauffeur');
            $table->unsignedBigInteger('idpieceafournir');
            $table->unsignedBigInteger('createdby')->nullable();
            $table->unsignedBigInteger('updatedby')->nullable();
            $table->timestamps();
            $table->foreign('idchauffeur')->references('id')->on('chauffeur')->onDelete('restrict');
            $table->foreign('idpieceafournir')->references('id')->on('pieceafournir')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('piecechauffeur');
    }
};
