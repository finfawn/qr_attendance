<?php

namespace App\Http\Controllers;

use App\Models\AttendanceSlot;
use App\Models\Event;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceSlotController extends Controller
{
    public function index(Event $event)
    {
        $slots = $event->attendanceSlots()->get();
        return view('planner.events.manage-attendance', compact('slots', 'event'));
    }

    public function create(Event $event)
    {
        return view('planner.events.create-attendance-slot', compact('event'));
    }

    public function store(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'absent_time' => [
                'required',
                'after:end_time',
                function ($attribute, $value, $fail) use ($request) {
                    $endTime = Carbon::parse($request->date . ' ' . $request->end_time);
                    $absentTime = Carbon::parse($request->date . ' ' . $value);
                    
                    if ($absentTime->lte($endTime)) {
                        $fail('The Cutoff time must be after the End time.');
                    }
                },
            ]
        ], [
            'end_time.after' => 'End time must be after the Start time.',
            'absent_time.after' => 'Cutoff time must be after the End time.'
        ]);

        // Custom validation for time sequence
        $startTime = strtotime($request->start_time);
        $endTime = strtotime($request->end_time);
        
        if ($endTime < $startTime) {
            return back()
                ->withInput()
                ->withErrors(['end_time' => 'End time cannot be before start time']);
        }

        $slot = $event->attendanceSlots()->create($validated);

        return redirect()->route('events.attendance-slots.index', $event)
            ->with('success', 'Attendance slot created successfully.');
    }

    public function show(Event $event, AttendanceSlot $attendanceSlot)
    {
        // Load event registrations with users
        $registrations = $event->registrations()
            ->with('user')
            ->get()
            ->map(function ($registration) use ($attendanceSlot) {
                // Get attendance for this specific slot
                $attendance = $attendanceSlot->attendances()
                    ->where('registration_id', $registration->id)
                    ->first();
                
                $registration->attendance = $attendance;
                return $registration;
            });
        
        return view('planner.events.slot-details', [
            'event' => $event,
            'slot' => $attendanceSlot,
            'registrations' => $registrations
        ]);
    }

    public function edit(Event $event, AttendanceSlot $attendance_slot)
    {
        return view('planner.events.edit-attendance-slot', [
            'event' => $event,
            'slot' => $attendance_slot
        ]);
    }

    public function update(Request $request, Event $event, AttendanceSlot $attendance_slot)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'absent_time' => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    $absentTime = Carbon::parse($request->date . ' ' . $value);
                    $startTime = Carbon::parse($request->date . ' ' . $request->start_time);
                    $endTime = Carbon::parse($request->date . ' ' . $request->end_time);

                    if ($absentTime->lte($startTime) || $absentTime->gte($endTime)) {
                        $fail('The absent time must be between start time and end time.');
                    }
                },
            ]
        ]);

        // Custom validation for time sequence
        $startTime = strtotime($request->start_time);
        $endTime = strtotime($request->end_time);
        
        if ($endTime < $startTime) {
            return back()
                ->withInput()
                ->withErrors(['end_time' => 'End time cannot be before start time']);
        }

        $attendance_slot->update($validated);

        return redirect()->route('events.attendance-slots.show', ['event' => $event, 'attendance_slot' => $attendance_slot])
            ->with('success', 'Attendance slot updated successfully.');
    }

    public function destroy(Event $event, AttendanceSlot $attendance_slot)
    {
        try {
            $attendance_slot->delete();

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Attendance slot deleted successfully',
                    'redirect' => route('events.attendance-slots.index', $event)
                ]);
            }

            return redirect()->route('events.attendance-slots.index', $event)
                ->with('success', 'Attendance slot deleted successfully');
        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete attendance slot'
                ], 500);
            }

            return back()->with('error', 'Failed to delete attendance slot');
        }
    }

    public function scan(Request $request, Event $event, AttendanceSlot $attendance_slot)
    {
        try {
            // Get current time with proper timezone
            $now = now()->timezone(config('app.timezone', 'Asia/Manila'));
            $startTime = Carbon::parse($attendance_slot->date . ' ' . $attendance_slot->start_time)
                ->timezone(config('app.timezone', 'Asia/Manila'));
            $absentTime = Carbon::parse($attendance_slot->date . ' ' . $attendance_slot->absent_time)
                ->timezone(config('app.timezone', 'Asia/Manila'));

            // Check if scanning is allowed at current time
            if ($now->lt($startTime)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Attendance recording has not started yet. Please wait until ' . $startTime->format('h:i A')
                ], 400);
            }

            if ($now->gt($absentTime)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Attendance recording has ended. Cutoff time was ' . $absentTime->format('h:i A')
                ], 400);
            }

            // Rest of your existing validation code
            $qrCode = $request->input('qr_code');
            
            // Log the incoming request for debugging
            \Log::info('QR Scan Request', [
                'event_id' => $event->id,
                'slot_id' => $attendance_slot->id,
                'qr_data' => $qrCode
            ]);

            // Parse QR code data
            if (!is_array($qrCode) || !isset($qrCode['user_id'])) {
                \Log::error('Invalid QR Data', ['qr_data' => $qrCode]);
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid QR code data structure'
                ], 400);
            }

            // Find the registration using registration_id from QR code
            $registration = Registration::where('id', $qrCode['registration_id'])
                ->where('event_id', $event->id)
                ->first();

            if (!$registration) {
                \Log::error('Registration not found', [
                    'registration_id' => $qrCode['registration_id'],
                    'event_id' => $event->id
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Student is not registered for this event'
                ], 400);
            }

            // Check if attendance already exists
            $existingAttendance = $attendance_slot->attendances()
                ->where('registration_id', $registration->id)
                ->first();

            if ($existingAttendance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Attendance already recorded for this slot'
                ], 400);
            }

            // Get user details
            $user = $registration->user;
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 400);
            }

            // Determine attendance status based on time windows
            $endTime = Carbon::parse($attendance_slot->date . ' ' . $attendance_slot->end_time)
                ->timezone(config('app.timezone', 'Asia/Manila'));

            if ($now->between($startTime, $endTime)) {
                $status = 'present';
            } elseif ($now->between($endTime, $absentTime)) {
                $status = 'late';
            } else {
                $status = 'absent';
            }

            // Record attendance
            $attendance = $attendance_slot->attendances()->create([
                'registration_id' => $registration->id,
                'status' => $status,
                'scanned_at' => $now
            ]);

            return response()->json([
                'success' => true,
                'status' => $status,
                'attendee' => [
                    'name' => $user->name,
                    'idno' => $user->idno,
                    'course' => $user->course,
                    'year' => $user->year,
                    'section' => $user->section
                ],
                'message' => $this->getStatusMessage($status)
            ]);

        } catch (\Exception $e) {
            \Log::error('QR Scan Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to record attendance: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getStatusMessage($status)
    {
        switch ($status) {
            case 'present':
                return 'Present! Scanned within regular time (Start - End time).';
            case 'late':
                return 'Late - Scanned between End time and Cutoff time.';
            case 'absent':
                return 'Absent - Scanned after the Cutoff time.';
            default:
                return 'Attendance status recorded.';
        }
    }

    public function details(Event $event, AttendanceSlot $attendance_slot)
    {
        $event->load('registrations.user');
        $attendance_slot->load('attendances');
        
        return view('planner.events.slot-details', [
            'event' => $event,
            'slot' => $attendance_slot
        ]);
    }

    public function getAttendeeCount(Event $event, AttendanceSlot $attendance_slot)
    {
        return response()->json([
            'count' => $attendance_slot->attendances()->count()
        ]);
    }
}
