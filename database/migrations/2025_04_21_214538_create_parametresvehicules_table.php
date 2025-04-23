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
        Schema::create('parametresvehicules', function (Blueprint $table) {
            $table->id();
            $table->string('keyparametresvehicule');
            $table->double('temperaturemin');
            $table->double('temperaturemax');
            $table->double('hauteurchargement');
            $table->double('longueurchargement');
            $table->double('largeurchargement');
            $table->boolean('isolationthermique');
            $table->string('normesanitaire');
            $table->string('modechargement');
            $table->string('systemerefroidissement');
            $table->string('materiauciterne');
            $table->double('capacitelitre');
            $table->integer('capacitepieds');
            $table->boolean('reefer');
            $table->integer('statut')->default(1);
            $table->unsignedBigInteger('createdby')->nullable();
            $table->unsignedBigInteger('updatedby')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parametresvehicules');
    }
};
