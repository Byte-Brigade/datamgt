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
        Schema::create('gap_alih_dayas', function (Blueprint $table) {
            $table->id();
            $table->string('jenis_pekerjaan');
            $table->string('nama_pegawai');
            $table->string('user')->nullable();
            $table->string('lokasi');
            $table->string('vendor');
            $table->integer('cost')->nullable();
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
        Schema::dropIfExists('gap_alih_dayas');
    }
};
