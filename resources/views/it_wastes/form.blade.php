<div class="row g-3">
    <div class="col-md-6"><label class="form-label">Kode Limbah</label><input class="form-control" value="{{ $itWaste->waste_code ?? $nextWasteCode }}" disabled><small class="text-muted">Dibuat otomatis oleh sistem saat data disimpan.</small></div>
    <div class="col-md-6"><label class="form-label">Kode Box Penampungan</label><input class="form-control" value="{{ $itWaste->box_code ?? $nextBoxCode }}" disabled><small class="text-muted">Limbah terkumpul otomatis pada box aktif.</small></div>
    <div class="col-md-4"><label class="form-label">Tanggal Limbah</label><input type="date" name="waste_date" class="form-control" value="{{ old('waste_date', optional($itWaste->waste_date ?? null)->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required></div>
    <div class="col-md-4"><label class="form-label">Jenis Limbah</label><select name="waste_type" class="form-select" required><option value="">-- Pilih Jenis --</option>@foreach (['Botol Tinta Bekas', 'Sisa Tinta', 'Limbah Cleaning Printer', 'Cartridge / Toner Bekas', 'Baterai Bekas', 'Kabel Elektronik', 'Komponen Elektronik', 'Lainnya'] as $type)<option value="{{ $type }}" {{ old('waste_type', $itWaste->waste_type ?? '') === $type ? 'selected' : '' }}>{{ $type }}</option>@endforeach</select></div>
    <div class="col-md-4"><label class="form-label">Jumlah dan Satuan</label><div class="input-group"><input type="number" name="quantity" class="form-control" min="0.01" step="0.01" value="{{ old('quantity', $itWaste->quantity ?? 1) }}" required><input name="unit" class="form-control" value="{{ old('unit', $itWaste->unit ?? 'pcs') }}" required></div></div>
    <div class="col-md-6"><label class="form-label">Deskripsi Limbah</label><input name="description" class="form-control" value="{{ old('description', $itWaste->description ?? '') }}" placeholder="Contoh: Botol tinta HP 682 Black kosong" required></div>
    <div class="col-md-6"><label class="form-label">Sumber Peralatan</label><select name="equipment_id" class="form-select"><option value="">Tidak dikaitkan dengan peralatan</option>@foreach ($equipments as $equipment)<option value="{{ $equipment->id }}" {{ (int) old('equipment_id', $itWaste->equipment_id ?? 0) === $equipment->id ? 'selected' : '' }}>{{ $equipment->name }}{{ $equipment->asset_tag ? ' - ' . $equipment->asset_tag : '' }}</option>@endforeach</select></div>
    <div class="col-md-6"><label class="form-label">Lokasi Penyimpanan</label><input name="storage_location" class="form-control" value="{{ old('storage_location', $itWaste->storage_location ?? '') }}" placeholder="Contoh: Gudang IT / Tempat penampungan B3"></div>
    <div class="col-md-6"><label class="form-label">Metode Penanganan</label><input name="handling_method" class="form-control" value="{{ old('handling_method', $itWaste->handling_method ?? '') }}" placeholder="Contoh: Dikumpulkan untuk vendor daur ulang"></div>
    <div class="col-md-4"><label class="form-label">Status Limbah</label><select name="collection_status" id="collection_status" class="form-select" required><option value="collected" {{ old('collection_status', $itWaste->collection_status ?? 'collected') === 'collected' ? 'selected' : '' }}>Terkumpul di Box</option><option value="ready_to_handover" {{ old('collection_status', $itWaste->collection_status ?? '') === 'ready_to_handover' ? 'selected' : '' }}>Box Penuh / Siap Diserahkan</option><option value="handed_over" {{ old('collection_status', $itWaste->collection_status ?? '') === 'handed_over' ? 'selected' : '' }}>Sudah Diserahkan ke Limbah B3</option></select></div>
    <div class="col-md-4"><label class="form-label">Tanggal Serah Terima</label><input type="date" name="handed_over_at" id="handed_over_at" class="form-control" value="{{ old('handed_over_at', optional($itWaste->handed_over_at ?? null)->format('Y-m-d')) }}"></div>
    <div class="col-12"><label class="form-label">Penerima Limbah B3</label><input name="handover_recipient" id="handover_recipient" class="form-control" value="{{ old('handover_recipient', $itWaste->handover_recipient ?? '') }}" placeholder="Contoh: Bagian Limbah B3 PT Mulia Grand Manufacture"></div>
    <div class="col-12"><label class="form-label">Keterangan</label><textarea name="notes" rows="3" class="form-control">{{ old('notes', $itWaste->notes ?? '') }}</textarea></div>
</div>
<script>
    (() => {
        const status = document.getElementById('collection_status');
        const handoverDate = document.getElementById('handed_over_at');
        const recipient = document.getElementById('handover_recipient');
        const sync = () => {
            const required = status.value === 'handed_over';
            handoverDate.required = required;
            recipient.required = required;
        };
        status.addEventListener('change', sync);
        sync();
    })();
</script>
