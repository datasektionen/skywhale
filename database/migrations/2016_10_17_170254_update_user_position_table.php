<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUserPositionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('position_user', function (Blueprint $table) {
            $table->dropColumn('position_id');
            $table->string('position');
        });
        Schema::drop('positions');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_position', function (Blueprint $table) {
            $table->integer('position_id')->unsigned();
            $table->dropColumn('position');
        });
        // Add the positions table
    }
}
