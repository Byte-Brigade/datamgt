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
            $table->string('asset_number')->unique();
            $table->string('status');
            $table->string('semester');
            $table->date('periode');
            $table->boolean('sto')->default(false);
            $table->timestamps();
            $table->foreign('asset_number')->references('asset_number')->on('gap_assets')->onDelete('cascade');
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
