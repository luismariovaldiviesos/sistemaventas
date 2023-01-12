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
        Schema::create('settings', function (Blueprint $table) {

            $table->id();
            $table->string('razonSocial',255);
            $table->string('nombreComercial',255)->nullable();
            $table->string('ruc',13)->unique();
            $table->string('estab'); //max 3
            $table->string('ptoEmi');  // max 3
            $table->string('dirMatriz',500);
            $table->string('dirEstablecimiento',500);
            $table->string('telefono',13)->nullable();
            $table->string('email');
            $table->integer('ambiente');  //1 0 2
            $table->integer('tipoEmision'); // 1
            $table->string('contribuyenteEspecial'); //5368
            $table->string('obligadoContabilidad'); // si no
            $table->string('logo')->nullable();
            $table->string('leyend',50)->nullable();
            $table->string('printer',30)->nullable();
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
        Schema::dropIfExists('settings');
    }
};
