<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EventRegistrationController extends Controller
{
    public function show(Event $event)
    {
        // Check if the user has permission to view this event
        if ($event->planner_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Load attendances with user information
        $event->load(['registrations.user']);

        // Debug information
        \Log::info('Event registrations:', [
            'event_id' => $event->id,
            'registrations_count' => $event->registrations->count(),
            'attendees' => $event->registrations->map(function($registration) {
                return [
                    'id' => $registration->id,
                    'user_id' => $registration->user_id,
                    'user_name' => optional($registration->user)->name,
                    'status' => $registration->status
                ];
            })
        ]);

        return view('planner.events.show-event', [
            'event' => $event,
            'qrCodeUrl' => Storage::url($event->qr_code_path),
            'eventCode' => $event->event_code
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'event_code' => 'required|string|exists:events,event_code'
        ]);

        $event = Event::where('event_code', $request->event_code)
            ->where('status', 'active')
            ->first();

        if (!$event) {
            return back()->with('error', 'This event is not available for registration.');
        }

        // Check if user is already registered
        $existingRegistration = Registration::where('user_id', Auth::id())
            ->where('event_id', $event->id)
            ->first();

        if ($existingRegistration) {
            return back()->with('error', 'You are already registered for this event.');
        }

        // Create new attendance record
        Registration::create([
            'event_id' => $event->id,
            'user_id' => Auth::id(),
            'status' => 'pending'
        ]);

        return redirect()->route('attendee.dashboard')
            ->with('success', 'Registration request submitted successfully. Please wait for approval.');
    }

    public function registerViaQr(Request $request)
    {
        try {
            $request->validate([
                'qr_data' => 'required|string'
            ]);

            // Decode QR data
            $qrData = json_decode($request->qr_data);
            
            if (!$qrData || !isset($qrData->event_code)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid QR code'
                ], 400);
            }

            $event = Event::where('event_code', $qrData->event_code)
                ->where('status', 'active')
                ->first();

            if (!$event) {
                return response()->json([
                    'success' => false,
                    'message' => 'Event not found or not active'
                ], 404);
            }

            // Check for existing registration
            $existingRegistration = Registration::where('user_id', auth()->id())
                ->where('event_id', $event->id)
                ->first();

            if ($existingRegistration) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are already registered for this event'
                ], 400);
            }

            // Create new registration
            Registration::create([
                'event_id' => $event->id,
                'user_id' => auth()->id(),
                'status' => 'pending'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Successfully registered for the event'
            ]);

        } catch (\Exception $e) {
            \Log::error('QR Registration Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing registration'
            ], 500);
        }
    }

    public function updateStatus(Request $request, Event $event, $attendeeId)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $attendee = $event->registrations()->where('user_id', $attendeeId)->firstOrFail();
        $attendee->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully'
        ]);
    }

    public function deleteAttendee(Event $event, $attendeeId)
    {
        $event->registrations()->where('user_id', $attendeeId)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Attendee removed successfully'
        ]);
    }
}