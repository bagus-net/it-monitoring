@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3"><div><h2 class="mb-1">Buat Checklist Perawatan</h2><p class="text-muted mb-0">Pilih periode untuk menampilkan peralatan yang dijadwalkan.</p></div><a href="{{ route('maintenance-checklists.index') }}" class="btn btn-outline-secondary">Kembali</a></div>

    <form method="GET" class="card mb-3 checklist-filter">
        <div class="card-body row g-3 align-items-end">
            <div class="col-md-5"><label class="form-label">Program Perawatan</label><select name="checklist_item_id" class="form-select" required><option value="">-- Pilih Program --</option>@foreach ($items as $item)<option value="{{ $item->id }}" {{ $checklistItemId === $item->id ? 'selected' : '' }}>{{ $item->title }}</option>@endforeach</select></div>
            @if ($equipmentId)<input type="hidden" name="equipment_id" value="{{ $equipmentId }}">@endif
            <div class="col-md-2"><label class="form-label">Tahun</label><input type="number" name="year" class="form-control" min="2000" max="2100" value="{{ $year }}"></div>
            <div class="col-md-3"><label class="form-label">Bulan</label><select name="month" class="form-select">@foreach ($monthNames as $number => $name)<option value="{{ $number }}" {{ $month === $number ? 'selected' : '' }}>{{ $name }}</option>@endforeach</select></div>
            <div class="col-md-2"><button class="btn btn-outline-primary w-100">Tampilkan</button></div>
        </div>
    </form>

    @if ($checklistItemId)
        @if ($equipment->isEmpty())
            <div class="alert alert-warning">Tidak ada peralatan terjadwal untuk Program dan periode ini. Buat jadwal tahunan atau bulanan terlebih dahulu.</div>
        @else
            <form method="POST" action="{{ route('maintenance-checklists.store') }}">@csrf
                <input type="hidden" name="checklist_item_id" value="{{ $checklistItemId }}"><input type="hidden" name="year" value="{{ $year }}"><input type="hidden" name="month" value="{{ $month }}">
                <div class="card checklist-document">
                    <div class="card-header"><div><strong>CHECKLIST PERAWATAN IT</strong><small>{{ $monthNames[$month] }} {{ $year }}</small></div><span>{{ $items->firstWhere('id', $checklistItemId)->title }}</span></div>
                    <div class="card-body">
                        <div class="row g-3 mb-3"><div class="col-md-4"><label class="form-label">Tanggal</label><input type="date" name="checked_at" class="form-control" value="{{ old('checked_at', sprintf('%04d-%02d-01', $year, $month)) }}" required></div><div class="col-md-4"><label class="form-label">Dilaporkan Oleh</label><input class="form-control" value="{{ auth()->user()->name }}" disabled><small class="text-muted">Otomatis diisi sesuai akun yang login</small></div><div class="col-md-4"><label class="form-label">Mengetahui</label><input class="form-control" value="Menunggu persetujuan" disabled><small class="text-muted">Diisi setelah disetujui oleh Master</small></div></div>
                        <div class="table-responsive"><table class="table table-bordered checklist-table"><thead><tr><th>No.</th><th>Nama Peralatan</th><th>Tanggal Jadwal</th><th>Check Point</th><th class="text-center">OK</th><th class="text-center">Not OK</th><th>Keterangan</th></tr></thead><tbody>@foreach ($equipment as $index => $item)<tr><td>{{ $index + 1 }}</td><td><strong>{{ $item->name }}</strong><small>{{ $item->asset_tag ?? $item->serial_number }}</small><input type="hidden" name="entries[{{ $index }}][equipment_id]" value="{{ $item->id }}"></td><td>{{ count($scheduledDatesByEquipment->get($item->id, [])) ? implode(', ', $scheduledDatesByEquipment->get($item->id)) : '-' }}</td><td>{{ $items->firstWhere('id', $checklistItemId)->title }}</td><td class="text-center"><input type="radio" name="entries[{{ $index }}][result]" value="ok" checked></td><td class="text-center"><input type="radio" name="entries[{{ $index }}][result]" value="not_ok"></td><td><input name="entries[{{ $index }}][remarks]" class="form-control form-control-sm" value="{{ old('entries.' . $index . '.remarks') }}" placeholder="Informasi atau perbaikan"></td></tr>@endforeach</tbody></table></div>
                        <div class="mt-3"><label class="form-label">Catatan Dokumen</label><textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea></div>
                    </div>
                    <div class="card-footer text-end"><button class="btn btn-brand">Simpan Checklist</button></div>
                </div>
            </form>
        @endif
    @endif
</div>
<style>.checklist-document .card-header { display:flex; justify-content:space-between; align-items:center; background:#f1f5f9; color:#1e293b; }.checklist-document .card-header small,.checklist-table small { display:block; color:#64748b; font-weight:400; }.checklist-table th { background:#fff3e6; font-size:.82rem; }.checklist-table td { vertical-align:middle; }</style>
@endsection
