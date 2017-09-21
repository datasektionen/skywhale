<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('position_id')->unsigned()->nullable();
            $table->integer('election_id')->unsigned()->nullable();
            $table->string('description')->nullable();
            $table->enum('action', [
                'nominated', 
                'accepted', 
                'declined', 
                'regretted', 
                'opened', 
                'closed', 
                'nomination_stopped', 
                'acceptance_stopped'
            ]);
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
        Schema::drop('events');
    }
}
