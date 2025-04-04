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
        Schema::create('factura_impuestos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factura_id')->constrained()->onDelete('cascade');
            $table->string('nombre_impuesto');  // Ejemplo: "IVA", "ICE"
            $table->string('codigo_impuesto');  // Código del impuesto (Ej: "2" para IVA, "3" para ICE)
            $table->string('codigo_porcentaje'); // Código del porcentaje (Ej: "0" para IVA 0%, "4" para IVA 15%)
            $table->decimal('base_imponible', 10, 2); // Monto sobre el cual se calcula el impuesto
            $table->decimal('valor_impuesto', 10, 2); // Valor del impuesto calculado
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
        Schema::dropIfExists('factura_impuestos');
    }
};
