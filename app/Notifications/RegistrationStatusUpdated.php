<?php

namespace App\Notifications;

use App\Models\Event;
use App\Models\Attendance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RegistrationStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $event;
    protected $attendance;
    protected $status;

    public function __construct(Event $event, Attendance $attendance)
    {
        $this->event = $event;
        $this->attendance = $attendance;
        $this->status = $attendance->status;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        if ($this->status === 'approved') {
            return (new MailMessage)
                ->subject('Event Registration Approved - ' . $this->event->title)
                ->greeting('Hello ' . $notifiable->name . '!')
                ->line('Your registration for the event "' . $this->event->title . '" has been approved.')
                ->line('Event Details:')
                ->line('Date: ' . $this->event->date)
                ->line('Time: ' . $this->event->time)
                ->line('Location: ' . $this->event->location)
                ->line('Your QR code is attached to this email. Please present this QR code when attending the event.')
                ->line('Thank you for registering!')
                ->attachData(
                    $this->generateQRCode($notifiable->qr_code),
                    'qr-code.png',
                    [
                        'mime' => 'image/png',
                    ]
                );
        } else {
            return (new MailMessage)
                ->subject('Event Registration Update - ' . $this->event->title)
                ->greeting('Hello ' . $notifiable->name . '!')
                ->line('Your registration for the event "' . $this->event->title . '" has been rejected.')
                ->line('If you have any questions, please contact the event organizer.')
                ->line('Thank you for your interest in the event.');
        }
    }

    protected function generateQRCode($qrCode)
    {
        $renderer = new \BaconQrCode\Renderer\ImageRenderer(
            new \BaconQrCode\Renderer\RendererStyle\RendererStyle(400),
            new \BaconQrCode\Renderer\Image\ImagickImageBackEnd()
        );

        $writer = new \BaconQrCode\Writer($renderer);
        return $writer->writeString($qrCode);
    }
}
