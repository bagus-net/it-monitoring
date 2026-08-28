<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceSchedule;
use App\Models\MaintenanceLog;
use App\Models\ChecklistItem;
use App\Models\Equipment;
use App\Models\User;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    private const MONTH_NAMES_SHORT = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    private const CATEGORY_ORDER = [
        'Perawatan Software' => 1,
        'Perawatan Hardware' => 2,
        'Perawatan Networking' => 3,
    ];

    public function schedules(Request $request)
    {
        $ids = MaintenanceSchedule::select('checklist_item_id')->groupBy('checklist_item_id')->pluck('checklist_item_id')->toArray();
        $groups = [];
        if(!empty($ids)){
            $items = ChecklistItem::whereIn('id',$ids)->get()->keyBy('id');
            foreach($ids as $id){
                $freqs = MaintenanceSchedule::where('checklist_item_id',$id)->pluck('frequency')->unique()->values()->toArray();
                $equipmentIds = MaintenanceSchedule::where('checklist_item_id',$id)->pluck('equipment_id')->unique()->values()->toArray();
                $groups[] = [
                    'item' => $items->get($id),
                    'frequencies' => $freqs,
                    'equipment_ids' => $equipmentIds,
                ];
            }
        }

        usort($groups, function ($a, $b) {
            $categoryA = $a['item']->category ?? 'Lainnya';
            $categoryB = $b['item']->category ?? 'Lainnya';
            $orderA = self::CATEGORY_ORDER[$categoryA] ?? 99;
            $orderB = self::CATEGORY_ORDER[$categoryB] ?? 99;

            if ($orderA !== $orderB) {
                return $orderA <=> $orderB;
            }

            return (($a['item']->sort_order ?? 0) <=> ($b['item']->sort_order ?? 0));
        });

        // Opsi periode & fitur untuk cetak Jadwal Perawatan IT tahunan
        $monthNamesShort = self::MONTH_NAMES_SHORT;
        $availableYears = MaintenanceSchedule::where('frequency', 'annual')->distinct()->orderByDesc('year')->pluck('year');
        $printYear = (int) $request->input('print_year', $availableYears->first() ?? now()->year);

        $allItems = ChecklistItem::orderBy('sort_order')->get()->sortBy(function ($item) {
            $category = $item->category ?? 'Lainnya';
            return ((self::CATEGORY_ORDER[$category] ?? 99) * 1000) + ($item->sort_order ?? 0);
        })->values();
        $selectedItemIds = array_map('intval', (array) $request->input('print_items', $allItems->pluck('id')->all()));

        $weeksByItem = [];
        MaintenanceSchedule::where('frequency', 'annual')
            ->where('year', $printYear)
            ->whereIn('checklist_item_id', $selectedItemIds)
            ->get(['checklist_item_id', 'month', 'week_of_month'])
            ->each(function ($row) use (&$weeksByItem) {
                $months = $row->month ? [(int) $row->month] : range(1, 12);
                $weeks = $row->week_of_month ? [(int) $row->week_of_month] : range(1, 4);
                foreach ($months as $month) {
                    foreach ($weeks as $week) {
                        $weeksByItem[$row->checklist_item_id][$month][$week] = true;
                    }
                }
            });

        $printCategories = $allItems->whereIn('id', $selectedItemIds)
            ->groupBy(fn ($item) => $item->category ?: 'Lainnya');

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
            'reporter' => $reporterUser,
            'acknowledger' => $acknowledgerUser,
        ];

        return view('maintenances.schedules', compact(
            'groups', 'monthNamesShort', 'availableYears', 'printYear',
            'allItems', 'selectedItemIds', 'weeksByItem', 'printCategories',
            'signatures', 'signatureNames'
        ));
    }

    public function createSchedule()
    {
        $equipments = Equipment::with('type')->orderBy('name')->get();
        $equipmentsByType = $equipments->groupBy(fn ($equipment) => $equipment->type->name ?? 'Tanpa Tipe')->sortKeys();
        $items = ChecklistItem::orderBy('sort_order')->get();
        return view('maintenances.create_schedule', compact('equipments', 'equipmentsByType', 'items'));
    }

    // new create method to serve `maintenances.create`
    public function create()
    {
        $equipments = Equipment::with('type')->orderBy('name')->get();
        $equipmentsByType = $equipments->groupBy(fn ($equipment) => $equipment->type->name ?? 'Tanpa Tipe')->sortKeys();
        $items = ChecklistItem::orderBy('sort_order')->get();
        return view('maintenances.create', compact('equipments', 'equipmentsByType', 'items'));
    }

    public function storeSchedule(Request $request)
    {
        $data = $request->validate([
            'checklist_item_id' => 'required|exists:checklist_items,id',
            'equipment_ids' => 'nullable|array',
            'equipment_ids.*' => 'exists:equipments,id',
            'frequency' => 'required|in:annual,monthly',
            'day_of_month' => 'nullable|integer|min:1|max:31',
            'year' => 'nullable|integer|min:2000|max:2100',
            'months' => 'nullable|array',
            'months.*' => 'integer|min:1|max:12',
            'weeks' => 'nullable|array',
            'weeks.*' => 'integer|min:1|max:4',
            'assigned_to' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $equipmentIds = $data['equipment_ids'] ?? [];
        // if no equipment selected, treat as all (null equipment_id)
        if (empty($equipmentIds)) {
            $equipmentIds = [null];
        }

        $weeks = $data['weeks'] ?? [];
        if ($data['frequency'] === 'annual') {
            $months = $data['months'] ?? [];
            foreach ($equipmentIds as $eid) {
                if (empty($months)) {
                    if(empty($weeks)){
                        MaintenanceSchedule::create([
                            'equipment_id' => $eid,
                            'frequency' => 'annual',
                            'month' => null,
                            'week_of_month' => null,
                            'year' => $data['year'] ?? date('Y'),
                            'checklist_item_id' => $data['checklist_item_id'],
                            'assigned_to' => $data['assigned_to'] ?? null,
                            'notes' => $data['notes'] ?? null,
                        ]);
                    } else {
                        foreach($weeks as $w){
                            MaintenanceSchedule::create([
                                'equipment_id' => $eid,
                                'frequency' => 'annual',
                                'month' => null,
                                'week_of_month' => $w,
                                'year' => $data['year'] ?? date('Y'),
                                'checklist_item_id' => $data['checklist_item_id'],
                                'assigned_to' => $data['assigned_to'] ?? null,
                                'notes' => $data['notes'] ?? null,
                            ]);
                        }
                    }
                } else {
                    foreach ($months as $m) {
                            if(empty($weeks)){
                            MaintenanceSchedule::create([
                                'equipment_id' => $eid,
                                'frequency' => 'annual',
                                'month' => $m,
                                'week_of_month' => null,
                                'year' => $data['year'] ?? date('Y'),
                                'checklist_item_id' => $data['checklist_item_id'],
                                'assigned_to' => $data['assigned_to'] ?? null,
                                'notes' => $data['notes'] ?? null,
                            ]);
                        } else {
                            foreach($weeks as $w){
                                MaintenanceSchedule::create([
                                    'equipment_id' => $eid,
                                    'frequency' => 'annual',
                                    'month' => $m,
                                    'week_of_month' => $w,
                                    'year' => $data['year'] ?? date('Y'),
                                    'checklist_item_id' => $data['checklist_item_id'],
                                    'assigned_to' => $data['assigned_to'] ?? null,
                                    'notes' => $data['notes'] ?? null,
                                ]);
                            }
                        }
                    }
                }
            }
        } else {
            // monthly
            foreach ($equipmentIds as $eid) {
                if(empty($weeks)){
                    MaintenanceSchedule::create([
                        'equipment_id' => $eid,
                        'frequency' => 'monthly',
                        'day_of_month' => $data['day_of_month'] ?? null,
                        'week_of_month' => null,
                        'checklist_item_id' => $data['checklist_item_id'],
                        'assigned_to' => $data['assigned_to'] ?? null,
                        'notes' => $data['notes'] ?? null,
                    ]);
                } else {
                    foreach($weeks as $w){
                        MaintenanceSchedule::create([
                            'equipment_id' => $eid,
                            'frequency' => 'monthly',
                            'day_of_month' => $data['day_of_month'] ?? null,
                            'week_of_month' => $w,
                            'checklist_item_id' => $data['checklist_item_id'],
                            'assigned_to' => $data['assigned_to'] ?? null,
                            'notes' => $data['notes'] ?? null,
                        ]);
                    }
                }
            }
        }
        return redirect()->route('maintenances.schedules')->with('success','Schedule created');
    }

    public function checklists()
    {
        $items = ChecklistItem::with('equipmentType')->orderBy('sort_order')->get();
        return view('maintenances.checklists', compact('items'));
    }

    public function grid(Request $request)
    {
        $year = $request->input('year', date('Y'));
        $equipmentId = $request->input('equipment_id');

        $equipments = Equipment::orderBy('name')->get();
        $items = ChecklistItem::with('equipmentType')->orderBy('sort_order')->get();

        // fetch logs for the year
        $logsAll = MaintenanceLog::whereYear('performed_at', $year)->get();

        $logsIndex = [];
        $logsAny = []; // keyed by checklist_item_id|month for any equipment
        foreach ($logsAll as $l) {
            $m = (int) $l->performed_at->format('n');
            $d = (int) $l->performed_at->format('j');
            $w = (int) (floor(($d - 1) / 7) + 1);
            if ($w > 4) $w = 4;
            $key = $l->equipment_id . '|' . $l->checklist_item_id . '|' . $m . '|' . $w;
            $logsIndex[$key] = true;
            $anyKey = $l->checklist_item_id . '|' . $m . '|' . $w;
            $logsAny[$anyKey] = true;
        }

        return view('maintenances.grid', compact('year','equipments','items','logsIndex','logsAny','equipmentId'));
    }

    public function showSchedule(Request $request, $checklistItemId)
    {
        $year = $request->input('year', date('Y'));
        $item = ChecklistItem::findOrFail($checklistItemId);
        $schedules = MaintenanceSchedule::where('checklist_item_id', $checklistItemId)
            ->where(function($q) use($year){
                $q->whereNull('year')->orWhere('year', $year);
            })->get();
        $equipments = Equipment::orderBy('name')->get()->keyBy('id');

        // determine equipment list: if any schedule has equipment_id null -> all equipments
        $equipmentIds = $schedules->pluck('equipment_id')->unique()->values()->toArray();
        if(in_array(null, $equipmentIds)){
            $equipmentList = $equipments->values();
        } else {
            $equipmentList = $equipments->only($equipmentIds)->values();
        }

        // build grid index by equipment|month|week
        $grid = [];
        foreach($schedules as $s){
            $eid = $s->equipment_id; // may be null meaning all
            $keyEids = $eid ? [$eid] : $equipmentList->pluck('id')->toArray();
            $m = $s->month; // nullable
            $w = $s->week_of_month; // nullable
            foreach($keyEids as $ke){
                $gk = $ke . '|' . ($m ?? 'all') . '|' . ($w ?? 'all');
                $grid[$gk] = true;
            }
        }

        return view('maintenances.schedule_view', compact('item','equipmentList','grid','schedules','year'));
    }

    public function editSchedule($checklistItemId)
    {
        $item = ChecklistItem::findOrFail($checklistItemId);
        $schedules = MaintenanceSchedule::where('checklist_item_id', $checklistItemId)->get();
        $equipments = Equipment::orderBy('name')->get()->keyBy('id');

        // determine selected equipments
        $equipmentIds = $schedules->pluck('equipment_id')->unique()->values()->toArray();
        $selectedEquipments = [];
        if(in_array(null, $equipmentIds)){
            $selectedEquipments = [];// empty means all
        } else {
            $selectedEquipments = array_filter($equipmentIds);
        }

        // determine frequency (pick first)
        $frequency = $schedules->pluck('frequency')->first() ?? 'monthly';

        // gather months and weeks
        $months = $schedules->pluck('month')->filter()->unique()->values()->toArray();
        $weeks = $schedules->pluck('week_of_month')->filter()->unique()->values()->toArray();
        $day = $schedules->pluck('day_of_month')->filter()->first();
        $year = $schedules->pluck('year')->filter()->unique()->values()->first() ?? date('Y');

        return view('maintenances.edit', compact('item','equipments','selectedEquipments','frequency','months','weeks','day','year'));
    }

    public function updateSchedule(Request $request, $checklistItemId)
    {
        $data = $request->validate([
            'checklist_item_id' => 'required|exists:checklist_items,id',
            'equipment_ids' => 'nullable|array',
            'equipment_ids.*' => 'exists:equipments,id',
            'frequency' => 'required|in:annual,monthly',
            'day_of_month' => 'nullable|integer|min:1|max:31',
            'year' => 'nullable|integer|min:2000|max:2100',
            'months' => 'nullable|array',
            'months.*' => 'integer|min:1|max:12',
            'weeks' => 'nullable|array',
            'weeks.*' => 'integer|min:1|max:4',
            'assigned_to' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        // remove existing schedules for this checklist item
        MaintenanceSchedule::where('checklist_item_id', $checklistItemId)->delete();

        // reuse creation logic (same as store)
        $equipmentIds = $data['equipment_ids'] ?? [];
        if (empty($equipmentIds)) {
            $equipmentIds = [null];
        }

        $weeks = $data['weeks'] ?? [];
        if ($data['frequency'] === 'annual') {
            $months = $data['months'] ?? [];
            foreach ($equipmentIds as $eid) {
                if (empty($months)) {
                    if(empty($weeks)){
                        MaintenanceSchedule::create([
                            'equipment_id' => $eid,
                            'frequency' => 'annual',
                            'month' => null,
                            'week_of_month' => null,
                            'year' => $data['year'] ?? date('Y'),
                            'checklist_item_id' => $data['checklist_item_id'],
                            'assigned_to' => $data['assigned_to'] ?? null,
                            'notes' => $data['notes'] ?? null,
                        ]);
                    } else {
                            foreach($weeks as $w){
                            MaintenanceSchedule::create([
                                'equipment_id' => $eid,
                                'frequency' => 'annual',
                                'month' => null,
                                'week_of_month' => $w,
                                'year' => $data['year'] ?? date('Y'),
                                'checklist_item_id' => $data['checklist_item_id'],
                                'assigned_to' => $data['assigned_to'] ?? null,
                                'notes' => $data['notes'] ?? null,
                            ]);
                        }
                    }
                } else {
                    foreach ($months as $m) {
                            if(empty($weeks)){
                            MaintenanceSchedule::create([
                                'equipment_id' => $eid,
                                'frequency' => 'annual',
                                'month' => $m,
                                'week_of_month' => null,
                                'year' => $data['year'] ?? date('Y'),
                                'checklist_item_id' => $data['checklist_item_id'],
                                'assigned_to' => $data['assigned_to'] ?? null,
                                'notes' => $data['notes'] ?? null,
                            ]);
                        } else {
                            foreach($weeks as $w){
                                MaintenanceSchedule::create([
                                    'equipment_id' => $eid,
                                    'frequency' => 'annual',
                                    'month' => $m,
                                    'week_of_month' => $w,
                                    'year' => $data['year'] ?? date('Y'),
                                    'checklist_item_id' => $data['checklist_item_id'],
                                    'assigned_to' => $data['assigned_to'] ?? null,
                                    'notes' => $data['notes'] ?? null,
                                ]);
                            }
                        }
                    }
                }
            }
        } else {
            // monthly
            foreach ($equipmentIds as $eid) {
                if(empty($weeks)){
                        MaintenanceSchedule::create([
                        'equipment_id' => $eid,
                        'frequency' => 'monthly',
                        'day_of_month' => $data['day_of_month'] ?? null,
                        'week_of_month' => null,
                        'year' => $data['year'] ?? date('Y'),
                        'checklist_item_id' => $data['checklist_item_id'],
                        'assigned_to' => $data['assigned_to'] ?? null,
                        'notes' => $data['notes'] ?? null,
                    ]);
                } else {
                        foreach($weeks as $w){
                        MaintenanceSchedule::create([
                            'equipment_id' => $eid,
                            'frequency' => 'monthly',
                            'day_of_month' => $data['day_of_month'] ?? null,
                            'week_of_month' => $w,
                            'year' => $data['year'] ?? date('Y'),
                            'checklist_item_id' => $data['checklist_item_id'],
                            'assigned_to' => $data['assigned_to'] ?? null,
                            'notes' => $data['notes'] ?? null,
                        ]);
                    }
                }
            }
        }

        return redirect()->route('maintenances.schedules')->with('success','Schedule updated');
    }

    public function destroySchedule($checklistItemId)
    {
        MaintenanceSchedule::where('checklist_item_id',$checklistItemId)->delete();
        return redirect()->route('maintenances.schedules')->with('success','Jadwal program dihapus');
    }

    public function storeLog(Request $request)
    {
        $data = $request->validate([
            'equipment_id' => 'required|exists:equipments,id',
            'checklist_item_id' => 'nullable|exists:checklist_items,id',
            'performed_at' => 'nullable|date',
            'performed_by' => 'nullable|string|max:255',
            'result' => 'required|in:ok,needs_repair,n/a',
            'remarks' => 'nullable|string',
        ]);

        $data['performed_at'] = $data['performed_at'] ?? now();
        MaintenanceLog::create($data);
        return back()->with('success','Log saved');
    }
}
