<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSPJDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_spj_file', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spj_id')->references('id')->on('t_spj');
            $table->foreignId('spj_label_id')->references('id')->on('t_spj_label');
            $table->foreignId('file_id')->nullable()->references('id')->on('t_media');
            $table->text('link')->nullable();
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
        Schema::dropIfExists('t_spj_file');
    }
}
