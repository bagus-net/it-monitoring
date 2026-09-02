<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\EquipmentTransfer;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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
            'transfer_mode' => ['required', Rule::in(['assign', 'swap'])],
            'swap_equipment_id' => ['nullable', 'different:equipment_id', 'exists:equipments,id'],
            'to_user_id' => ['nullable', 'exists:users,id', Rule::exists('users', 'id')->where('is_active', true)],
            'to_owner_name' => ['nullable', 'string', 'max:255'],
            'to_department' => ['nullable', 'string', 'max:255'],
            'to_location_id' => ['nullable', 'exists:locations,id'],
            'effective_date' => ['required', 'date'],
            'reason' => ['required', 'string', 'max:2000'],
            'notes' => ['nullable', 'string', 'max:4000'],
        ]);

        if ($data['transfer_mode'] === 'assign' && empty($data['to_user_id']) && empty($data['to_owner_name'])) {
            return back()->withInput()->withErrors(['to_owner_name' => 'Pilih user atau isi nama PIC baru.']);
        }
        if ($data['transfer_mode'] === 'swap' && empty($data['swap_equipment_id'])) {
            return back()->withInput()->withErrors(['swap_equipment_id' => 'Pilih alat pasangan untuk menukar PIC.']);
        }

        $equipment = Equipment::with('owner')->findOrFail($data['equipment_id']);
        $swapEquipment = $data['transfer_mode'] === 'swap' ? Equipment::with('owner')->findOrFail($data['swap_equipment_id']) : null;
        $equipmentIds = collect([$equipment, $swapEquipment])->filter();
        if ($equipmentIds->contains(fn ($item) => EquipmentTransfer::where('equipment_id', $item->id)->whereIn('status', ['pending_approval', 'approved'])->exists())) {
            return back()->withInput()->with('error', 'Salah satu alat sudah memiliki mutasi yang belum selesai.');
        }

        $targetUser = !empty($data['to_user_id']) ? User::find($data['to_user_id']) : null;
        DB::transaction(function () use ($data, $equipment, $swapEquipment, $targetUser) {
            $swapGroup = $swapEquipment ? (string) Str::uuid() : null;
            $common = ['reason' => $data['reason'], 'effective_date' => $data['effective_date'], 'requested_by' => auth()->id(), 'status' => 'pending_approval', 'notes' => $data['notes'] ?? null];
            EquipmentTransfer::create([
                ...$common, 'equipment_id' => $equipment->id, 'swap_group' => $swapGroup,
                'from_user_id' => $equipment->user_id, 'from_owner_name' => $equipment->owner_name ?: $equipment->owner?->name, 'from_department' => $equipment->department ?: $equipment->owner?->department, 'from_location_id' => $equipment->location_id,
                'to_user_id' => $swapEquipment ? $swapEquipment->user_id : ($data['to_user_id'] ?? null), 'to_owner_name' => $swapEquipment ? ($swapEquipment->owner_name ?: $swapEquipment->owner?->name) : ($data['to_owner_name'] ?: $targetUser?->name), 'to_department' => $swapEquipment ? ($swapEquipment->department ?: $swapEquipment->owner?->department) : ($data['to_department'] ?: $targetUser?->department), 'to_location_id' => $swapEquipment ? null : ($data['to_location_id'] ?? null),
            ]);
            if ($swapEquipment) {
                EquipmentTransfer::create([
                    ...$common, 'equipment_id' => $swapEquipment->id, 'swap_group' => $swapGroup,
                    'from_user_id' => $swapEquipment->user_id, 'from_owner_name' => $swapEquipment->owner_name ?: $swapEquipment->owner?->name, 'from_department' => $swapEquipment->department ?: $swapEquipment->owner?->department, 'from_location_id' => $swapEquipment->location_id,
                    'to_user_id' => $equipment->user_id, 'to_owner_name' => $equipment->owner_name ?: $equipment->owner?->name, 'to_department' => $equipment->department ?: $equipment->owner?->department, 'to_location_id' => null,
                ]);
            }
        });

        return redirect()->route('equipment-transfers.index')->with('success', $swapEquipment ? 'Pengajuan tukar PIC berhasil dibuat untuk dua alat.' : 'Pengajuan mutasi berhasil dibuat.');
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
        $group = $equipmentTransfer->swap_group ? EquipmentTransfer::where('swap_group', $equipmentTransfer->swap_group)->get() : collect([$equipmentTransfer]);
        $group->each(fn ($transfer) => $transfer->update(['status' => $action === 'approve' ? 'approved' : 'rejected', 'approved_by' => auth()->id(), 'approved_at' => now()]));

        return back()->with('success', $action === 'approve' ? 'Mutasi disetujui dan menunggu serah terima.' : 'Mutasi ditolak.');
    }

    public function complete(EquipmentTransfer $equipmentTransfer)
    {
        abort_unless($equipmentTransfer->status === 'approved', 422, 'Mutasi harus disetujui sebelum serah terima.');

        DB::transaction(function () use ($equipmentTransfer) {
            $group = $equipmentTransfer->swap_group ? EquipmentTransfer::where('swap_group', $equipmentTransfer->swap_group)->get() : collect([$equipmentTransfer]);
            abort_unless($group->every(fn ($transfer) => $transfer->status === 'approved'), 422, 'Semua mutasi pasangan harus disetujui terlebih dahulu.');
            $group->each(function ($transfer) {
                $transfer->equipment()->update(['user_id' => $transfer->to_user_id, 'owner_name' => $transfer->to_owner_name, 'department' => $transfer->to_department, 'location_id' => $transfer->to_location_id ?: $transfer->from_location_id]);
                $transfer->update(['status' => 'completed', 'completed_by' => auth()->id(), 'completed_at' => now()]);
            });
        });

        return back()->with('success', 'Serah terima selesai. PIC aktif peralatan sudah diperbarui.');
    }

    public function destroy(EquipmentTransfer $equipmentTransfer)
    {
        $equipmentTransfer->delete();

        return redirect()->route('equipment-transfers.index')->with('success', 'Riwayat mutasi berhasil dihapus.');
    }
}
