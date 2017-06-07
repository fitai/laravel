<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLiftsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lifts', function (Blueprint $table) {
            $table->bigIncrements('lift_id');
            $table->bigInteger('athlete_id');
            $table->integer('sampling_rate');
            $table->dateTime('lift_start');
            $table->string('lift_type');
            $table->integer('lift_weight');
            $table->string('weight_units', 5);
            $table->integer('init_num_reps');
            $table->integer('final_num_reps')->nullable();
            $table->integer('calc_reps')->nullable();
            $table->text('user_comment')->nullable();
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
        Schema::dropIfExists('lifts');
    }
}
