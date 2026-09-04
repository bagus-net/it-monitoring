<?php

namespace App\Http\Controllers;

use App\Models\ChecklistItem;
use App\Models\MonthlySchedule;
use App\Models\Equipment;
use App\Models\MaintenanceSchedule;
use App\Models\User;
use Illuminate\Http\Request;

class MonthlyScheduleController extends Controller
{
    // List monthly schedules grouped by Program Perawatan + Tahun
    public function index(Request $request)
    {
        $selectedYear = $request->integer('year') ?: null;
        $selectedMonth = $request->integer('month') ?: null;
        $selectedProgram = $request->integer('checklist_item_id') ?: null;

        $annualScheduleRows = MaintenanceSchedule::query()
            ->where('frequency', 'annual')
            ->when($selectedProgram, fn ($query) => $query->where('checklist_item_id', $selectedProgram))
            ->when($selectedYear, fn ($query) => $query->where(fn ($yearQuery) => $yearQuery
                ->whereNull('year')
                ->orWhere('year', $selectedYear)))
            ->get(['checklist_item_id', 'year', 'month']);

        $availableYears = MonthlySchedule::query()
            ->select('year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->merge($annualScheduleRows->pluck('year')->filter())
            ->when($annualScheduleRows->contains(fn ($schedule) => is_null($schedule->year)), fn ($years) => $years->push(now()->year))
            ->unique()
            ->sortDesc()
            ->values();

        $programOptions = ChecklistItem::whereIn('id', MonthlySchedule::select('checklist_item_id')->distinct())
            ->orWhereIn('id', $annualScheduleRows->pluck('checklist_item_id')->unique())
            ->orderBy('title')
            ->get();

        $scheduledMonthsByGroup = MonthlySchedule::query()
            ->when($selectedYear, fn ($query) => $query->where('year', $selectedYear))
            ->when($selectedProgram, fn ($query) => $query->where('checklist_item_id', $selectedProgram))
            ->get(['checklist_item_id', 'year', 'month'])
            ->groupBy(fn ($schedule) => $schedule->checklist_item_id . '|' . $schedule->year)
            ->map(fn ($schedules) => $schedules->pluck('month')->unique()->sort()->values()->all());

        $annualSchedulesByProgram = $annualScheduleRows->groupBy('checklist_item_id');

        $rows = MonthlySchedule::with('checklistItem')
            ->when($selectedYear, fn ($query) => $query->where('year', $selectedYear))
            ->when($selectedMonth, fn ($query) => $query->where('month', $selectedMonth))
            ->when($selectedProgram, fn ($query) => $query->where('checklist_item_id', $selectedProgram))
            ->get();

        $monthShort = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ];

        $missingMonthlyPrograms = $annualScheduleRows
            ->groupBy(fn ($schedule) => $schedule->checklist_item_id . '|' . ($schedule->year ?? now()->year))
            ->map(function ($annualSchedules, $key) use ($scheduledMonthsByGroup, $monthShort, $selectedMonth) {
                [$checklistItemId, $year] = explode('|', $key);
                $requiredMonths = $annualSchedules->contains(fn ($schedule) => is_null($schedule->month))
                    ? range(1, 12)
                    : $annualSchedules->pluck('month')->filter()->map(fn ($month) => (int) $month)->unique()->sort()->values()->all();
                $scheduledMonths = $scheduledMonthsByGroup->get($key, []);
                $missingMonths = array_values(array_diff($requiredMonths, $scheduledMonths));

                if ($selectedMonth) {
                    $missingMonths = in_array($selectedMonth, $missingMonths, true) ? [$selectedMonth] : [];
                }

                return [
                    'checklist_item_id' => (int) $checklistItemId,
                    'year' => (int) $year,
                    'missing_month_labels' => array_map(fn ($month) => $monthShort[$month], $missingMonths),
                ];
            })
            ->filter(fn ($program) => count($program['missing_month_labels']) > 0)
            ->map(function ($program) {
                $program['checklist_item'] = ChecklistItem::find($program['checklist_item_id']);
                return $program;
            })
            ->filter(fn ($program) => $program['checklist_item'])
            ->sortBy(fn ($program) => $program['checklist_item']->title)
            ->values();

        $groups = $rows->groupBy(fn($r) => $r->checklist_item_id . '|' . $r->year)
            ->map(function ($items) use ($monthShort, $scheduledMonthsByGroup, $annualSchedulesByProgram) {
                $first = $items->first();
                $months = $items->pluck('month')->unique()->sort()->values()->toArray();
                $allScheduledMonths = $scheduledMonthsByGroup->get(
                    $first->checklist_item_id . '|' . $first->year,
                    []
                );
                $annualSchedules = $annualSchedulesByProgram->get($first->checklist_item_id, collect())
                    ->filter(fn ($schedule) => is_null($schedule->year) || (int) $schedule->year === (int) $first->year);
                $requiredMonths = $annualSchedules->contains(fn ($schedule) => is_null($schedule->month))
                    ? range(1, 12)
                    : $annualSchedules->pluck('month')->filter()->map(fn ($month) => (int) $month)->unique()->sort()->values()->all();
                $remainingMonths = array_values(array_diff($requiredMonths, $allScheduledMonths));

                return [
                    'checklist_item_id' => $first->checklist_item_id,
                    'checklist_item' => $first->checklistItem,
                    'year' => $first->year,
                    'equipment_count' => $items->pluck('equipment_id')->unique()->count(),
                    'months' => $months,
                    'month_labels' => array_map(fn ($m) => $monthShort[$m] ?? $m, $months),
                    'remaining_months' => $remainingMonths,
                    'remaining_month_labels' => array_map(fn ($m) => $monthShort[$m] ?? $m, $remainingMonths),
                ];
            })
            ->sortByDesc('year')
            ->values();

        $summary = [
            'program_count' => $groups->count(),
            'equipment_count' => $rows->pluck('equipment_id')->unique()->count(),
            'month_count' => $rows->pluck('month')->unique()->count(),
            'programs_missing_monthly_schedule' => $missingMonthlyPrograms->count(),
        ];

        return view('monthly_schedules.index', compact(
            'groups', 'availableYears', 'programOptions', 'selectedYear',
            'selectedMonth', 'selectedProgram', 'summary', 'missingMonthlyPrograms'
        ));
    }

