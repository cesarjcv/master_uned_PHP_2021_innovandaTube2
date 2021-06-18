<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateListaReproduccionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lista_reproduccions', function (Blueprint $table) {
            $table->id();
            $table->string('listid', 36)->unique();
            $table->string('nombre', 200);
            $table->text('descripcion')->nullable();
            $table->dateTime('fecha')->nullable();
            $table->string('imagen', 150)->nullable();
            $table->foreignId('idcanal')->constrained('canals')->onUpdate('cascade')->onDelete('cascade');
            $table->datetime('actualizado')->default("1000-01-01 00:00:00");
            $table->string('etagDatos', 28)->nullable();
            $table->string('etagVideos', 28)->nullable();
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
        Schema::dropIfExists('lista_reproduccions');
    }
}
