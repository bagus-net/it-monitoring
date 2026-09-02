@extends('layouts.app')

@section('content')
<div class="container mt-4 transfer-page">
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div><div class="text-uppercase fw-bold small text-primary">Peralatan IT</div><h2 class="mb-1">Ajukan Mutasi Peralatan</h2><p class="text-muted mb-0">Pengajuan akan menunggu persetujuan Master sebelum serah terima.</p></div>
        <a href="{{ route('equipment-transfers.index') }}" class="btn btn-outline-secondary">Kembali</a>
    </div>
    @if($errors->any())<div class="alert alert-danger"><ul class="mb-0 ps-3">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <div class="card transfer-form"><div class="card-body">
        <form method="POST" action="{{ route('equipment-transfers.store') }}" class="row g-3">@csrf
            <div class="col-12"><label class="form-label">Jenis Proses</label><div class="transfer-mode-grid"><label class="transfer-mode-card"><input type="radio" name="transfer_mode" value="assign" @checked(old('transfer_mode', 'assign') === 'assign')><span><i class="bi bi-person-plus"></i><strong>Mutasi PIC</strong><small>Pindahkan satu alat ke PIC baru.</small></span></label><label class="transfer-mode-card swap"><input type="radio" name="transfer_mode" value="swap" @checked(old('transfer_mode') === 'swap')><span><i class="bi bi-arrow-left-right"></i><strong>Tukar PIC Antar Alat</strong><small>Menukar PIC dari dua alat sekaligus.</small></span></label></div></div>
            <div class="col-12"><label class="form-label">Peralatan</label><select name="equipment_id" id="equipment_id" class="form-select" required><option value="">-- Pilih Peralatan --</option>@foreach($equipment as $item)<option value="{{ $item->id }}" @selected((string) old('equipment_id', request('equipment_id')) === (string) $item->id)>{{ $item->name }} - {{ $item->asset_tag ?: 'Tanpa kode aset' }} | PIC: {{ $item->owner_name ?: ($item->owner->name ?? 'Belum ada') }}</option>@endforeach</select></div>
            <div class="col-12 swap-equipment-wrap d-none"><label class="form-label">Alat Pasangan untuk Tukar PIC</label><select name="swap_equipment_id" id="swap_equipment_id" class="form-select"><option value="">-- Pilih alat kedua --</option>@foreach($equipment as $item)<option value="{{ $item->id }}" data-owner="{{ $item->owner_name ?: ($item->owner->name ?? '') }}" data-department="{{ $item->department ?: ($item->owner->department ?? '') }}">{{ $item->name }} - {{ $item->asset_tag ?: 'Tanpa kode aset' }} | PIC: {{ $item->owner_name ?: ($item->owner->name ?? 'Belum ada') }}</option>@endforeach</select><div class="form-text">PIC alat kedua akan otomatis menjadi PIC baru alat utama, dan sebaliknya.</div></div>
            <div class="col-md-6"><label class="form-label">PIC Baru (User)</label><select name="to_user_id" id="to_user_id" class="form-select"><option value="">-- Pilih User --</option>@foreach($users as $user)<option value="{{ $user->id }}" data-name="{{ $user->name }}" data-department="{{ $user->department }}" @selected(old('to_user_id') == $user->id)>{{ $user->name }}{{ $user->department ? ' - ' . $user->department : '' }}</option>@endforeach</select><div class="form-text">Boleh dikosongkan jika PIC belum memiliki akun.</div></div>
            <div class="col-md-6"><label class="form-label">Nama PIC Baru</label><input name="to_owner_name" id="to_owner_name" class="form-control" value="{{ old('to_owner_name') }}"></div>
            <div class="col-md-6"><label class="form-label">Departemen Baru</label><input name="to_department" id="to_department" class="form-control" value="{{ old('to_department') }}"></div>
            <div class="col-md-6"><label class="form-label">Lokasi Baru</label><select name="to_location_id" class="form-select"><option value="">-- Pertahankan / kosong --</option>@foreach($locations as $location)<option value="{{ $location->id }}" @selected(old('to_location_id') == $location->id)>{{ $location->name }}</option>@endforeach</select></div>
            <div class="col-md-6"><label class="form-label">Tanggal Efektif</label><input type="date" name="effective_date" class="form-control" value="{{ old('effective_date', date('Y-m-d')) }}" required></div>
            <div class="col-12"><label class="form-label">Alasan Mutasi</label><textarea name="reason" class="form-control" rows="3" required>{{ old('reason') }}</textarea></div>
            <div class="col-12"><label class="form-label">Catatan</label><textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea></div>
            <div class="col-12 text-end"><button class="btn btn-brand">Kirim Pengajuan</button></div>
        </form>
    </div></div>
