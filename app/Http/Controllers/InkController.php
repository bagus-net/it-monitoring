<?php

namespace App\Http\Controllers;

use App\Models\InkTransaction;
use App\Models\InkType;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class InkController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $inkTypes = InkType::withCount('transactions')
            ->when($search !== '', function ($query) use ($search) {
                $keyword = '%' . $search . '%';
                $query->where(fn ($inner) => $inner->where('name', 'like', $keyword)
                    ->orWhere('brand', 'like', $keyword)
                    ->orWhere('color', 'like', $keyword));
            })
            ->orderBy('name')
            ->paginate($this->resolvePerPage($request))
            ->withQueryString();

        $transactions = InkTransaction::with(['inkType', 'equipment.type', 'equipment.owner', 'equipment.assetLocation', 'creator'])
            ->latest('transaction_date')
            ->latest('id')
            ->paginate(15, ['*'], 'history_page')
            ->withQueryString();

        $summary = [
            'types' => InkType::count(),
            'units' => (int) InkType::sum('current_stock'),
            'low_stock' => InkType::whereColumn('current_stock', '<=', 'minimum_stock')->count(),
            'transactions' => InkTransaction::count(),
        ];
        $printers = Equipment::with(['type', 'owner', 'assetLocation'])
            ->where(function ($query) {
                $query->where('name', 'like', '%printer%')
                    ->orWhereHas('type', fn ($type) => $type->where('name', 'like', '%printer%'));
            })
            ->orderBy('name')
            ->get();
        $editing = $request->integer('edit') ? InkType::find($request->integer('edit')) : null;

        return view('ink.index', compact('inkTypes', 'transactions', 'summary', 'search', 'editing', 'printers'));
    }

    public function storeType(Request $request)
    {
        $data = $this->validateType($request);
        InkType::create($data);

        return back()->with('success', 'Jenis tinta berhasil ditambahkan.');
    }

    public function updateType(Request $request, InkType $inkType)
    {
        $data = $this->validateType($request, $inkType);
        $inkType->update($data);

        return back()->with('success', 'Jenis tinta berhasil diperbarui.');
    }

    public function destroyType(InkType $inkType)
    {
        if ($inkType->transactions()->exists()) {
            return back()->with('error', 'Jenis tinta tidak dapat dihapus karena sudah memiliki riwayat transaksi.');
        }

        $inkType->delete();

        return back()->with('success', 'Jenis tinta berhasil dihapus.');
    }

    public function storeTransaction(Request $request)
    {
        $data = $request->validate([
            'ink_type_id' => ['required', 'exists:ink_types,id'],
            'equipment_id' => ['nullable', 'exists:equipments,id'],
            'type' => ['required', Rule::in(['in', 'out'])],
            'quantity' => ['required', 'integer', 'min:1'],
            'transaction_date' => ['required', 'date'],
            'reference' => ['nullable', 'string', 'max:255'],
            'recipient' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($data) {
            $inkType = InkType::whereKey($data['ink_type_id'])->lockForUpdate()->firstOrFail();
            $before = (int) $inkType->current_stock;
            $quantity = (int) $data['quantity'];
            $after = $data['type'] === 'in' ? $before + $quantity : $before - $quantity;

            if ($after < 0) {
                abort(422, 'Stok tinta tidak mencukupi untuk transaksi pemakaian.');
            }

            $inkType->update(['current_stock' => $after]);
            InkTransaction::create([
                ...$data,
                'stock_before' => $before,
                'stock_after' => $after,
                'created_by' => auth()->id(),
            ]);
        });

        return back()->with('success', 'Transaksi stok tinta berhasil dicatat.');
    }

    private function validateType(Request $request, ?InkType $inkType = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:100'],
            'unit' => ['required', 'string', 'max:30'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
    }
}
