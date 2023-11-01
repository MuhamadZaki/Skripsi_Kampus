<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuratTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('surat', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('id_pengirim');
            $table->bigInteger('id_penerima')->nullable();
            $table->bigInteger('id_divisi')->nullable();
            $table->bigInteger('id_judul');
            $table->string('perihal');
            $table->string('path')->unique()->nullable();
            $table->string('filename')->nullable();
            $table->enum('status', ['Belum Dibaca', 'Sudah Dibaca'])->default('Belum Dibaca');
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
        Schema::dropIfExists('surat');
    }
}
