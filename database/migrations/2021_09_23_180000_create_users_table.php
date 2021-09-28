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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->string('address');
            $table->string('photo');
            $table->enum('gender',['m','f'])->default('m');
            $table->string('fcm_token');
            $table->string('facebook_id')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('password');
            $table->foreignId('country_id')->constrained();
            $table->unsignedBigInteger('government_id');
            $table->foreign('government_id')->references('id')->on('countries');
            $table->unsignedBigInteger('area_id');
            $table->foreign('area_id')->references('id')->on('countries');
            $table->foreignId('wallet_id')->constrained();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('patients');
    }
}
