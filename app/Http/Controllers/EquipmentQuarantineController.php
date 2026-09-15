<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\EquipmentQuarantine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EquipmentQuarantineController extends Controller
{
    public function index()
    {
        $quarantines = EquipmentQuarantine::with(['equipment.assetLocation', 'quarantinedBy'])
            ->whereNull('released_at')
            ->latest('quarantined_at')
            ->latest('id')
            ->paginate(20);

        return view('equipment_quarantines.index', compact('quarantines'));
    }

    public function create()
    {
        $equipments = Equipment::with(['type', 'assetLocation'])
            ->where('condition', 'rusak')
            ->whereDoesntHave('quarantines', fn ($query) => $query->whereNull('released_at'))
            ->orderBy('name')
            ->get();

        return view('equipment_quarantines.create', compact('equipments'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'equipment_id' => ['required', 'exists:equipments,id'],
            'quarantined_at' => ['required', 'date'],
            'outcome' => ['required', 'in:rusak,nonaktif'],
            'reason' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($data) {
            $equipment = Equipment::lockForUpdate()->findOrFail($data['equipment_id']);
            abort_unless($equipment->condition === 'rusak', 422, 'Hanya peralatan dengan kondisi rusak yang dapat dikarantina.');
            $isAlreadyQuarantined = EquipmentQuarantine::where('equipment_id', $equipment->id)
                ->whereNull('released_at')
                ->exists();

            abort_if($isAlreadyQuarantined, 422, 'Peralatan ini sudah berada di karantina.');

            $equipment->update([
                'condition' => $data['outcome'],
                'status' => 'nonaktif',
            ]);

            EquipmentQuarantine::create([
                ...$data,
                'quarantined_by_user_id' => auth()->id(),
            ]);
        });

        return redirect()->route('equipment-quarantines.index')
            ->with('success', 'Peralatan berhasil dipindahkan ke karantina.');
    }
}
