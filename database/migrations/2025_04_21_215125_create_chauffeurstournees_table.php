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
        Schema::create('chauffeurstournee', function (Blueprint $table) {
            $table->id(); // Ajoute une clé primaire auto-incrémentée
            $table->string('keychauffeurstournee')->unique();
            $table->integer('statut')->default(10);
            $table->unsignedBigInteger('idchauffeur')->nullable();
            $table->unsignedBigInteger('idtournee')->nullable();
            $table->unsignedBigInteger('createdby')->nullable();
            $table->unsignedBigInteger('updatedby')->nullable();
            $table->timestamps();
            $table->foreign('idchauffeur')->references('id')->on('chauffeur')->onDelete('restrict');
            $table->foreign('idtournee')->references('id')->on('tournee')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chauffeurstournee');
    }
};
