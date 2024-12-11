<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Event;
use App\Models\User;
use App\Models\AttendanceSlot;
use App\Models\Attendance;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Illuminate\Support\Facades\Storage;

class Registration extends Model
{
    protected $fillable = [
        'event_id',
        'user_id',
        'status',
        'qr_code'
    ];

    // Define possible status values
    public static $statuses = [
        'pending' => 'Pending',
        'approved' => 'Approved',
        'rejected' => 'Rejected'
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attendanceSlot(): BelongsTo
    {
        return $this->belongsTo(AttendanceSlot::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function getStatusColorClass(): string
    {
        return match($this->status) {
            'approved' => 'bg-green-100 text-green-800 dark:bg-green-800/50 dark:text-green-100',
            'rejected' => 'bg-red-100 text-red-800 dark:bg-red-800/50 dark:text-red-100',
            default => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800/50 dark:text-yellow-100',
        };
    }

    public function getRowColorClass(): string
    {
        return match($this->status) {
            'approved' => 'bg-green-50 dark:bg-green-900/20',
            'rejected' => 'bg-red-50 dark:bg-red-900/20',
            default => 'bg-yellow-50 dark:bg-yellow-900/20',
        };
    }

    public function generateQrCode(): string
    {
        $data = $this->id . '_' . $this->event_id . '_' . $this->user_id . '_' . time();
        
        $qrCode = new QrCode(
            data: $data,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 300,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            foregroundColor: new Color(0, 0, 0),
            backgroundColor: new Color(255, 255, 255)
        );
        
        $writer = new PngWriter();
        $result = $writer->write($qrCode);
        
        // Save QR code image
        $qrCodePath = 'qr_codes/' . $data . '.png';
        Storage::disk('public')->put($qrCodePath, $result->getString());
        
        return $data;
    }
}
