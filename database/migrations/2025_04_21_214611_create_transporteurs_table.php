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
        Schema::create('transporteur', function (Blueprint $table) {
            $table->id();
            $table->string('keytransporteur')->unique();
            $table->string('nom');
            $table->string('prenom');
            $table->string('email');
            $table->string('raisonsociale');
            $table->string('typetransporteur');
            $table->string('contact');
            $table->string('adresse');
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
        Schema::dropIfExists('transporteur');
    }
};
