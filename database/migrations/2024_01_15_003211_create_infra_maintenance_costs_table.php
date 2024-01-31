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
        Schema::create('infra_maintenance_costs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id');
            $table->string('nama_project');
            $table->string('entity');
            $table->string('category');
            $table->string('jenis_pekerjaan');
            $table->unsignedBigInteger('nilai_oe_interior')->default(0);
            $table->unsignedBigInteger('nilai_oe_me');
            $table->unsignedBigInteger('total_oe');
            $table->string('nama_vendor');
            $table->unsignedBigInteger('nilai_project_memo');
            $table->unsignedBigInteger('nilai_project_final');
            $table->unsignedBigInteger('kerja_tambah_kurang');
            $table->date('periode');
            $table->foreign('branch_id')->on('branches')->references('id');
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
        Schema::dropIfExists('infra_maintenance_costs');
    }
};
