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
        Schema::create('t_surat_disposisi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surat_id')->references('id')->on('t_surat');
            $table->foreignId('disposisi_id')->nullable()->references('id')->on('t_disposisi');
            $table->timestamp('tanggal_diterima')->nullable();
            $table->timestamp('tanggal_diproses')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('status')->nullable();
            $table->integer('urutan')->default(0);
            $table->foreignId('verifikator_id')->nullable()->references('id')->on('t_user');
            $table->foreignId('verifikator_role_id')->nullable()->references('id')->on('t_role');
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
        Schema::dropIfExists('t_surat_disposisi');
    }
}
