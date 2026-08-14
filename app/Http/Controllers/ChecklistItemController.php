<?php

namespace App\Http\Controllers;

use App\Models\ChecklistItem;
use App\Models\EquipmentType;
use Illuminate\Http\Request;

class ChecklistItemController extends Controller
{
    public function index()
    {
        $items = ChecklistItem::with('equipmentType')->orderBy('sort_order')->paginate(50);
        return view('masters.checklist_items.index', compact('items'));
    }

    public function create()
    {
        $types = EquipmentType::orderBy('name')->get();
        return view('masters.checklist_items.create', compact('types'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'equipment_type_id' => 'nullable|exists:equipment_types,id',
            'frequency' => 'nullable|in:monthly,annual',
            'sort_order' => 'nullable|integer',
        ]);

        ChecklistItem::create($data);
        return redirect()->route('masters.checklist-items.index')->with('success','Program perawatan ditambahkan');
    }

    public function edit(ChecklistItem $item)
    {
        $types = EquipmentType::orderBy('name')->get();
        return view('masters.checklist_items.edit', compact('item','types'));
    }

    public function update(Request $request, ChecklistItem $item)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'equipment_type_id' => 'nullable|exists:equipment_types,id',
            'frequency' => 'nullable|in:monthly,annual',
            'sort_order' => 'nullable|integer',
        ]);

        $item->update($data);
        return redirect()->route('masters.checklist-items.index')->with('success','Program perawatan diperbarui');
    }

    public function destroy(ChecklistItem $item)
    {
        $item->delete();
        return back()->with('success','Program perawatan dihapus');
    }
}
