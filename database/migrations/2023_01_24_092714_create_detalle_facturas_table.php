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
        Schema::create('detalle_facturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factura_id')->constrained();
            $table->foreignId('product_id')->constrained();
            $table->integer('cantidad');
            $table->string('descripcion');
            $table->decimal('precioUnitario',10,2);
            $table->decimal('descuento',10,2);
            $table->decimal('precioTotalSinImpuesto',10,2);
            $table->string('formaPago'); //01 efectivo
            $table->decimal('subtotal12',10,2);
            $table->decimal('subtotal0',10,2);
            $table->decimal('iva12',10,2);
            $table->decimal('total',10,2);
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
        Schema::dropIfExists('detalle_facturas');
    }
};
