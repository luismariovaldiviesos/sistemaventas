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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('businame',100);
            $table->string('typeidenti',10); // ruc ci etc
            $table->string('valueidenti',20)->unique(); // valor del ruc ci etc
            $table->string('address',250)->nullable();
            $table->string('email',250)->unique();
            $table->string('phone',10);
            $table->string('notes',500);
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
        Schema::dropIfExists('customers');
    }
};
