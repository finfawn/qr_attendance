<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceSlot extends Model
{
    protected $fillable = [
        'event_id',
        'title',
        'date',
        'start_time',
        'end_time',
        'absent_time'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function registrations()
    {
        return $this->belongsToMany(Registration::class, 'attendances')
                    ->withPivot('status', 'scanned_at')
                    ->withTimestamps();
    }
}