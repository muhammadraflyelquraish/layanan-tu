<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLetterDispositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_letter_disposition', function (Blueprint $table) {
            $table->id();
            $table->foreignId('letter_id')->references('id')->on('t_letter');
            $table->foreignId('position_id')->references('id')->on('t_role');
            $table->timestamp('tanggal_diterima')->nullable();
            $table->timestamp('tanggal_diproses')->nullable();
            $table->foreignId('verifikator_id')->nullable()->references('id')->on('t_user');
            $table->text('keterangan')->nullable();
            $table->string('status')->nullable();
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
        Schema::dropIfExists('t_letter_disposition');
    }
}
