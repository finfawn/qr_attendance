<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Registration;

class AttendanceApproved extends Mailable
{
    use Queueable, SerializesModels;

    public $registration;
    public $qrCode;

    public function __construct(Registration $registration, string $qrCode)
    {
        $this->registration = $registration;
        $this->qrCode = $qrCode;
    }

    public function build()
    {
        // Fix the path by removing the extra /storage/
        $qrCodePath = storage_path('app/public/' . str_replace('storage/', '', $this->qrCode));

        return $this->markdown('emails.attendance.approved')
                    ->subject('Event Registration Approved')
                    ->attach($qrCodePath, [
                        'as' => 'qr-code.png',
                        'mime' => 'image/png'
                    ])
                    ->with([
                        'qrCodeUrl' => 'cid:qr-code.png'
                    ]);
    }
} 