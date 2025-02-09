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
        Schema::create('xml_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('factura_id')->nullable(); // Relación con la tabla facturas
            $table->string('secuencial')->unique(); // Secuencial único para cada archivo
            $table->string('cliente')->nullable(); // Nombre del cliente
            $table->string('directorio')->nullable(); // Ruta donde se encuentra el archivo
            $table->string('estado')->default('creado'); // Estado actual del archivo
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
        Schema::dropIfExists('xml_files');
    }
};
