<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateElectionPositionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('election_position', function (Blueprint $table) {
            $table->dropColumn('position_id');
            $table->string('position');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('election_position', function (Blueprint $table) {
            $table->dropColumn('position');
            $table->int('position_id')->unsigned();
        });
    }
}
