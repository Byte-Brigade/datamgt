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
        Schema::create('gap_perdin_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gap_perdin_id');
            $table->date('periode');
            $table->unsignedBigInteger('value');
            $table->string('category');
            $table->string('tipe');
            $table->foreign('gap_perdin_id')->references('id')->on('gap_perdins')->onDelete('cascade');
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
        Schema::dropIfExists('gap_perdin_details');
    }
};
