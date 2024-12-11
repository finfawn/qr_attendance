<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\AccountSuspended;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function admin()
    {
        $planners = User::with(['events' => function($query) {
            $query->select('id', 'title', 'date', 'planner_id');
        }])->where('role', 'planner')->get()->groupBy('course');

        $attendees = User::where('role', 'attendee')
            ->get()
            ->groupBy('course');

        return view('admin.admin', compact('planners', 'attendees'));
    }

    public function deleteUser(User $user)
    {
        try {
            Mail::to($user->email)->send(new AccountSuspended());
            $user->delete();
            return redirect()->back()->with('success', 'User deleted successfully and notification sent.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error deleting user: ' . $e->getMessage());
        }
    }
}