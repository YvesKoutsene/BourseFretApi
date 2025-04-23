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
        Schema::create('attributionfret', function (Blueprint $table) {
            $table->string('keyattribution')->unique();
            $table->unsignedBigInteger('idfret');
            $table->unsignedBigInteger('idtransporteur');
            $table->integer('statut')->default(1);
            $table->unsignedBigInteger('createdby')->nullable();
            $table->unsignedBigInteger('updatedby')->nullable();
            $table->timestamps();
            $table->foreign('idfret')->references('id')->on('fret')->onDelete('restrict');
            $table->foreign('idtransporteur')->references('id')->on('transporteur')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attributionfrets');
    }
};
