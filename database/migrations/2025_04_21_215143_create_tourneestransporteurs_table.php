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
        Schema::create('tourneestransporteur', function (Blueprint $table) {
            $table->string('keytourneestransporteur')->unique();
            $table->integer('statut')->default(10);
            $table->unsignedBigInteger('idtournee')->nullable();
            $table->unsignedBigInteger('idtransporteur')->nullable();
            $table->unsignedBigInteger('createdby')->nullable();
            $table->unsignedBigInteger('updatedby')->nullable();
            $table->timestamps();
            $table->foreign('idtournee')->references('id')->on('tournee')->onDelete('restrict');
            $table->foreign('idtransporteur')->references('id')->on('transporteur')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tourneestransporteur');
    }
};
