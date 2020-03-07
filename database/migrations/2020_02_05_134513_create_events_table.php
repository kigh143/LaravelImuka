<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      

        Schema::create('events', function (Blueprint $table) {
           
            $table->bigIncrements('id');
            $table->string('title');
            $table->string('event_type');
            $table->string('venue');
            $table->string('image');
            $table->string('link')->nullable();
            $table->text('description')->nullable();
            $table->string('location');
            $table->text('country');
            $table->string('region');
            $table->string('category');
<<<<<<< HEAD
            $table->string('price')->nullable();
=======
>>>>>>> 65057f170880112619cc6007bb1c01d552039189
            $table->text('contact')->nullable();
            $table->text('session_objectives')->nullable();
            $table->date('date_from')->nullable();
            $table->date('date_to')->nullable();
            $table->string('time_from')->nullable();
            $table->string('time_to')->nullable();
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
        Schema::dropIfExists('events');
    }
}
