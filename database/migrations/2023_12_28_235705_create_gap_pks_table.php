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
        Schema::create('gap_pks', function (Blueprint $table) {
            $table->id();
            $table->string('vendor');
            $table->string('entity');
            $table->string('type');
            $table->string('description');
            $table->date('contract_date')->nullable();
            $table->string('contract_no')->nullable();
            $table->string('durasi_kontrak')->nullable();
            $table->date('awal')->nullable();
            $table->date('akhir')->nullable();
            $table->integer('tahun_akhir')->nullable();
            $table->string('status');
            $table->date('periode');
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
        Schema::dropIfExists('gap_pks');
    }
};
