<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\EquipmentType;
use Illuminate\Http\Request;

class EquipmentController extends Controller
{
    public function index()
    {
        $equipments = Equipment::with(['type','manufacturer','location'])->orderBy('name')->get();
        return view('equipments.index', compact('equipments'));
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
            'serial_number' => 'nullable|string|max:255',
            'equipment_type_id' => 'nullable|exists:equipment_types,id',
            'manufacturer_id' => 'nullable|exists:manufacturers,id',
            'location_id' => 'nullable|exists:locations,id',
            'ip_address' => 'nullable|ip',
            'purchase_date' => 'nullable|date',
            'capacity' => 'nullable|string|max:255',
            'specification' => 'nullable|string',
            'manufacture_year' => 'nullable|integer|min:1900|max:2100',
            'status' => 'nullable|string',
            'condition' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        Equipment::create($data);
        return redirect()->route('equipments.index')->with('success','Equipment created');
    }

    public function show(Equipment $equipment)
    {
        $equipment->load('type','logs','manufacturer','location');
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
            'serial_number' => 'nullable|string|max:255',
            'equipment_type_id' => 'nullable|exists:equipment_types,id',
            'manufacturer_id' => 'nullable|exists:manufacturers,id',
            'location_id' => 'nullable|exists:locations,id',
            'ip_address' => 'nullable|ip',
            'purchase_date' => 'nullable|date',
            'capacity' => 'nullable|string|max:255',
            'specification' => 'nullable|string',
            'manufacture_year' => 'nullable|integer|min:1900|max:2100',
            'status' => 'nullable|string',
            'condition' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $equipment->update($data);
        return redirect()->route('equipments.index')->with('success','Equipment updated');
    }

    public function destroy(Equipment $equipment)
    {
        $equipment->delete();
        return redirect()->route('equipments.index')->with('success','Equipment deleted');
    }
}
