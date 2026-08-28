<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $role = $request->input('role');
        $users = User::withCount('equipments')
            ->when($role, fn ($query, $value) => $query->where('role', $value))
            ->when($search !== '', function ($query) use ($search) {
                $keyword = '%' . $search . '%';
                $query->where(fn ($inner) => $inner->where('name', 'like', $keyword)
                    ->orWhere('email', 'like', $keyword)
                    ->orWhere('department', 'like', $keyword));
            })
            ->orderBy('name')
            ->paginate($this->resolvePerPage($request))
            ->withQueryString();

        $summary = [
            'total' => User::count(),
            'master' => User::where('role', User::ROLE_MASTER)->count(),
            'admin_it' => User::where('role', User::ROLE_ADMIN_IT)->count(),
            'user' => User::where('role', User::ROLE_USER)->count(),
        ];

        return view('users.index', compact('users', 'summary', 'search', 'role'));
    }

    public function create()
    {
        return view('users.create', [
            'user' => null,
            'equipments' => $this->assignableEquipments(),
            'selectedEquipments' => [],
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateUser($request);
        $equipmentIds = $data['equipment_ids'] ?? [];
        unset($data['equipment_ids'], $data['profile_photo']);

        $user = User::create($data);
        if ($request->hasFile('profile_photo')) {
            $user->update(['profile_photo_path' => $request->file('profile_photo')->store('profile-photos', 'public')]);
        }
        $this->syncEquipments($user, $equipmentIds);

        return redirect()->route('users.index')->with('success', 'User baru berhasil dibuat.');
    }

    public function edit(User $user)
    {
        return view('users.edit', [
            'user' => $user,
            'equipments' => $this->assignableEquipments($user),
            'selectedEquipments' => $user->equipments()->pluck('id')->all(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $data = $this->validateUser($request, $user);
        $equipmentIds = $data['equipment_ids'] ?? [];
        unset($data['equipment_ids'], $data['profile_photo']);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $previousPhotoPath = $user->profile_photo_path;
        if ($request->hasFile('profile_photo')) {
            $data['profile_photo_path'] = $request->file('profile_photo')->store('profile-photos', 'public');
        }
        $user->update($data);
        if (isset($data['profile_photo_path']) && $previousPhotoPath && $previousPhotoPath !== $data['profile_photo_path']) {
            Storage::disk('public')->delete($previousPhotoPath);
        }
        $this->syncEquipments($user, $equipmentIds);

        return redirect()->route('users.index')->with('success', 'Data user berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $user->equipments()->update(['user_id' => null]);
        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }

    private function validateUser(Request $request, ?User $user = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user?->id)],
            'department' => ['nullable', 'string', 'max:255'],
            'role' => ['required', Rule::in(array_keys(User::ROLE_LABELS))],
            'is_active' => ['nullable', 'boolean'],
            'password' => [$user ? 'nullable' : 'required', 'confirmed', Password::min(8)],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'equipment_ids' => ['nullable', 'array'],
            'equipment_ids.*' => ['integer', 'exists:equipments,id'],
        ]);
    }

    /** Peralatan yang belum dipegang akun lain, ditambah peralatan milik user yang sedang diedit. */
    private function assignableEquipments(?User $user = null)
    {
        return Equipment::with(['type', 'assetLocation'])
            ->where(fn ($query) => $query->whereNull('user_id')->when($user, fn ($inner) => $inner->orWhere('user_id', $user->id)))
            ->orderBy('name')
            ->get();
    }

    private function syncEquipments(User $user, array $equipmentIds): void
    {
        $user->equipments()->whereNotIn('id', $equipmentIds ?: [0])->update(['user_id' => null]);

        if ($equipmentIds) {
            Equipment::whereIn('id', $equipmentIds)->update(array_filter([
                'user_id' => $user->id,
                'owner_name' => $user->name,
                'department' => $user->department,
            ], fn ($value) => $value !== null));
        }
    }
}
