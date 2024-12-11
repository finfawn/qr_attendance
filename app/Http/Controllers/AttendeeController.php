<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registration;
use Illuminate\Http\Request;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Illuminate\Support\Facades\Storage;

class AttendeeController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $currentEvent = $user->registrations()
            ->with(['event.attendanceSlots' => function($query) {
                $query->orderBy('date', 'asc')
                      ->orderBy('start_time', 'asc');
            }])
            ->where('status', 'approved')
            ->whereHas('event', function ($query) {
                $query->where('status', 'active');
            })
            ->latest()
            ->first();

        $currentSlot = null;
        $upcomingSlots = collect();
        $pastSlots = collect();
        $registration = null;

        if ($currentEvent && $currentEvent->event) {
            $now = now();
            $registration = $currentEvent;

            // Get current slot
            $currentSlot = $currentEvent->event->attendanceSlots()
                ->where('date', $now->toDateString())
                ->where('start_time', '<=', $now->format('H:i:s'))
                ->where('absent_time', '>=', $now->format('H:i:s'))
                ->first();

            // Get past slots
            $pastSlots = $currentEvent->event->attendanceSlots()
                ->where(function($query) use ($now) {
                    $query->where('date', '<', $now->toDateString())
                        ->orWhere(function($q) use ($now) {
                            $q->where('date', '=', $now->toDateString())
                                ->where('end_time', '<', $now->format('H:i:s'));
                        });
                })
                ->orderByDesc('date')
                ->orderByDesc('start_time')
                ->get();

            // Get upcoming slots
            $upcomingSlots = $currentEvent->event->attendanceSlots()
                ->where(function($query) use ($now) {
                    $query->where('date', '>', $now->toDateString())
                        ->orWhere(function($q) use ($now) {
                            $q->where('date', '=', $now->toDateString())
                                ->where('start_time', '>', $now->format('H:i:s'));
                        });
                })
                ->orderBy('date')
                ->orderBy('start_time')
                ->get();

            // Eager load attendance records for all slots
            $slotIds = collect([$currentSlot])
                ->merge($pastSlots)
                ->merge($upcomingSlots)
                ->filter()
                ->pluck('id');

            $attendanceRecords = \App\Models\Attendance::whereIn('attendance_slot_id', $slotIds)
                ->whereHas('registration', function($query) use ($user, $currentEvent) {
                    $query->where('user_id', $user->id)
                        ->where('event_id', $currentEvent->event->id);
                })
                ->get()
                ->keyBy('attendance_slot_id');
        }

        return view('attendee.dashboard', [
            'currentEvent' => $currentEvent?->event,
            'currentSlot' => $currentSlot,
            'upcomingSlots' => $upcomingSlots,
            'pastSlots' => $pastSlots,
            'registration' => $registration,
            'attendanceRecords' => $attendanceRecords ?? collect(),
        ]);
    }

    public function showDashboard()
   {
       $user = auth()->user();
       $currentSlot = AttendanceSlot::where('date', today())
           ->whereHas('registrations', function ($query) use ($user) {
               $query->where('user_id', $user->id);
           })
           ->first();

       $registration = $currentSlot ? $currentSlot->registrations()->where('user_id', $user->id)->first() : null;

       return view('attendee.dashboard', compact('currentSlot', 'registration'));
   }
}