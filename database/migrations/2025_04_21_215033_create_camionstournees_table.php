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
        Schema::create('camionstournees', function (Blueprint $table) {
            $table->string('keycamionstournee')->unique();
            $table->integer('statut')->default(10);
            $table->unsignedBigInteger('idcamion')->nullable();
            $table->unsignedBigInteger('idtournee')->nullable();
            $table->unsignedBigInteger('createdby')->nullable();
            $table->unsignedBigInteger('updatedby')->nullable();
            $table->timestamps();
            $table->foreign('idcamion')->references('id')->on('camion')->onDelete('restrict');
            $table->foreign('idtournee')->references('id')->on('tournee')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('camionstournee');
    }
};
