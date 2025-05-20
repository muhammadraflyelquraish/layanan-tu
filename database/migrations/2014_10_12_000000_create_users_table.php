<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_user', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('no_identity')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->foreignId('role_id')->references('id')->on('t_role');
            $table->enum('status', ['ACTIVE', 'INACTIVE']);
            $table->rememberToken();
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
        Schema::dropIfExists('t_user');
    }
}
