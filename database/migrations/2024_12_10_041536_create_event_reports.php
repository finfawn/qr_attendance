<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('event_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->integer('total_participants');
            $table->integer('present_count');
            $table->integer('late_count');
            $table->integer('absent_count');
            $table->integer('excused_count');
            $table->float('attendance_rate');
            $table->json('hourly_check_in_distribution')->nullable();
            $table->json('department_statistics')->nullable();
            $table->text('summary')->nullable();
            $table->foreignId('generated_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('event_reports');
    }
};