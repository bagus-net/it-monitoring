<?php

namespace App\Http\Controllers;

use App\Models\ChecklistItem;
use App\Models\MonthlySchedule;
use App\Models\Equipment;
use App\Models\MaintenanceSchedule;
use Illuminate\Http\Request;

class MonthlyScheduleController extends Controller
{
    // List monthly schedules grouped by Program Perawatan + Tahun
    public function index()
    {
        $rows = MonthlySchedule::with('checklistItem')->get();

        $groups = $rows->groupBy(fn($r) => $r->checklist_item_id . '|' . $r->year)
            ->map(function ($items) {
                $first = $items->first();
                return [
                    'checklist_item_id' => $first->checklist_item_id,
                    'checklist_item' => $first->checklistItem,
                    'year' => $first->year,
                    'equipment_count' => $items->pluck('equipment_id')->unique()->count(),
                    'months' => $items->pluck('month')->unique()->sort()->values()->toArray(),
                ];
            })
            ->sortByDesc('year')
            ->values();

        return view('monthly_schedules.index', compact('groups'));
    }

    // Show monthly schedule detail: all equipment x months for this program & year
    public function show($checklistItemId, $year)
    {
        $checklistItem = ChecklistItem::findOrFail($checklistItemId);

        $rows = MonthlySchedule::with('equipment')
            ->where('checklist_item_id', $checklistItemId)
            ->where('year', $year)
            ->get();

        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        // group per equipment, then per month
        $byEquipment = $rows->groupBy('equipment_id')->map(function ($items) {
            return [
                'equipment' => $items->first()->equipment,
                'months' => $items->keyBy('month'),
            ];
        })->sortBy(fn($g) => $g['equipment']->name ?? '');

        return view('monthly_schedules.show', compact('checklistItem', 'year', 'byEquipment', 'monthNames'));
    }

    // Create form - list annual schedules to pick from
    public function create()
    {
        $ids = MaintenanceSchedule::where('frequency', 'annual')
            ->select('checklist_item_id')
            ->groupBy('checklist_item_id')
            ->pluck('checklist_item_id');

        $annualSchedules = [];
        foreach ($ids as $id) {
            $rows = MaintenanceSchedule::where('checklist_item_id', $id)
                ->where('frequency', 'annual')
                ->get();

            $years = $rows->pluck('year')->filter()->unique()->values();
            if ($years->isEmpty()) {
                $years = collect([date('Y')]);
            }

            foreach ($years as $year) {
                $yearRows = $rows->filter(fn($r) => ($r->year ?? date('Y')) == $year);
                $months = $yearRows->pluck('month')->filter()->unique()->sort()->values()->toArray();
                $equipmentCount = $yearRows->pluck('equipment_id')->unique()->count();

                $annualSchedules[] = [
                    'checklist_item_id' => $id,
                    'year' => $year,
                    'months' => $months, // empty means all months
                    'equipment_count' => $equipmentCount,
                ];
            }
        }

        $items = ChecklistItem::whereIn('id', $ids)->get()->keyBy('id');

        return view('monthly_schedules.create', compact('annualSchedules', 'items'));
    }

    // Step 2: choose which months (from the annual schedule) to fill dates for
    public function selectMonths($checklistItemId, $year)
    {
        $checklistItem = ChecklistItem::findOrFail($checklistItemId);

        $months = MaintenanceSchedule::where('checklist_item_id', $checklistItemId)
            ->where('frequency', 'annual')
            ->where(function ($q) use ($year) {
                $q->whereNull('year')->orWhere('year', $year);
            })
            ->pluck('month')->filter()->unique()->sort()->values()->toArray();

        // no specific months recorded on the annual schedule => all months apply
        if (empty($months)) {
            $months = range(1, 12);
        }

        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        return view('monthly_schedules.select_months', compact('checklistItem', 'months', 'year', 'monthNames'));
    }

