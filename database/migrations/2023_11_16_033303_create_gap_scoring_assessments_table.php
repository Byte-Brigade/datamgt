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
        Schema::create('gap_scoring_assessments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id');
            $table->string('entity');
            $table->text('description');
            $table->string('pic');
            $table->string('dokumen_perintah_kerja');
            $table->string('vendor');
            $table->date('tgl_scoring')->nullable();
            $table->string('scoring_vendor')->nullable();
            $table->string('schedule_scoring');
            $table->string('type')->nullable();
            $table->text('keterangan')->nullable();
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
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
        Schema::dropIfExists('gap_scoring_assessments');
    }
};
