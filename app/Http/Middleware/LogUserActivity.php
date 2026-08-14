<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;

class LogUserActivity
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (!in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true) || $response->getStatusCode() >= 400) {
            return $response;
        }

        $route = $request->route();
        $routeName = $route?->getName();
        if (!$routeName || str_contains($routeName, 'notifications')) {
            return $response;
        }

        $user = $request->user();
        $action = match ($request->method()) {
            'POST' => 'Membuat data',
            'PUT', 'PATCH' => 'Memperbarui data',
            'DELETE' => 'Menghapus data',
        };

        ActivityLog::create([
            'user_id' => $user?->id,
            'actor_name' => $user?->name ?? $request->session()->get('actor_name', 'Pengguna Web'),
            'action' => $action,
            'module' => $this->moduleFromRoute($routeName),
            'route_name' => $routeName,
            'method' => $request->method(),
            'url' => $request->path(),
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
            'metadata' => [
                'parameters' => array_keys($request->except(['_token', '_method', 'password', 'photo', 'error_photo', 'repair_attachment'])),
            ],
        ]);

        return $response;
    }

    private function moduleFromRoute(string $routeName): string
    {
        return match (true) {
            str_starts_with($routeName, 'equipments.') => 'Peralatan IT',
            str_starts_with($routeName, 'it-repair-tickets.') => 'Perbaikan IT',
            str_starts_with($routeName, 'maintenance-checklists.') => 'Pelaksanaan Checklist IT',
            str_starts_with($routeName, 'web-monitoring-checklists.') => 'Checklist Web Monitoring',
            str_starts_with($routeName, 'maintenances.') => 'Jadwal & Perawatan',
            str_starts_with($routeName, 'monthly_schedules.') => 'Jadwal Bulanan',
            str_starts_with($routeName, 'masters.') => 'Master Data',
            str_starts_with($routeName, 'sites.') => 'Web Monitoring',
            default => 'Sistem',
        };
    }
}
