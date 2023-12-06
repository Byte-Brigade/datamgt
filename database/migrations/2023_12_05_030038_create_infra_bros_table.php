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
            $table->string('activity')->nullable();
            $table->string('status')->nullable();
            $table->date('target')->nullable();
            $table->date('jatuh_tempo_sewa')->nullable();
            $table->double('all_progress');
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
