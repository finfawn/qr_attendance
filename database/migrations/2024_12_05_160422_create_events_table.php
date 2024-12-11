<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');                  // Event title
            $table->text('description')->nullable(); // Event description
            $table->date('date');                    // Event date
            $table->time('start_time');              // Start time
            $table->time('end_time');                // End time
            $table->unsignedBigInteger('planner_id'); // Planner ID
            $table->string('location')->nullable();  // Event location
            $table->string('status')->default('active'); // Event status
            $table->string('event_code')->unique(); // Unique event code for registration
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('planner_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('events');
    }
}

