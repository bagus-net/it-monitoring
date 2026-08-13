<?php

namespace App\Services;

use App\Models\MonitoringLog;
use App\Models\Site;
use Illuminate\Support\Facades\Http;
use Throwable;

class SiteMonitorService
{
    /**
     * Cek satu situs: catat status, kode HTTP, dan response time-nya.
     */
    public function check(Site $site): MonitoringLog
    {
        $start = microtime(true);
        $status = 'DOWN';
        $statusCode = null;
        $message = null;

        try {
            $response = Http::timeout(10)
                ->withOptions(['allow_redirects' => true])
                ->get($site->url);

            $statusCode = $response->status();
            $status = ($response->successful() || $response->redirect()) ? 'UP' : 'DOWN';
        } catch (Throwable $e) {
            $status = 'ERROR';
            $message = substr($e->getMessage(), 0, 500);
        }

        $responseTimeMs = (int) round((microtime(true) - $start) * 1000);

        $log = $site->logs()->create([
            'status_code' => $statusCode,
            'response_time_ms' => $responseTimeMs,
            'status' => $status,
            'message' => $message,
        ]);

        $site->update([
            'last_status' => $status,
            'last_status_code' => $statusCode,
            'last_response_time_ms' => $responseTimeMs,
            'last_checked_at' => now(),
        ]);

        return $log;
    }

    /**
     * Cek semua situs yang statusnya aktif.
     */
    public function checkAll(): void
    {
        Site::active()->get()->each(fn (Site $site) => $this->check($site));
    }
}
