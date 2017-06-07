<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLiftTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lift_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name_safe');
            $table->string('name_display');
            $table->integer('min_weight');
            $table->integer('max_weight');
            $table->decimal('weight_interval', 2, 1)->nullable();
            $table->string('type');
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
        Schema::dropIfExists('lift_types');
    }
}
