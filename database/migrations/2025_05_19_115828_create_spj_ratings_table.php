<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSPJRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_spj_rating', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spj_id')->references('id')->on('t_spj');
            $table->foreignId('user_id')->references('id')->on('t_user');
            $table->integer('rating')->nullable();
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
        Schema::dropIfExists('t_spj_rating');
    }
}
