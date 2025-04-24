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
        Schema::create('fret', function (Blueprint $table) {
            $table->id();
            $table->string('keyfret')->unique();
            $table->string('raisonannulation');
            $table->dateTime('jourchargement');
            $table->dateTime('jourdechargement');
            $table->string('naturemarchandise');
            $table->double('poidsmarchandise');
            $table->double('poidsrestant')->nullable();
            $table->integer('nombreconteneurs');
            $table->integer('nombrecamions');
            $table->text('commentaire')->nullable();
            $table->string('boursepulication');
            $table->dateTime('debutpublication');
            $table->dateTime('finpublication');
            $table->string('numerodossier');
            $table->boolean('isdemande')->default(10);
            $table->string('documentsupplementaire')->nullable();
            $table->string('numerofret')->nullable();
            $table->string('photofret')->nullable();
            $table->integer('statut')->default(1);
            $table->unsignedBigInteger('idlieuchargement');
            $table->unsignedBigInteger('idlieudechargement');
            $table->unsignedBigInteger('idclient');
            $table->unsignedBigInteger('idparametresvehicule');
            $table->unsignedBigInteger('idtypevehicule');
            $table->unsignedBigInteger('idtypemarchandise');
            $table->unsignedBigInteger('createdby')->nullable();
            $table->unsignedBigInteger('updatedby')->nullable();
            $table->timestamps();
            $table->foreign('idlieuchargement')->references('id')->on('lieu')->onDelete('restrict');
            $table->foreign('idlieudechargement')->references('id')->on('lieu')->onDelete('restrict');
            $table->foreign('idclient')->references('id')->on('client')->onDelete('restrict');
            $table->foreign('idparametresvehicule')->references('id')->on('parametresvehicules')->onDelete('restrict');
            $table->foreign('idtypevehicule')->references('id')->on('typevehicule')->onDelete('restrict');
            $table->foreign('idtypemarchandise')->references('id')->on('typemarchandise')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fret');
    }
};
