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
        Schema::create('collaboration', function (Blueprint $table) {
            $table->id();
            $table->string('keycollaboration')->unique();
            $table->string('typecollaboration');
            $table->string('telephonechauffeur');
            $table->string('adressechauffeur');
            $table->string('personneaprevenir');
            $table->string('contactgarant');
            $table->datetime('datedebut');
            $table->datetime('datefin');
            $table->unsignedBigInteger('idtransporteur');
            $table->unsignedBigInteger('idchauffeur');
            $table->integer('statut')->default(1);
            $table->foreign('idtransporteur')->references('id')->on('transporteur')->onDelete('restrict');
            $table->foreign('idchauffeur')->references('id')->on('chauffeur')->onDelete('restrict');
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
        Schema::dropIfExists('collaboration');
    }
};
