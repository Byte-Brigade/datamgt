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
            $table->unique('asset_number','asset_id');
            $table->dropColumn('periode');
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
            $table->dropUnique('asset_id');
            $table->date('periode')->nullable();
        });
    }
};
