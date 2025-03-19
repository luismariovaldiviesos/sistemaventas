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
        Schema::create('impuestos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // IVA, ICE, IRBPNR, etc.
            $table->integer('codigo'); // Código SRI (Ej: 2 para IVA, 3 para ICE)
            $table->integer('codigo_porcentaje'); // Código del porcentaje (Ej: 4 para IVA 15%)
            $table->decimal('porcentaje', 5, 2); // Porcentaje (Ej: 15.00 para IVA 15%)
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
        Schema::dropIfExists('impuestos');
    }
};
