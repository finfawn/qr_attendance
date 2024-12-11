<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'attendance_slot_id',
        'registration_id',
        'status',
        'scanned_at'
    ];

    protected $casts = [
        'scanned_at' => 'datetime'
    ];

    public function attendanceSlot()
    {
        return $this->belongsTo(AttendanceSlot::class);
    }

    public function registration()
    {
        return $this->belongsTo(Registration::class);
    }
}
