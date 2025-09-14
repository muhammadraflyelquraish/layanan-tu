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
            $table->string('no_identity')->nullable()->unique();
            $table->string('email')->nullable()->unique();
            $table->boolean('email_verified')->default(false);
            $table->string('password')->nullable();
            $table->enum('status', ['ACTIVE', 'INACTIVE']);
            $table->text('avatar')->nullable();
            $table->text('avatar_original')->nullable();
            $table->enum('user_type', ['REGISTER', 'GOOGLE', 'LAYANAN'])->default('REGISTER');
            $table->foreignId('role_id')->nullable()->references('id')->on('t_role');
            $table->foreignId('prodi_id')->nullable()->references('id')->on('t_prodi');
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
