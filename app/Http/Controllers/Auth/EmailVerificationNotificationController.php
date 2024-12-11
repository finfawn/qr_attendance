<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        try {
            $request->user()->sendEmailVerificationNotification();
            Log::info('Verification email sent to: ' . $request->user()->email);
            
            return back()->with('status', 'verification-link-sent');
        } catch (\Exception $e) {
            Log::error('Failed to send verification email: ' . $e->getMessage());
            return back()->with('error', 'Failed to send verification email. Please try again later.');
        }
    }
}