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
        Schema::create('pieceinscription', function (Blueprint $table) {
            $table->string('keypieceinscription')->unique();
            $table->string('piece');
            $table->integer('statut')->default(1);
            $table->unsignedBigInteger('idinscription');
            $table->unsignedBigInteger('idpieceafournir');
            $table->unsignedBigInteger('createdby')->nullable();
            $table->unsignedBigInteger('updatedby')->nullable();
            $table->timestamps();
            $table->foreign('idinscription')->references('id')->on('inscription')->onDelete('restrict');
            $table->foreign('idpieceafournir')->references('id')->on('pieceafournir')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pieceinscription');
    }
};
