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
            $table->boolean('end_contract')->default(false);
            $table->boolean('need_update')->default(false);
            $table->boolean('on_progress')->default(false);
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
            $table->dropColumn('renewal');
            $table->dropColumn('end_contract');
            $table->dropColumn('need_update');
            $table->dropColumn('on_progress');
        });
    }
};
