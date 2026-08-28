<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\ItWaste;
use App\Models\ItWasteBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItWasteController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $batches = ItWasteBatch::withCount('wastes')
            ->when($search !== '', function ($query) use ($search) {
                $keyword = '%' . $search . '%';
                $query->where(fn ($inner) => $inner
                    ->orWhere('storage_location', 'like', $keyword)
                    ->orWhere('box_code', 'like', $keyword)
                    ->orWhere('handover_recipient', 'like', $keyword));
            })
            ->latest('opened_at')
            ->latest('id')
            ->paginate($this->resolvePerPage($request))
            ->withQueryString();

        $summary = [
            'records' => ItWaste::count(),
            'types' => ItWaste::distinct('waste_type')->count('waste_type'),
            'this_month' => ItWaste::whereYear('waste_date', now()->year)->whereMonth('waste_date', now()->month)->count(),
            'ready_to_handover' => ItWasteBatch::where('status', 'ready_to_handover')->count(),
        ];

        $handoverBoxes = ItWasteBatch::where('status', 'handed_over')
            ->select('box_code')
            ->orderBy('box_code')
            ->pluck('box_code');

        return view('it_wastes.index', compact('batches', 'summary', 'handoverBoxes', 'search'));
    }

    public function create()
    {
        $nextBoxCode = $this->nextBoxCode(now()->year);

        return view('it_wastes.create_batch', compact('nextBoxCode'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'opened_at' => 'required|date',
            'storage_location' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:2000',
        ]);
        $year = (int) date('Y', strtotime($data['opened_at']));
        $data['box_code'] = $this->nextBoxCode($year);
        $data['status'] = 'open';
        $data['created_by_user_id'] = auth()->id();
        $batch = ItWasteBatch::create($data);

        return redirect()->route('it-wastes.show', $batch)->with('success', 'Box limbah berhasil dibuat. Tambahkan limbah ke dalam batch ini.');
    }

    public function show(ItWasteBatch $itWasteBatch)
    {
        $itWasteBatch->load(['wastes.equipment', 'wastes.creator', 'creator']);
        $equipments = Equipment::orderBy('name')->get(['id', 'name', 'asset_tag']);
        $nextWasteCode = $this->nextWasteCode(now()->year);

        return view('it_wastes.show_batch', compact('itWasteBatch', 'equipments', 'nextWasteCode'));
    }

    public function storeWaste(Request $request, ItWasteBatch $itWasteBatch)
    {
        abort_unless($itWasteBatch->status === 'open', 422, 'Box ini sudah ditutup dan tidak dapat ditambahkan limbah.');
        $request->merge(['collection_status' => 'collected']);
        $data = $this->validateWaste($request);
        $data['waste_code'] = $this->nextWasteCode((int) date('Y', strtotime($data['waste_date'])));
        $data['it_waste_batch_id'] = $itWasteBatch->id;
        $data['box_code'] = $itWasteBatch->box_code;
        $data['collection_status'] = 'collected';
        $data['created_by_user_id'] = auth()->id();
        ItWaste::create($data);

        return redirect()->route('it-wastes.show', $itWasteBatch)->with('success', 'Limbah berhasil ditambahkan ke ' . $itWasteBatch->box_code . '.');
    }

    public function updateBatch(Request $request, ItWasteBatch $itWasteBatch)
    {
        $data = $request->validate([
            'status' => 'required|in:open,ready_to_handover,handed_over',
            'handover_recipient' => 'nullable|required_if:status,handed_over|string|max:255',
            'handed_over_at' => 'nullable|required_if:status,handed_over|date',
        ]);
        $itWasteBatch->update($data);
        $itWasteBatch->wastes()->update([
            'collection_status' => $data['status'] === 'open' ? 'collected' : $data['status'],
            'handover_recipient' => $data['handover_recipient'] ?? null,
            'handed_over_at' => $data['handed_over_at'] ?? null,
        ]);

        return redirect()->route('it-wastes.show', $itWasteBatch)->with('success', 'Status box limbah berhasil diperbarui.');
    }

    public function destroyBatch(ItWasteBatch $itWasteBatch)
    {
        if ($itWasteBatch->wastes()->exists()) {
            return redirect()->route('it-wastes.index')->with('error', 'Box tidak dapat dihapus karena sudah memiliki catatan limbah.');
        }

        $itWasteBatch->delete();

        return redirect()->route('it-wastes.index')->with('success', 'Box limbah berhasil dihapus.');
    }

    public function edit(ItWaste $itWaste)
    {
        $equipments = Equipment::orderBy('name')->get(['id', 'name', 'asset_tag']);
        $nextWasteCode = $this->nextWasteCode($itWaste->waste_date->year);
        $nextBoxCode = $this->nextBoxCode($itWaste->waste_date->year);

        return view('it_wastes.edit', compact('itWaste', 'equipments', 'nextWasteCode', 'nextBoxCode'));
    }

    public function update(Request $request, ItWaste $itWaste)
    {
        $data = $this->validateWaste($request);
        $year = (int) date('Y', strtotime($data['waste_date']));
        if (!$itWaste->waste_code) {
            $data['waste_code'] = $this->nextWasteCode($year);
        }
        if (!$itWaste->box_code) {
            $data['box_code'] = $this->activeOrNewBoxCode($year, $data['collection_status']);
        }
        $itWaste->update($data);

        return redirect()->route('it-wastes.index')->with('success', 'Data limbah IT berhasil diperbarui.');
    }

    public function destroy(ItWaste $itWaste)
    {
        $itWaste->delete();

        return redirect()->route('it-wastes.index')->with('success', 'Data limbah IT berhasil dihapus.');
    }

    public function printHandover(Request $request)
    {
        $data = $request->validate(['box_code' => 'required|string|max:100']);
        $batch = ItWasteBatch::with(['wastes.equipment', 'wastes.creator'])
            ->where('box_code', $data['box_code'])
            ->where('status', 'handed_over')
            ->firstOrFail();
        $wastes = $batch->wastes->sortBy('waste_date');

        return view('it_wastes.print_handover', [
            'boxCode' => $data['box_code'],
            'wastes' => $wastes,
            'recipient' => $batch->handover_recipient ?? 'Bagian Limbah B3',
            'handoverDate' => $batch->handed_over_at,
        ]);
    }

    private function validateWaste(Request $request): array
    {
        return $request->validate([
            'waste_date' => 'required|date',
            'waste_type' => 'required|string|max:100',
            'description' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0.01',
            'unit' => 'required|string|max:30',
            'equipment_id' => 'nullable|exists:equipments,id',
            'collection_status' => 'required|in:collected,ready_to_handover,handed_over',
            'storage_location' => 'nullable|string|max:255',
            'handling_method' => 'nullable|string|max:255',
            'handover_recipient' => 'nullable|required_if:collection_status,handed_over|string|max:255',
            'handed_over_at' => 'nullable|required_if:collection_status,handed_over|date',
            'notes' => 'nullable|string|max:2000',
        ]);
    }

    private function activeOrNewBoxCode(int $year, string $status): string
    {
        if ($status === 'collected') {
            $activeBox = ItWaste::where('collection_status', 'collected')
                ->where('box_code', 'like', 'BOX-IT-B3-' . $year . '-%')
                ->latest('id')
                ->value('box_code');
            if ($activeBox) {
                return $activeBox;
            }
        }

        return $this->nextBoxCode($year);
    }

    private function nextWasteCode(int $year): string
    {
        $prefix = 'LMB-' . $year . '-';
        $sequence = ItWaste::where('waste_code', 'like', $prefix . '%')->count() + 1;

        return $prefix . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }

    private function nextBoxCode(int $year): string
    {
        $prefix = 'BOX-IT-B3-' . $year . '-';
        $sequence = ItWaste::where('box_code', 'like', $prefix . '%')->distinct('box_code')->count('box_code') + 1;

        return $prefix . str_pad((string) $sequence, 3, '0', STR_PAD_LEFT);
    }
}
