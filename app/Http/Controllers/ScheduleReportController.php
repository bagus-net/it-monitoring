<?php

namespace App\Http\Controllers;

use App\Models\ChecklistItem;
use App\Models\Equipment;
use App\Models\MaintenanceLog;
use App\Models\MaintenanceSchedule;
use App\Models\MonthlySchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScheduleReportController extends Controller
{
    private const MONTH_NAMES = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    public function annual(Request $request)
    {
        $year = (int) $request->input('year', date('Y'));
        $checklistItemId = $request->input('checklist_item_id');
        $equipmentId = $request->input('equipment_id');

        $equipmentQuery = Equipment::orderBy('name');
        if ($equipmentId) {
            $equipmentQuery->whereKey($equipmentId);
        }
        $equipments = $equipmentQuery->get()->keyBy('id');

        $scheduleQuery = MaintenanceSchedule::with('checklistItem')
            ->where('frequency', 'annual')
            ->where('year', $year);
        if ($checklistItemId) {
            $scheduleQuery->where('checklist_item_id', $checklistItemId);
        }
        if ($equipmentId) {
            $scheduleQuery->where(function ($query) use ($equipmentId) {
                $query->whereNull('equipment_id')->orWhere('equipment_id', $equipmentId);
            });
        }
        $schedules = $scheduleQuery->get();

        $matrix = [];
        foreach ($schedules as $schedule) {
            $targetEquipmentIds = $schedule->equipment_id ? [$schedule->equipment_id] : $equipments->keys()->all();
            $months = $schedule->month ? [(int) $schedule->month] : range(1, 12);
            $weeks = $schedule->week_of_month ? [(int) $schedule->week_of_month] : range(1, 4);

            foreach ($targetEquipmentIds as $targetEquipmentId) {
                if (!$equipments->has($targetEquipmentId)) {
                    continue;
                }
                $key = $targetEquipmentId . '|' . $schedule->checklist_item_id;
                if (!isset($matrix[$key])) {
                    $matrix[$key] = [
                        'equipment' => $equipments->get($targetEquipmentId),
                        'checklist_item' => $schedule->checklistItem,
                        'weeks' => [],
                    ];
                }

                foreach ($months as $month) {
                    foreach ($weeks as $week) {
                        $matrix[$key]['weeks'][$month][$week] = true;
                    }
                }
            }
        }

        $summary = ['programs' => 0, 'equipments' => 0, 'planned_weeks' => 0];
        $programIds = [];
        $equipmentIds = [];
        foreach ($matrix as $row) {
            $programIds[$row['checklist_item']->id] = true;
            $equipmentIds[$row['equipment']->id] = true;
            foreach ($row['weeks'] as $weeks) {
                $summary['planned_weeks'] += count($weeks);
            }
        }
        $summary['programs'] = count($programIds);
        $summary['equipments'] = count($equipmentIds);

        $items = ChecklistItem::orderBy('title')->get();
        $equipmentOptions = Equipment::orderBy('name')->get();
        $periodLabel = 'Tahun ' . $year;
        $byProgram = collect($matrix)
            ->groupBy(fn ($row) => $row['checklist_item']->id)
            ->map(function ($rows) {
                return [
                    'checklist_item' => $rows->first()['checklist_item'],
                    'rows' => $rows->sortBy(fn ($row) => $row['equipment']->name ?? '')->values(),
                ];
            })
            ->sortBy(fn ($group) => $group['checklist_item']->title ?? '')
            ->values();

        return view('reports.annual', compact('year', 'checklistItemId', 'equipmentId', 'items', 'equipmentOptions', 'byProgram', 'summary', 'periodLabel'));
    }

    public function monthly(Request $request)
    {
        $year = (int) $request->input('year', date('Y'));
        $checklistItemId = $request->input('checklist_item_id');
        $equipmentId = $request->input('equipment_id');

        $schedules = MonthlySchedule::with(['checklistItem', 'equipment'])
            ->where('year', $year)
            ->when($checklistItemId, fn ($query) => $query->where('checklist_item_id', $checklistItemId))
            ->when($equipmentId, fn ($query) => $query->where('equipment_id', $equipmentId))
            ->get()
            ->sortBy(fn ($schedule) => ($schedule->checklistItem->title ?? '') . '|' . ($schedule->equipment->name ?? ''));

        $matrix = [];
        $programIds = [];
        $equipmentIds = [];
        $totalDates = 0;
        foreach ($schedules as $schedule) {
            $key = $schedule->checklist_item_id . '|' . $schedule->equipment_id;
            if (!isset($matrix[$key])) {
                $matrix[$key] = [
                    'checklist_item' => $schedule->checklistItem,
                    'equipment' => $schedule->equipment,
                    'months' => [],
                ];
            }
            $matrix[$key]['months'][$schedule->month] = $schedule->dates ?? [];
            $programIds[$schedule->checklist_item_id] = true;
            $equipmentIds[$schedule->equipment_id] = true;
            $totalDates += count($schedule->dates ?? []);
        }

        $items = ChecklistItem::orderBy('title')->get();
        $equipmentOptions = Equipment::orderBy('name')->get();
        $monthNames = self::MONTH_NAMES;
        $periodLabel = 'Tahun ' . $year;
        $summary = [
            'programs' => count($programIds),
            'equipments' => count($equipmentIds),
            'dates' => $totalDates,
        ];
        $byProgram = collect($matrix)
            ->groupBy(fn ($row) => $row['checklist_item']->id)
            ->map(function ($rows) {
                return [
                    'checklist_item' => $rows->first()['checklist_item'],
                    'rows' => $rows->sortBy(fn ($row) => $row['equipment']->name ?? '')->values(),
                ];
            })
            ->sortBy(fn ($group) => $group['checklist_item']->title ?? '')
            ->values();

        return view('reports.monthly', compact('year', 'checklistItemId', 'equipmentId', 'items', 'equipmentOptions', 'monthNames', 'byProgram', 'summary', 'periodLabel'));
    }
}
