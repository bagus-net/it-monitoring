<?php

namespace App\Http\Controllers;

use App\Models\SparepartTransaction;
use App\Models\SparepartType;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SparepartController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $sparepartTypes = SparepartType::withCount('transactions')
            ->when($search !== '', function ($query) use ($search) {
                $keyword = '%' . $search . '%';
                $query->where(fn ($inner) => $inner->where('code', 'like', $keyword)
                    ->orWhere('name', 'like', $keyword)
                    ->orWhere('category', 'like', $keyword)
                    ->orWhere('brand', 'like', $keyword));
            })
            ->orderBy('name')
            ->paginate($this->resolvePerPage($request))
            ->withQueryString();

        $transactions = SparepartTransaction::with(['sparepartType', 'equipment.type', 'equipment.owner', 'equipment.assetLocation', 'creator'])
            ->latest('transaction_date')
            ->latest('id')
            ->paginate(15, ['*'], 'history_page')
            ->withQueryString();

        $summary = [
            'types' => SparepartType::count(),
            'units' => (int) SparepartType::sum('current_stock'),
            'low_stock' => SparepartType::whereColumn('current_stock', '<=', 'minimum_stock')->count(),
            'transactions' => SparepartTransaction::count(),
        ];
        $editing = $request->integer('edit') ? SparepartType::find($request->integer('edit')) : null;
        $equipmentOptions = Equipment::with(['type', 'owner', 'assetLocation'])->orderBy('name')->get();

        return view('spareparts.index', compact('sparepartTypes', 'transactions', 'summary', 'search', 'editing', 'equipmentOptions'));
    }

    public function storeType(Request $request)
    {
        $data = $this->validateType($request);
        $data['code'] = $this->nextCode();
        SparepartType::create($data);

        return back()->with('success', 'Jenis sparepart berhasil ditambahkan.');
    }

    public function updateType(Request $request, SparepartType $sparepartType)
    {
        $data = $this->validateType($request, $sparepartType);
        unset($data['code']);
        $sparepartType->update($data);

        return back()->with('success', 'Jenis sparepart berhasil diperbarui.');
    }

    public function destroyType(SparepartType $sparepartType)
    {
        if ($sparepartType->transactions()->exists()) {
            return back()->with('error', 'Jenis sparepart tidak dapat dihapus karena sudah memiliki riwayat transaksi.');
        }

        $sparepartType->delete();

        return back()->with('success', 'Jenis sparepart berhasil dihapus.');
    }

    public function storeTransaction(Request $request)
    {
        $data = $request->validate([
            'sparepart_type_id' => ['required', 'exists:sparepart_types,id'],
            'equipment_id' => ['nullable', 'exists:equipments,id'],
            'type' => ['required', Rule::in(['in', 'out'])],
            'quantity' => ['required', 'integer', 'min:1'],
            'transaction_date' => ['required', 'date'],
            'reference' => ['nullable', 'string', 'max:255'],
            'recipient' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($data) {
            $sparepartType = SparepartType::whereKey($data['sparepart_type_id'])->lockForUpdate()->firstOrFail();
            $before = (int) $sparepartType->current_stock;
            $quantity = (int) $data['quantity'];
            $after = $data['type'] === 'in' ? $before + $quantity : $before - $quantity;

            if ($after < 0) {
                abort(422, 'Stok sparepart tidak mencukupi untuk transaksi pemakaian.');
            }

            $sparepartType->update(['current_stock' => $after]);
            SparepartTransaction::create([
                ...$data,
                'stock_before' => $before,
                'stock_after' => $after,
                'created_by' => auth()->id(),
            ]);
        });

        return back()->with('success', 'Transaksi stok sparepart berhasil dicatat.');
    }

    private function validateType(Request $request, ?SparepartType $sparepartType = null): array
    {
        return $request->validate([
            'code' => ['nullable', 'string', 'max:80', Rule::unique('sparepart_types', 'code')->ignore($sparepartType?->id)],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'brand' => ['nullable', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:30'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
    }

    private function nextCode(): string
    {
        $number = SparepartType::count() + 1;
        do {
            $code = 'SP-' . str_pad((string) $number, 4, '0', STR_PAD_LEFT);
            $number++;
        } while (SparepartType::where('code', $code)->exists());

        return $code;
    }
}
