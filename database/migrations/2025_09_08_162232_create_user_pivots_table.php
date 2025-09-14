<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserPivotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_user_role', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('t_user');
            $table->foreignId('role_id')->references('id')->on('t_role');
            $table->foreignId('prodi_id')->nullable()->references('id')->on('t_prodi');
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
        Schema::dropIfExists('t_user_role');
    }
}
