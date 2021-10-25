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
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('useridentity',255)->unique();
            $table->string('email',255)->unique();
            $table->string('password',255);
            $table->string('name',255);
            $table->string('nickname',255)->nullable();
            $table->string('token',1000)->nullable();
            $table->timestamp('dob')->nullable();
            $table->boolean('is_admin')->default(0);
            $table->unsignedBigInteger('idImage')->nullable();
            $table->string('description',255)->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
