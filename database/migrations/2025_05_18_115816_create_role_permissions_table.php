<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolePermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_role_permission', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->references('id')->on('t_role');
            $table->enum('menu', ['DASHBOARD', 'USER', 'ROLE', 'LETTER', 'SPJ']);
            $table->boolean('is_permitted')->default(false);
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
        Schema::dropIfExists('t_role_permission');
    }
}
