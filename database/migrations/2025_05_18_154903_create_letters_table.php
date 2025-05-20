<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLettersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_letter', function (Blueprint $table) {
            $table->id();
            $table->string('kode');
            $table->string('nomor_agenda')->nullable();
            $table->date('tanggal_surat')->nullable();
            $table->string('nomor_surat')->nullable();
            $table->string('asal_surat')->nullable();
            $table->string('hal')->nullable();
            $table->foreignId('pemohon_id')->references('id')->on('t_user');
            $table->date('tanggal_diterima')->nullable();
            $table->string('untuk')->nullable();
            $table->string('status')->nullable();
            $table->foreignId('proposal_file')->nullable()->references('id')->on('t_media');
            $table->boolean('disertai_dana')->nullable();
            $table->text('alasan_penolakan')->nullable();
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
        Schema::dropIfExists('t_letter');
    }
}
