@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4 report-page">
    <div class="report-header">
        <div>
            <div class="report-kicker">Laporan Pengendalian Perawatan</div>
            <h1>Laporan Jadwal Tahunan</h1>
            <p>Periode: <strong>{{ $periodLabel }}</strong> | Dicetak: {{ now()->format('d M Y H:i') }}</p>
        </div>
        <a href="{{ route('reports.monthly') }}" class="btn btn-outline-primary">Laporan Bulanan</a>
    </div>

    <form method="GET" class="report-filter mb-4">
        <div>
            <label for="year">Tahun</label>
            <input id="year" name="year" class="form-control" type="number" min="2000" max="2100" value="{{ $year }}">
        </div>
        <div>
            <label for="checklist_item_id">Program Perawatan</label>
            <select id="checklist_item_id" name="checklist_item_id" class="form-select">
                <option value="">Semua Program</option>
                @foreach ($items as $item)
                    <option value="{{ $item->id }}" {{ (string) $checklistItemId === (string) $item->id ? 'selected' : '' }}>{{ $item->title }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="equipment_id">Peralatan</label>
            <select id="equipment_id" name="equipment_id" class="form-select">
                <option value="">Semua Peralatan</option>
                @foreach ($equipmentOptions as $equipment)
                    <option value="{{ $equipment->id }}" {{ (string) $equipmentId === (string) $equipment->id ? 'selected' : '' }}>{{ $equipment->name }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn btn-primary" type="submit">Tampilkan</button>
        <button class="btn btn-outline-secondary" type="button" onclick="window.print()">Cetak</button>
    </form>

    <section class="summary-grid mb-4">
        <div class="summary-card"><span>Program Perawatan</span><strong>{{ $summary['programs'] }}</strong><small>tercakup dalam laporan</small></div>
        <div class="summary-card success"><span>Peralatan Terjadwal</span><strong>{{ $summary['equipments'] }}</strong><small>peralatan IT</small></div>
        <div class="summary-card info"><span>Jadwal Mingguan</span><strong>{{ $summary['planned_weeks'] }}</strong><small>slot minggu terjadwal</small></div>
    </section>

    <section class="report-table-section">
        <div class="report-table-heading">
            <div><h2>Rekapan Jadwal Tahunan</h2><p>Badge pada setiap bulan menunjukkan minggu pelaksanaan yang telah ditetapkan.</p></div>
            <div class="legend"><span><i class="legend-dot scheduled"></i>Minggu terjadwal</span></div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered report-table align-middle">
                <thead>
                    <tr>
                        <th>Program / Peralatan</th>
                        @for ($month = 1; $month <= 12; $month++)
                            <th class="text-center">{{ ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][$month] }}</th>
                        @endfor
                    </tr>
                </thead>
                <tbody>
                    @forelse ($byProgram as $program)
                        <tr class="program-heading-row" style="--program-color:{{ $program['checklist_item']->schedule_color }}; --program-tint:{{ $program['checklist_item']->schedule_tint }}">
                            <td colspan="13"><span class="program-dot"></span><strong>{{ $program['checklist_item']->title ?? 'Tanpa Program' }}</strong></td>
                        </tr>
                        @foreach ($program['rows'] as $row)
                            <tr>
                                <td class="equipment-cell"><i class="bi bi-hdd text-muted"></i> {{ $row['equipment']->name }}</td>
                                @for ($month = 1; $month <= 12; $month++)
                                    @php $weeks = array_keys($row['weeks'][$month] ?? []); sort($weeks); @endphp
                                    <td class="text-center">
                                        @if (count($weeks))
                                            <div class="week-badges">@foreach ($weeks as $week)<span class="week-badge" style="background-color:{{ $program['checklist_item']->schedule_color }}">W{{ $week }}</span>@endforeach</div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                @endfor
                            </tr>
                        @endforeach
                    @empty
                        <tr><td colspan="13" class="empty-state">Tidak ada jadwal tahunan untuk filter periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>

<style>
.report-page { color: #263238; }
.report-header { display:flex; justify-content:space-between; align-items:flex-start; gap:20px; padding:24px; background:#f8fafc; border-left:5px solid #0f766e; margin-bottom:20px; }
.report-kicker { color:#0f766e; font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em; }
.report-header h1 { font-size:1.65rem; margin:4px 0; }
.report-header p, .report-table-heading p { color:#64748b; margin:0; font-size:.9rem; }
.report-filter { display:grid; grid-template-columns:140px minmax(220px,1fr) minmax(220px,1fr) auto auto; gap:12px; align-items:end; padding:16px; border:1px solid #dbe3ea; background:#fff; }
.report-filter label { display:block; margin-bottom:4px; font-size:.78rem; font-weight:700; color:#475569; }
.summary-grid { display:grid; grid-template-columns:repeat(4, minmax(150px, 1fr)); gap:14px; }
.summary-card { padding:16px; border:1px solid #dbe3ea; border-top:4px solid #475569; background:#fff; }
.summary-card span,.summary-card small { display:block; color:#64748b; font-size:.78rem; }
.summary-card strong { display:block; font-size:1.8rem; line-height:1.25; }
.summary-card.success { border-top-color:#15803d; }.summary-card.info { border-top-color:#2563eb; }
.report-table-section { border:1px solid #dbe3ea; background:#fff; }.report-table-heading { display:flex; justify-content:space-between; gap:16px; align-items:center; padding:16px; border-bottom:1px solid #dbe3ea; }.report-table-heading h2 { font-size:1rem; margin:0 0 2px; }
.report-table { margin:0; font-size:.78rem; }.report-table th { white-space:nowrap; background:#f1f5f9; color:#334155; }.report-table td:first-child { min-width:230px; }.report-table td small { display:block; color:#64748b; margin-top:2px; }.program-heading-row td { padding:8px 12px !important; background:var(--program-tint); color:var(--program-color); font-size:.84rem; }.program-dot { display:inline-block; width:10px; height:10px; border-radius:50%; margin-right:7px; background:var(--program-color); }.equipment-cell { padding-left:24px !important; }.week-badges { display:flex; flex-wrap:wrap; gap:3px; justify-content:center; }.week-badge { display:inline-flex; align-items:center; justify-content:center; min-width:24px; height:22px; padding:0 4px; border-radius:4px; color:#fff; font-size:.7rem; font-weight:700; }.legend { display:flex; flex-wrap:wrap; gap:12px; font-size:.78rem; }.legend-dot { display:inline-block; width:9px; height:9px; border-radius:50%; margin-right:4px; }.legend-dot.scheduled { background:#2563eb; }.empty-state { padding:28px !important; text-align:center; color:#64748b; }
@media (max-width: 900px) { .report-filter,.summary-grid { grid-template-columns:1fr 1fr; }.report-header,.report-table-heading { flex-direction:column; }.report-filter button { width:100%; } }
@page { size:A3 landscape; margin:8mm; }
@media print { .navbar,.hero,.report-filter,.btn { display:none !important; }.report-page { width:100%; margin:0 !important; }.report-header { padding:8px 10px; margin-bottom:6px; border:1px solid #000; }.report-header h1 { font-size:14pt; }.summary-grid { gap:5px; margin-bottom:6px !important; }.summary-card { padding:6px 8px; border-width:2px; }.summary-card strong { font-size:13pt; }.report-table-section { border:0; }.report-table-heading { padding:6px 0; }.report-table-heading p,.legend { font-size:7pt; }.table-responsive { overflow:visible !important; }.report-table { width:100% !important; table-layout:fixed; font-size:7pt; }.report-table thead { display:table-header-group; }.report-table tr { break-inside:avoid; page-break-inside:avoid; }.report-table td:first-child { width:170px; min-width:0; }.report-table th,.report-table td { padding:3px 2px !important; }.program-heading-row td { padding:4px 6px !important; }.equipment-cell { padding-left:10px !important; }.week-badges { gap:2px; }.week-badge { min-width:18px; height:15px; padding:0 2px; font-size:6pt; }.program-dot { width:6px; height:6px; margin-right:3px; } }
</style>
@endsection
