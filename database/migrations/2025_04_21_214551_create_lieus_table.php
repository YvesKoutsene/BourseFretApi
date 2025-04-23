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
        Schema::create('lieu', function (Blueprint $table) {
            $table->id();
            $table->string('keylieu')->unique();
            $table->string('nom');
            $table->unsignedBigInteger('idparent')->nullable();
            $table->integer('statut')->default(1);
            $table->unsignedBigInteger('createdby')->nullable();
            $table->unsignedBigInteger('updatedby')->nullable();
            $table->timestamps();
            $table->foreign('idparent')->references('id')->on('lieu')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lieu');
    }
};