</div>
<script>
    const modeInputs = document.querySelectorAll('input[name="transfer_mode"]');
    const swapWrap = document.querySelector('.swap-equipment-wrap');
    const equipmentInput = document.getElementById('equipment_id');
    const swapInput = document.getElementById('swap_equipment_id');
    function updateTransferMode() {
        const isSwap = document.querySelector('input[name="transfer_mode"]:checked')?.value === 'swap';
        swapWrap.classList.toggle('d-none', !isSwap);
        swapInput.required = isSwap;
        Array.from(swapInput.options).forEach(option => option.hidden = isSwap && option.value === equipmentInput.value);
    }
    modeInputs.forEach(input => input.addEventListener('change', updateTransferMode));
    equipmentInput.addEventListener('change', updateTransferMode);
    updateTransferMode();
    document.getElementById('to_user_id').addEventListener('change', function () {
        const option = this.selectedOptions[0];
        if (!option.value) return;
        document.getElementById('to_owner_name').value = option.dataset.name || '';
        document.getElementById('to_department').value = option.dataset.department || '';
    });
</script>
<style>
.transfer-page{max-width:1180px;margin-top:0!important}.transfer-page>div:first-child{padding:4px 4px 18px;border-bottom:1px solid #e8edf4}.transfer-page>div:first-child h2{font-size:1.75rem;font-weight:800;color:#18243d;letter-spacing:-.03em}.transfer-page>div:first-child .text-primary{color:#7c5cfc!important;font-size:.68rem;letter-spacing:.13em}.transfer-page>div:first-child .btn{border-radius:10px;font-weight:700}.transfer-page .transfer-form{border:1px solid #e7ebf2;border-radius:16px;box-shadow:0 8px 24px rgba(35,52,85,.06);overflow:hidden}.transfer-page .transfer-form .card-body{padding:24px}.transfer-page .form-label{margin-bottom:6px;color:#69758d;font-size:.68rem;font-weight:800;letter-spacing:.04em;text-transform:uppercase}.transfer-page .form-control,.transfer-page .form-select{min-height:42px;border-radius:10px;border-color:#dfe5ee;background:#fbfcfe;color:#34415a;font-size:.76rem}.transfer-page .form-control:focus,.transfer-page .form-select:focus{border-color:#c4b5fd;box-shadow:0 0 0 3px rgba(124,92,252,.12);background:#fff}.transfer-page textarea{min-height:100px;resize:vertical}.transfer-page .form-text{color:#94a0b2;font-size:.67rem}.transfer-page .btn-brand{border:0;border-radius:10px;background:linear-gradient(135deg,#7c5cfc,#a78bfa);box-shadow:0 8px 16px rgba(124,92,252,.2);font-weight:700}.transfer-page .alert{border-radius:12px}.transfer-mode-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;margin-bottom:4px}.transfer-mode-card{position:relative;display:block;cursor:pointer}.transfer-mode-card input{position:absolute;opacity:0}.transfer-mode-card span{display:flex;align-items:flex-start;gap:11px;min-height:76px;padding:15px;border:1px solid #e7ebf2;border-radius:13px;background:#fbfcfe;transition:all .15s}.transfer-mode-card i{display:flex;align-items:center;justify-content:center;width:32px;height:32px;flex:0 0 32px;border-radius:9px;background:#efeafe;color:#7c5cfc;font-size:.9rem}.transfer-mode-card strong{display:block;color:#34415a;font-size:.78rem}.transfer-mode-card small{display:block;margin-top:4px;color:#94a0b2;font-size:.67rem}.transfer-mode-card input:checked+span{border-color:#8b72ff;background:#f5f2ff;box-shadow:0 0 0 3px rgba(124,92,252,.1)}.transfer-mode-card.swap input:checked+span{border-color:#2ec7b4;background:#effcf9;box-shadow:0 0 0 3px rgba(20,184,166,.1)}.transfer-mode-card.swap i{background:#e2f8f4;color:#0f9c8a}.swap-equipment-wrap{padding:15px;border:1px solid #d9f3ee;border-radius:13px;background:#f5fffd}.swap-equipment-wrap .form-label{color:#0f9c8a}.transfer-page form.row{row-gap:18px}
@media(max-width:767px){.transfer-page>div:first-child{gap:14px;flex-direction:column!important}.transfer-page>div:first-child>.btn{align-self:stretch}.transfer-page .transfer-form .card-body{padding:16px}.transfer-mode-grid{grid-template-columns:1fr}}
</style>
@endsection
