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
        Schema::create('infra_scorings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id');
            $table->string('entity');
            $table->string('description');
            $table->string('pic');
            $table->string('status_pekerjaan')->nullable();
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
            $table->char('schedule_scoring', 20)->nullable();
            $table->enum('type', ['Post Project', 'Assessment']);
            $table->text('keterangan')->nullable();
            $table->date('periode');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['description', 'vendor', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('infra_scorings');
    }
};
