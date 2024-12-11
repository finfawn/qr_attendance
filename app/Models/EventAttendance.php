<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventAttendance extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'event_id',
        'user_id',
        'time_in',
        'time_out',
        'status',
        'attendance_method',
        'remarks',
        'location_checked_in',
        'device_info',
        'verified_by'
    ];

    protected $casts = [
        'time_in' => 'datetime',
        'time_out' => 'datetime'
    ];

    // Relationships
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // Status constants
    const STATUS_PRESENT = 'present';
    const STATUS_LATE = 'late';
    const STATUS_ABSENT = 'absent';
    const STATUS_EXCUSED = 'excused';

    // Helper methods
    public function markAsPresent()
    {
        $this->update(['status' => self::STATUS_PRESENT]);
    }

    public function markAsLate()
    {
        $this->update(['status' => self::STATUS_LATE]);
    }

    public function markAsAbsent()
    {
        $this->update(['status' => self::STATUS_ABSENT]);
    }

    public function markAsExcused()
    {
        $this->update(['status' => self::STATUS_EXCUSED]);
    }
} 