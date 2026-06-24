<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        // Proteksi langsung di method — hanya admin
        abort_unless(auth()->user()->isAdmin(), 403);

        $query = ActivityLog::with('user')->latest();

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('model_type')) {
            $query->where('model_type', 'like', '%' . $request->model_type . '%');
        }

        if ($request->filled('tgl_dari')) {
            $query->whereDate('created_at', '>=', $request->tgl_dari);
        }

        if ($request->filled('tgl_sampai')) {
            $query->whereDate('created_at', '<=', $request->tgl_sampai);
        }

        if ($request->filled('search')) {
            $query->where('description', 'like', "%{$request->search}%");
        }

        $logs  = $query->paginate(30)->withQueryString();
        $users = User::orderBy('name')->get();

        return view('activity-log.index', compact('logs', 'users'));
    }

    public function show(ActivityLog $activityLog)
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        return view('activity-log.show', compact('activityLog'));
    }

    public function destroy(ActivityLog $activityLog)
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $activityLog->delete();
        return back()->with('success', 'Log berhasil dihapus.');
    }

    public function hapusSemua(Request $request)
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $query = ActivityLog::query();

        if (!$request->filled('tgl_sampai')) {
            $query->where('created_at', '<', now()->subDays(30));
        } else {
            $query->whereDate('created_at', '<=', $request->tgl_sampai);
        }

        $count = $query->count();
        $query->delete();

        return back()->with('success', "{$count} log berhasil dihapus.");
    }
}
