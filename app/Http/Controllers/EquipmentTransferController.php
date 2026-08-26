<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\EquipmentTransfer;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class EquipmentTransferController extends Controller
{
    private const STATUSES = [
        'pending_approval' => 'Menunggu Persetujuan',
        'approved' => 'Disetujui, Menunggu Serah Terima',
        'completed' => 'Selesai',
        'rejected' => 'Ditolak',
        'cancelled' => 'Dibatalkan',
    ];

    public function notifications(): JsonResponse
    {
        $user = auth()->user();

        $latestPendingApproval = EquipmentTransfer::with('equipment')
            ->where('status', 'pending_approval')
            ->latest('created_at')
            ->first();

        $latestMyUnfinished = EquipmentTransfer::with('equipment')
            ->where('requested_by', $user?->id)
            ->whereIn('status', ['pending_approval', 'approved'])
            ->latest('created_at')
            ->first();

        return response()->json([
            'pendingApprovalCount' => EquipmentTransfer::where('status', 'pending_approval')->count(),
            'myUnfinishedCount' => EquipmentTransfer::where('requested_by', $user?->id)
                ->whereIn('status', ['pending_approval', 'approved'])
                ->count(),
            'latestPendingApproval' => $latestPendingApproval ? [
                'id' => $latestPendingApproval->id,
                'equipment' => $latestPendingApproval->equipment->name ?? 'Peralatan tidak ditemukan',
                'status' => $latestPendingApproval->status,
            ] : null,
            'latestMyUnfinished' => $latestMyUnfinished ? [
                'id' => $latestMyUnfinished->id,
                'equipment' => $latestMyUnfinished->equipment->name ?? 'Peralatan tidak ditemukan',
                'status' => $latestMyUnfinished->status,
            ] : null,
        ]);
    }

    public function index(Request $request)
    {
        $status = $request->input('status');
        $transfers = EquipmentTransfer::with(['equipment', 'fromUser', 'toUser', 'requester'])
            ->when($status, fn ($query, $value) => $query->where('status', $value))
            ->latest()
            ->paginate($this->resolvePerPage($request))
            ->withQueryString();

        $summary = collect(self::STATUSES)->mapWithKeys(fn ($label, $key) => [$key => EquipmentTransfer::where('status', $key)->count()]);

        return view('equipment_transfers.index', compact('transfers', 'status', 'summary'));
    }

    public function create(Request $request)
    {
        $equipment = Equipment::with(['owner', 'assetLocation'])->whereDoesntHave('transfers', fn ($query) => $query->whereIn('status', ['pending_approval', 'approved']))->orderBy('name')->get();
        $users = User::where('is_active', true)->where('role', User::ROLE_USER)->orderBy('name')->get();
        $locations = Location::orderBy('name')->get();

        return view('equipment_transfers.create', compact('equipment', 'users', 'locations'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'equipment_id' => ['required', 'exists:equipments,id'],
            'to_user_id' => ['nullable', 'required_without:to_owner_name', 'exists:users,id', Rule::exists('users', 'id')->where('is_active', true)],
            'to_owner_name' => ['nullable', 'required_without:to_user_id', 'string', 'max:255'],
            'to_department' => ['nullable', 'string', 'max:255'],
            'to_location_id' => ['nullable', 'exists:locations,id'],
            'effective_date' => ['required', 'date'],
            'reason' => ['required', 'string', 'max:2000'],
            'notes' => ['nullable', 'string', 'max:4000'],
        ]);

        $equipment = Equipment::with('owner')->findOrFail($data['equipment_id']);
        if (EquipmentTransfer::where('equipment_id', $equipment->id)->whereIn('status', ['pending_approval', 'approved'])->exists()) {
            return back()->withInput()->with('error', 'Peralatan ini sudah memiliki mutasi yang belum selesai.');
        }

        $targetUser = !empty($data['to_user_id']) ? User::find($data['to_user_id']) : null;
        EquipmentTransfer::create([
            ...$data,
            'from_user_id' => $equipment->user_id,
            'from_owner_name' => $equipment->owner_name ?: $equipment->owner?->name,
            'from_department' => $equipment->department ?: $equipment->owner?->department,
            'from_location_id' => $equipment->location_id,
            'to_owner_name' => $data['to_owner_name'] ?: $targetUser?->name,
            'to_department' => $data['to_department'] ?: $targetUser?->department,
            'requested_by' => auth()->id(),
            'status' => 'pending_approval',
        ]);

        return redirect()->route('equipment-transfers.index')->with('success', 'Pengajuan mutasi berhasil dibuat.');
    }

    public function show(EquipmentTransfer $equipmentTransfer)
    {
        $equipmentTransfer->load(['equipment', 'fromUser', 'toUser', 'fromLocation', 'toLocation', 'requester', 'approver', 'completer']);
        return view('equipment_transfers.show', ['transfer' => $equipmentTransfer, 'statuses' => self::STATUSES]);
    }

    public function print(EquipmentTransfer $equipmentTransfer)
    {
        $equipmentTransfer->load(['equipment', 'fromUser', 'toUser', 'fromLocation', 'toLocation', 'requester', 'approver', 'completer']);

        return view('equipment_transfers.print', ['transfer' => $equipmentTransfer, 'statuses' => self::STATUSES]);
    }

    public function approve(Request $request, EquipmentTransfer $equipmentTransfer)
    {
        $action = $request->validate(['action' => ['required', Rule::in(['approve', 'reject'])]])['action'];
        abort_unless($equipmentTransfer->status === 'pending_approval', 422, 'Mutasi ini sudah diproses.');
        $equipmentTransfer->update([
            'status' => $action === 'approve' ? 'approved' : 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', $action === 'approve' ? 'Mutasi disetujui dan menunggu serah terima.' : 'Mutasi ditolak.');
    }

    public function complete(EquipmentTransfer $equipmentTransfer)
    {
        abort_unless($equipmentTransfer->status === 'approved', 422, 'Mutasi harus disetujui sebelum serah terima.');

        DB::transaction(function () use ($equipmentTransfer) {
            $equipmentTransfer->equipment()->update([
                'user_id' => $equipmentTransfer->to_user_id,
                'owner_name' => $equipmentTransfer->to_owner_name,
                'department' => $equipmentTransfer->to_department,
                'location_id' => $equipmentTransfer->to_location_id ?: $equipmentTransfer->from_location_id,
            ]);
            $equipmentTransfer->update(['status' => 'completed', 'completed_by' => auth()->id(), 'completed_at' => now()]);
        });

        return back()->with('success', 'Serah terima selesai. PIC aktif peralatan sudah diperbarui.');
    }

    public function destroy(EquipmentTransfer $equipmentTransfer)
    {
        $equipmentTransfer->delete();

        return redirect()->route('equipment-transfers.index')->with('success', 'Riwayat mutasi berhasil dihapus.');
    }
}
