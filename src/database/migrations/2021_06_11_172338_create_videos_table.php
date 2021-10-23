<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->string('videoid', 12)->unique();
            $table->string('titulo', 100);
            $table->text('descripcion')->nullable();
            $table->dateTime('fecha')->nullable();
            $table->string('imagen', 150)->nullable();
            $table->foreignId('idlistarep')->constrained('lista_reproduccions')->onUpdate('cascade')->onDelete('cascade');
            $table->datetime('actualizado')->default("1000-01-01 00:00:00");
            $table->string('etagDatos', 28)->nullable();
            //$table->text('embedHtml');
            $table->float('proporcion', 6, 4)->default(1.0);
            $table->integer('duracion')->default(0);
            $table->integer('estrep')->default(0);
            $table->integer('estgusta')->default(0);
            $table->integer('estnogusta')->default(0);
            $table->integer('estfav')->default(0);
            $table->integer('estcom')->default(0);
            $table->boolean('visible')->default(true);
            $table->integer('estrellas')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('videos');
    }
}
