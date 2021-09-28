<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScheduleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->date('schedule_date');
            $table->json('schedule_day');
            $table->time('starting_from')->nullable();
            $table->time('ending_at')->nullable();
            $table->tinyInteger('working_day')->default(0);
            $table->foreignId('doctor_id')->constrained();
            $table->foreignId('clinic_id')->constrained();
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
        Schema::dropIfExists('schedule');
    }
}
