<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\AttendanceSlotController;
use App\Http\Controllers\EventRegistrationController;
use App\Http\Controllers\AttendeeController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

// Add this near the top of your routes file
if (str_contains(request()->getHost(), 'ngrok')) {
    \URL::forceScheme('https');
}

// Public route
Route::get('/', function () {
    return view('welcome');
});

// API Routes
Route::prefix('api')->group(function () {
    Route::get('/events/public', [EventController::class, 'getPublicEvents'])->name('events.public');
    // Add any other API routes here
});

// Routes for authenticated users who have verified their email
Route::middleware(['auth', 'verified'])->group(function () {
    // General route for authenticated users with verified email
    Route::get('/dashboard', function () {
        $user = auth()->user();

        // Redirect based on role
        return match ($user->role) {
            'admin' => redirect()->route('admin.admin'),
            'planner' => redirect()->route('planner.dashboard'),
            'attendee' => redirect()->route('attendee.dashboard'),
            default => abort(403, 'Unauthorized access'),
        };
    })->name('dashboard');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin routes
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'admin'])->name('admin.admin');
        Route::post('/events/{event}/registrations/approve-all', [RegistrationController::class, 'approveAll'])
            ->name('registrations.approve-all');
        Route::delete('/admin/users/{user}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
    });

    // Planner routes
    Route::middleware('role:planner')->prefix('planner')->group(function () {
        Route::get('/dashboard', [EventController::class, 'index'])->name('planner.dashboard');
        
        // Event routes
        Route::get('/events/create', [EventController::class, 'createEvent'])->name('events.create-event');
        Route::post('/events', [EventController::class, 'store'])->name('events.store');
        Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
        Route::get('/events/{event}/edit', [EventController::class, 'edit'])->name('events.edit');
        Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
        Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');
    
        // Attendance routes
        Route::prefix('events/{event}/registration')->group(function () {
            Route::get('/', [RegistrationController::class, 'index'])->name('attendanceindex');
            Route::post('/', [RegistrationController::class, 'store'])->name('registration.store');
            Route::post('/qr-scan', [RegistrationController::class, 'processQRCode'])->name('registration.qr-scan');
            Route::post('/{registration}/approve', [RegistrationController::class, 'approveAttendance'])
                ->name('registration.approve');
            Route::post('/{registration}/update', [RegistrationController::class, 'update'])->name('registration.update');
            Route::post('/{registration}/destroy', [RegistrationController::class, 'destroy'])->name('registration.destroy');
        });

        
        // Attendance Slot routes
        Route::prefix('events/{event}/attendance-slots')->group(function () {
            Route::get('/create', [AttendanceSlotController::class, 'create'])
                ->name('events.attendance-slots.create');
            Route::post('/', [AttendanceSlotController::class, 'store'])
                ->name('events.attendance-slots.store');
            Route::put('/{attendance_slot}', [AttendanceSlotController::class, 'update'])
                ->name('events.attendance-slots.update');
            Route::delete('/{attendance_slot}', [AttendanceSlotController::class, 'destroy'])
                ->name('events.attendance-slots.destroy');
            
            // Keep other attendance slot routes
            Route::get('/', [AttendanceSlotController::class, 'index'])
                ->name('events.attendance-slots.index');
            Route::get('/{attendance_slot}', [AttendanceSlotController::class, 'show'])
                ->name('events.attendance-slots.show');
            Route::get('/{attendance_slot}/edit', [AttendanceSlotController::class, 'edit'])
                ->name('events.attendance-slots.edit');
            Route::post('/{attendance_slot}/scan', [AttendanceSlotController::class, 'scan'])
                ->name('events.attendance-slots.scan');
            Route::put('/{attendance_slot}/registrations/{registration}/status', [AttendanceSlotController::class, 'updateAttendanceStatus'])
                ->name('events.attendance-slots.update-status');
        });

            Route::get('/events/{event}/manage-attendance', [EventController::class, 'manageAttendance'])
            ->name('events.manage-attendance');
    });

    // QR Code Verification Route (accessible by verified users)
    Route::get('/registration/verify/{code}', [RegistrationController::class, 'verifyQrCode'])
        ->name('registration.verify');

    // Attendee routes
    Route::middleware('role:attendee')->group(function () {
        Route::get('/attendee/dashboard', [AttendeeController::class, 'dashboard'])
            ->name('attendee.dashboard');
        Route::post('/events/register', [EventRegistrationController::class, 'register'])
            ->name('events.register');
        });
});

    // Additional authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/registration/record', [RegistrationController::class, 'recordQrAttendance'])
        ->name('registration.record-qr');
    Route::post('/events/register-via-qr', [EventRegistrationController::class, 'registerViaQr'])
        ->name('events.register-via-qr');
});

Route::get('/planner/events/{event}/attendance-slots/{attendance_slot}/count', [AttendanceSlotController::class, 'getAttendeeCount'])
    ->name('events.attendance-slots.count');

Route::get('/planner/events/{event}/reports', [EventController::class, 'showReports'])
    ->name('events.reports');

require __DIR__ . '/auth.php';
