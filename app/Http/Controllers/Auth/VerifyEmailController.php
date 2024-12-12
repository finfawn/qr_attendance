<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class VerifyEmailController extends Controller
{
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        try {
            // First, get the user and ensure they exist
            $user = User::findOrFail($request->route('id'));
            
            // Force login the user if they're not authenticated
            if (!auth()->check()) {
                auth()->login($user);
            }

            // Log the verification attempt with full URL for debugging
            Log::info('Email verification attempt', [
                'user_id' => $user->id,
                'email' => $user->email,
                'hash' => $request->route('hash'),
                'signature' => $request->query('signature'),
                'expires' => $request->query('expires'),
                'current_time' => now()->timestamp,
                'full_url' => $request->fullUrl(),
                'is_authenticated' => auth()->check()
            ]);

            // Check if signature is valid
            if (!$request->hasValidSignature()) {
                Log::error('Invalid signature for email verification');
                return redirect()->route('login')
                    ->with('error', 'The verification link has expired or is invalid.');
            }

            if ($user->hasVerifiedEmail()) {
                return $this->redirectToRole($user);
            }

            if ($user->markEmailAsVerified()) {
                event(new Verified($user));
                Log::info('Email verified successfully for user: ' . $user->email);
            }

            return $this->redirectToRole($user);

        } catch (\Exception $e) {
            Log::error('Email verification failed: ' . $e->getMessage());
            return redirect()->route('login')
                ->with('error', 'Email verification failed. Please try again.');
        }
    }

    /**
     * Redirect based on user role.
     */
    protected function redirectToRole($user): RedirectResponse
    {
        return match ($user->role) {
            'admin' => redirect()->route('admin.admin')->with('verified', true),
            'planner' => redirect()->route('planner.dashboard')->with('verified', true),
            'attendee' => redirect()->route('attendee.dashboard')->with('verified', true),
            default => redirect('/')->with('verified', true),
        };
    }
}
