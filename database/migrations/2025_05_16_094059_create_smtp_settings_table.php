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
        Schema::create('smtp_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->references('id')->on('setthings')->onDelete('cascade');
            $table->string('provider')->nullable(); // Gmail, Outlook, etc.
            $table->string('mailer')->default('smtp');
            $table->string('host');
            $table->string('port');
            $table->string('encryption')->nullable();
            $table->string('username');
            $table->text('password'); // se encripta al guardar
            $table->string('from_address');
            $table->string('from_name');
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
        Schema::dropIfExists('smtp_settings');
    }
};
