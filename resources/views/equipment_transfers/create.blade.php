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
            <div class="col-12"><label class="form-label">Peralatan</label><select name="equipment_id" id="equipment_id" class="form-select" required><option value="">-- Pilih Peralatan --</option>@foreach($equipment as $item)<option value="{{ $item->id }}" @selected((string) old('equipment_id', request('equipment_id')) === (string) $item->id)>{{ $item->name }} - {{ $item->asset_tag ?: 'Tanpa kode aset' }} | PIC: {{ $item->owner_name ?: ($item->owner->name ?? 'Belum ada') }}</option>@endforeach</select></div>
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
    document.getElementById('to_user_id').addEventListener('change', function () {
        const option = this.selectedOptions[0];
        if (!option.value) return;
        document.getElementById('to_owner_name').value = option.dataset.name || '';
        document.getElementById('to_department').value = option.dataset.department || '';
    });
</script>
@endsection
