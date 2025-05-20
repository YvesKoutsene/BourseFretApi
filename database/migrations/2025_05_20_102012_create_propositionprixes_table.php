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
        Schema::create('propositionprix', function (Blueprint $table) {
            $table->id();
            $table->string('keypropositionprix')->unique();
            $table->unsignedBigInteger('idfret');
            //$table->unsignedBigInteger('idaffreteur');
            $table->double('prix');
            $table->string('commentaire');
            $table->string('raisonrefus');            
            $table->integer('statut')->default(10);
            $table->unsignedBigInteger('createdby')->nullable();
            $table->unsignedBigInteger('updatedby')->nullable();     
            $table->timestamps();
            $table->foreign('idfret')->references('id')->on('fret')->onDelete('restrict');
            //$table->foreign('idaffreteur')->references('id')->on('affreteur')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('propositionprix');
    }
};
