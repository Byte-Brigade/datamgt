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
        Schema::create('kdo_mobil_biaya_sewas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gap_kdo_mobil_id');
            $table->date('periode');
            $table->integer('value');
            $table->foreign('gap_kdo_mobil_id')->references('id')->on('gap_kdo_mobils')->onDelete('cascade');

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
        Schema::dropIfExists('kdo_mobil_biaya_sewas');
    }
};
