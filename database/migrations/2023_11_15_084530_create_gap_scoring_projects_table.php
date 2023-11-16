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
        Schema::create('gap_scoring_projects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id');
            $table->string('entity');
            $table->text('description');
            $table->string('pic');
            $table->string('status_pekerjaan');
            $table->string('dokumen_perintah_kerja');
            $table->string('vendor');
            $table->unsignedBigInteger('nilai_project');
            $table->date('tgl_selesai_pekerjaan');
            $table->date('tgl_bast');
            $table->date('tgl_request_scoring');
            $table->date('tgl_scoring')->nullable();
            $table->integer('sla');
            $table->integer('actual');
            $table->boolean('meet_the_sla');
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
        Schema::dropIfExists('gap_scoring_projects');
    }
};
