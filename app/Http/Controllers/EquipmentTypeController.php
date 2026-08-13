<?php

namespace App\Http\Controllers;

use App\Models\EquipmentType;
use Illuminate\Http\Request;

class EquipmentTypeController extends Controller
{
    public function index()
    {
        $items = EquipmentType::orderBy('name')->get();
        return view('masters.equipment_types.index', compact('items'));
    }

    public function create()
    {
        return view('masters.equipment_types.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name'=>'required|string|max:255','description'=>'nullable|string']);
        EquipmentType::create($data);
        return redirect()->route('masters.equipment-types.index')->with('success','Tipe ditambahkan');
    }

    public function edit(EquipmentType $equipmentType)
    {
        return view('masters.equipment_types.edit', ['item'=>$equipmentType]);
    }

    public function update(Request $request, EquipmentType $equipmentType)
    {
        $data = $request->validate(['name'=>'required|string|max:255','description'=>'nullable|string']);
        $equipmentType->update($data);
        return redirect()->route('masters.equipment-types.index')->with('success','Tipe diperbarui');
    }

    public function destroy(EquipmentType $equipmentType)
    {
        $equipmentType->delete();
        return back()->with('success','Dihapus');
    }
}
