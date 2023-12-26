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
        Schema::create('gap_perdins', function (Blueprint $table) {
            $table->id();
            $table->string('divisi_pembebanan');
            $table->string('category');
            $table->string('user');
            $table->date('periode');
            $table->string('tipe');
            $table->timestamps();

        });
    }
    public function down()
    {
        Schema::dropIfExists('gap_perdins');
    }
};
