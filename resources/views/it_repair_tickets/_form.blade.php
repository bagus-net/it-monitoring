@php
    $ticket = $ticket ?? null;
    $status = old('status', $ticket?->status ?? 'open');
    $priority = old('priority', $ticket?->priority ?? 'normal');
@endphp
<div class="row g-3">
    <div class="col-md-6"><label class="form-label">Peralatan IT</label><select name="equipment_id" class="form-select"><option value="">-- Pilih Peralatan --</option>@foreach ($equipment as $item)<option value="{{ $item->id }}" {{ (string) old('equipment_id', $ticket?->equipment_id ?? '') === (string) $item->id ? 'selected' : '' }}>{{ $item->name }} - {{ $item->owner_name ?: 'PIC belum diisi' }} - {{ $item->assetLocation?->name ?: ($item->getRawOriginal('location') ?: 'Lokasi belum diisi') }}</option>@endforeach</select></div>
    <div class="col-md-3"><label class="form-label">Bagian / Departemen</label><input name="department" class="form-control" value="{{ old('department', $ticket?->department ?? '') }}" placeholder="Contoh: XPDC / IKO"></div>
    <div class="col-md-3"><label class="form-label">Tanggal & Jam Lapor</label><input type="datetime-local" name="reported_at" class="form-control" value="{{ old('reported_at', ($ticket?->reported_at ?? now())->format('Y-m-d\TH:i')) }}" required></div>
    <div class="col-md-4"><label class="form-label">Prioritas</label><select name="priority" class="form-select"><option value="low" {{ $priority === 'low' ? 'selected' : '' }}>Rendah</option><option value="normal" {{ $priority === 'normal' ? 'selected' : '' }}>Normal</option><option value="high" {{ $priority === 'high' ? 'selected' : '' }}>Tinggi</option><option value="urgent" {{ $priority === 'urgent' ? 'selected' : '' }}>Mendesak</option></select></div>
    <div class="col-md-4"><label class="form-label">Status Tiket</label><select name="status" class="form-select"><option value="open" {{ $status === 'open' ? 'selected' : '' }}>Open</option><option value="in_progress" {{ $status === 'in_progress' ? 'selected' : '' }}>Proses</option><option value="resolved" {{ $status === 'resolved' ? 'selected' : '' }}>Selesai</option></select></div>
    <div class="col-md-4"><label class="form-label">Dilaporkan Oleh</label><input name="reported_by" class="form-control" value="{{ old('reported_by', $ticket?->reported_by ?? '') }}"></div>
    <div class="col-12"><label class="form-label">Keluhan / Kerusakan</label><textarea name="problem_description" class="form-control" rows="3" required placeholder="Jelaskan gangguan atau kerusakan yang terjadi">{{ old('problem_description', $ticket?->problem_description ?? '') }}</textarea></div>
    <div class="col-12"><label class="form-label">Tindakan Perbaikan</label><textarea name="repair_action" class="form-control" rows="3" placeholder="Isi setelah perbaikan dilakukan">{{ old('repair_action', $ticket?->repair_action ?? '') }}</textarea></div>
    <div class="col-md-4"><label class="form-label">Diperbaiki Oleh</label><input name="assigned_to" class="form-control" value="{{ old('assigned_to', $ticket?->assigned_to ?? '') }}"></div>
    <div class="col-md-4"><label class="form-label">Mulai Perbaikan</label><input type="datetime-local" name="started_at" class="form-control" value="{{ old('started_at', $ticket?->started_at?->format('Y-m-d\TH:i')) }}"></div>
    <div class="col-md-4"><label class="form-label">Selesai Perbaikan</label><input type="datetime-local" name="resolved_at" class="form-control" value="{{ old('resolved_at', $ticket?->resolved_at?->format('Y-m-d\TH:i')) }}"></div>
    <div class="col-12"><label class="form-label">Catatan Tambahan</label><textarea name="notes" class="form-control" rows="2">{{ old('notes', $ticket?->notes ?? '') }}</textarea></div>
</div>
