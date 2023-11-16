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
        Schema::create('gap_scorings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id');
            $table->string('entity');
            $table->text('description');
            $table->string('pic');
            $table->string('status_pekerjaan');
            $table->string('dokumen_perintah_kerja');
            $table->string('vendor');
            $table->unsignedBigInteger('nilai_project')->nullable();
            $table->date('tgl_selesai_pekerjaan')->nullable();
            $table->date('tgl_bast')->nullable();
            $table->date('tgl_request_scoring')->nullable();
            $table->date('tgl_scoring')->nullable();
            $table->integer('sla')->nullable();
            $table->integer('actual')->nullable();
            $table->boolean('meet_the_sla')->nullable();
            $table->string('scoring_vendor')->nullable();
            $table->string('schedule_scoring');
            $table->string('type');
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
        Schema::dropIfExists('gap_scorings');
    }
};