    // Print all maintenance programs scheduled in one selected month and year.
    public function printMonth(Request $request)
    {
        $data = $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:2100',
        ]);

        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $scheduledDatesByProgram = MonthlySchedule::query()
            ->where('month', $data['month'])
            ->where('year', $data['year'])
            ->get(['checklist_item_id', 'dates'])
            ->groupBy('checklist_item_id')
            ->map(function ($items) {
                return $items->pluck('dates')
                        ->flatten()
                        ->map(fn ($day) => (int) $day)
                        ->unique()
                        ->sort()
                        ->values()
                        ->all();
            });

        $categoryOrder = [
            'Perawatan Software' => 1,
            'Perawatan Hardware' => 2,
            'Perawatan Networking' => 3,
        ];

        $programCategories = ChecklistItem::orderBy('sort_order')
            ->get()
            ->sortBy(function ($item) use ($categoryOrder) {
                $category = $item->category ?: 'Lainnya';
                return (($categoryOrder[$category] ?? 99) * 1000) + ($item->sort_order ?? 0);
            })
            ->groupBy(fn ($item) => $item->category ?: 'Lainnya')
            ->map(function ($items) use ($scheduledDatesByProgram) {
                return $items->map(fn ($item) => [
                    'title' => $item->title,
                    'color' => $item->schedule_color,
                    'dates' => $scheduledDatesByProgram->get($item->id, []),
                ]);
            });

        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $data['month'], $data['year']);

        $signatures = User::documentSignatories();

        return view('monthly_schedules.print_month', [
            'month' => $data['month'],
            'year' => $data['year'],
            'monthName' => $monthNames[$data['month']],
            'daysInMonth' => $daysInMonth,
            'programCategories' => $programCategories,
            'signatures' => $signatures,
            'signatureNames' => [
                'reporter' => $signatures['reporter']?->name ?? 'Bagus',
                'acknowledger' => $signatures['acknowledger']?->name ?? 'Arifin',
            ],
        ]);
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

        $scheduledMonths = $rows->pluck('month')->filter()->unique()->sort()->values()->all();
        $scheduledMonthLabels = array_map(fn ($month) => $monthNames[$month] ?? 'Bulan ' . $month, $scheduledMonths);

        // group per equipment, then per month
        $byEquipment = $rows->groupBy('equipment_id')->map(function ($items) {
            return [
                'equipment' => $items->first()->equipment,
                'months' => $items->keyBy('month'),
            ];
        })->sortBy(fn($g) => $g['equipment']->name ?? '');

        $signatures = User::documentSignatories();
        $signatureNames = [
            'reporter' => $signatures['reporter']?->name ?? 'Bagus',
            'acknowledger' => $signatures['acknowledger']?->name ?? 'Arifin',
        ];

        return view('monthly_schedules.show', compact(
            'checklistItem', 'year', 'byEquipment', 'monthNames', 'scheduledMonths', 'scheduledMonthLabels',
            'signatures', 'signatureNames'
        ));
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

        return view('monthly_schedules.edit', compact('checklistItem', 'equipment', 'monthsData', 'year', 'monthNames'))
            ->with('items', ChecklistItem::orderBy('title')->get());
    }

    // AJAX: dates already saved for a given program + year + month, used as a template source
    public function templateDates(Request $request)
    {
        $data = $request->validate([
            'checklist_item_id' => 'required|exists:checklist_items,id',
            'year' => 'required|integer|min:2000|max:2100',
            'month' => 'required|integer|min:1|max:12',
        ]);

        $dates = MonthlySchedule::where('checklist_item_id', $data['checklist_item_id'])
            ->where('year', $data['year'])
            ->where('month', $data['month'])
            ->get()
            ->mapWithKeys(fn ($schedule) => [$schedule->equipment_id => $schedule->dates ?? []]);

        return response()->json(['dates' => $dates]);
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
