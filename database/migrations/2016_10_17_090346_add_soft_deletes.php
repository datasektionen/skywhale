<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSoftDeletes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function ($table) {
            $table->softDeletes();
        });
        Schema::table('elections', function ($table) {
            $table->softDeletes();
        });
        Schema::table('positions', function ($table) {
            $table->softDeletes();
        });
        Schema::table('position_user', function ($table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function ($table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('elections', function ($table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('positions', function ($table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('position_user', function ($table) {
            $table->dropColumn('deleted_at');
        });
    }
}
