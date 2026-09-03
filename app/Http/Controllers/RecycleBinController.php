<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Innovation;
use App\Models\IsoDocument;
use App\Models\ItWaste;
use App\Models\ItWasteBatch;
use App\Models\MaintenanceChecklist;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RecycleBinController extends Controller
{
    public function index()
    {
        $records = collect();
        foreach ($this->recordTypes() as $type => $definition) {
            $definition['model']::onlyTrashed()->latest('deleted_at')->get()->each(function ($record) use ($records, $type, $definition) {
                $records->push([
                    'type' => $type,
                    'label' => $definition['label'],
                    'id' => $record->id,
                    'name' => $definition['name']($record),
                    'deleted_at' => $record->deleted_at,
                ]);
            });
        }

        return view('recycle_bin.index', ['records' => $records->sortByDesc('deleted_at')->values()]);
    }

    public function restore(string $type, int $id)
    {
        $record = $this->findTrashed($type, $id);
        $record->restore();

        return back()->with('success', $this->recordTypes()[$type]['label'] . ' berhasil dipulihkan.');
    }

    public function forceDelete(Request $request, string $type, int $id)
    {
        $record = $this->findTrashed($type, $id);
        if ($record instanceof User && IsoDocument::withTrashed()->where('created_by_user_id', $record->id)->exists()) {
            return back()->with('error', 'User tidak dapat dihapus permanen karena masih menjadi pembuat Dokumen ISO.');
        }

        $this->deleteAssociatedFile($record);
        $record->forceDelete();

        return back()->with('success', $this->recordTypes()[$type]['label'] . ' dihapus permanen.');
    }

    private function findTrashed(string $type, int $id)
    {
        abort_unless(array_key_exists($type, $this->recordTypes()), 404);

        return $this->recordTypes()[$type]['model']::onlyTrashed()->findOrFail($id);
    }

    private function deleteAssociatedFile($record): void
    {
        if ($record instanceof Equipment && $record->photo_path) {
            Storage::disk('public')->delete($record->photo_path);
        }
        if ($record instanceof User && $record->profile_photo_path) {
            Storage::disk('public')->delete($record->profile_photo_path);
        }
        if ($record instanceof Innovation && $record->paper_path) {
            Storage::disk('public')->delete($record->paper_path);
        }
        if ($record instanceof IsoDocument && $record->file_path) {
            Storage::delete($record->file_path);
        }
        if ($record instanceof IsoDocument) {
            $record->files->each(fn ($file) => Storage::delete($file->file_path));
        }
    }

    private function recordTypes(): array
    {
        return [
            'equipment' => ['model' => Equipment::class, 'label' => 'Peralatan IT', 'name' => fn ($record) => $record->name],
            'user' => ['model' => User::class, 'label' => 'User', 'name' => fn ($record) => $record->name],
            'maintenance_checklist' => ['model' => MaintenanceChecklist::class, 'label' => 'Checklist Perawatan', 'name' => fn ($record) => 'Checklist #' . $record->id],
            'innovation' => ['model' => Innovation::class, 'label' => 'Inovasi IT', 'name' => fn ($record) => $record->title],
            'iso_document' => ['model' => IsoDocument::class, 'label' => 'Dokumen ISO', 'name' => fn ($record) => $record->title],
            'it_waste' => ['model' => ItWaste::class, 'label' => 'Limbah IT', 'name' => fn ($record) => $record->waste_code ?: $record->description],
            'it_waste_batch' => ['model' => ItWasteBatch::class, 'label' => 'Box Limbah', 'name' => fn ($record) => $record->box_code],
        ];
    }
}
