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
        Schema::create('deleted_facturas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('factura_id'); // Relación con la tabla facturas
            $table->string('secuencial'); // Secuencial de la factura
            $table->string('cliente'); // Nombre del cliente
            $table->string('ruc_cliente'); // RUC del cliente
            $table->string('correo_cliente'); // Dirección del cliente
            $table->date('fecha_emision'); // Fecha de emisión de la factura
            $table->string('clave_acceso'); // Clave de acceso
            $table->string('estado')->default('pendiente'); // Estado (pendiente de eliminación)
            $table->timestamps();
            $table->foreign('factura_id')->references('id')->on('facturas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deleted_facturas');
    }
};
