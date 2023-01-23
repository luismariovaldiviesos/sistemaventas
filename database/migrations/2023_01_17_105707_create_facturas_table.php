<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('customer_id')->constrained();
            $table->string('codDoc',2); // máximo dos en tamaño
            $table->string('claveAcceso',49); // maximo 49 en tamaño
            $table->string('secuencial',9); // ma´ximo 9 en tamaño
            $table->enum('estado',['PAGADA','PENDIENTE','ELIMINADA'])->default('PAGADA');
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
        Schema::dropIfExists('facturas');
    }
};
