<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ops_skbirtgs', function (Blueprint $table) {
            $table->id();
            $table->string('no_surat');
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('penerima_kuasa_1')->nullable();
            $table->unsignedBigInteger('penerima_kuasa_2')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();

            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->foreign('penerima_kuasa_1')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('penerima_kuasa_2')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ops_skbirtgs');
    }
};
