<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

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
        // Load relationships
        $event->load('attendanceSlots.registrations.user');

        // Get current time in the configured timezone
        $now = now()->timezone(config('app.timezone'));

        // Parse times for each slot
        $slots = $event->attendanceSlots->map(function ($slot) use ($now) {
            $startTime = \Carbon\Carbon::parse($slot->date . ' ' . $slot->start_time);
            $endTime = \Carbon\Carbon::parse($slot->date . ' ' . $slot->end_time);
            $absentTime = \Carbon\Carbon::parse($slot->date . ' ' . $slot->absent_time);

            return array_merge($slot->toArray(), [
                'startTime' => $startTime,
                'endTime' => $endTime,
                'absentTime' => $absentTime,
                'now' => $now
            ]);
        });

        return view('planner.events.manage-attendance', [
            'event' => $event,
            'slots' => $slots,
            'now' => $now
        ]);
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

/**
 * Show the reports for a specific event.
 *
 * @param \App\Models\Event $event
 * @return \Illuminate\View\View
 */
public function showReports(Event $event)
{
    // Ensure the user can access this event
    if ($event->planner_id !== auth()->id()) {
        abort(403, 'Unauthorized action.');
    }

    // Get all attendance slots for this event
    $attendanceSlots = $event->attendanceSlots()
        ->with(['attendances.registration.user'])
        ->get();

    // Initialize statistics arrays
    $stats = [
        'present_count' => 0,
        'late_count' => 0,
        'absent_count' => 0,
        'total_attendees' => $event->registrations()->count()
    ];

    // Initialize hierarchical statistics
    $hierarchicalStats = [];
    $trendData = [];

    // Process each attendance slot
    foreach ($attendanceSlots as $slot) {
        $slotDate = Carbon::parse($slot->date)->format('Y-m-d');
        if (!isset($trendData[$slotDate])) {
            $trendData[$slotDate] = ['present' => 0, 'late' => 0, 'absent' => 0];
        }

        foreach ($slot->attendances as $attendance) {
            $user = $attendance->registration->user;
            $course = $user->course;
            $year = $user->year;
            $section = $user->section;

            // Initialize course if not exists
            if (!isset($hierarchicalStats[$course])) {
                $hierarchicalStats[$course] = [
                    'years' => [],
                    'total' => [
                        'present' => 0,
                        'late' => 0,
                        'absent' => 0,
                        'total' => 0
                    ]
                ];
            }

            // Initialize year if not exists
            if (!isset($hierarchicalStats[$course]['years'][$year])) {
                $hierarchicalStats[$course]['years'][$year] = [
                    'sections' => [],
                    'total' => [
                        'present' => 0,
                        'late' => 0,
                        'absent' => 0,
                        'total' => 0
                    ]
                ];
            }

            // Initialize section if not exists
            if (!isset($hierarchicalStats[$course]['years'][$year]['sections'][$section])) {
                $hierarchicalStats[$course]['years'][$year]['sections'][$section] = [
                    'present' => 0,
                    'late' => 0,
                    'absent' => 0,
                    'total' => 0,
                    'students' => []
                ];
            }

            // Update statistics
            $stats[$attendance->status . '_count']++;
            $hierarchicalStats[$course]['total'][$attendance->status]++;
            $hierarchicalStats[$course]['total']['total']++;
            $hierarchicalStats[$course]['years'][$year]['total'][$attendance->status]++;
            $hierarchicalStats[$course]['years'][$year]['total']['total']++;
            $hierarchicalStats[$course]['years'][$year]['sections'][$section][$attendance->status]++;
            $hierarchicalStats[$course]['years'][$year]['sections'][$section]['total']++;

            // Track individual student performance
            if (!isset($hierarchicalStats[$course]['years'][$year]['sections'][$section]['students'][$user->id])) {
                $hierarchicalStats[$course]['years'][$year]['sections'][$section]['students'][$user->id] = [
                    'name' => $user->name,
                    'idno' => $user->idno,
                    'attendances' => []
                ];
            }

            $hierarchicalStats[$course]['years'][$year]['sections'][$section]['students'][$user->id]['attendances'][] = [
                'status' => $attendance->status,
                'date' => $slot->date,
                'time' => $attendance->scanned_at
            ];

            // Update trend data
            $trendData[$slotDate][$attendance->status]++;
        }
    }

    // Sort data
    ksort($hierarchicalStats);
    foreach ($hierarchicalStats as &$courseData) {
        ksort($courseData['years']);
        foreach ($courseData['years'] as &$yearData) {
            ksort($yearData['sections']);
        }
    }

    // Prepare chart data
    $chartData = [
        'courses' => [
            'labels' => array_keys($hierarchicalStats),
            'present' => array_column(array_column($hierarchicalStats, 'total'), 'present'),
            'late' => array_column(array_column($hierarchicalStats, 'total'), 'late'),
            'absent' => array_column(array_column($hierarchicalStats, 'total'), 'absent')
        ],
        'trends' => [
            'labels' => array_keys($trendData),
            'present' => array_column($trendData, 'present'),
            'late' => array_column($trendData, 'late'),
            'absent' => array_column($trendData, 'absent')
        ]
    ];

    // Calculate percentages
    $totalAttendances = $stats['present_count'] + $stats['late_count'] + $stats['absent_count'];
    $percentages = [
        'present' => $totalAttendances > 0 ? ($stats['present_count'] / $totalAttendances) * 100 : 0,
        'late' => $totalAttendances > 0 ? ($stats['late_count'] / $totalAttendances) * 100 : 0,
        'absent' => $totalAttendances > 0 ? ($stats['absent_count'] / $totalAttendances) * 100 : 0
    ];

    return view('planner.events.reports', compact(
        'event',
        'stats',
        'hierarchicalStats',
        'chartData',
        'percentages',
        'attendanceSlots'
    ));
}
}