<?php

namespace App\Http\Controllers;

use App\Models\MonitoringLog;
use App\Models\Site;
use App\Services\SiteMonitorService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('dashboard');
    }

    public function data(): JsonResponse
    {
        try {
            return response()->json($this->buildPayload());
        } catch (\Exception $e) {
            // log and return an empty safe payload so pages can still render
            logger()->error('Dashboard data error: ' . $e->getMessage());
            return response()->json([
                'summary' => ['totalSites' => 0, 'upSites' => 0, 'downSites' => 0, 'avgResponse' => null],
                'sites' => [],
                'recentLogs' => [],
                'generatedAt' => now()->toIso8601String(),
            ]);
        }
    }

    public function checkNow(SiteMonitorService $monitor): JsonResponse
    {
        $monitor->checkAll();

        return response()->json($this->buildPayload());
    }

    private function buildPayload(): array
    {
        $sites = Site::orderBy('name')->get()->map(function (Site $site) {
            $history = $site->recentLogs(50)->map(fn ($log) => [
                't' => $log->created_at->timestamp * 1000,
                'code' => $log->status_code,
                'ms' => $log->response_time_ms,
                'status' => $log->status,
            ]);

            return [
                'id' => $site->id,
                'name' => $site->name,
                'url' => $site->url,
                'active' => $site->is_active,
                'lastStatus' => $site->last_status,
                'lastCode' => $site->last_status_code,
                'lastResponseMs' => $site->last_response_time_ms,
                'lastChecked' => optional($site->last_checked_at)->toIso8601String(),
                'history' => $history,
                'uptimePct' => $site->uptimePercentage(50),
            ];
        });

        $recentLogs = MonitoringLog::with('site')
            ->latest()
            ->limit(30)
            ->get()
            ->map(fn ($log) => [
                't' => $log->created_at->timestamp * 1000,
                'name' => optional($log->site)->name ?? '(situs dihapus)',
                'code' => $log->status_code,
                'ms' => $log->response_time_ms,
                'status' => $log->status,
            ]);

        $responseValues = $sites->pluck('lastResponseMs')->filter();

        return [
            'summary' => [
                'totalSites' => $sites->count(),
                'upSites' => $sites->where('lastStatus', 'UP')->count(),
                'downSites' => $sites->whereIn('lastStatus', ['DOWN', 'ERROR'])->count(),
                'avgResponse' => $responseValues->isNotEmpty() ? (int) round($responseValues->avg()) : null,
            ],
            'sites' => $sites->values(),
            'recentLogs' => $recentLogs,
            'generatedAt' => now()->toIso8601String(),
        ];
    }
}
