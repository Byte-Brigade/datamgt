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
        Schema::create('gap_kdo_mobils', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('gap_kdo_id');
            $table->string('vendor')->nullable();
            $table->string('nopol')->nullable();
            $table->date('awal_sewa')->nullable();
            $table->date('akhir_sewa')->nullable();
            $table->json('biaya_sewa')->nullable();
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->foreign('gap_kdo_id')->references('id')->on('gap_kdos')->onDelete('cascade');
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
        Schema::dropIfExists('gap_kdo_mobils');
    }
};
