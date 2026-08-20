<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\LicenseTransaction;
use App\Models\LicenseType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class LicenseController extends Controller
{
    private const TYPES = ['purchase', 'assign', 'release', 'renew'];

    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $licenses = LicenseType::withCount('transactions')->when($search !== '', function ($query) use ($search) {
            $keyword = '%' . $search . '%';
            $query->where(fn ($inner) => $inner->where('code', 'like', $keyword)->orWhere('name', 'like', $keyword)->orWhere('vendor', 'like', $keyword)->orWhere('category', 'like', $keyword));
        })->orderBy('name')->paginate($this->resolvePerPage($request))->withQueryString();
        $transactions = LicenseTransaction::with(['licenseType', 'equipment', 'user', 'creator'])->latest('transaction_date')->latest('id')->paginate(15, ['*'], 'history_page')->withQueryString();
        $editing = $request->integer('edit') ? LicenseType::find($request->integer('edit')) : null;
        $users = User::where('is_active', true)->orderBy('name')->get();
        $equipment = Equipment::with(['owner', 'assetLocation'])->orderBy('name')->get();
        $summary = [
            'licenses' => LicenseType::count(),
            'seats' => (int) LicenseType::sum('total_seats'),
            'available' => (int) LicenseType::sum(DB::raw('total_seats - used_seats')),
            'expiring' => LicenseType::whereNotNull('expiry_date')->whereBetween('expiry_date', [now()->toDateString(), now()->addDays(30)->toDateString()])->count(),
        ];

        return view('licenses.index', compact('licenses', 'transactions', 'editing', 'users', 'equipment', 'summary', 'search'));
    }

    public function storeType(Request $request)
    {
        $data = $this->validateType($request);
        $data['code'] = $this->nextCode();
        LicenseType::create($data);
        return back()->with('success', 'Lisensi berhasil ditambahkan.');
    }

    public function updateType(Request $request, LicenseType $licenseType)
    {
        $data = $this->validateType($request, $licenseType);
        unset($data['code']);
        $licenseType->update($data);
        return back()->with('success', 'Lisensi berhasil diperbarui.');
    }

    public function destroyType(LicenseType $licenseType)
    {
        if ($licenseType->transactions()->exists()) return back()->with('error', 'Lisensi tidak dapat dihapus karena sudah memiliki riwayat.');
        $licenseType->delete();
        return back()->with('success', 'Lisensi berhasil dihapus.');
    }

    public function storeTransaction(Request $request)
    {
        $data = $request->validate([
            'license_type_id' => ['required', 'exists:license_types,id'],
            'type' => ['required', Rule::in(self::TYPES)],
            'quantity' => ['required', 'integer', 'min:1'],
            'transaction_date' => ['required', 'date'],
            'equipment_id' => ['nullable', 'exists:equipments,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($data) {
            $license = LicenseType::whereKey($data['license_type_id'])->lockForUpdate()->firstOrFail();
            $before = (int) $license->used_seats;
            $quantity = (int) $data['quantity'];
            $after = match ($data['type']) {
                'assign' => $before + $quantity,
                'release' => $before - $quantity,
                default => $before,
            };
            if ($after < 0) abort(422, 'Seat terpakai tidak dapat kurang dari nol.');
            if ($after > $license->total_seats) abort(422, 'Seat terpakai melebihi total seat lisensi.');
            $totalSeats = $data['type'] === 'purchase' ? $license->total_seats + $quantity : $license->total_seats;
            if ($data['type'] === 'renew') $totalSeats = $license->total_seats;
            $license->update(['total_seats' => $totalSeats, 'used_seats' => $after]);
            LicenseTransaction::create([...$data, 'seats_before' => $before, 'seats_after' => $after, 'created_by' => auth()->id()]);
        });
        return back()->with('success', 'Transaksi lisensi berhasil dicatat.');
    }

    private function validateType(Request $request, ?LicenseType $licenseType = null): array
    {
        return $request->validate([
            'code' => ['nullable', 'string', 'max:80', Rule::unique('license_types', 'code')->ignore($licenseType?->id)],
            'name' => ['required', 'string', 'max:255'], 'category' => ['nullable', 'string', 'max:100'], 'vendor' => ['nullable', 'string', 'max:255'],
            'license_key' => ['nullable', 'string', 'max:500'], 'total_seats' => ['required', 'integer', 'min:1'], 'used_seats' => ['required', 'integer', 'min:0', 'lte:total_seats'],
            'start_date' => ['nullable', 'date'], 'expiry_date' => ['nullable', 'date', 'after_or_equal:start_date'], 'cost' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['active', 'expired', 'suspended'])], 'notes' => ['nullable', 'string', 'max:2000'],
        ]);
    }

    private function nextCode(): string
    {
        $number = LicenseType::count() + 1;
        do {
            $code = 'LIC-' . str_pad((string) $number, 4, '0', STR_PAD_LEFT);
            $number++;
        } while (LicenseType::where('code', $code)->exists());

        return $code;
    }
}
