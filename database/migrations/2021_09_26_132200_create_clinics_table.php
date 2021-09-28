<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClinicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clinics', function (Blueprint $table) {
            $table->id();
            $table->string('address');
            $table->decimal('lon',10,6);
            $table->decimal('lat',10,6);
            $table->integer('price');
            $table->string('phone');
            $table->string('landline');
            $table->integer('average_consulting_time_minutes');
            $table->foreignId('country_id')->constrained();
            $table->unsignedBigInteger('government_id');
            $table->foreign('government_id')->references('id')->on('countries');
            $table->unsignedBigInteger('area_id');
            $table->foreign('area_id')->references('id')->on('countries');
            $table->foreignId('doctor_id')->constrained();
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
        Schema::dropIfExists('clinics');
    }
}
