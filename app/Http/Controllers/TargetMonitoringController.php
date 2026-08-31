<?php

namespace App\Http\Controllers;

use App\Models\Innovation;
use App\Models\ItRepairTicket;
use App\Models\TargetMonitoring;
use Illuminate\Http\Request;

class TargetMonitoringController extends Controller
{
    private const MONTHS = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];

    public function index(Request $request)
    {
        $year = $request->integer('year') ?: now()->year;
        $startMonth = max(1, min(12, $request->integer('start_month') ?: 1));
        $endMonth = max($startMonth, min(12, $request->integer('end_month') ?: 6));
        $months = collect(self::MONTHS)->slice($startMonth - 1, $endMonth - $startMonth + 1);
        $manualValues = TargetMonitoring::where('year', $year)->whereBetween('month', [$startMonth, $endMonth])->get()->keyBy(fn ($record) => $record->metric_key . '|' . $record->month);
        $slowRepairs = ItRepairTicket::whereNotNull('started_at')->whereNotNull('resolved_at')->whereYear('resolved_at', $year)->get()->filter(fn ($ticket) => $ticket->resolved_at->diffInMinutes($ticket->started_at) > 180)->groupBy(fn ($ticket) => $ticket->resolved_at->month)->map->count();
        $innovations = Innovation::whereYear('innovation_date', $year)->get()->groupBy(fn ($innovation) => $innovation->innovation_date->month)->map->count();

        $metrics = [
            ['key' => 'slow_repairs', 'label' => 'Meminimalkan kerusakan Hardware PC, Network, Print dan Juga Software', 'target' => '< 4 kasus / bulan', 'values' => $slowRepairs, 'manual' => false, 'achieved' => fn ($value) => $value < 4],
            ['key' => 'innovations', 'label' => 'Membuat Inovasi dalam Bidang IT', 'target' => 'Min. 2 / bulan', 'values' => $innovations, 'manual' => false, 'achieved' => fn ($value) => $value >= 2],
            ['key' => 'trouble_findings', 'label' => 'Temuan Trouble', 'target' => 'Max. 1 kasus / bulan', 'values' => $manualValues, 'manual' => true, 'achieved' => fn ($value) => $value <= 1],
            ['key' => 'energy_waste', 'label' => 'Mencegah Pemborosan Energi', 'target' => '0 kasus', 'values' => $manualValues, 'manual' => true, 'achieved' => fn ($value) => $value === 0],
        ];

        $monthOptions = self::MONTHS;

        return view('target_monitorings.index', compact('year', 'startMonth', 'endMonth', 'months', 'monthOptions', 'metrics', 'manualValues'));
    }

    public function updateManual(Request $request)
    {
        $data = $request->validate(['year' => 'required|integer|min:2000|max:2100', 'start_month' => 'required|integer|min:1|max:12', 'end_month' => 'required|integer|min:1|max:12', 'manual' => 'required|array']);
        foreach ($data['manual'] as $key => $months) {
            if (!in_array($key, ['trouble_findings', 'energy_waste'], true)) continue;
            foreach ($months as $month => $value) {
                TargetMonitoring::updateOrCreate(['year' => $data['year'], 'month' => $month, 'metric_key' => $key], ['value' => max(0, (int) ($value['value'] ?? 0)), 'notes' => $value['notes'] ?? null, 'updated_by_user_id' => auth()->id()]);
            }
        }
        return redirect()->route('target-monitorings.index', ['year' => $data['year'], 'start_month' => $data['start_month'], 'end_month' => $data['end_month']])->with('success', 'Data manual Pemantauan Sasaran berhasil disimpan.');
    }
}