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
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('name')->nullable();
            $table->string('email');
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->string('profile_pic')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('facebook_id')->nullable();
            $table->string('apple_id')->nullable();
            $table->string('google_id')->nullable();
            $table->boolean('push_notifictaion')->nullable();
            $table->boolean('gps_location')->nullable();
            $table->string('timezone')->nullable();
            $table->softDeletes();
            $table->unique(['email', 'deleted_at']);
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
        Schema::dropIfExists('users');
    }
}
