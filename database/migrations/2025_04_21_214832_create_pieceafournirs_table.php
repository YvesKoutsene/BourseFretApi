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
        Schema::create('pieceafournir', function (Blueprint $table) {
            $table->id();
            $table->string('keypieceafournir')->unique();
            $table->string('libelle');
            $table->string('description');
            $table->boolean('isrequired')->default(true);
            $table->string('extension');
            $table->integer('typepiece');
            $table->integer('statut')->default(10);
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
        Schema::dropIfExists('pieceafournir');
    }
};
