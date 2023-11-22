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
        Schema::create('infra_sewa_gedungs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id');
            $table->enum('status_kepemilikan', ['Sewa', 'Milik', 'Pinjam Pakai'])->nullable();
            $table->integer('jangka_waktu')->nullable();
            $table->date('open_date')->nullable();
            $table->date('jatuh_tempo')->nullable();
            $table->string('owner')->nullable();
            $table->bigInteger('biaya_per_tahun')->nullable();
            $table->bigInteger('total_biaya')->nullable();
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->unique(['branch_id']);
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
        Schema::dropIfExists('infra_sewa_gedungs');
    }
};
