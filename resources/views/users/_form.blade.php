@csrf
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Nama Lengkap</label>
        <input type="text" name="name" value="{{ old('name', $user?->name) }}" class="form-control" required>
        <div class="form-text">Nama ini dipakai sebagai PIC pada peralatan yang ditugaskan.</div>
    </div>
    <div class="col-md-6">
        <label class="form-label">Email</label>
        <input type="email" name="email" value="{{ old('email', $user?->email) }}" class="form-control" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Foto Profil</label>
        <div class="d-flex align-items-center gap-3">
            <img class="user-form-avatar" src="{{ $user?->profile_photo_path ? asset('storage/' . $user->profile_photo_path) : asset('images/default-avatar.svg') }}" alt="Foto profil">
            <div class="flex-grow-1"><input type="file" name="profile_photo" class="form-control @error('profile_photo') is-invalid @enderror" accept=".jpg,.jpeg,.png,.webp"><small class="text-muted">JPG, PNG, atau WebP. Maksimum 2 MB.</small>@error('profile_photo')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        </div>
    </div>
    <div class="col-md-4">
        <label class="form-label">Departemen</label>
        <input type="text" name="department" value="{{ old('department', $user?->department) }}" class="form-control">
    </div>
    <div class="col-md-4">
        <label class="form-label">Hak Akses</label>
        <select name="role" class="form-select" required>
            @foreach(\App\Models\User::ROLE_LABELS as $key => $label)
                <option value="{{ $key }}" @selected(old('role', $user?->role ?? \App\Models\User::ROLE_USER) === $key)>{{ $label }}</option>
            @endforeach
        </select>
        <div class="form-text">Master: semua menu · Admin IT: tanpa jadwal & log · User: hanya tiket peralatannya.</div>
    </div>
    <div class="col-md-4">
        <label class="form-label">Status Akun</label>
        <select name="is_active" class="form-select">
            <option value="1" @selected((string) old('is_active', $user?->is_active ?? 1) === '1')>Aktif</option>
            <option value="0" @selected((string) old('is_active', $user?->is_active ?? 1) === '0')>Nonaktif</option>
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Kata Sandi {{ $user ? '(kosongkan bila tidak diubah)' : '' }}</label>
        <input type="password" name="password" class="form-control" {{ $user ? '' : 'required' }}>
    </div>
    <div class="col-md-6">
        <label class="form-label">Ulangi Kata Sandi</label>
        <input type="password" name="password_confirmation" class="form-control" {{ $user ? '' : 'required' }}>
    </div>
    <div class="col-12">
        <label class="form-label">Peralatan IT yang Dipegang</label>
        <input type="search" id="equipmentPickerSearch" class="form-control mb-2" placeholder="Cari peralatan (nama, jenis, lokasi, atau PIC)...">
        <div class="equipment-picker">
            @forelse($equipments as $equipment)
                <label class="equipment-option">
                    <input type="checkbox" name="equipment_ids[]" value="{{ $equipment->id }}" @checked(in_array($equipment->id, old('equipment_ids', $selectedEquipments)))>
                    <span>
                        <strong>{{ $equipment->name }}</strong>
                        <small>PIC: {{ $equipment->owner_name ?: 'Belum ada PIC' }}</small>
                        <small>{{ $equipment->type->name ?? 'Tanpa jenis' }} · {{ $equipment->assetLocation?->name ?: 'Lokasi belum diisi' }}</small>
                    </span>
                </label>
            @empty
                <div class="text-muted p-2">Semua peralatan sudah dipegang user lain.</div>
            @endforelse
        </div>
        <div id="equipmentPickerEmpty" class="text-muted p-2 d-none">Peralatan tidak ditemukan.</div>
        <div class="form-text">Peralatan yang dipilih otomatis memakai user ini sebagai PIC, dan hanya user tersebut yang bisa melihat tiketnya.</div>
    </div>
</div>
<div class="d-flex justify-content-end gap-2 mt-3">
    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Batal</a>
    <button type="submit" class="btn btn-brand">Simpan</button>
</div>
<style>.user-form-avatar{width:54px;height:54px;flex:0 0 54px;border:1px solid #cbd5e1;border-radius:50%;object-fit:cover;background:#e0f2fe}.equipment-picker{max-height:290px;overflow:auto;border:1px solid #dbe5ef;border-radius:5px;background:#fff}.equipment-option{display:flex;gap:9px;align-items:flex-start;padding:8px 11px;border-bottom:1px solid #eef2f7;cursor:pointer}.equipment-option:last-child{border-bottom:0}.equipment-option small{display:block;color:#64748b;font-size:.74rem}.equipment-option:hover{background:#f8fafc}</style>
<script>
    (function () {
        const input = document.getElementById('equipmentPickerSearch');
        if (!input) return;
        input.addEventListener('input', () => {
            const keyword = input.value.trim().toLowerCase();
            let visible = 0;
            document.querySelectorAll('.equipment-option').forEach(option => {
                const match = option.textContent.toLowerCase().includes(keyword);
                option.style.display = match ? '' : 'none';
                if (match) visible += 1;
            });
            document.getElementById('equipmentPickerEmpty')?.classList.toggle('d-none', visible > 0);
        });
    })();
</script>
