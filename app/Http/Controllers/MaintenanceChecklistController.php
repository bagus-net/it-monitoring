<?php

namespace App\Http\Controllers;

use App\Models\ChecklistItem;
use App\Models\Equipment;
use App\Models\MaintenanceChecklist;
use App\Models\MaintenanceSchedule;
use App\Models\MonthlySchedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class MaintenanceChecklistController extends Controller
{
    private const MONTH_NAMES = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    public function notifications(): JsonResponse
    {
        $user = auth()->user();
        $latest = null;
        $pendingApprovalCount = 0;

        if ($user && ($user->isMaster() || $user->isAdminIt())) {
            $latest = MaintenanceChecklist::with('checklistItem')
                ->whereNull('acknowledged_at')
                ->latest('created_at')
                ->first();

            $pendingApprovalCount = MaintenanceChecklist::whereNull('acknowledged_at')->count();
        }

        return response()->json([
            'pendingApprovalCount' => $pendingApprovalCount,
            'latest' => $latest ? [
                'id' => $latest->id,
                'program' => $latest->checklistItem?->title ?: 'Checklist Perawatan',
                'period' => (self::MONTH_NAMES[$latest->month] ?? $latest->month) . ' ' . $latest->year,
            ] : null,
        ]);
    }

    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $selectedYear = $request->integer('year') ?: null;
        $selectedMonth = $request->integer('month') ?: null;
        $selectedProgram = $request->integer('checklist_item_id') ?: null;
        $selectedApproval = $request->input('approval');
        $selectedApproval = in_array($selectedApproval, ['approved', 'pending'], true) ? $selectedApproval : null;

        $availableYears = MonthlySchedule::select('year')->distinct()->orderByDesc('year')->pluck('year');
        $programOptions = ChecklistItem::orderBy('title')->get();

        $filteredChecklists = MaintenanceChecklist::query()
            ->when($search !== '', function ($query) use ($search) {
                $keyword = '%' . $search . '%';
                $query->where(function ($inner) use ($keyword) {
                    $inner->where('reported_by', 'like', $keyword)
                        ->orWhere('acknowledged_by', 'like', $keyword)
                        ->orWhere('notes', 'like', $keyword)
                        ->orWhereHas('checklistItem', fn ($relation) => $relation->where('title', 'like', $keyword))
                        ->orWhereHas('entries', fn ($relation) => $relation->where('remarks', 'like', $keyword)
                            ->orWhereHas('equipment', fn ($equipment) => $equipment->where('name', 'like', $keyword)));
                });
            })
            ->when($selectedYear, fn ($query) => $query->where('year', $selectedYear))
            ->when($selectedMonth, fn ($query) => $query->where('month', $selectedMonth))
            ->when($selectedProgram, fn ($query) => $query->where('checklist_item_id', $selectedProgram))
            ->when($selectedApproval === 'approved', fn ($query) => $query->whereNotNull('acknowledged_at'))
            ->when($selectedApproval === 'pending', fn ($query) => $query->whereNull('acknowledged_at'));

        $summaryChecklists = (clone $filteredChecklists)->with('entries')->get();
        $checklists = $filteredChecklists->with(['checklistItem', 'entries.equipment'])
            ->orderByDesc('checked_at')
            ->paginate($this->resolvePerPage($request))
            ->withQueryString();

        $displayedChecklists = $checklists->getCollection();
        $scheduledEquipmentByPeriod = MonthlySchedule::query()
            ->whereIn('checklist_item_id', $displayedChecklists->pluck('checklist_item_id')->unique())
            ->whereIn('year', $displayedChecklists->pluck('year')->unique())
            ->whereIn('month', $displayedChecklists->pluck('month')->unique())
            ->get(['checklist_item_id', 'year', 'month', 'equipment_id', 'dates'])
            ->groupBy(fn ($schedule) => $schedule->checklist_item_id . '|' . $schedule->year . '|' . $schedule->month)
            ->map(fn ($schedules) => [
                'equipment_ids' => $schedules->pluck('equipment_id')->unique(),
                'dates' => $schedules->pluck('dates')->flatten()->map(fn ($day) => (int) $day)->unique()->sort()->values(),
            ]);

        $displayedChecklists->each(function ($checklist) use ($scheduledEquipmentByPeriod) {
            $periodKey = $checklist->checklist_item_id . '|' . $checklist->year . '|' . $checklist->month;
            $periodSchedule = $scheduledEquipmentByPeriod->get($periodKey, [
                'equipment_ids' => collect(),
                'dates' => collect(),
            ]);
            $scheduledEquipmentIds = $periodSchedule['equipment_ids'];
            $checkedEquipmentIds = $checklist->entries->pluck('equipment_id')->unique();
            $checkedScheduledEquipmentCount = $checkedEquipmentIds->intersect($scheduledEquipmentIds)->count();
            $isComplete = $scheduledEquipmentIds->isNotEmpty() && $checkedScheduledEquipmentCount === $scheduledEquipmentIds->count();

            $checklist->setAttribute('scheduled_equipment_count', $scheduledEquipmentIds->count());
            $checklist->setAttribute('checked_scheduled_equipment_count', $checkedScheduledEquipmentCount);
            $checklist->setAttribute('scheduled_dates', $periodSchedule['dates']->all());
            $checklist->setAttribute('is_complete', $isComplete);
            $checklist->setAttribute('overall_result', $isComplete
                ? ($checklist->entries->contains('result', 'not_ok') ? 'not_ok' : 'ok')
                : null);
        });
        $entries = $summaryChecklists->flatMap(fn ($checklist) => $checklist->entries);
        $summary = [
            'documents' => $summaryChecklists->count(),
            'ok' => $entries->where('result', 'ok')->count(),
            'not_ok' => $entries->where('result', 'not_ok')->count(),
        ];

        $scheduledChecklistPeriods = MonthlySchedule::query()
            ->when($selectedYear, fn ($query) => $query->where('year', $selectedYear))
            ->when($selectedMonth, fn ($query) => $query->where('month', $selectedMonth))
            ->when($selectedProgram, fn ($query) => $query->where('checklist_item_id', $selectedProgram))
            ->get(['checklist_item_id', 'year', 'month'])
            ->unique(fn ($schedule) => $schedule->checklist_item_id . '|' . $schedule->year . '|' . $schedule->month);

        $completedChecklistPeriods = MaintenanceChecklist::query()
            ->when($selectedYear, fn ($query) => $query->where('year', $selectedYear))
            ->when($selectedMonth, fn ($query) => $query->where('month', $selectedMonth))
            ->when($selectedProgram, fn ($query) => $query->where('checklist_item_id', $selectedProgram))
            ->get(['checklist_item_id', 'year', 'month'])
            ->map(fn ($checklist) => $checklist->checklist_item_id . '|' . $checklist->year . '|' . $checklist->month)
            ->unique();

        $scheduledChecklistKeys = $scheduledChecklistPeriods
            ->map(fn ($schedule) => $schedule->checklist_item_id . '|' . $schedule->year . '|' . $schedule->month);

        $scheduleProgress = [
            'scheduled' => $scheduledChecklistKeys->count(),
            'completed' => $scheduledChecklistKeys->intersect($completedChecklistPeriods)->count(),
            'pending' => $scheduledChecklistKeys->diff($completedChecklistPeriods)->count(),
        ];

        return view('maintenance_checklists.index', compact(
            'checklists', 'summary', 'scheduleProgress', 'search',
            'availableYears', 'programOptions', 'selectedYear', 'selectedMonth',
            'selectedProgram', 'selectedApproval'
        ));
    }

    public function bulkPdf(Request $request)
    {
        $year = $request->integer('year') ?: null;
        $month = $request->integer('month') ?: null;
        $program = $request->integer('checklist_item_id') ?: null;
        $approval = in_array($request->input('approval'), ['approved', 'pending'], true) ? $request->input('approval') : null;
        $search = trim((string) $request->input('search'));

        $checklists = MaintenanceChecklist::with(['checklistItem', 'entries.equipment'])
            ->when($year, fn ($query) => $query->where('year', $year))
            ->when($month, fn ($query) => $query->where('month', $month))
            ->when($program, fn ($query) => $query->where('checklist_item_id', $program))
            ->when($approval === 'approved', fn ($query) => $query->whereNotNull('acknowledged_at'))
            ->when($approval === 'pending', fn ($query) => $query->whereNull('acknowledged_at'))
            ->when($search !== '', function ($query) use ($search) {
                $keyword = '%' . $search . '%';
                $query->where(function ($inner) use ($keyword) {
                    $inner->where('reported_by', 'like', $keyword)
                        ->orWhere('notes', 'like', $keyword)
                        ->orWhereHas('checklistItem', fn ($relation) => $relation->where('title', 'like', $keyword));
                });
            })
            ->orderByDesc('checked_at')
            ->get();

        $documents = $checklists->map(function ($checklist) {
            $scheduledDates = $this->scheduledDatesByEquipment($checklist->checklist_item_id, $checklist->year, $checklist->month);

            return [
                'id' => $checklist->id,
                'program' => $checklist->checklistItem?->title ?: 'Checklist Perawatan',
                'year' => $checklist->year,
                'month' => self::MONTH_NAMES[$checklist->month] ?? $checklist->month,
                'checkedAt' => $checklist->checked_at?->format('d M Y') ?: '-',
                'reportedBy' => $checklist->reported_by ?: '-',
                'approvedBy' => $checklist->acknowledged_at ? ($checklist->acknowledged_by ?: '-') : 'Menunggu persetujuan',
                'approved' => (bool) $checklist->acknowledged_at,
                'notes' => $checklist->notes ?: '-',
                'entries' => $checklist->entries->sortBy(fn ($entry) => $entry->equipment?->name)->values()->map(fn ($entry) => [
                    'name' => $entry->equipment?->name ?: '-',
                    'assetTag' => $entry->equipment?->asset_tag ?: ($entry->equipment?->serial_number ?: '-'),
                    'dates' => count($scheduledDates->get($entry->equipment_id, [])) ? implode(', ', $scheduledDates->get($entry->equipment_id)) : '-',
                    'result' => $entry->result === 'ok' ? 'OK' : 'NOT OK',
                    'remarks' => $entry->remarks ?: '-',
                ])->all(),
            ];
        })->values();

        return response()->json(['documents' => $documents]);
    }

    public function bulkPrint(Request $request)
    {
        $year = $request->integer('year') ?: null;
        $month = $request->integer('month') ?: null;
        $program = $request->integer('checklist_item_id') ?: null;
        $approval = in_array($request->input('approval'), ['approved', 'pending'], true) ? $request->input('approval') : null;
        $search = trim((string) $request->input('search'));
        $checklists = MaintenanceChecklist::with(['checklistItem', 'entries.equipment', 'reporter', 'acknowledger'])
            ->when($year, fn ($query) => $query->where('year', $year))
            ->when($month, fn ($query) => $query->where('month', $month))
            ->when($program, fn ($query) => $query->where('checklist_item_id', $program))
            ->when($approval === 'approved', fn ($query) => $query->whereNotNull('acknowledged_at'))
            ->when($approval === 'pending', fn ($query) => $query->whereNull('acknowledged_at'))
            ->when($search !== '', fn ($query) => $query->where(fn ($inner) => $inner
                ->where('reported_by', 'like', '%' . $search . '%')
                ->orWhere('notes', 'like', '%' . $search . '%')
                ->orWhereHas('checklistItem', fn ($relation) => $relation->where('title', 'like', '%' . $search . '%'))))
            ->orderByDesc('checked_at')
            ->get();

        $documents = $checklists->map(function ($checklist) {
            $scheduledDates = $this->scheduledDatesByEquipment($checklist->checklist_item_id, $checklist->year, $checklist->month);
            return [
                'checklist' => $checklist,
                'monthName' => self::MONTH_NAMES[$checklist->month] ?? $checklist->month,
                'scheduledDates' => $scheduledDates,
                'summary' => [
                    'total' => $checklist->entries->count(),
                    'ok' => $checklist->entries->where('result', 'ok')->count(),
                    'not_ok' => $checklist->entries->where('result', 'not_ok')->count(),
                ],
            ];
        });

        return view('maintenance_checklists.bulk_print', compact('documents'));
    }

    public function create(Request $request)
    {
        $items = ChecklistItem::orderBy('title')->get();
        $checklistItemId = $request->integer('checklist_item_id');
        $equipmentId = $request->integer('equipment_id') ?: null;
        $year = $request->integer('year') ?: (int) date('Y');
        $month = $request->integer('month') ?: (int) date('n');
        $monthlyScheduleGroups = MonthlySchedule::with('checklistItem')
            ->where('year', $year)
            ->get(['checklist_item_id', 'year', 'month'])
            ->groupBy('checklist_item_id')
            ->map(function ($schedules) {
                $checklistItem = $schedules->first()->checklistItem;
                $completedMonths = MaintenanceChecklist::where('checklist_item_id', $checklistItem->id)
                    ->where('year', $schedules->first()->year)
                    ->pluck('month')
                    ->all();
                $year = (int) $schedules->first()->year;
                $scheduleDates = $schedules->pluck('month')->unique()->mapWithKeys(function ($month) use ($checklistItem, $year) {
                    return [$month => $this->scheduledDatesByEquipment($checklistItem->id, $year, (int) $month)
                        ->flatten()
                        ->map(fn ($day) => (int) $day)
                        ->filter(fn ($day) => $day > 0)
                        ->unique()
                        ->sort()
                        ->values()
                        ->all()];
                })->all();

                return [
                    'item' => $checklistItem,
                    'months' => $schedules->pluck('month')->unique()->sort()->values(),
                    'schedule_dates' => $scheduleDates,
                    'completed_months' => $completedMonths,
                ];
            })
            ->filter(fn ($group) => $group['item'])
            ->sortBy(fn ($group) => $group['item']->title)
            ->values();
        $equipment = $checklistItemId
            ? ($equipmentId ? Equipment::whereKey($equipmentId)->get() : $this->scheduledEquipment($checklistItemId, $year, $month))
            : collect();
        $monthNames = self::MONTH_NAMES;
        $scheduledDatesByEquipment = $checklistItemId
            ? $this->scheduledDatesByEquipment($checklistItemId, $year, $month)
            : collect();

        return view('maintenance_checklists.create', compact(
            'items', 'checklistItemId', 'equipmentId', 'year', 'month', 'equipment',
            'monthNames', 'scheduledDatesByEquipment', 'monthlyScheduleGroups'
        ));
    }

    public function store(Request $request)
    {
        $data = $this->validateChecklist($request);

        if (MaintenanceChecklist::where('checklist_item_id', $data['checklist_item_id'])
            ->where('year', $data['year'])
            ->where('month', $data['month'])
            ->exists()) {
            return back()->withInput()->withErrors([
                'month' => 'Checklist untuk Program Perawatan dan periode ini sudah dibuat.',
            ]);
        }

        DB::transaction(function () use ($data) {
            $checklist = MaintenanceChecklist::create([
                'checklist_item_id' => $data['checklist_item_id'],
                'year' => $data['year'],
                'month' => $data['month'],
                'checked_at' => $data['checked_at'],
                'reported_by' => auth()->user()->name,
                'reported_by_user_id' => auth()->id(),
                'notes' => $data['notes'] ?? null,
            ]);

            $this->syncEntries($checklist, $data['entries']);
        });

        return redirect()->route('maintenance-checklists.index')->with('success', 'Checklist perawatan berhasil disimpan.');
    }

    public function show(MaintenanceChecklist $maintenanceChecklist)
    {
        $maintenanceChecklist->load(['checklistItem', 'entries.equipment', 'reporter', 'acknowledger']);
        $monthNames = self::MONTH_NAMES;
        $scheduledDatesByEquipment = $this->scheduledDatesByEquipment(
            $maintenanceChecklist->checklist_item_id,
            $maintenanceChecklist->year,
            $maintenanceChecklist->month
        );
        $summary = [
            'total' => $maintenanceChecklist->entries->count(),
            'ok' => $maintenanceChecklist->entries->where('result', 'ok')->count(),
            'not_ok' => $maintenanceChecklist->entries->where('result', 'not_ok')->count(),
        ];

        $reporterUser = User::where(function ($query) {
            $query->where('name', 'like', '%bagus%')
                ->orWhere('name', 'like', '%admin it%')
                ->orWhere('name', 'like', '%adminit%');
        })->first();

        $acknowledgerUser = User::where('name', 'like', '%arifin%')->first();

        $signatureNames = [
            'reporter' => $reporterUser?->name ?? 'Admin IT / Bagus',
            'acknowledger' => $acknowledgerUser?->name ?? 'Arifin',
        ];

        $signatures = [
            'reporter' => $reporterUser ?? $maintenanceChecklist->reporter,
            'acknowledger' => $acknowledgerUser ?? $maintenanceChecklist->acknowledger,
        ];

        return view('maintenance_checklists.show', compact(
            'maintenanceChecklist', 'monthNames', 'scheduledDatesByEquipment', 'summary',
            'signatures', 'signatureNames'
        ));
    }

    public function edit(MaintenanceChecklist $maintenanceChecklist)
    {
        $maintenanceChecklist->load(['entries.equipment']);
        $items = ChecklistItem::orderBy('title')->get();
        $equipment = $maintenanceChecklist->entries->pluck('equipment')->filter()->sortBy('name')->values();
        $entryIndex = $maintenanceChecklist->entries->keyBy('equipment_id');
        $monthNames = self::MONTH_NAMES;
        $scheduledDatesByEquipment = $this->scheduledDatesByEquipment(
            $maintenanceChecklist->checklist_item_id,
            $maintenanceChecklist->year,
            $maintenanceChecklist->month
        );

        return view('maintenance_checklists.edit', compact(
            'maintenanceChecklist', 'items', 'equipment', 'entryIndex', 'monthNames',
            'scheduledDatesByEquipment'
        ));
    }

    public function update(Request $request, MaintenanceChecklist $maintenanceChecklist)
    {
        $data = $this->validateChecklist($request);

        DB::transaction(function () use ($maintenanceChecklist, $data) {
            $maintenanceChecklist->update([
                'checklist_item_id' => $data['checklist_item_id'],
                'year' => $data['year'],
                'month' => $data['month'],
                'checked_at' => $data['checked_at'],
                'notes' => $data['notes'] ?? null,
            ]);

            $maintenanceChecklist->entries()->delete();
            $this->syncEntries($maintenanceChecklist, $data['entries']);
        });

        return redirect()->route('maintenance-checklists.show', $maintenanceChecklist)->with('success', 'Checklist perawatan berhasil diperbarui.');
    }

    public function approve(Request $request, MaintenanceChecklist $maintenanceChecklist)
    {
        abort_unless(auth()->user()->isMaster(), 403);

        $data = $request->validate([
            'acknowledged_at' => 'nullable|date',
        ]);

        $maintenanceChecklist->update([
            'acknowledged_by' => auth()->user()->name,
            'acknowledged_by_user_id' => auth()->id(),
            'acknowledged_at' => $data['acknowledged_at'] ?? now(),
        ]);

        return redirect()->route('maintenance-checklists.show', $maintenanceChecklist)->with('success', 'Checklist disetujui.');
    }

    public function destroy(Request $request, MaintenanceChecklist $maintenanceChecklist)
    {
        $maintenanceChecklist->delete();

        return redirect()->route('maintenance-checklists.index', $request->only([
            'year', 'month', 'checklist_item_id', 'approval', 'search'
        ]))->with('success', 'Checklist perawatan berhasil dihapus. Jadwal pada periode ini perlu di-check ulang.');
    }

    private function validateChecklist(Request $request): array
    {
        return $request->validate([
            'checklist_item_id' => 'required|exists:checklist_items,id',
            'year' => 'required|integer|min:2000|max:2100',
            'month' => 'required|integer|min:1|max:12',
            'checked_at' => 'required|date',
            'notes' => 'nullable|string',
            'entries' => 'required|array|min:1',
            'entries.*.equipment_id' => 'required|exists:equipments,id',
            'entries.*.result' => 'required|in:ok,not_ok',
            'entries.*.remarks' => 'nullable|string',
        ]);
    }

    private function syncEntries(MaintenanceChecklist $checklist, array $entries): void
    {
        foreach ($entries as $entry) {
            $checklist->entries()->create([
                'equipment_id' => $entry['equipment_id'],
                'result' => $entry['result'],
                'remarks' => $entry['remarks'] ?? null,
            ]);
        }
    }

    private function scheduledEquipment(int $checklistItemId, int $year, int $month)
    {
        $equipmentIds = MonthlySchedule::where('checklist_item_id', $checklistItemId)
            ->where('year', $year)
            ->where('month', $month)
            ->pluck('equipment_id')
            ->unique()
            ->values();

        if ($equipmentIds->isNotEmpty()) {
            return Equipment::whereIn('id', $equipmentIds)->orderBy('name')->get();
        }

        $annualSchedules = MaintenanceSchedule::where('checklist_item_id', $checklistItemId)
            ->where('frequency', 'annual')
            ->where('year', $year)
            ->where(function ($query) use ($month) {
                $query->whereNull('month')->orWhere('month', $month);
            })
            ->get();

        if ($annualSchedules->isEmpty()) {
            return collect();
        }

        if ($annualSchedules->contains(fn ($schedule) => is_null($schedule->equipment_id))) {
            return Equipment::orderBy('name')->get();
        }

        return Equipment::whereIn('id', $annualSchedules->pluck('equipment_id')->unique())->orderBy('name')->get();
    }

    private function scheduledDatesByEquipment(int $checklistItemId, int $year, int $month)
    {
        return MonthlySchedule::query()
            ->where('checklist_item_id', $checklistItemId)
            ->where('year', $year)
            ->where('month', $month)
            ->get(['equipment_id', 'dates'])
            ->mapWithKeys(fn ($schedule) => [
                $schedule->equipment_id => collect($schedule->dates)
                    ->map(fn ($day) => (int) $day)
                    ->unique()
                    ->sort()
                    ->values()
                    ->all(),
            ]);
    }
}
