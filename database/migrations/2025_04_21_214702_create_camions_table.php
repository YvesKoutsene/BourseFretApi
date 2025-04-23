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
        Schema::create('camion', function (Blueprint $table) {
            $table->id();
            $table->string('keycamion')->unique();
            $table->string('plaque1')->unique();
            $table->string('plaque2')->unique();
            $table->string('formecamion');
            $table->double('poidsvide');
            $table->double('poidsmax');
            $table->integer('statut')->default(1);
            $table->unsignedBigInteger('idtransporteur')->nullable();
            $table->unsignedBigInteger('idchauffeur')->nullable();
            $table->integer('createdby')->nullable();
            $table->integer('updatedby')->nullable();
            $table->timestamps();
            $table->foreign('idtransporteur')->references('id')->on('transporteur')->onDelete('set null');
            $table->foreign('idchauffeur')->references('id')->on('chauffeur')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('camion');
    }
};
