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
        $courses = [
            'Bachelor of Science in Agriculture',
            'Bachelor of Science in Agribusiness', 
            'Bachelor of Arts in Communication', 
            'Bachelor of Arts in English Language', 
            'Bachelor of Arts in Filipino Language',
            'Bachelor of Science in Agricultural and Biosystems Engineering',
            'Bachelor of Science in Biological Sciences',
            'Bachelor of Science in Civil Engineering',
            'Bachelor of Science in Industrial Engineering',
            'Bachelor of Science in Electrical Engineering',
            'Bachelor of Science in Forestry',
            'Bachelor of Science in Computer Science',
            'Bachelor of Science in Information Technology',
            'Bachelor of Science in Hospitality Management',
            'Bachelor of Science in Tourism Management',
            'Bachelor of Science in Food Science and Technology',
            'Bachelor of Science in Entrepreneurship',
            'Bachelor of Science in Nutrition and Dietetics',
            'Bachelor of Science in Nursing',
            'Bachelor of Science in Midwifery',
            'Bachelor of Science in Physical Therapy',
            'Bachelor of Science in Occupational Therapy',
            'Bachelor of Science in Pharmacy',
            'Bachelor of Science in Medical Technology',
            'Bachelor of Science in Dentistry',
            'Bachelor of Science in Veterinary Medicine'
        ];

        $years = [
            '1','2','3','4'
        ];
    
        // Initialize QR code path
        $qrCodePath = null;
    
        // Only check storage if user has a QR code path
        if ($user->qr_code) {
            $qrCodeExists = Storage::disk('public')->exists($user->qr_code);
            $qrCodePath = $qrCodeExists ? $user->qr_code_url : null;
        }
    
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
            'course' => $request->input('course'),
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
