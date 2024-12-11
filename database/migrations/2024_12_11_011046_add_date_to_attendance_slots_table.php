<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('attendance_slots', function (Blueprint $table) {
            $table->date('date')->after('event_id');
        });
    }

    public function down()
    {
        Schema::table('attendance_slots', function (Blueprint $table) {
            $table->dropColumn('date');
        });
    }
};