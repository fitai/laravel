<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVariationsToLiftTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lift_types', function (Blueprint $table) {
            $table->string('variation')->nullable();
            $table->string('equipment')->nullable();
            $table->integer('min_weight')->nullable()->change();
            $table->integer('max_weight')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lift_types', function (Blueprint $table) {
            $table->dropColumn('variation');
            $table->dropColumn('equipment');
        });
    }
}
