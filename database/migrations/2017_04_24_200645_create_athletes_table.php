<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAthletesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('athletes', function (Blueprint $table) {
            $table->increments('athlete_id');
            $table->integer('user_id');
            $table->integer('client_id');
            $table->integer('coach_id');
            $table->integer('team_id');
            $table->string('athlete_first_name');
            $table->string('athlete_last_name');
            $table->integer('athlete_age')->nullable();
            $table->string('athlete_gender')->nullable();
            $table->text('athlete_misc')->nullable();
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
        Schema::dropIfExists('athletes');
    }
}
