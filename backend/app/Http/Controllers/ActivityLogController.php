<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityLog;

class ActivityLogController extends Controller
{
    public function index()
    {
        // Only admins can see this
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }
        
        $logs = ActivityLog::with('user')->latest()->paginate(50);
        return view('admin.activity-log', compact('logs'));
    }
}
