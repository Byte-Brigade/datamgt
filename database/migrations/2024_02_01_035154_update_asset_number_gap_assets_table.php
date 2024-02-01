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
            $table->string('asset_number')->change();
            $table->date('tgl_awal_susut')->nullable();
            $table->date('tgl_akhir_susut')->nullable();
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
            $table->integer('asset_number')->change();
            $table->dropColumn('tgl_awal_susut');
            $table->dropColumn('tgl_akhir_susut');
        });
    }
};
