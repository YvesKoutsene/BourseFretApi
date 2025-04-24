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
        Schema::create('chauffeur', function (Blueprint $table) {
            $table->id();
            $table->string('keychauffeur')->unique();
            $table->string('nom');
            $table->string('prenom');
            $table->unsignedBigInteger('idtransporteur')->nullable();
            $table->integer('statut')->default(10);
            $table->unsignedBigInteger('createdby')->nullable();
            $table->unsignedBigInteger('updatedby')->nullable();
            $table->timestamps();
            $table->foreign('idtransporteur')->references('id')->on('transporteur')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chauffeur');
    }
};
