<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\EquipmentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EquipmentController extends Controller
{
    public function index()
    {
        $equipments = Equipment::with(['type', 'manufacturer', 'assetLocation'])->orderBy('name')->get();
        $summary = [
            'total' => $equipments->count(),
            'active' => $equipments->whereNotIn('condition', ['rusak', 'perbaikan'])->count(),
            'attention' => $equipments->whereIn('condition', ['rusak', 'perbaikan'])->count(),
        ];

        return view('equipments.index', compact('equipments', 'summary'));
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
            'maintenanceChecklistEntries.maintenanceChecklist.checklistItem',
            'repairTickets',
        ]);
        return view('equipments.show', compact('equipment'));
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
        if ($equipment->photo_path) {
            Storage::disk('public')->delete($equipment->photo_path);
        }
        $equipment->delete();
        return redirect()->route('equipments.index')->with('success','Equipment deleted');
    }
}
