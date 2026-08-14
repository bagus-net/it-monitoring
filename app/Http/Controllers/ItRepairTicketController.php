<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\ItRepairTicket;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ItRepairTicketController extends Controller
{
    public function notifications(): JsonResponse
    {
        $latestTicket = ItRepairTicket::with('equipment')
            ->where('status', 'open')
            ->latest('created_at')
            ->first();

        return response()->json([
            'openCount' => ItRepairTicket::where('status', 'open')->count(),
            'latest' => $latestTicket ? [
                'id' => $latestTicket->id,
                'number' => $latestTicket->ticket_number,
                'equipment' => $latestTicket->equipment->name ?? 'Peralatan belum dipilih',
                'problem' => $latestTicket->problem_description,
                'createdAt' => $latestTicket->created_at?->toIso8601String(),
            ] : null,
        ]);
    }

    public function index(Request $request)
    {
        $status = $request->input('status');
        $tickets = ItRepairTicket::with('equipment')
            ->when($status, fn ($query) => $query->where('status', $status))
            ->orderByRaw("FIELD(status, 'open', 'in_progress', 'resolved')")
            ->orderByDesc('reported_at')
            ->get();
        $summary = [
            'open' => ItRepairTicket::where('status', 'open')->count(),
            'in_progress' => ItRepairTicket::where('status', 'in_progress')->count(),
            'resolved' => ItRepairTicket::where('status', 'resolved')->count(),
        ];

        return view('it_repair_tickets.index', compact('tickets', 'summary', 'status'));
    }

    public function create()
    {
        $equipment = Equipment::orderBy('name')->get();

        return view('it_repair_tickets.create', compact('equipment'));
    }

    public function store(Request $request)
    {
        $data = $this->validateRequestTicket($request);
        $data['ticket_number'] = $this->nextTicketNumber();
        $data['status'] = 'open';
        if ($request->hasFile('error_photo')) {
            $data['error_photo_path'] = $request->file('error_photo')->store('repair-ticket-attachments', 'public');
        }
        ItRepairTicket::create($data);

        return redirect()->route('it-repair-tickets.index')->with('success', 'Tiket perbaikan IT berhasil dibuat.');
    }

    public function show(ItRepairTicket $itRepairTicket)
    {
        $itRepairTicket->load('equipment');

        return view('it_repair_tickets.show', compact('itRepairTicket'));
    }

    public function edit(ItRepairTicket $itRepairTicket)
    {
        return redirect()->route('it-repair-tickets.repair', $itRepairTicket);
    }

    public function update(Request $request, ItRepairTicket $itRepairTicket)
    {
        return $this->updateRepair($request, $itRepairTicket);
    }

    public function repair(ItRepairTicket $itRepairTicket)
    {
        $itRepairTicket->load('equipment');

        return view('it_repair_tickets.repair', compact('itRepairTicket'));
    }

    public function updateRepair(Request $request, ItRepairTicket $itRepairTicket)
    {
        $data = $this->validateRepair($request);
        if ($request->hasFile('repair_attachment')) {
            if ($itRepairTicket->repair_attachment_path) {
                Storage::disk('public')->delete($itRepairTicket->repair_attachment_path);
            }
            $data['repair_attachment_path'] = $request->file('repair_attachment')->store('repair-ticket-attachments', 'public');
        }
        $itRepairTicket->update($data);

        return redirect()->route('it-repair-tickets.show', $itRepairTicket)->with('success', 'Tiket perbaikan IT berhasil diperbarui.');
    }

    public function destroy(ItRepairTicket $itRepairTicket)
    {
        if ($itRepairTicket->error_photo_path) {
            Storage::disk('public')->delete($itRepairTicket->error_photo_path);
        }
        if ($itRepairTicket->repair_attachment_path) {
            Storage::disk('public')->delete($itRepairTicket->repair_attachment_path);
        }
        $itRepairTicket->delete();

        return redirect()->route('it-repair-tickets.index')->with('success', 'Tiket perbaikan IT berhasil dihapus.');
    }

    private function validateRequestTicket(Request $request): array
    {
        return $request->validate([
            'equipment_id' => 'nullable|exists:equipments,id',
            'equipment_category' => 'nullable|string|max:100',
            'error_type' => 'nullable|string|max:100',
            'error_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'department' => 'nullable|string|max:255',
            'reported_at' => 'required|date',
            'problem_description' => 'required|string',
            'priority' => 'required|in:low,normal,high,urgent',
            'reported_by' => 'nullable|string|max:255',
        ]);
    }

    private function validateRepair(Request $request): array
    {
        return $request->validate([
            'repair_action' => 'nullable|string',
            'repair_attachment' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'status' => 'required|in:open,in_progress,resolved',
            'assigned_to' => 'nullable|string|max:255',
            'started_at' => 'nullable|date',
            'resolved_at' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);
    }

    private function nextTicketNumber(): string
    {
        $prefix = 'IT-' . now()->format('Y') . '-';
        $lastNumber = ItRepairTicket::where('ticket_number', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('ticket_number');
        $sequence = $lastNumber ? ((int) substr($lastNumber, -4)) + 1 : 1;

        return $prefix . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }
}
