<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Fetch notifications for the Division / End-User.
     * RIS status tracking was removed from the user side (requests are now
     * submitted as physical forms and encoded by staff), so this currently
     * returns an empty set. Kept as a stub so the header polling endpoint
     * still answers cleanly.
     */
    public function fetch(Request $request)
    {
        $user = auth()->user();

        // Failsafe: If no user is logged in, return empty immediately
        if (!$user) {
            return response()->json(['success' => false, 'notifications' => []]);
        }

        return response()->json(['success' => true, 'notifications' => []]);
    }
}