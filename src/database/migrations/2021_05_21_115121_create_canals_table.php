<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCanalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('canals', function (Blueprint $table) {
            $table->id();
            $table->string('channelid', 25)->unique();
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();
            $table->dateTime('fecha')->nulable();
            $table->string('imagen', 150)->nullable();
            $table->datetime('actualizado')->default("1000-01-01 00:00:00");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('canals');
    }
}
