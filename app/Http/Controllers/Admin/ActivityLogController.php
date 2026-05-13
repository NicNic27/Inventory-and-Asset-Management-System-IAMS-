<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $query = ActivityLog::with('user');

        // Apply Search Filter (Search by description, action, or user's name/email)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQ) use ($search) {
                      $userQ->where('firstname', 'like', "%{$search}%")
                            ->orWhere('lastname', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Apply Action Filter Dropdown
        if ($request->filled('action_filter') && $request->action_filter !== 'All') {
            $query->where('action', $request->action_filter);
        }

        // Apply Date Filter
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Fetch paginated results, newest first
        $logs = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return view('admin.activity_logs.index', compact('logs', 'perPage'));
    }
}