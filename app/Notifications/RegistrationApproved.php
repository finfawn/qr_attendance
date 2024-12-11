<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Attendance;

class RegistrationApproved extends Notification
{
    protected $attendance;

    public function __construct(Attendance $attendance)
    {
        $this->attendance = $attendance;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Registration Approved - ' . $this->attendance->event->name)
            ->markdown('emails.registration-approved', [
                'attendance' => $this->attendance,
                'event' => $this->attendance->event,
                'user' => $notifiable,
            ]);
    }
}
