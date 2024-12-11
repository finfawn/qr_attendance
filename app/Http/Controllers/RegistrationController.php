<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Models\Event;
use App\Models\Registration;
use App\Models\AttendanceSlot;
use App\Mail\AttendanceApproved;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Color\Color;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class RegistrationController extends Controller
{
    public function update(Request $request, Event $event, Registration $registration)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:pending,approved,rejected',
            ]);

            $oldStatus = $registration->status;
            $registration->update($validated);

            if ($validated['status'] === 'approved' && $oldStatus !== 'approved') {
                \Log::info('Starting approval process for registration: ' . $registration->id);
                
                try {
                    // Generate QR code
                    $registration->qr_code = $registration->generateQrCode();
                    $registration->save();
                    \Log::info('QR code generated and saved');

                    // Create QR code image
                    $qrCode = new QrCode($registration->qr_code,
                              encoding: new Encoding('UTF-8'),
                              errorCorrectionLevel: ErrorCorrectionLevel::High,
                              size: 300,
                              margin: 10);

                    $writer = new PngWriter();
                    $result = $writer->write($qrCode);
                    \Log::info('QR code image generated');

                    // Save QR code image
                    $qrCodePath = 'qr_codes/' . $registration->qr_code . '.png';
                    Storage::disk('public')->put($qrCodePath, $result->getString());
                    \Log::info('QR code image saved to: ' . $qrCodePath);

                    // Send email
                    \Log::info('Attempting to send email to: ' . $registration->user->email);
                    Mail::to($registration->user->email)
                        ->send(new AttendanceApproved($registration, Storage::url($qrCodePath)));
                    \Log::info('Email sent successfully');

                } catch (\Exception $inner) {
                    \Log::error('Inner exception: ' . $inner->getMessage());
                    throw $inner;
                }
            }

            return back()->with('success', 'Registration status updated successfully.');
        } catch (\Exception $e) {
            \Log::error('Registration update failed: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return back()->with('error', 'Failed to update registration status: ' . $e->getMessage());
        }
    }

    public function recordQrAttendance(Request $request)
    {
        try {
            $data = json_decode($request->qr_data);
            
            if (!$data || !isset($data->event_code)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid QR code'
                ], 400);
            }

            // Find the attendance record
            $registration = Registration::where('qr_code', $data->qr_code)
                ->whereHas('event', function($query) use ($data) {
                    $query->where('event_code', $data->event_code)
                          ->where('status', 'active');
                })
                ->first();

            if (!$registration) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid registration record or event'
                ], 404);
            }

            // Check if event is still active
            if ($registration->event->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'This event is no longer active'
                ], 400);
            }

            // Find the current attendance slot
            $currentSlot = $registration->event->attendanceSlots()
                ->where('start_time', '<=', Carbon::now()->format('H:i:s'))
                ->where('absent_time', '>', Carbon::now()->format('H:i:s'))
                ->first();

            if (!$currentSlot) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active attendance slot at this time'
                ], 400);
            }

            // Determine attendance status based on time
            $now = Carbon::now();
            $status = 'present';
            
            if ($now->format('H:i:s') > $currentSlot->end_time) {
                $status = 'late';
            }

            // Record the attendance
            $registration->update([
                'status' => $status,
                'scanned_at' => $now,
                'attendance_slot_id' => $currentSlot->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Attendance recorded successfully',
                'status' => $status
            ]);

        } catch (\Exception $e) {
            \Log::error('QR Attendance Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing attendance'
            ], 500);
        }
    }

    public function verifyQrCode($code)
    {
        try {
            $registration = Registration::where('event_code', $code)
                ->whereHas('event', function($query) {
                    $query->where('status', 'active');
                })
                ->first();

            if (!$registration) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired QR code'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'registration_id' => $registration->id,
                    'event_name' => $registration->event->title,
                    'attendee_name' => $registration->user->name
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('QR Verification Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while verifying QR code'
            ], 500);
        }
    }

    public function store(Request $request, Event $event)
    {
        $request->validate([
            'user_id' => [
                'required',
                'exists:users,id',
                Rule::unique('registrations')->where(function ($query) use ($event) {
                    return $query->where('event_id', $event->id);
                })
            ]
        ], [
            'user_id.unique' => 'This student is already registered for this event.'
        ]);

        $registration = $event->registrations()->create([
            'user_id' => $request->user_id,
            'status' => 'pending'
        ]);

        return redirect()->back()->with('success', 'Attendee added successfully.');
    }

    public function index()
    {
        $registration = Registration::all();
        return view('planner.events.attendanceindex', compact('registration'));
    }

    public function destroy(Event $event, Registration $registration)
    {
        try {
            $registration->delete();
            return back()->with('success', 'Attendance record deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('Failed to delete attendance: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete attendance record.');
        }
    }

    public function approveAll(Event $event)
    {
        try {
            $pendingRegistrations = Registration::where('event_id', $event->id)
                                              ->where('status', 'pending')
                                              ->get();

            foreach ($pendingRegistrations as $registration) {
                $oldStatus = $registration->status;
                $registration->status = 'approved';
                
                // Generate QR code
                $registration->qr_code = $registration->generateQrCode();
                $registration->save();

                // Create QR code image
                $qrCode = new QrCode($registration->qr_code,
                          encoding: new Encoding('UTF-8'),
                          errorCorrectionLevel: ErrorCorrectionLevel::High,
                          size: 300,
                          margin: 10);

                $writer = new PngWriter();
                $result = $writer->write($qrCode);

                // Save QR code image
                $qrPath = 'qrcodes/' . $registration->id . '.png';
                Storage::put('public/' . $qrPath, $result->getString());
                $registration->qr_image_path = $qrPath;
                $registration->save();

                // Send email notification
                if ($registration->user && $registration->user->email) {
                    Mail::to($registration->user->email)->queue(new AttendanceApproved($registration));
                }
            }

            return response()->json([
                'message' => count($pendingRegistrations) . ' registrations approved successfully',
                'approved_count' => count($pendingRegistrations)
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in bulk approval: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to approve registrations'], 500);
        }
    }
}
