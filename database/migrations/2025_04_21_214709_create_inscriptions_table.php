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
        Schema::create('inscription', function (Blueprint $table) {
            $table->id();
            $table->string('keyinscription')->unique();
            $table->string('email')->unique();
            $table->string('token');
            $table->integer('statut')->default(10);
            $table->unsignedBigInteger('idclient')->nullable();
            $table->unsignedBigInteger('idtransporteur')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('createdby')->nullable();
            $table->unsignedBigInteger('updatedby')->nullable();
            $table->foreign('idclient')->references('id')->on('client')->onDelete('set null');
            $table->foreign('idtransporteur')->references('id')->on('transporteur')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inscription');
    }
};
