<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPostSpecificDates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('election_position', function (Blueprint $table) {
            $table->datetime('nomination_stop')->nullable()->default(null);
            $table->datetime('acceptance_stop')->nullable()->default(null);
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
            $table->dropColumn('nomination_stop');
            $table->dropColumn('acceptance_stop');
        });
    }
}
