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
            $table->string('name'); // Nombre del archivo XML
            $table->string('status'); // Estado (no_firmado, firmado, autorizado, rechazado)
            $table->text('path'); // Ruta del archivo
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
        Schema::dropIfExists('xml_files');
    }
};
