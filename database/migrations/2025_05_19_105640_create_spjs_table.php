<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSPJSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_spj', function (Blueprint $table) {
            $table->id();
            $table->foreignId('letter_id')->references('id')->on('t_letter');
            $table->foreignId('user_id')->references('id')->on('t_user');
            $table->string('jenis')->nullable();
            $table->string('status')->nullable();
            $table->timestamp('tanggal_proses')->nullable();
            $table->timestamp('tanggal_selesai')->nullable();
            $table->text('catatan')->nullable();
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
        Schema::dropIfExists('t_spj');
    }
}
