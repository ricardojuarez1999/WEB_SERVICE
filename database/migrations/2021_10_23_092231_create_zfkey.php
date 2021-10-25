<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZfkey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('views', function(Blueprint $table) {
            $table->foreign('useridentityUser')->references('useridentity')->on('users');
        });

        Schema::table('users', function(Blueprint $table) {
            $table->foreign('idImage')->references('id')->on('images');
        });

        Schema::table('posts', function(Blueprint $table) {
            $table->foreign('idImage')->references('id')->on('images');
        });

        Schema::table('posts', function(Blueprint $table) {
            $table->foreign('userIdentityUser')->references('useridentity')->on('users');
        });

        Schema::table('messages', function(Blueprint $table) {
            $table->foreign('idChat')->references('id')->on('chats');
        });

        Schema::table('messages', function(Blueprint $table) {
            $table->foreign('sendUser')->references('useridentity')->on('users');
        });

        Schema::table('chats', function(Blueprint $table) {
            $table->foreign('user1')->references('useridentity')->on('users');
        });

        Schema::table('chats', function(Blueprint $table) {
            $table->foreign('user2')->references('useridentity')->on('users');
        });

        Schema::table('amigos', function(Blueprint $table) {
            $table->foreign('user1')->references('useridentity')->on('users');
        });

        Schema::table('amigos', function(Blueprint $table) {
            $table->foreign('user2')->references('useridentity')->on('users');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('zfkey');
    }
}
