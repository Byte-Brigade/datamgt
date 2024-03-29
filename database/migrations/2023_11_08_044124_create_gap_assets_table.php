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
        Schema::create('gap_assets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id');
            $table->string('category');
            $table->integer('asset_number');
            $table->text('asset_description')->nullable();
            $table->date('date_in_place_service')->nullable();
            $table->unsignedBigInteger('asset_cost');
            $table->unsignedBigInteger('accum_depre');
            $table->string('asset_location')->nullable();
            $table->string('major_category');
            $table->string('minor_category')->nullable();
            $table->unsignedBigInteger('depre_exp');
            $table->unsignedBigInteger('net_book_value');
            $table->date('periode');
            $table->string('remark')->nullable();
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
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
        Schema::dropIfExists('gap_assets');
    }
};
