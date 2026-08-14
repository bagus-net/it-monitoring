<?php

namespace App\Http\Controllers;

use App\Models\ChecklistItem;
use App\Models\Equipment;
use App\Models\MaintenanceChecklist;
use App\Models\MaintenanceSchedule;
use App\Models\MonthlySchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaintenanceChecklistController extends Controller
{
    private const MONTH_NAMES = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    public function index()
    {
        $checklists = MaintenanceChecklist::with(['checklistItem', 'entries.equipment'])
            ->orderByDesc('checked_at')
            ->paginate(50);
        $byProgram = $checklists->groupBy('checklist_item_id')
            ->map(function ($items) {
                return [
                    'checklistItem' => $items->first()->checklistItem,
                    'checklists' => $items,
                ];
            })
            ->sortBy(fn ($group) => $group['checklistItem']->title ?? '')
            ->values();
        $entries = $checklists->getCollection()->flatMap(fn ($checklist) => $checklist->entries);
        $summary = [
            'documents' => MaintenanceChecklist::count(),
            'ok' => $entries->where('result', 'ok')->count(),
            'not_ok' => $entries->where('result', 'not_ok')->count(),
        ];

        return view('maintenance_checklists.index', compact('checklists', 'byProgram', 'summary'));
    }

    public function create(Request $request)
    {
        $items = ChecklistItem::orderBy('title')->get();
        $checklistItemId = $request->integer('checklist_item_id');
        $year = $request->integer('year') ?: (int) date('Y');
        $month = $request->integer('month') ?: (int) date('n');
        $equipment = $checklistItemId ? $this->scheduledEquipment($checklistItemId, $year, $month) : collect();
        $monthNames = self::MONTH_NAMES;

        return view('maintenance_checklists.create', compact('items', 'checklistItemId', 'year', 'month', 'equipment', 'monthNames'));
    }

    public function store(Request $request)
    {
        $data = $this->validateChecklist($request);

        DB::transaction(function () use ($data) {
            $checklist = MaintenanceChecklist::create([
                'checklist_item_id' => $data['checklist_item_id'],
                'year' => $data['year'],
                'month' => $data['month'],
                'checked_at' => $data['checked_at'],
                'reported_by' => $data['reported_by'] ?? null,
                'acknowledged_by' => $data['acknowledged_by'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            $this->syncEntries($checklist, $data['entries']);
        });

        return redirect()->route('maintenance-checklists.index')->with('success', 'Checklist perawatan berhasil disimpan.');
    }

    public function show(MaintenanceChecklist $maintenanceChecklist)
    {
        $maintenanceChecklist->load(['checklistItem', 'entries.equipment']);
        $monthNames = self::MONTH_NAMES;
        $summary = [
            'total' => $maintenanceChecklist->entries->count(),
            'ok' => $maintenanceChecklist->entries->where('result', 'ok')->count(),
            'not_ok' => $maintenanceChecklist->entries->where('result', 'not_ok')->count(),
        ];

        return view('maintenance_checklists.show', compact('maintenanceChecklist', 'monthNames', 'summary'));
    }

    public function edit(MaintenanceChecklist $maintenanceChecklist)
    {
        $maintenanceChecklist->load(['entries.equipment']);
        $items = ChecklistItem::orderBy('title')->get();
        $equipment = $maintenanceChecklist->entries->pluck('equipment')->filter()->sortBy('name')->values();
        $entryIndex = $maintenanceChecklist->entries->keyBy('equipment_id');
        $monthNames = self::MONTH_NAMES;

        return view('maintenance_checklists.edit', compact('maintenanceChecklist', 'items', 'equipment', 'entryIndex', 'monthNames'));
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
                'reported_by' => $data['reported_by'] ?? null,
                'acknowledged_by' => $data['acknowledged_by'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            $maintenanceChecklist->entries()->delete();
            $this->syncEntries($maintenanceChecklist, $data['entries']);
        });

        return redirect()->route('maintenance-checklists.show', $maintenanceChecklist)->with('success', 'Checklist perawatan berhasil diperbarui.');
    }

    public function destroy(MaintenanceChecklist $maintenanceChecklist)
    {
        $maintenanceChecklist->delete();

        return redirect()->route('maintenance-checklists.index')->with('success', 'Checklist perawatan berhasil dihapus.');
    }

    private function validateChecklist(Request $request): array
    {
        return $request->validate([
            'checklist_item_id' => 'required|exists:checklist_items,id',
            'year' => 'required|integer|min:2000|max:2100',
            'month' => 'required|integer|min:1|max:12',
            'checked_at' => 'required|date',
            'reported_by' => 'nullable|string|max:255',
            'acknowledged_by' => 'nullable|string|max:255',
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
}
