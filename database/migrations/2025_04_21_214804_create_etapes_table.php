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
        Schema::create('etape', function (Blueprint $table) {
            $table->id();
            $table->string('keyetape')->unique();
            $table->string('postion');
            $table->dateTime('datepostion');
            $table->double('longitude');
            $table->double('latitude');
            $table->unsignedBigInteger('idtournee');
            $table->integer('statut')->default(10);
            $table->unsignedBigInteger('createdby')->nullable();
            $table->unsignedBigInteger('updatedby')->nullable();
            $table->timestamps();
            $table->foreign('idtournee')->references('id')->on('tournee')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etape');
    }
};
