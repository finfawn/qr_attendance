<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventAttendancesTable extends Migration
{
    public function up()
    {
        Schema::create('event_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->dateTime('time_in');
            $table->dateTime('time_out')->nullable();
            $table->enum('status', ['present', 'late', 'absent', 'excused'])->default('present');
            $table->string('attendance_method')->default('qr'); // qr, manual, etc.
            $table->text('remarks')->nullable();
            $table->string('location_checked_in')->nullable(); // Can be used for location verification
            $table->string('device_info')->nullable(); // Store device information for security
            $table->foreignId('verified_by')->nullable()->constrained('users'); // Admin who verified the attendance
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['event_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('event_attendances');
    }
}