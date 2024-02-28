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
        Schema::create('gap_asset_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gap_asset_id');
            $table->string('status');
            $table->string('semester');
            $table->date('periode');
            $table->boolean('sto')->default(false);
            $table->timestamps();
            $table->foreign('gap_asset_id')->references('id')->on('gap_assets')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gap_asset_details');
    }
};
