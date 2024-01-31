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
        Schema::create('ops_pajak_reklames', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id')->unique();
            $table->string('no_izin')->nullable();
            $table->unsignedBigInteger('nilai_pajak')->nullable();
            $table->string('file_izin_reklame')->nullable();
            $table->string('file_skpd')->nullable();
            $table->date('periode_awal')->nullable();
            $table->date('periode_akhir')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ops_pajak_reklames');
    }
};
