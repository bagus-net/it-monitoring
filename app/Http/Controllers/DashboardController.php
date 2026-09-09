<?php

namespace App\Http\Controllers;

use App\Models\MonitoringLog;
use App\Models\Site;
use App\Models\Equipment;
use App\Models\ItRepairTicket;
use App\Models\MaintenanceChecklist;
use App\Models\WebMonitoringChecklist;
use App\Models\EquipmentTransfer;
use App\Models\InkType;
use App\Models\InkTransaction;
use App\Models\SparepartType;
use App\Models\SparepartTransaction;
use App\Models\LicenseType;
use App\Models\LicenseTransaction;
use App\Services\SiteMonitorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $periodOptions = [3 => '3 Bulan', 6 => '6 Bulan', 12 => '12 Bulan'];
        $selectedPeriod = (int) $request->input('period', 6);
        $selectedPeriod = array_key_exists($selectedPeriod, $periodOptions) ? $selectedPeriod : 6;
        $selectedYear = (int) $request->input('year', now()->year);
        $selectedYear = $selectedYear >= 2000 && $selectedYear <= 2100 ? $selectedYear : now()->year;
        $selectedMonth = (int) $request->input('month', now()->month);
        $selectedMonth = $selectedMonth >= 1 && $selectedMonth <= 12 ? $selectedMonth : now()->month;
        $periodEnd = now()->setDate($selectedYear, $selectedMonth, 1)->endOfMonth();
        $periodStart = $periodEnd->copy()->startOfMonth()->subMonths($selectedPeriod - 1);

        $overview = [
            'assets' => Equipment::count(),
            'assetAttention' => Equipment::whereIn('condition', ['rusak', 'perbaikan'])->count(),
            'ticketsOpen' => ItRepairTicket::whereIn('status', ['open', 'in_progress'])->count(),
            'ticketsUrgent' => ItRepairTicket::whereIn('priority', ['high', 'urgent'])->whereIn('status', ['open', 'in_progress'])->count(),
            'webChecklistMonth' => WebMonitoringChecklist::whereYear('checked_at', now()->year)->whereMonth('checked_at', now()->month)->count(),
            'maintenanceChecklistMonth' => MaintenanceChecklist::whereYear('checked_at', now()->year)->whereMonth('checked_at', now()->month)->count(),
            'sites' => Site::count(),
            'sitesDown' => Site::whereIn('last_status', ['DOWN', 'ERROR'])->count(),
            'transfersPending' => EquipmentTransfer::whereIn('status', ['pending_approval', 'approved'])->count(),
            'inkLowStock' => InkType::whereColumn('current_stock', '<=', 'minimum_stock')->count(),
            'sparepartLowStock' => SparepartType::whereColumn('current_stock', '<=', 'minimum_stock')->count(),
            'licenseExpiring' => LicenseType::whereNotNull('expiry_date')->whereBetween('expiry_date', [now()->toDateString(), now()->addDays(30)->toDateString()])->count(),
            'licenseAvailableSeats' => (int) LicenseType::sum(DB::raw('total_seats - used_seats')),
        ];

        $assetStatus = [
            'Normal' => max(0, $overview['assets'] - $overview['assetAttention']),
            'Perlu Perhatian' => $overview['assetAttention'],
            'Tiket Aktif' => $overview['ticketsOpen'],
        ];
        $months = collect();
        $cursor = $periodEnd->copy()->startOfMonth();
        $limitDate = $periodStart->copy()->startOfMonth();

        while ($cursor->greaterThanOrEqualTo($limitDate)) {
            $months->push([
                'key' => $cursor->format('Y-m'),
                'label' => $cursor->translatedFormat('M Y'),
            ]);
            $cursor->subMonth();
        }

        $months = $months->reverse()->values();
        $monthKeys = $months->pluck('key');
        $monthlyCount = function (string $model, string $column = 'created_at') use ($periodStart, $periodEnd) {
            return $model::query()->whereBetween($column, [$periodStart, $periodEnd])->get()->groupBy(fn ($item) => $item->{$column}->format('Y-m'))->map->count();
        };
        $dashboardTrend = $months->map(fn ($month) => [
            'label' => $month['label'],
            'tickets' => $monthlyCount(ItRepairTicket::class, 'reported_at')->get($month['key'], 0),
            'checklists' => $monthlyCount(MaintenanceChecklist::class, 'checked_at')->get($month['key'], 0) + $monthlyCount(WebMonitoringChecklist::class, 'checked_at')->get($month['key'], 0),
            'stock' => $monthlyCount(InkTransaction::class, 'transaction_date')->get($month['key'], 0) + $monthlyCount(SparepartTransaction::class, 'transaction_date')->get($month['key'], 0),
            'licenses' => $monthlyCount(LicenseTransaction::class, 'transaction_date')->get($month['key'], 0),
        ])->values();

        $recentTickets = ItRepairTicket::with('equipment')->latest('reported_at')->limit(5)->get();
        $recentWebChecklists = WebMonitoringChecklist::with('site')->latest('checked_at')->limit(4)->get();
        $recentMaintenanceChecklists = MaintenanceChecklist::with('checklistItem')->latest('checked_at')->limit(4)->get();

        $yearOptions = range(now()->year - 5, now()->year + 1);
        return view('dashboard', compact('overview', 'assetStatus', 'dashboardTrend', 'recentTickets', 'recentWebChecklists', 'recentMaintenanceChecklists', 'periodOptions', 'selectedPeriod', 'selectedYear', 'selectedMonth', 'yearOptions'));
    }

    public function monitoring(): View
    {
        return view('web_monitoring');
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

    public function crypto(): JsonResponse
    {
        try {
            $prices = Cache::remember('dashboard.crypto.prices', now()->addSeconds(20), function () {
                $response = Http::acceptJson()->timeout(8)->get('https://api.coingecko.com/api/v3/simple/price', [
                    'ids' => 'bitcoin,ethereum',
                    'vs_currencies' => 'usd',
                    'include_24hr_change' => 'true',
                ]);

                $response->throw();

                return $response->json();
            });

            return response()->json([
                'prices' => collect(['bitcoin', 'ethereum'])->mapWithKeys(fn ($coin) => [$coin => [
                    'usd' => data_get($prices, $coin . '.usd'),
                    'change24h' => data_get($prices, $coin . '.usd_24h_change'),
                ]]),
                'updatedAt' => now()->toIso8601String(),
            ]);
        } catch (\Throwable $exception) {
            logger()->warning('Crypto price update failed: ' . $exception->getMessage());

            return response()->json(['message' => 'Harga crypto tidak tersedia saat ini.'], 503);
        }
    }

    public function gold(): JsonResponse
    {
        $fallbackPrice = 2610000;

        try {
            $price = Cache::remember('dashboard.gold.antam.1g', now()->addMinute(), function () {
                $html = Http::withUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/126 Safari/537.36')
                    ->withHeaders(['Accept' => 'text/html,application/xhtml+xml'])
                    ->timeout(15)
                    ->retry(2, 300)
                    ->get('https://www.logammulia.com/id/harga-emas-hari-ini')
                    ->throw()
                    ->body();
                preg_match_all('/<tr[^>]*>(.*?)<\/tr>/is', $html, $rows);

                foreach ($rows[1] as $row) {
                    $text = preg_replace('/\s+/', ' ', trim(strip_tags($row)));
                    if (!preg_match('/\b1\s*gr\b/i', $text)) {
                        continue;
                    }

                    preg_match_all('/\d[\d.,]*/', $text, $numbers);
                    $prices = collect($numbers[0] ?? [])
                        ->map(fn ($number) => (int) preg_replace('/[^0-9]/', '', $number))
                        ->filter(fn ($number) => $number >= 1000000)
                        ->values();

                    if ($prices->isNotEmpty()) {
                        return $prices->first();
                    }
                }

                $text = preg_replace('/\s+/', ' ', strip_tags($html));
                if (preg_match('/\b1\s*gr\b.*?([0-9]{1,3}(?:[.,][0-9]{3})+)/i', $text, $match)) {
                    return (int) preg_replace('/[^0-9]/', '', $match[1]);
                }

                throw new \RuntimeException('Harga ANTAM 1 gram tidak ditemukan.');
            });

            abort_if($price <= 0, 503, 'Harga gold tidak tersedia.');

            return response()->json([
                'symbol' => 'ANTAM 1 gr',
                'price' => $price,
                'currency' => 'IDR',
                'source' => 'Logam Mulia ANTAM',
                'isFallback' => false,
                'updatedAt' => now()->toIso8601String(),
            ]);
        } catch (\Throwable $exception) {
            logger()->warning('Gold price update failed: ' . $exception->getMessage());

            return response()->json([
                'symbol' => 'ANTAM 1 gr',
                'price' => $fallbackPrice,
                'currency' => 'IDR',
                'source' => 'Harga terakhir ANTAM',
                'isFallback' => true,
                'updatedAt' => now()->toIso8601String(),
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
