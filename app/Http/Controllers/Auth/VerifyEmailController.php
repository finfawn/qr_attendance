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
            // Log the verification attempt with full URL for debugging
            Log::info('Email verification attempt', [
                'user_id' => $request->route('id'),
                'hash' => $request->route('hash'),
                'signature' => $request->query('signature'),
                'expires' => $request->query('expires'),
                'current_time' => now()->timestamp,
                'full_url' => $request->fullUrl()
            ]);

            // Check if signature is valid
            if (!$request->hasValidSignature()) {
                Log::error('Invalid signature for email verification', [
                    'expires' => $request->query('expires'),
                    'current_time' => now()->timestamp,
                    'difference' => $request->query('expires') - now()->timestamp
                ]);
                return redirect()->route('login')
                    ->with('error', 'The verification link has expired or is invalid.');
            }

            $user = User::find($request->route('id'));
            
            if (!$user) {
                throw new \Exception('User not found');
            }

            if (!auth()->check()) {
                auth()->login($user);
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
