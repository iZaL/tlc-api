<?php

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
//            $table->string('name_en')->nullable();
//            $table->string('name_ar')->nullable();
//            $table->string('name_hi')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->unique();
            $table->string('mobile')->unique();
            $table->string('password');
            $table->boolean('admin')->default(0);
            $table->string('otp')->nullable();
            $table->string('api_token')->nullable();
            $table->string('image')->nullable();
            $table->boolean('active')->default(0)->nullable();
            $table->rememberToken();
            $table->timestamps();
//            $table->softDeletes();
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
