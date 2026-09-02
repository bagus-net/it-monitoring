<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\ItRepairTicket;
use App\Services\WhatsappNotificationService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ItRepairTicketController extends Controller
{
    /** Karyawan hanya boleh melihat tiket miliknya sendiri atau tiket peralatan yang dia pegang. */
    private function applyOwnershipScope($query)
    {
        $user = auth()->user();
        if (!$user || !$user->isEmployee()) {
            return $query;
        }

        return $query->where(function ($inner) use ($user) {
            $inner->where('user_id', $user->id)
                ->orWhereHas('equipment', fn ($relation) => $relation->where('user_id', $user->id)->orWhere('owner_name', $user->name));
        });
    }

    private function authorizeTicketAccess(ItRepairTicket $ticket): void
    {
        $user = auth()->user();
        if (!$user || !$user->isEmployee()) {
            return;
        }

        $ownsTicket = $ticket->user_id === $user->id
            || ($ticket->equipment && ($ticket->equipment->user_id === $user->id || $ticket->equipment->owner_name === $user->name));
        abort_unless($ownsTicket, 403, 'Anda hanya dapat melihat tiket peralatan Anda sendiri.');
    }

    public function notifications(): JsonResponse
    {
        $latestTicket = ItRepairTicket::with('equipment')
            ->tap(fn ($query) => $this->applyOwnershipScope($query))
            ->where('status', 'open')
            ->latest('created_at')
            ->first();

        return response()->json([
            'openCount' => $this->applyOwnershipScope(ItRepairTicket::query())->where('status', 'open')->count(),
            'inProgressCount' => $this->applyOwnershipScope(ItRepairTicket::query())->where('status', 'in_progress')->count(),
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
        $search = trim((string) $request->input('search'));
        $filters = [
            'priority' => $request->input('priority'),
            'category' => $request->input('category'),
            'repair_category' => $request->input('repair_category'),
            'location_id' => $request->input('location_id'),
            'from' => $request->input('from'),
            'to' => $request->input('to'),
        ];
        $tickets = ItRepairTicket::with('equipment.assetLocation')
            ->tap(fn ($query) => $this->applyOwnershipScope($query))
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($filters['priority'], fn ($query, $priority) => $query->where('priority', $priority))
            ->when($filters['category'], fn ($query, $category) => $query->where('equipment_category', $category))
            ->when($filters['repair_category'], fn ($query, $value) => $query->where('repair_category', $value))
            ->when($filters['location_id'], fn ($query, $locationId) => $query->whereHas('equipment', fn ($relation) => $relation->where('location_id', $locationId)))
            ->when($filters['from'], fn ($query, $from) => $query->whereDate('reported_at', '>=', $from))
            ->when($filters['to'], fn ($query, $to) => $query->whereDate('reported_at', '<=', $to))
            ->when($search !== '', function ($query) use ($search) {
                $keyword = '%' . $search . '%';
                $query->where(function ($inner) use ($keyword) {
                    $inner->where('ticket_number', 'like', $keyword)
                        ->orWhere('problem_description', 'like', $keyword)
                        ->orWhere('repair_action', 'like', $keyword)
                        ->orWhere('department', 'like', $keyword)
                        ->orWhere('reported_by', 'like', $keyword)
                        ->orWhere('assigned_to', 'like', $keyword)
                        ->orWhere('equipment_category', 'like', $keyword)
                        ->orWhere('software_name', 'like', $keyword)
                        ->orWhere('repair_category', 'like', $keyword)
                        ->orWhere('error_type', 'like', $keyword)
                        ->orWhereHas('equipment', fn ($relation) => $relation->where('name', 'like', $keyword));
                });
            })
            ->orderByRaw("FIELD(status, 'open', 'in_progress', 'resolved')")
            ->orderByDesc('reported_at')
            ->paginate($this->resolvePerPage($request))
            ->withQueryString();
        $summary = [
            'open' => $this->applyOwnershipScope(ItRepairTicket::query())->where('status', 'open')->count(),
            'in_progress' => $this->applyOwnershipScope(ItRepairTicket::query())->where('status', 'in_progress')->count(),
            'resolved' => $this->applyOwnershipScope(ItRepairTicket::query())->where('status', 'resolved')->count(),
            'hardware' => $this->applyOwnershipScope(ItRepairTicket::query())->where('repair_category', 'hardware')->count(),
            'software' => $this->applyOwnershipScope(ItRepairTicket::query())->where('repair_category', 'software')->count(),
        ];
        $categories = ItRepairTicket::whereNotNull('equipment_category')->distinct()->orderBy('equipment_category')->pluck('equipment_category');
        $locations = \App\Models\Location::orderBy('name')->get(['id', 'name']);
        $myEquipments = auth()->user()->isEmployee()
            ? Equipment::with(['type', 'manufacturer', 'assetLocation', 'owner'])
                ->where(fn ($query) => $query->where('user_id', auth()->id())->orWhere('owner_name', auth()->user()->name))
                ->orderBy('name')
                ->get()
            : collect();

        return view('it_repair_tickets.index', compact('tickets', 'summary', 'status', 'search', 'filters', 'categories', 'locations', 'myEquipments'));
    }

    public function create()
    {
        $equipment = Equipment::with('assetLocation')
            ->when(auth()->user()->isEmployee(), fn ($query) => $query->where(fn ($inner) => $inner->where('user_id', auth()->id())->orWhere('owner_name', auth()->user()->name)))
            ->orderBy('name')
            ->get();

        return view('it_repair_tickets.create', compact('equipment'));
    }

    public function store(Request $request, WhatsappNotificationService $whatsapp)
    {
        $data = $this->validateRequestTicket($request);
        $data['ticket_number'] = $this->nextTicketNumber();
        $data['status'] = 'open';
        $data['user_id'] = auth()->id();
        $data['reported_by'] = $data['reported_by'] ?? auth()->user()->name;
        $data['department'] = $data['department'] ?? auth()->user()->department;

        $equipment = $request->filled('equipment_id') ? Equipment::find($request->equipment_id) : null;
        if ($equipment) {
            $data['equipment_owner_user_id'] = $equipment->user_id;
            $data['equipment_owner_name'] = $equipment->owner_name ?: $equipment->owner?->name;
            $data['equipment_owner_department'] = $equipment->department ?: $equipment->owner?->department;
        }

        if ($request->hasFile('error_photo')) {
            $data['error_photo_path'] = $request->file('error_photo')->store('repair-ticket-attachments', 'public');
        }
        $ticket = ItRepairTicket::create($data);
        $whatsapp->notifyNewTicket($ticket->load('equipment'));

        return redirect()->route('it-repair-tickets.index')->with('success', 'Tiket perbaikan IT berhasil dibuat.');
    }

    public function show(ItRepairTicket $itRepairTicket)
    {
        $this->authorizeTicketAccess($itRepairTicket);
        $itRepairTicket->load(['equipment', 'reporter', 'technician', 'approver']);
        $signatures = [
            'reporter' => $this->signerFor($itRepairTicket->reporter, $itRepairTicket->reported_by),
            'technician' => $this->signerFor($itRepairTicket->technician, $itRepairTicket->assigned_to),
            'approver' => $itRepairTicket->approver,
        ];

        return view('it_repair_tickets.show', compact('itRepairTicket', 'signatures'));
    }

    public function approve(ItRepairTicket $itRepairTicket)
    {
        abort_unless($itRepairTicket->status === 'resolved', 422, 'Tiket hanya dapat disetujui setelah berstatus Selesai.');

        $itRepairTicket->update([
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('it-repair-tickets.show', $itRepairTicket)->with('success', 'Tiket disetujui dan tanda tangan Anda otomatis dipakai pada dokumen cetak.');
    }

    /** Akun terkait dipakai lebih dulu, bila kosong dicocokkan dengan nama yang tercatat pada tiket. */
    private function signerFor(?\App\Models\User $user, ?string $name): ?\App\Models\User
    {
        if ($user) {
            return $user;
        }

        return $name
            ? \App\Models\User::whereRaw('LOWER(name) = ?', [mb_strtolower(trim($name))])->first()
            : null;
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
        $technicians = User::where('is_active', true)
            ->whereIn('role', [User::ROLE_MASTER, User::ROLE_ADMIN_IT])
            ->orderBy('name')
            ->get(['id', 'name', 'department']);

        return view('it_repair_tickets.repair', compact('itRepairTicket', 'technicians'));
    }

    public function updateRepair(Request $request, ItRepairTicket $itRepairTicket)
    {
        $data = $this->validateRepair($request);
        $data['technician_id'] = $data['technician_id'] ?: auth()->id();
        $selectedTechnician = !empty($data['technician_id']) ? User::find($data['technician_id']) : null;
        $data['assigned_to'] = $selectedTechnician?->name ?: ($data['assigned_to'] ?: auth()->user()->name);
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
            'repair_category' => 'required|in:hardware,software',
            'software_name' => 'nullable|required_if:repair_category,software|string|max:100',
            'equipment_category' => 'nullable|required_if:repair_category,hardware|string|max:100',
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
            'technician_id' => ['nullable', 'exists:users,id', Rule::exists('users', 'id')->where('is_active', true)->whereIn('role', [User::ROLE_MASTER, User::ROLE_ADMIN_IT])],
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
