<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnNotified extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('position_user', function (Blueprint $table) {
            $table->datetime('notified')->nullable()->default(null);
        });
	\DB::table('position_user')
		->update(['notified' => '2016-11-05 12:00:00']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('position_user', function (Blueprint $table) {
            $table->dropColumn('notified');
        });
    }
}
