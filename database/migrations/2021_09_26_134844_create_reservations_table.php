<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->integer('reservation_number');
            $table->time('reservation_time');
            $table->tinyInteger('consultation')->default(1);
            $table->tinyInteger('status')->default('1');
            $table->decimal('wallet_amount',7,2);
            $table->decimal('price',7,2);
            $table->foreignId('payment_id')->constrained();
            $table->foreignId('patient_id')->constrained();
            $table->foreignId('clinic_id')->constrained();
            $table->foreignId('doctor_id')->constrained();
            $table->foreignId('schedule_id')->constrained();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('reservations');
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
        Schema::dropIfExists('reservations');
    }
}
