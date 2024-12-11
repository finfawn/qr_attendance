<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $courses = ['Bachelor of Science in Agriculture',
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
                    'Bachelor of Science in Veterinary Medicine',
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
                    'Bachelor of Science in Veterinary Medicine',
                    'Bachelor of Science in Agriculture',
                    'Bachelor of Science in Agribusiness',
                    'Bachelor of Arts in Communication',
                    'Bachelor of Arts in English Language',
                    'Bachelor of Arts in Filipino Language',]; // Example list of courses
        $years = [1, 2, 3, 4]; // Example list of years
    
        return view('auth.register', [
            'courses' => $courses,
            'years' => $years,
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:attendee,planner'], // Ensure role is either 'attendee' or 'planner'
            'idno' => ['required', 'regex:/^\d{4}-\d{2}-\d{4}$/'], // Format: YYYY-MM-DDDDD 
            'course' => ['required', 'string', 'max:255'], // Required for all roles
            'year' => ['required', 'integer', 'between:1,4'], // Required for all roles
            'section' => ['required', 'string', 'max:10'],   // Required for all roles
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'idno' => $request->idno,
            'course' => $request->course,
            'year' => $request->year,
            'section' => $request->section,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('login')
            ->with('success', 'Registration successful! Please login to verify your email.');
    }
}