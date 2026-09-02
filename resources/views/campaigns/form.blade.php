@php
    $campaign = $campaign ?? null;
@endphp
<div class="row g-3">
    <div class="col-md-8"><label class="form-label">Nama Campaign</label><input name="name" class="form-control" value="{{ old('name', $campaign?->name) }}" placeholder="Contoh: Upgrade Awareness Keamanan IT" required></div>
    <div class="col-md-4"><label class="form-label">Status</label><select name="status" class="form-select" required>@foreach(['planned'=>'Planned','active'=>'Active','paused'=>'Paused','completed'=>'Completed','archived'=>'Archived'] as $key => $label)<option value="{{ $key }}" @selected(old('status', $campaign?->status ?? 'planned') === $key)>{{ $label }}</option>@endforeach</select></div>
    <div class="col-md-6"><label class="form-label">PIC Campaign</label><select name="owner_user_id" class="form-select"><option value="">-- Pilih PIC --</option>@foreach($users as $user)<option value="{{ $user->id }}" @selected((string) old('owner_user_id', $campaign?->owner_user_id) === (string) $user->id)>{{ $user->name }}{{ $user->department ? ' - ' . $user->department : '' }}</option>@endforeach</select></div>
    <div class="col-md-3"><label class="form-label">Channel</label><input name="channel" class="form-control" value="{{ old('channel', $campaign?->channel) }}" placeholder="Email / Event / Web"></div>
    <div class="col-md-3"><label class="form-label">Target Audiens</label><input name="audience" class="form-control" value="{{ old('audience', $campaign?->audience) }}" placeholder="Karyawan / Customer"></div>
    <div class="col-md-3"><label class="form-label">Mulai</label><input type="date" name="start_date" class="form-control" value="{{ old('start_date', $campaign?->start_date?->format('Y-m-d')) }}"></div>
    <div class="col-md-3"><label class="form-label">Selesai</label><input type="date" name="end_date" class="form-control" value="{{ old('end_date', $campaign?->end_date?->format('Y-m-d')) }}"></div>
    <div class="col-md-2"><label class="form-label">Target</label><input type="number" step="0.01" min="0" name="target_value" class="form-control" value="{{ old('target_value', $campaign?->target_value) }}"></div>
    <div class="col-md-2"><label class="form-label">Realisasi</label><input type="number" step="0.01" min="0" name="achieved_value" class="form-control" value="{{ old('achieved_value', $campaign?->achieved_value ?? 0) }}"></div>
    <div class="col-md-2"><label class="form-label">Satuan</label><input name="target_unit" class="form-control" value="{{ old('target_unit', $campaign?->target_unit) }}" placeholder="% / leads"></div>
    <div class="col-md-3"><label class="form-label">Anggaran</label><input type="number" step="0.01" min="0" name="budget" class="form-control" value="{{ old('budget', $campaign?->budget) }}" placeholder="0"></div>
    <div class="col-md-3"><label class="form-label">Progress Otomatis</label><div class="campaign-progress-preview"><span>{{ $campaign?->progress ?? 0 }}%</span><div><i style="width:{{ $campaign?->progress ?? 0 }}%"></i></div></div></div>
    <div class="col-12"><label class="form-label">Objective</label><textarea name="objective" class="form-control" rows="2" placeholder="Apa hasil bisnis yang ingin dicapai?">{{ old('objective', $campaign?->objective) }}</textarea></div>
    <div class="col-12"><label class="form-label">Deskripsi Eksekusi</label><textarea name="description" class="form-control" rows="3" placeholder="Rencana, milestone, dan deliverable campaign...">{{ old('description', $campaign?->description) }}</textarea></div>
    <div class="col-12"><label class="form-label">Catatan</label><textarea name="notes" class="form-control" rows="2">{{ old('notes', $campaign?->notes) }}</textarea></div>
</div>
