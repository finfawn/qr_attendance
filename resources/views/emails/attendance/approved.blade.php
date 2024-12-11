@component('mail::message')
# Registration Approved

Dear {{ $registration->user->name }},

Your registration for the event {{ $registration->event->title }} has been approved.

**Event Details:**
- Date: {{ \Carbon\Carbon::parse($registration->event->date)->format('F d, Y') }}
- Time: {{ \Carbon\Carbon::parse($registration->event->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($registration->event->end_time)->format('h:i A') }}
- Location: {{ $registration->event->location }}

Please use the QR code below for attendance:

<div style="text-align: center;">
    <img src="{{ $qrCodeUrl }}" alt="QR Code" style="width: 200px; height: 200px;">
</div>

Thanks,<br>
sQRypt
@endcomponent