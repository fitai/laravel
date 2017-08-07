<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameCollarsTableToTrackersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('collars', 'trackers');
        Schema::table('trackers', function (Blueprint $table) {
            $table->renameColumn('collar_id', 'tracker_id');
        });
        Schema::table('lifts', function (Blueprint $table) {
            $table->renameColumn('collar_id', 'tracker_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('trackers', 'collars');
        Schema::table('collars', function (Blueprint $table) {
            //
        });
    }
}
