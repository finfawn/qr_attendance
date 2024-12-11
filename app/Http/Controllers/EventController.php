<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function index()
    {
        // Fetch all events for the planner
        $events = Event::where('planner_id', auth()->id())->get();  

        // Pass the events to the view
        return view('planner.dashboard', compact('events'));
    }

    public function createEvent()
    {
        return view('planner.events.create-event');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'location' => 'nullable|string',
        ]);

        $event = Event::create([
            'title' => $request->title,
            'description' => $request->description,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'planner_id' => auth()->id(),
            'location' => $request->location,
            'status' => 'active',
        ]);

        // The event_code is automatically generated via the boot method
        // Generate QR code URL
        $qrCodeUrl = $event->getQrCodeUrl();

        return redirect()->route('planner.dashboard')
            ->with('success', 'Event created successfully.')
            ->with('qrCodeUrl', $qrCodeUrl);
    }

    public function show(Event $event)
    {
        if ($event->planner_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $event->load('registrations.user');
        
        return view('planner.events.show-event', [
            'event' => $event,
            'qrCodeUrl' => Storage::url($event->qr_code_path),
            'eventCode' => $event->event_code
        ]);
    }

    public function edit(Event $event)
    {
        if ($event->planner_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('planner.events.edit-event', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        if ($event->planner_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'location' => 'nullable|string',
            'status' => 'required|in:' . implode(',', array_keys(Event::$statuses)),
        ]);

        $event->update($request->all());
        return redirect()->route('events.show', $event)->with('success', 'Event updated successfully.');
    }

    public function destroy(Event $event)
    {
        if ($event->planner_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $event->delete();
        return redirect()->route('planner.dashboard')->with('success', 'Event deleted successfully.');
    }

    public function getPublicEvents()
    {
        $events = Event::where('status', 'active')
            ->select('id', 'title', 'description', 'date', 'start_time', 'end_time', 'location')
            ->orderBy('date')
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'date' => $event->date,
                    'time' => date('g:i A', strtotime($event->start_time)) . ' - ' . date('g:i A', strtotime($event->end_time)),
                    'location' => $event->location ?? 'TBA',
                    'description' => $event->description
                ];
            });

        return response()->json($events);
    }

    public function manageAttendance(Event $event)
    {
        $event->load('attendanceSlots.registrations.user');
        return view('planner.events.manage-attendance', compact('event'));
    }

    public function registerViaQr(Request $request)
{
    $request->validate([
        'event_code' => 'required|string|exists:events,event_code',
    ]);

    $event = Event::where('event_code', $request->event_code)->first();

    if (!$event) {
        return response()->json(['message' => 'Invalid event code.'], 404);
    }

    // Check if the user is already registered
    $user = auth()->user();
    $alreadyRegistered = $event->registrations()->where('user_id', $user->id)->exists();

    if ($alreadyRegistered) {
        return response()->json(['message' => 'You are already registered for this event.'], 400);
    }

    // Register the user for the event
    $event->registrations()->create([
        'user_id' => $user->id,
        'status' => 'pending',
    ]);

    return response()->json(['message' => 'Registered successfully. Please wait for the event planner to approve your registration.']);
}
}