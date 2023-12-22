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
        Schema::create('infra_bros', function (Blueprint $table) {
            $table->id();
            $table->string('branch_name');
            $table->string('branch_type')->nullable();
            $table->string('category')->nullable();
            $table->string('status')->nullable();
            $table->string('target')->nullable();
            $table->date('jatuh_tempo_sewa')->nullable();
            $table->string('start_date')->nullable();
            $table->double('all_progress')->nullable();
            $table->double('gedung')->nullable();
            $table->double('layout')->nullable();
            $table->double('kontraktor')->nullable();
            $table->double('line_telp')->nullable();
            $table->double('tambah_daya')->nullable();
            $table->double('renovation')->nullable();
            $table->double('inventory_non_it')->nullable();
            $table->double('barang_it')->nullable();
            $table->double('asuransi')->nullable();
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
        Schema::dropIfExists('infra_bros');
    }
};
