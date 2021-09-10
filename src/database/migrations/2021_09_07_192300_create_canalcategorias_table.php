<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCanalCategoriasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('canalcategorias', function (Blueprint $table) {
            $table->primary(['idcanal', 'idcategoria']);
            $table->foreignId('idcanal')->constrained('canals')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('idcategoria')->constrained('categorias')->onUpdate('cascade')->onDelete('cascade');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('canalcategorias');
    }
}