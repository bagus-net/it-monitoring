@extends('layouts.app')

@section('content')
<div class="container mt-4 maintenance-check-page">
    <div class="d-flex justify-content-between align-items-center mb-3"><div><h2 class="mb-1">Buat Checklist Perawatan</h2><p class="text-muted mb-0">Pilih periode untuk menampilkan peralatan yang dijadwalkan.</p></div><a href="{{ route('maintenance-checklists.index') }}" class="btn btn-outline-secondary">Kembali</a></div>

    <form method="GET" class="card mb-3 checklist-filter">
        <div class="card-body row g-3 align-items-end">
            <div class="col-md-4"><label class="form-label">Tahun Jadwal</label><input type="number" name="year" class="form-control" min="2000" max="2100" value="{{ $year }}"></div>
            <div class="col-md-3"><button class="btn btn-outline-primary w-100">Tampilkan Jadwal</button></div>
        </div>
    </form>

    <div class="card mb-3 checklist-schedule-picker">
        <div class="card-header">
            <div>
                <strong>Pilih Jadwal Bulanan untuk Checklist</strong>
                <small>Klik bulan yang tersedia. Bulan dengan tanda selesai sudah memiliki checklist dan tidak dapat dibuat ulang.</small>
            </div>
            <span class="badge bg-primary">{{ $year }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Program Perawatan</th>
                            <th>Bulan Terjadwal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($monthlyScheduleGroups as $scheduleGroup)
                            <tr>
                                <td>
                                    <span class="program-dot" style="background:{{ $scheduleGroup['item']->schedule_color }}"></span>
                                    <strong>{{ $scheduleGroup['item']->title }}</strong>
                                </td>
                                <td class="schedule-months">
                                    @foreach ($scheduleGroup['months'] as $scheduledMonth)
                                        @if (in_array($scheduledMonth, $scheduleGroup['completed_months']))
                                            <span class="schedule-month schedule-month-completed" title="Checklist sudah dibuat">
                                                <i class="bi bi-check-circle-fill"></i> {{ $monthNames[$scheduledMonth] }}
                                            </span>
                                        @else
                                            <a href="{{ route('maintenance-checklists.create', ['checklist_item_id' => $scheduleGroup['item']->id, 'year' => $year, 'month' => $scheduledMonth]) }}"
                                               class="schedule-month schedule-month-ready"
                                               title="Buat checklist {{ $monthNames[$scheduledMonth] }}">
                                                <i class="bi bi-clipboard-check"></i> {{ $monthNames[$scheduledMonth] }}
                                            </a>
                                        @endif
                                    @endforeach
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted py-4">Belum ada Jadwal Bulanan untuk tahun {{ $year }}.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

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
<style>.checklist-document .card-header,.checklist-schedule-picker .card-header{display:flex;justify-content:space-between;align-items:center;background:#f1f5f9;color:#1e293b}.checklist-document .card-header small,.checklist-schedule-picker .card-header small,.checklist-table small{display:block;color:#64748b;font-weight:400}.checklist-table th,.checklist-schedule-picker thead th{background:#fff3e6;font-size:.82rem}.checklist-table td{vertical-align:middle}.program-dot{display:inline-block;width:9px;height:9px;margin-right:7px;border-radius:50%}.schedule-months{display:flex;flex-wrap:wrap;gap:6px}.schedule-month{display:inline-flex;align-items:center;gap:5px;padding:5px 8px;border-radius:4px;font-size:.78rem;font-weight:700;text-decoration:none}.schedule-month-ready{border:1px solid #0b5ea8;background:#eaf4fe;color:#075985}.schedule-month-ready:hover{background:#0b5ea8;color:#fff}.schedule-month-completed{border:1px solid #86efac;background:#dcfce7;color:#166534;cursor:default}</style>
@endsection
