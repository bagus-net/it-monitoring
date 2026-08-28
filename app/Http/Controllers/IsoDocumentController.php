<?php

namespace App\Http\Controllers;

use App\Models\IsoDocument;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class IsoDocumentController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $user = $request->user();
        $documents = IsoDocument::with(['creator', 'permittedUsers'])
            ->when(!$this->canManage($user), fn ($query) => $query->whereHas('permittedUsers', fn ($users) => $users->whereKey($user->id)))
            ->when($search !== '', function ($query) use ($search) {
                $keyword = '%' . $search . '%';
                $query->where(fn ($inner) => $inner
                    ->where('document_number', 'like', $keyword)
                    ->orWhere('title', 'like', $keyword)
                    ->orWhere('category', 'like', $keyword));
            })
            ->latest('document_date')
            ->latest('id')
            ->paginate($this->resolvePerPage($request))
            ->withQueryString();

        $summary = [
            'available' => $documents->total(),
            'categories' => IsoDocument::when(!$this->canManage($user), fn ($query) => $query->whereHas('permittedUsers', fn ($users) => $users->whereKey($user->id)))->distinct('category')->count('category'),
            'shared_with_me' => $user->accessibleIsoDocuments()->count(),
        ];

        return view('iso_documents.index', compact('documents', 'summary', 'search'));
    }

    public function create(Request $request)
    {
        $this->authorizeManage($request);
        $users = User::where('is_active', true)->orderBy('name')->get();

        return view('iso_documents.create', compact('users'));
    }

    public function store(Request $request)
    {
        $this->authorizeManage($request);
        $data = $this->validateDocument($request, true);
        $file = $request->file('document_file');
        $data['document_number'] = $this->nextDocumentNumber();
        $data['file_path'] = $file->store('iso-documents');
        $data['file_name'] = $file->getClientOriginalName();
        $data['created_by_user_id'] = $request->user()->id;
        unset($data['document_file'], $data['permitted_user_ids']);

        $document = IsoDocument::create($data);
        $document->permittedUsers()->sync($request->input('permitted_user_ids', []));

        return redirect()->route('iso-documents.show', $document)->with('success', 'Dokumen ISO berhasil dibagikan.');
    }

    public function show(Request $request, IsoDocument $isoDocument)
    {
        $this->authorizeAccess($request, $isoDocument);
        $isoDocument->load(['creator', 'permittedUsers']);

        return view('iso_documents.show', compact('isoDocument'));
    }

    public function edit(Request $request, IsoDocument $isoDocument)
    {
        $this->authorizeManage($request);
        $users = User::where('is_active', true)->orderBy('name')->get();
        $isoDocument->load('permittedUsers');

        return view('iso_documents.edit', compact('isoDocument', 'users'));
    }

    public function update(Request $request, IsoDocument $isoDocument)
    {
        $this->authorizeManage($request);
        $data = $this->validateDocument($request, false);
        $previousFilePath = $isoDocument->file_path;
        if ($request->hasFile('document_file')) {
            $file = $request->file('document_file');
            $data['file_path'] = $file->store('iso-documents');
            $data['file_name'] = $file->getClientOriginalName();
        }
        unset($data['document_file'], $data['permitted_user_ids']);
        $isoDocument->update($data);
        $isoDocument->permittedUsers()->sync($request->input('permitted_user_ids', []));
        if (isset($data['file_path']) && $previousFilePath && $previousFilePath !== $data['file_path']) {
            Storage::delete($previousFilePath);
        }

        return redirect()->route('iso-documents.show', $isoDocument)->with('success', 'Dokumen ISO berhasil diperbarui.');
    }

    public function download(Request $request, IsoDocument $isoDocument)
    {
        $this->authorizeAccess($request, $isoDocument);
        abort_unless(Storage::exists($isoDocument->file_path), 404);

        return Storage::download($isoDocument->file_path, $isoDocument->file_name);
    }

    public function preview(Request $request, IsoDocument $isoDocument)
    {
        $this->authorizeAccess($request, $isoDocument);
        abort_unless(Storage::exists($isoDocument->file_path), 404);
        $extension = strtolower(pathinfo($isoDocument->file_name, PATHINFO_EXTENSION));
        abort_unless(in_array($extension, ['pdf', 'xls', 'xlsx'], true), 422);

        return response()->file(Storage::path($isoDocument->file_path), [
            'Content-Type' => $extension === 'pdf' ? 'application/pdf' : Storage::mimeType($isoDocument->file_path),
            'Content-Disposition' => 'inline; filename="' . str_replace('"', '', $isoDocument->file_name) . '"',
        ]);
    }

    public function destroy(Request $request, IsoDocument $isoDocument)
    {
        $this->authorizeManage($request);
        Storage::delete($isoDocument->file_path);
        $isoDocument->delete();

        return redirect()->route('iso-documents.index')->with('success', 'Dokumen ISO berhasil dihapus.');
    }

    private function validateDocument(Request $request, bool $requireFile): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'revision' => 'nullable|string|max:30',
            'document_date' => 'nullable|date',
            'description' => 'nullable|string|max:2000',
            'document_file' => ($requireFile ? 'required' : 'nullable') . '|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:20480',
            'permitted_user_ids' => 'required|array|min:1',
            'permitted_user_ids.*' => 'exists:users,id',
        ]);
    }

    private function authorizeAccess(Request $request, IsoDocument $document): void
    {
        abort_unless($this->canManage($request->user()) || $document->permittedUsers()->whereKey($request->user()->id)->exists(), 403);
    }

    private function authorizeManage(Request $request): void
    {
        abort_unless($this->canManage($request->user()), 403);
    }

    private function canManage(User $user): bool
    {
        return $user->isMaster() || $user->isAdminIt();
    }

    private function nextDocumentNumber(): string
    {
        $prefix = 'ISO-' . now()->format('Y') . '-';
        $number = IsoDocument::where('document_number', 'like', $prefix . '%')->count() + 1;

        return $prefix . str_pad((string) $number, 4, '0', STR_PAD_LEFT);
    }
}
