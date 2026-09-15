@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3"><div><h2 class="mb-1">Karantina Aset Rusak</h2><p class="text-muted mb-0">Hanya peralatan dengan kondisi rusak yang dapat dimasukkan ke karantina.</p></div><a href="{{ route('equipment-quarantines.index') }}" class="btn btn-outline-secondary">Kembali</a></div>
    <form method="POST" action="{{ route('equipment-quarantines.store') }}" class="card"><div class="card-body row g-3">@csrf
        <div class="col-12"><label class="form-label">Peralatan IT Rusak</label><select name="equipment_id" class="form-select" required><option value="">-- Pilih Peralatan Rusak --</option>@foreach ($equipments as $equipment)<option value="{{ $equipment->id }}" @selected(old('equipment_id') == $equipment->id)>{{ $equipment->name }}{{ $equipment->asset_tag ? ' - ' . $equipment->asset_tag : '' }}{{ $equipment->assetLocation?->name ? ' | ' . $equipment->assetLocation->name : '' }}</option>@endforeach</select>@if ($equipments->isEmpty())<small class="text-danger">Tidak ada peralatan dengan kondisi rusak yang dapat dikarantina.</small>@endif</div>
        <div class="col-md-4"><label class="form-label">Tanggal Karantina</label><input type="date" name="quarantined_at" class="form-control" value="{{ old('quarantined_at', now()->format('Y-m-d')) }}" required></div>
        <div class="col-md-4"><label class="form-label">Hasil Karantina</label><select name="outcome" class="form-select" required><option value="rusak" @selected(old('outcome') === 'rusak')>Rusak / Perlu Perbaikan</option><option value="nonaktif" @selected(old('outcome') === 'nonaktif')>Nonaktif / Tidak Dipakai</option></select></div>
        <div class="col-md-4"><label class="form-label">Alasan</label><input name="reason" class="form-control" value="{{ old('reason') }}" placeholder="Contoh: Kerusakan hardware" required></div>
        <div class="col-12"><label class="form-label">Catatan</label><textarea name="notes" rows="4" class="form-control" placeholder="Detail pemeriksaan, nomor tiket, atau rencana tindak lanjut">{{ old('notes') }}</textarea></div>
    </div><div class="card-footer text-end"><button class="btn btn-primary" {{ $equipments->isEmpty() ? 'disabled' : '' }}><i class="bi bi-shield-exclamation"></i> Simpan ke Karantina</button></div></form>
</div>
@endsection
