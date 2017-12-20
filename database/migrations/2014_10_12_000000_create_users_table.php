<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->increments('id');
            $table->unsignedBigInteger('facebook_id')->unique()->nullable();
            $table->string('username')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->integer('age');
            $table->text('device_token')->nullable();
            $table->string('email')->unique();
            $table->string('profile_image_id')->nullable();
            $table->string('cover_image_id')->nullable();
            $table->foreign('profile_image_id')->references('id')->on('images');
            $table->foreign('cover_image_id')->references('id')->on('images');
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('phone')->nullable();
            $table->string('password')->nullable();
            $table->dateTime('birthdate');
            $table->enum('gender',['male','female','other']);
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
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('users');
    }
}
