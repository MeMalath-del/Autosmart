<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index(Request $request)
    {
        $query = AuditLog::with('user');

        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('model')) {
            $query->where('auditable_type', 'like', '%'.$request->model.'%');
        }

        $logs = $query->latest()->paginate(50);
        $events = AuditLog::distinct('event')->pluck('event');

        return view('admin.audit.index', compact('logs', 'events'));
    }

    public function show(AuditLog $log)
    {
        $log->load('user');

        return view('admin.audit.show', compact('log'));
    }
}
