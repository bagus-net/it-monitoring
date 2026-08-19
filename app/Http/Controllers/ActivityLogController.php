<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $module = $request->input('module');
        $action = $request->input('action');
        $search = trim((string) $request->input('search'));

        $logs = ActivityLog::query()
            ->when($module, fn ($query) => $query->where('module', $module))
            ->when($action, fn ($query) => $query->where('action', $action))
            ->when($search !== '', function ($query) use ($search) {
                $keyword = '%' . $search . '%';
                $query->where(function ($inner) use ($keyword) {
                    $inner->where('actor_name', 'like', $keyword)
                        ->orWhere('action', 'like', $keyword)
                        ->orWhere('module', 'like', $keyword)
                        ->orWhere('route_name', 'like', $keyword)
                        ->orWhere('url', 'like', $keyword)
                        ->orWhere('ip_address', 'like', $keyword);
                });
            })
            ->latest()
            ->paginate($this->resolvePerPage($request))
            ->withQueryString();
        $modules = ActivityLog::whereNotNull('module')->distinct()->orderBy('module')->pluck('module');
        $summary = [
            'today' => ActivityLog::whereDate('created_at', today())->count(),
            'create' => ActivityLog::where('action', 'Membuat data')->whereDate('created_at', today())->count(),
            'update' => ActivityLog::where('action', 'Memperbarui data')->whereDate('created_at', today())->count(),
            'delete' => ActivityLog::where('action', 'Menghapus data')->whereDate('created_at', today())->count(),
        ];

        return view('activity_logs.index', compact('logs', 'modules', 'module', 'action', 'summary', 'search'));
    }
}
