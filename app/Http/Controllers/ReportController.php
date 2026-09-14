<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Equipment;
use App\Models\EquipmentType;
use App\Models\ItRepairTicket;
use App\Models\ItWaste;
use App\Models\InkTransaction;
use App\Models\InkType;
use App\Models\LicenseTransaction;
use App\Models\LicenseType;
use App\Models\Location;
use App\Models\MaintenanceChecklist;
use App\Models\SparepartTransaction;
use App\Models\Cctv;
use App\Models\SparepartType;
use App\Models\WebMonitoringChecklist;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function equipments(Request $request)
    {
        $filters = [
            'equipment_type_id' => $request->input('equipment_type_id'),
            'location_id' => $request->input('location_id'),
            'condition' => $request->input('condition'),
            'criticality' => $request->input('criticality'),
        ];

        $equipments = Equipment::with(['type', 'manufacturer', 'assetLocation'])
            ->when($filters['equipment_type_id'], fn ($query, $value) => $query->where('equipment_type_id', $value))
            ->when($filters['location_id'], fn ($query, $value) => $query->where('location_id', $value))
            ->when($filters['condition'], fn ($query, $value) => $query->where('condition', $value))
            ->when($filters['criticality'], fn ($query, $value) => $query->where('criticality', $value))
            ->orderBy('name')
            ->get();

        $summary = [
            'total' => $equipments->count(),
            'good' => $equipments->whereNotIn('condition', ['rusak', 'perbaikan'])->count(),
            'attention' => $equipments->whereIn('condition', ['rusak', 'perbaikan'])->count(),
            'unassigned' => $equipments->whereNull('owner_name')->count(),
        ];

        $byType = $equipments->groupBy(fn ($equipment) => $equipment->type->name ?? 'Tanpa Jenis')
            ->map(fn ($items, $type) => [
                'type' => $type,
                'total' => $items->count(),
                'good' => $items->whereNotIn('condition', ['rusak', 'perbaikan'])->count(),
                'attention' => $items->whereIn('condition', ['rusak', 'perbaikan'])->count(),
            ])
            ->sortByDesc('total')
            ->values();

        return view('reports.equipments', [
            'equipments' => $equipments,
            'summary' => $summary,
            'byType' => $byType,
            'filters' => $filters,
            'types' => EquipmentType::orderBy('name')->get(['id', 'name']),
            'locations' => Location::orderBy('name')->get(['id', 'name']),
            'conditions' => Equipment::whereNotNull('condition')->distinct()->orderBy('condition')->pluck('condition'),
        ]);
    }

    public function equipmentAdditions(Request $request)
    {
        $filters = [
            'from' => $request->input('from', now()->startOfMonth()->toDateString()),
            'to' => $request->input('to', now()->toDateString()),
        ];

        $equipments = Equipment::with(['type', 'manufacturer', 'assetLocation', 'owner'])
            ->whereDate('created_at', '>=', $filters['from'])
            ->whereDate('created_at', '<=', $filters['to'])
            ->orderByDesc('created_at')
            ->get();

        return view('reports.equipment_additions', compact('equipments', 'filters'));
    }

    public function repairs(Request $request)
    {
        $filters = [
            'from' => $request->input('from', now()->startOfMonth()->toDateString()),
            'to' => $request->input('to', now()->toDateString()),
            'status' => $request->input('status'),
            'repair_category' => $request->input('repair_category'),
        ];

        $tickets = ItRepairTicket::with('equipment.assetLocation')
            ->when($filters['from'], fn ($query, $value) => $query->whereDate('reported_at', '>=', $value))
            ->when($filters['to'], fn ($query, $value) => $query->whereDate('reported_at', '<=', $value))
            ->when($filters['status'], fn ($query, $value) => $query->where('status', $value))
            ->when($filters['repair_category'], fn ($query, $value) => $query->where('repair_category', $value))
            ->orderByDesc('reported_at')
            ->get();

        $resolved = $tickets->where('status', 'resolved')->filter(fn ($ticket) => $ticket->started_at && $ticket->resolved_at);
        $summary = [
            'total' => $tickets->count(),
            'open' => $tickets->where('status', 'open')->count(),
            'in_progress' => $tickets->where('status', 'in_progress')->count(),
            'resolved' => $tickets->where('status', 'resolved')->count(),
            'hardware' => $tickets->where('repair_category', 'hardware')->count(),
            'software' => $tickets->where('repair_category', 'software')->count(),
            'avg_hours' => $resolved->count() ? round($resolved->avg(fn ($ticket) => $ticket->started_at->diffInMinutes($ticket->resolved_at) / 60), 1) : 0,
        ];

        $byProblem = $tickets->groupBy(fn ($ticket) => $ticket->error_type ?: 'Tidak dikategorikan')
            ->map(fn ($items, $problem) => ['problem' => $problem, 'total' => $items->count()])
            ->sortByDesc('total')
            ->take(10)
            ->values();

        return view('reports.repairs', compact('tickets', 'summary', 'byProblem', 'filters'));
    }

    public function checklists(Request $request)
    {
        $filters = [
            'from' => $request->input('from', now()->startOfMonth()->toDateString()),
            'to' => $request->input('to', now()->toDateString()),
        ];

        $webChecklists = WebMonitoringChecklist::with('site')
            ->withCount('entries')
            ->when($filters['from'], fn ($query, $value) => $query->whereDate('checked_at', '>=', $value))
            ->when($filters['to'], fn ($query, $value) => $query->whereDate('checked_at', '<=', $value))
            ->orderByDesc('checked_at')
            ->get();

        $equipmentChecklists = MaintenanceChecklist::with(['checklistItem', 'entries'])
            ->when($filters['from'], fn ($query, $value) => $query->whereDate('checked_at', '>=', $value))
            ->when($filters['to'], fn ($query, $value) => $query->whereDate('checked_at', '<=', $value))
            ->orderByDesc('checked_at')
            ->get();

        $entries = $equipmentChecklists->flatMap->entries;
        $summary = [
            'web_total' => $webChecklists->count(),
            'web_security' => $webChecklists->where('checklist_type', 'security')->count(),
            'web_functional' => $webChecklists->where('checklist_type', 'functional')->count(),
            'equipment_total' => $equipmentChecklists->count(),
            'equipment_ok' => $entries->where('result', 'ok')->count(),
            'equipment_not_ok' => $entries->where('result', 'not_ok')->count(),
        ];

        return view('reports.checklists', compact('webChecklists', 'equipmentChecklists', 'summary', 'filters'));
    }

    public function inventory(Request $request)
    {
        $filters = [
            'type' => $request->input('type', 'all'),
            'from' => $request->input('from', now()->startOfMonth()->toDateString()),
            'to' => $request->input('to', now()->toDateString()),
        ];
        $show = fn (string $type): bool => $filters['type'] === 'all' || $filters['type'] === $type;

        $inks = $show('ink') ? InkType::orderBy('name')->get() : collect();
        $spareparts = $show('sparepart') ? SparepartType::orderBy('name')->get() : collect();
        $wastes = $show('waste') ? ItWaste::with(['equipment', 'creator'])->whereDate('waste_date', '>=', $filters['from'])->whereDate('waste_date', '<=', $filters['to'])->latest('waste_date')->get() : collect();
        $licenses = $show('license') ? LicenseType::orderBy('expiry_date')->get() : collect();
        $cctvs = $show('cctv') ? Cctv::with('networkZone')->orderBy('name')->get() : collect();

        $histories = collect();
        if ($show('ink')) {
            $histories = $histories->merge(InkTransaction::with(['inkType', 'creator'])
                ->whereDate('transaction_date', '>=', $filters['from'])->whereDate('transaction_date', '<=', $filters['to'])
                ->get()->map(fn ($item) => $this->historyRow('Tinta', $item->transaction_date, $item->type, $item->quantity, $item->inkType?->name, $item->reference, $item->creator?->name)));
        }
        if ($show('sparepart')) {
            $histories = $histories->merge(SparepartTransaction::with(['sparepartType', 'creator'])
                ->whereDate('transaction_date', '>=', $filters['from'])->whereDate('transaction_date', '<=', $filters['to'])
                ->get()->map(fn ($item) => $this->historyRow('Sparepart', $item->transaction_date, $item->type, $item->quantity, $item->sparepartType?->name, $item->reference, $item->creator?->name)));
        }
        if ($show('license')) {
            $histories = $histories->merge(LicenseTransaction::with(['licenseType', 'creator'])
                ->whereDate('transaction_date', '>=', $filters['from'])->whereDate('transaction_date', '<=', $filters['to'])
                ->get()->map(fn ($item) => $this->historyRow('Lisensi', $item->transaction_date, $item->type, $item->quantity, $item->licenseType?->name, $item->reference, $item->creator?->name)));
        }
        if ($show('waste')) {
            $histories = $histories->merge($wastes->map(fn ($item) => $this->historyRow('Limbah IT', $item->waste_date, $item->collection_status ?: 'Pencatatan limbah', $item->quantity . ' ' . $item->unit, $item->waste_type, $item->waste_code, $item->creator?->name)));
        }
        $histories = $histories->sortByDesc('date')->values();

        $summary = [
            'ink_total' => $inks->count(),
            'ink_low' => $inks->where('is_low_stock', true)->count(),
            'sparepart_total' => $spareparts->count(),
            'sparepart_low' => $spareparts->where('is_low_stock', true)->count(),
            'waste_total' => $wastes->count(),
            'license_expiring' => $licenses->filter(fn ($license) => $license->expiry_date && $license->expiry_date->lte(now()->addDays(30)))->count(),
            'cctv_online' => $cctvs->where('status', 'online')->count(),
            'cctv_total' => $cctvs->count(),
        ];

        return view('reports.inventory', compact('inks', 'spareparts', 'wastes', 'licenses', 'cctvs', 'histories', 'summary', 'filters'));
    }

    private function historyRow(string $module, $date, string $action, $quantity, ?string $item, ?string $reference, ?string $actor): array
    {
        return compact('module', 'date', 'action', 'quantity', 'item', 'reference', 'actor');
    }

    public function activities(Request $request)
    {
        $filters = [
            'from' => $request->input('from', now()->startOfMonth()->toDateString()),
            'to' => $request->input('to', now()->toDateString()),
            'module' => $request->input('module'),
            'action' => $request->input('action'),
        ];

        $logs = ActivityLog::query()
            ->when($filters['from'], fn ($query, $value) => $query->whereDate('created_at', '>=', $value))
            ->when($filters['to'], fn ($query, $value) => $query->whereDate('created_at', '<=', $value))
            ->when($filters['module'], fn ($query, $value) => $query->where('module', $value))
            ->when($filters['action'], fn ($query, $value) => $query->where('action', $value))
            ->latest()
            ->get();

        $summary = [
            'total' => $logs->count(),
            'created' => $logs->where('action', 'Membuat data')->count(),
            'updated' => $logs->where('action', 'Memperbarui data')->count(),
            'deleted' => $logs->where('action', 'Menghapus data')->count(),
        ];

        $byUser = $logs->groupBy('actor_name')
            ->map(fn ($items, $actor) => ['actor' => $actor, 'total' => $items->count()])
            ->sortByDesc('total')
            ->take(10)
            ->values();

        return view('reports.activities', [
            'logs' => $logs,
            'summary' => $summary,
            'byUser' => $byUser,
            'filters' => $filters,
            'modules' => ActivityLog::distinct()->orderBy('module')->pluck('module'),
        ]);
    }
}
