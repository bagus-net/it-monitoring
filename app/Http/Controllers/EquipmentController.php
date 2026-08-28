<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\EquipmentType;
use App\Models\MonthlySchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EquipmentController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $filters = [
            'equipment_type_id' => $request->input('equipment_type_id'),
            'manufacturer_id' => $request->input('manufacturer_id'),
            'location_id' => $request->input('location_id'),
            'condition' => $request->input('condition'),
            'criticality' => $request->input('criticality'),
            'department' => $request->input('department'),
            'purchase_year' => $request->input('purchase_year'),
            'purchase_date_from' => $request->input('purchase_date_from'),
            'purchase_date_to' => $request->input('purchase_date_to'),
        ];
        $equipments = Equipment::with(['type', 'manufacturer', 'assetLocation'])
            ->when($filters['equipment_type_id'], fn ($query, $value) => $query->where('equipment_type_id', $value))
            ->when($filters['manufacturer_id'], fn ($query, $value) => $query->where('manufacturer_id', $value))
            ->when($filters['location_id'], fn ($query, $value) => $query->where('location_id', $value))
            ->when($filters['condition'], fn ($query, $value) => $query->where('condition', $value))
            ->when($filters['criticality'], fn ($query, $value) => $query->where('criticality', $value))
            ->when($filters['department'], fn ($query, $value) => $query->where('department', $value))
            ->when($filters['purchase_year'], fn ($query, $value) => $query->whereYear('purchase_date', $value))
            ->when($filters['purchase_date_from'], fn ($query, $value) => $query->whereDate('purchase_date', '>=', $value))
            ->when($filters['purchase_date_to'], fn ($query, $value) => $query->whereDate('purchase_date', '<=', $value))
            ->when($search !== '', function ($query) use ($search) {
                $keyword = '%' . $search . '%';
                $query->where(function ($inner) use ($keyword) {
                    $inner->where('name', 'like', $keyword)
                        ->orWhere('asset_tag', 'like', $keyword)
                        ->orWhere('serial_number', 'like', $keyword)
                        ->orWhere('model', 'like', $keyword)
                        ->orWhere('operating_system', 'like', $keyword)
                        ->orWhere('ip_address', 'like', $keyword)
                        ->orWhere('owner_name', 'like', $keyword)
                        ->orWhere('department', 'like', $keyword)
                        ->orWhere('condition', 'like', $keyword)
                        ->orWhere('status', 'like', $keyword)
                        ->orWhereHas('type', fn ($relation) => $relation->where('name', 'like', $keyword))
                        ->orWhereHas('manufacturer', fn ($relation) => $relation->where('name', 'like', $keyword))
                        ->orWhereHas('assetLocation', fn ($relation) => $relation->where('name', 'like', $keyword));
                });
            })
            ->orderBy('name')
            ->paginate($this->resolvePerPage($request))
            ->withQueryString();
        $summary = [
            'total' => Equipment::count(),
            'active' => Equipment::whereNotIn('condition', ['rusak', 'perbaikan'])->count(),
            'attention' => Equipment::whereIn('condition', ['rusak', 'perbaikan'])->count(),
        ];

        $typeRecap = EquipmentType::withCount([
            'equipments',
            'equipments as good_count' => fn ($query) => $query->where(fn ($inner) => $inner->whereNull('condition')->orWhereNotIn('condition', ['rusak', 'perbaikan'])),
            'equipments as broken_count' => fn ($query) => $query->whereIn('condition', ['rusak', 'perbaikan']),
        ])->orderByDesc('equipments_count')->orderBy('name')->get();

        $criticalityLabels = [
            'critical' => 'Sangat Kritis',
            'high' => 'Tinggi',
            'medium' => 'Sedang',
            'low' => 'Rendah',
        ];
        $criticalityCounts = Equipment::selectRaw('criticality, COUNT(*) as total')->groupBy('criticality')->pluck('total', 'criticality');
        $criticalityRecap = collect($criticalityLabels)->map(fn ($label, $key) => [
            'key' => $key,
            'label' => $label,
            'total' => (int) ($criticalityCounts[$key] ?? 0),
        ])->values();
        $criticalityRecap->push([
            'key' => 'unset',
            'label' => 'Belum Ditentukan',
            'total' => (int) ($criticalityCounts[''] ?? 0) + (int) ($criticalityCounts[null] ?? 0),
        ]);

        $filterOptions = [
            'types' => EquipmentType::orderBy('name')->get(['id', 'name']),
            'manufacturers' => \App\Models\Manufacturer::orderBy('name')->get(['id', 'name']),
            'locations' => \App\Models\Location::orderBy('name')->get(['id', 'name']),
            'conditions' => Equipment::whereNotNull('condition')->distinct()->orderBy('condition')->pluck('condition'),
            'departments' => Equipment::whereNotNull('department')->where('department', '!=', '')->distinct()->orderBy('department')->pluck('department'),
            'criticalities' => $criticalityLabels,
            'purchase_years' => Equipment::whereNotNull('purchase_date')->selectRaw('DISTINCT YEAR(purchase_date) as year')->orderByDesc('year')->pluck('year'),
        ];

        return view('equipments.index', compact('equipments', 'summary', 'search', 'typeRecap', 'criticalityRecap', 'filters', 'filterOptions'));
    }

    public function create()
    {
        $types = EquipmentType::orderBy('name')->get();
        $manufacturers = \App\Models\Manufacturer::orderBy('name')->get();
        $locations = \App\Models\Location::orderBy('name')->get();
        return view('equipments.create', compact('types','manufacturers','locations'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'asset_tag' => 'nullable|string|max:100|unique:equipments,asset_tag',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'serial_number' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'operating_system' => 'nullable|string|max:255',
            'equipment_type_id' => 'nullable|exists:equipment_types,id',
            'manufacturer_id' => 'nullable|exists:manufacturers,id',
            'vendor_name' => 'nullable|string|max:255',
            'location_id' => 'nullable|exists:locations,id',
            'owner_name' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'ip_address' => 'nullable|ip',
            'purchase_date' => 'nullable|date',
            'warranty_expiry' => 'nullable|date',
            'support_contract_end' => 'nullable|date',
            'capacity' => 'nullable|string|max:255',
            'specification' => 'nullable|string',
            'technical_details' => 'nullable|array',
            'technical_details.*' => 'nullable|string|max:255',
            'manufacture_year' => 'nullable|integer|min:1900|max:2100',
            'status' => 'nullable|string',
            'condition' => 'nullable|string',
            'criticality' => 'nullable|in:low,medium,high,critical',
            'notes' => 'nullable|string',
        ]);

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('equipment-photos', 'public');
        }
        $data['technical_details'] = array_filter($data['technical_details'] ?? [], fn ($value) => $value !== null && $value !== '');

        Equipment::create($data);
        return redirect()->route('equipments.index')->with('success','Equipment created');
    }

    public function show(Equipment $equipment)
    {
        $equipment->load([
            'type',
            'manufacturer',
            'assetLocation',
            'owner',
        ]);

        $scheduledDatesByPeriod = MonthlySchedule::where('equipment_id', $equipment->id)
            ->get(['checklist_item_id', 'year', 'month', 'dates'])
            ->mapWithKeys(function ($schedule) {
                $periodKey = $schedule->checklist_item_id . '|' . $schedule->year . '|' . $schedule->month;

                return [$periodKey => collect($schedule->dates)
                    ->map(fn ($day) => (int) $day)
                    ->unique()
                    ->sort()
                    ->values()
                    ->all()];
            });

        return view('equipments.show', compact('equipment', 'scheduledDatesByPeriod'));
    }

    public function scan(Equipment $equipment)
    {
        $equipment->load([
            'type',
            'manufacturer',
            'assetLocation',
            'maintenanceChecklistEntries.maintenanceChecklist.checklistItem',
            'repairTickets',
            'transfers.fromLocation',
            'transfers.toLocation',
        ]);

        return view('equipments.scan', compact('equipment'));
    }

    public function label(Equipment $equipment)
    {
        return view('equipments.label', [
            'equipment' => $equipment,
            'scanUrl' => rtrim(config('app.equipment_scan_url'), '/') . route('equipments.scan', $equipment, false),
        ]);
    }

    public function downloadLabel(Equipment $equipment)
    {
        return view('equipments.label_download', [
            'equipment' => $equipment,
            'scanUrl' => rtrim(config('app.equipment_scan_url'), '/') . route('equipments.scan', $equipment, false),
        ]);
    }

    public function edit(Equipment $equipment)
    {
        $types = EquipmentType::orderBy('name')->get();
        $manufacturers = \App\Models\Manufacturer::orderBy('name')->get();
        $locations = \App\Models\Location::orderBy('name')->get();
        return view('equipments.edit', compact('equipment','types','manufacturers','locations'));
    }

    public function update(Request $request, Equipment $equipment)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'asset_tag' => 'nullable|string|max:100|unique:equipments,asset_tag,' . $equipment->id,
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'serial_number' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'operating_system' => 'nullable|string|max:255',
            'equipment_type_id' => 'nullable|exists:equipment_types,id',
            'manufacturer_id' => 'nullable|exists:manufacturers,id',
            'vendor_name' => 'nullable|string|max:255',
            'location_id' => 'nullable|exists:locations,id',
            'owner_name' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'ip_address' => 'nullable|ip',
            'purchase_date' => 'nullable|date',
            'warranty_expiry' => 'nullable|date',
            'support_contract_end' => 'nullable|date',
            'capacity' => 'nullable|string|max:255',
            'specification' => 'nullable|string',
            'technical_details' => 'nullable|array',
            'technical_details.*' => 'nullable|string|max:255',
            'manufacture_year' => 'nullable|integer|min:1900|max:2100',
            'status' => 'nullable|string',
            'condition' => 'nullable|string',
            'criticality' => 'nullable|in:low,medium,high,critical',
            'notes' => 'nullable|string',
        ]);

        if ($request->hasFile('photo')) {
            if ($equipment->photo_path) {
                Storage::disk('public')->delete($equipment->photo_path);
            }
            $data['photo_path'] = $request->file('photo')->store('equipment-photos', 'public');
        }
        $data['technical_details'] = array_filter($data['technical_details'] ?? [], fn ($value) => $value !== null && $value !== '');

        $equipment->update($data);
        return redirect()->route('equipments.index')->with('success','Equipment updated');
    }

    public function destroy(Equipment $equipment)
    {
        $equipment->delete();
        return redirect()->route('equipments.index')->with('success', 'Peralatan dipindahkan ke Sampah Data.');
    }
}
