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
        Schema::table('gap_assets', function (Blueprint $table) {
            $table->bigInteger('asset_cost')->change();
            $table->bigInteger('accum_depre')->change();
            $table->bigInteger('depre_exp')->change();
            $table->bigInteger('net_book_value')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('gap_assets', function (Blueprint $table) {
            $table->unsignedBigInteger('asset_cost')->change();
            $table->unsignedBigInteger('accum_depre')->change();
            $table->unsignedBigInteger('depre_exp')->change();
            $table->unsignedBigInteger('net_book_value')->change();
        });
    }
};
