<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLiftSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lift_schedules', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('athlete_id');
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
            $table->string('lift_type');
            $table->string('lift_variation');
            $table->string('lift_equipment');
            $table->integer('lift_weight');
            $table->string('tracker_id')->nullable();
            $table->integer('reps');
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
        Schema::dropIfExists('lift_schedules');
    }
}
