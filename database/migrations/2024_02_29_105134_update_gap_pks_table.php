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
        Schema::table('gap_pks', function (Blueprint $table) {
            $table->string('renewal')->nullable();
            $table->string('end_contract')->nullable();
            $table->string('need_update')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('gap_pks', function (Blueprint $table) {
            //
        });
    }
};
