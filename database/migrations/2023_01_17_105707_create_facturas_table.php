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
            $table->string('secuencial',9); // ma´ximo 9 en tamaño
             $table->string('numeroAutorizacion'); //sri capaz que toca en otra tabla
            $table->date('fechaAutorizacion'); // sri capaz que toca en otra tabla
            $table->string('codDoc',2); // máximo dos en tamaño
            $table->string('claveAcceso',49); // maximo 49 en tamaño
            $table->foreignId('customer_id')->constrained();
            //fecha factura created_at
            $table->foreignId('user_id')->constrained();
            $table->decimal('subtotal',10,2);
            $table->decimal('subtotal0',10,2);
            $table->decimal('subtotal12',10,2);
            $table->decimal('ice',10,2);
            $table->decimal('descuento',10,2);
            $table->decimal('iva12',10,2);
            $table->decimal('total',10,2);
            $table->string('formaPago',2);  // poner 01 by defautl en el front no se lvpo ;
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
