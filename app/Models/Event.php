<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    public static $statuses = [
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_COMPLETED => 'Completed',
        self::STATUS_CANCELLED => 'Cancelled',
    ];

    protected $fillable = [
        'title',
        'description',
        'date',
        'start_time',
        'end_time',
        'planner_id',
        'location',
        'status',
        'event_code',
        'qr_code_path'
    ];

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    public function planner()
    {
        return $this->belongsTo(User::class, 'planner_id');
    }

    public function getStatusColorClass()
    {
        return match($this->status) {
            self::STATUS_ACTIVE => 'bg-green-500 text-white',
            self::STATUS_COMPLETED => 'bg-blue-500 text-white',
            self::STATUS_CANCELLED => 'bg-red-500 text-white',
            default => 'bg-gray-500 text-white',
        };
    }

    public function attendanceSlots(): HasMany
    {
        return $this->hasMany(AttendanceSlot::class);
    }

    public function attendees()
    {
        return $this->belongsToMany(User::class, 'event_attendees')
                    ->where('event_attendees.role', 'attendee');
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($event) {
            // Generate a unique event code
            do {
                $event->event_code = strtoupper(substr(md5(uniqid()), 0, 8));
            } while (static::where('event_code', $event->event_code)->exists());
            
            // Generate QR code
            $qrCode = new QrCode(json_encode([
                'event_code' => $event->event_code,
                'event_id' => $event->id
            ]));
            
            $writer = new PngWriter();
            $result = $writer->write($qrCode);
            
            // Save QR code
            $qrPath = 'event_qrcodes/' . $event->event_code . '.png';
            Storage::disk('public')->put($qrPath, $result->getString());
            
            $event->qr_code_path = $qrPath;
        });
    }

    public function getQrCodeUrl()
    {
        return $this->qr_code_path ? Storage::url($this->qr_code_path) : null;
    }

    public function generateAndSaveQrCode()
    {
        return $this->getQrCodeUrl();
    }

    public function reports()
    {
        return $this->hasMany(EventReport::class);
    }

    public function generateReport()
    {
        $stats = $this->registrations()
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present_count,
                SUM(CASE WHEN status = "late" THEN 1 ELSE 0 END) as late_count,
                SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as absent_count,
                SUM(CASE WHEN status = "excused" THEN 1 ELSE 0 END) as excused_count
            ')
            ->first();

        return EventReport::create([
            'event_id' => $this->id,
            'total_participants' => $stats->total,
            'present_count' => $stats->present_count,
            'late_count' => $stats->late_count,
            'absent_count' => $stats->absent_count,
            'excused_count' => $stats->excused_count,
            'attendance_rate' => ($stats->present_count + $stats->late_count) / $stats->total * 100,
            'generated_by' => auth()->id(),
        ]);
    }
}