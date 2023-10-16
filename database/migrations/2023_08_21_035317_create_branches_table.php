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
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_type_id')->nullable();
            $table->string('branch_code', 9)->unique()->nullable();
            $table->string('branch_name', 20);
            $table->text('address');
            $table->string('telp', 50)->nullable();
            $table->string('npwp', 20)->nullable();
            $table->string('nitku', 22)->nullable();
            $table->string('izin', 64)->nullable();
            $table->enum('layanan_atm', ['24 Jam', 'Jam Operasional', 'Tidak Ada'])->nullable();
            $table->enum('status',['Sewa','Milik','Pinjam Pakai'])->nullable();
            $table->integer('masa_sewa')->nullable();
            $table->date('open_date')->nullable();
            $table->date('expired_date')->nullable();
            $table->string('owner')->nullable();
            $table->bigInteger('sewa_per_tahun')->nullable();
            $table->bigInteger('total_biaya_sewa')->nullable();
            $table->timestamps();
            $table->foreign('branch_type_id')->references('id')->on('branch_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('branches');
    }
};
