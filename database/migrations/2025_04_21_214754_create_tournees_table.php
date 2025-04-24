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
        Schema::create('tournee', function (Blueprint $table) {
            $table->id();
            $table->string('keytournee')->unique();
            $table->dateTime('datedepart');
            $table->dateTime('datearrivee');
            $table->double('poids');
            $table->string('numerobl');
            $table->string('numeroconteneur');
            $table->unsignedBigInteger('idfret');
            $table->unsignedBigInteger('idlieudepart');
            $table->unsignedBigInteger('idlieuarrivee');
            $table->integer('statut')->default(10);
            $table->unsignedBigInteger('createdby')->nullable();
            $table->unsignedBigInteger('updatedby')->nullable();
            $table->timestamps();
            $table->foreign('idfret')->references('id')->on('fret')->onDelete('restrict');
            $table->foreign('idlieudepart')->references('id')->on('lieu')->onDelete('restrict');
            $table->foreign('idlieuarrivee')->references('id')->on('lieu')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournee');
    }
};
