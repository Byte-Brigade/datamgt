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
        Schema::create('ops_apar_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ops_apar_id');
            $table->string('titik_posisi');
            $table->date('expired_date');
            $table->foreign('ops_apar_id')->references('id')->on('ops_apars')->onDelete('cascade');
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
        Schema::dropIfExists('ops_apar_details');
    }
};