    // Get equipment for selected checklist item (AJAX)
    public function getEquipmentByChecklist(Request $request)
    {
        $checklistItemId = $request->query('checklist_item_id');
        $month = $request->query('month');
        $year = $request->query('year');

        // Get all equipment that have annual schedules for this checklist item
        $equipmentList = Equipment::whereHas('maintenanceSchedules', function ($q) use ($checklistItemId) {
            $q->where('checklist_item_id', $checklistItemId);
        })->get();

        // Check existing monthly schedules
        $existingSchedules = MonthlySchedule::where('checklist_item_id', $checklistItemId)
            ->where('month', $month)
            ->where('year', $year)
            ->get()
            ->keyBy('equipment_id');

        return response()->json([
            'equipment' => $equipmentList,
            'existing' => $existingSchedules
        ]);
    }

    // Form to select dates per equipment, one section per chosen month
    public function edit(Request $request, $checklistItemId, $year)
    {
        $checklistItem = ChecklistItem::findOrFail($checklistItemId);

        $months = $request->query('months', []);
        if (empty($months)) {
            $months = MaintenanceSchedule::where('checklist_item_id', $checklistItemId)
                ->where('frequency', 'annual')
                ->where(function ($q) use ($year) {
                    $q->whereNull('year')->orWhere('year', $year);
                })
                ->pluck('month')->filter()->unique()->sort()->values()->toArray();
            if (empty($months)) {
                $months = range(1, 12);
            }
        }
        sort($months);

        // Equipment tied to this checklist's annual schedule
        $equipment = Equipment::whereHas('maintenanceSchedules', function ($q) use ($checklistItemId) {
            $q->where('checklist_item_id', $checklistItemId);
        })->get();

        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $monthsData = [];
        foreach ($months as $month) {
            $monthlySchedules = MonthlySchedule::where('checklist_item_id', $checklistItemId)
                ->where('month', $month)
                ->where('year', $year)
                ->get()
                ->keyBy('equipment_id');

            $monthsData[$month] = [
                'name' => $monthNames[$month] ?? $month,
                'days_in_month' => cal_days_in_month(CAL_GREGORIAN, $month, $year),
                'schedules' => $monthlySchedules,
            ];
        }

        return view('monthly_schedules.edit', compact('checklistItem', 'equipment', 'monthsData', 'year'));
    }

    // Store monthly schedule (multiple months at once)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'checklist_item_id' => 'required|exists:checklist_items,id',
            'year' => 'required|integer|min:2000|max:2100',
            'equipment_dates' => 'required|array', // month => equipment_id => dates array
        ]);

        $checklistItemId = $validated['checklist_item_id'];
        $year = $validated['year'];

        foreach ($request->input('equipment_dates', []) as $month => $equipmentDates) {
            foreach ($equipmentDates as $equipmentId => $dates) {
                if (empty($dates)) {
                    continue; // Skip if no dates selected
                }

                $dateArray = array_map('intval', array_filter($dates, fn($d) => !empty($d)));

                if (empty($dateArray)) {
                    continue;
                }

                MonthlySchedule::updateOrCreate(
                    [
                        'checklist_item_id' => $checklistItemId,
                        'equipment_id' => $equipmentId,
                        'month' => $month,
                        'year' => $year,
                    ],
                    [
                        'dates' => $dateArray,
                    ]
                );
            }
        }

        return redirect()->route('monthly_schedules.edit', [$checklistItemId, $year])
            ->with('success', 'Jadwal bulanan berhasil disimpan');
    }

    // Delete all monthly schedules for a Program Perawatan + Tahun
    public function destroy($checklistItemId, $year)
    {
        MonthlySchedule::where('checklist_item_id', $checklistItemId)
            ->where('year', $year)
            ->delete();

        return redirect()->route('monthly_schedules.index')
            ->with('success', 'Jadwal bulanan berhasil dihapus');
    }
}
