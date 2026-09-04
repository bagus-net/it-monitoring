<?php

namespace App\Http\Controllers;

use App\Models\Innovation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InnovationController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $year = $request->integer('year') ?: null;
        $availableYears = Innovation::selectRaw('YEAR(innovation_date) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        $innovations = Innovation::with('creator')
            ->when($year, fn ($query) => $query->whereYear('innovation_date', $year))
            ->when($search !== '', function ($query) use ($search) {
                $keyword = '%' . $search . '%';
                $query->where(fn ($inner) => $inner
                    ->where('title', 'like', $keyword)
                    ->orWhere('implementation', 'like', $keyword)
                    ->orWhere('notes', 'like', $keyword));
            })
            ->orderByDesc('innovation_date')
            ->paginate($this->resolvePerPage($request))
            ->withQueryString();

        return view('innovations.index', compact('innovations', 'availableYears', 'year', 'search'));
    }

    public function print(Request $request)
    {
        $year = $request->integer('year') ?: now()->year;
        $month = $request->integer('month') ?: null;

        $availableYears = Innovation::selectRaw('YEAR(innovation_date) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->push(now()->year)
            ->unique()
            ->sortDesc()
            ->values();

        $query = Innovation::whereYear('innovation_date', $year)->orderBy('innovation_date')->orderBy('id');
        if ($month) {
            $query->whereMonth('innovation_date', $month);
        }
        $innovations = $query->get();

        $monthsList = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        if ($month && isset($monthsList[$month])) {
            $activeMonths = [$month => $monthsList[$month]];
        } else {
            $activeMonths = $monthsList;
        }

        $groupedInnovations = [];
        foreach ($activeMonths as $mNum => $mName) {
            $items = $innovations->filter(fn ($item) => (int) $item->innovation_date?->month === $mNum)->values();
            $groupedInnovations[$mNum] = [
                'name' => $mName,
                'items' => $items,
            ];
        }

        $signatures = User::documentSignatories();
        $signatureNames = [
            'reporter' => $signatures['reporter']?->name ?? 'Bagus',
        ];

        return view('innovations.print', compact(
            'year', 'month', 'availableYears', 'monthsList', 'groupedInnovations', 'signatures', 'signatureNames'
        ));
    }

    public function create()
    {
        return view('innovations.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateInnovation($request);
        $this->storePaper($request, $data);
        $data['created_by_user_id'] = auth()->id();

        $innovation = Innovation::create($data);

        return redirect()->route('innovations.show', $innovation)->with('success', 'Inovasi IT berhasil ditambahkan.');
    }

    public function show(Innovation $innovation)
    {
        $innovation->load('creator');

        return view('innovations.show', compact('innovation'));
    }

    public function edit(Innovation $innovation)
    {
        return view('innovations.edit', compact('innovation'));
    }

    public function update(Request $request, Innovation $innovation)
    {
        $data = $this->validateInnovation($request);
        $this->storePaper($request, $data, $innovation);

        $innovation->update($data);

        return redirect()->route('innovations.show', $innovation)->with('success', 'Inovasi IT berhasil diperbarui.');
    }

    public function destroy(Innovation $innovation)
    {
        $innovation->delete();

        return redirect()->route('innovations.index')->with('success', 'Inovasi IT dipindahkan ke Sampah Data.');
    }

    private function validateInnovation(Request $request): array
    {
        return $request->validate([
            'innovation_date' => 'required|date',
            'title' => 'required|string|max:255',
            'implementation' => 'nullable|string',
            'implementation_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'paper' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);
    }

    private function storePaper(Request $request, array &$data, ?Innovation $innovation = null): void
    {
        unset($data['paper']);
        if (!$request->hasFile('paper')) {
            return;
        }
        if ($innovation?->paper_path) {
            Storage::disk('public')->delete($innovation->paper_path);
        }
        $file = $request->file('paper');
        $data['paper_path'] = $file->store('innovation-papers', 'public');
        $data['paper_name'] = $file->getClientOriginalName();
    }
}
