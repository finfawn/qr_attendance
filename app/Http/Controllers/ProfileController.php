<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $departments = [
            'Computer Science',
            'Information Technology',
            'Engineering',
            'Business Administration',
            'Nursing'
        ];

        $years = [
            '1','2','3','4'
        ];
    
        // Ensure the QR code file exists
        $qrCodeExists = Storage::disk('public')->exists($user->qr_code);
        $qrCodePath = $qrCodeExists ? $user->qr_code_url : null;
    
        return view('profile.edit', [
            'user' => $user,
            'qrCodePath' => $qrCodePath,
            'courses' => $courses,
            'years' => $years,
        ]);
    }


    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        
        // Update the user's profile with new fields
        $user->fill([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'idno' => $request->input('idno'),
            'department' => $request->input('department'),
            'year' => $request->input('year'),
            'section' => $request->input('section'),
        ]);
    
        // If the email has changed, reset the email verification
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
    
        $user->save();
    
        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }
    

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
