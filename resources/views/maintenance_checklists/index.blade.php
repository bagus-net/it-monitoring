@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div><h2 class="mb-1">Pelaksanaan Checklist</h2><p class="text-muted mb-0">Dokumen hasil perawatan per Program, Bulan, dan Tahun.</p></div>
        <a href="{{ route('maintenance-checklists.create') }}" class="btn btn-brand">Buat Checklist</a>
    </div>

    <div class="card card-colorful">
        <div class="card-header">Daftar Hasil Checklist</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Nama Peralatan</th><th>Periode</th><th>Tanggal Checklist</th><th>Kondisi</th><th>Pelapor</th><th>Keterangan</th><th>Aksi</th></tr></thead>
                    <tbody>
                        @forelse ($byProgram as $program)
                            <tr class="checklist-program-row" style="--program-color:{{ $program['checklistItem']->schedule_color }};--program-tint:{{ $program['checklistItem']->schedule_tint }}"><td colspan="7"><span class="program-dot"></span><strong>{{ $program['checklistItem']->title }}</strong></td></tr>
                            @foreach ($program['checklists'] as $checklist)
                                @foreach ($checklist->entries->sortBy(fn ($entry) => $entry->equipment->name ?? '') as $entry)
                                    <tr>
                                        <td class="equipment-cell"><strong>{{ $entry->equipment->name ?? '-' }}</strong><small>{{ $entry->equipment->asset_tag ?? $entry->equipment->serial_number ?? '' }}</small></td>
                                        <td>{{ ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][$checklist->month] }} {{ $checklist->year }}</td>
                                        <td>{{ $checklist->checked_at?->format('d M Y') }}</td>
                                        <td><span class="result-badge result-{{ $entry->result }}">{{ $entry->result === 'ok' ? 'OK' : 'NOT OK' }}</span></td>
                                        <td>{{ $checklist->reported_by ?? '-' }}</td>
                                        <td>{{ $entry->remarks ?? '-' }}</td>
                                        <td class="text-nowrap"><a class="btn btn-sm btn-outline-secondary" href="{{ route('maintenance-checklists.show', $checklist) }}">Detail</a><a class="btn btn-sm btn-outline-primary" href="{{ route('maintenance-checklists.edit', $checklist) }}">Edit</a></td>
                                    </tr>
                                @endforeach
                            @endforeach
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-4">Belum ada dokumen checklist perawatan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<style>.program-dot { display:inline-block; width:9px; height:9px; border-radius:50%; margin-right:6px; background:var(--program-color); }.checklist-program-row td { padding:9px 12px !important; background:var(--program-tint); color:var(--program-color); }.equipment-cell { padding-left:24px !important; }.equipment-cell small { display:block; color:#64748b; }.result-badge { display:inline-block; padding:4px 8px; border-radius:3px; font-size:.75rem; font-weight:700; }.result-ok { background:#dcfce7; color:#166534; }.result-not_ok { background:#fee2e2; color:#991b1b; }</style>
@endsection
