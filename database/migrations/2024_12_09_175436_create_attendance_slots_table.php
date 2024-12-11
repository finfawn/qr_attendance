<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('attendance_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->string('title')->comment('e.g. Morning Time In, Afternoon Time In');
            $table->time('start_time');
            $table->time('end_time');
            $table->time('absent_time')->comment('Time after which attendance will be marked as absent. Check-ins between end_time and absent_time will be marked as late');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendance_slots');
    }
};