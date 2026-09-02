@extends('layouts.app')

@section('content')
<div class="container mt-4 report-page">
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <div class="report-eyebrow">Laporan</div>
            <h2 class="mb-1">Laporan Checklist Web &amp; Peralatan IT</h2>
            <p class="text-muted mb-0">Rekap pelaksanaan checklist monitoring website dan checklist perawatan peralatan IT.</p>
        </div>
    </div>
    <div class="row g-3 mb-3">
        <div class="col-6 col-lg-2"><div class="report-stat total"><span>Checklist Web</span><strong>{{ $summary['web_total'] }}</strong><div class="report-meter"><span style="width:{{ round($summary['web_total'] / max(1, $summary['web_total'] + $summary['equipment_total']) * 100) }}%"></span></div><small>dokumen</small></div></div>
        <div class="col-6 col-lg-2"><div class="report-stat info"><span>Security</span><strong>{{ $summary['web_security'] }}</strong><small>checklist keamanan</small></div></div>
        <div class="col-6 col-lg-2"><div class="report-stat accent"><span>Functional</span><strong>{{ $summary['web_functional'] }}</strong><small>checklist fungsional</small></div></div>
        <div class="col-6 col-lg-2"><div class="report-stat neutral"><span>Checklist Peralatan</span><strong>{{ $summary['equipment_total'] }}</strong><div class="report-meter teal"><span style="width:{{ round($summary['equipment_total'] / max(1, $summary['web_total'] + $summary['equipment_total']) * 100) }}%"></span></div><small>dokumen</small></div></div>
        <div class="col-6 col-lg-2"><div class="report-stat good"><span>Hasil OK</span><strong>{{ $summary['equipment_ok'] }}</strong><small>item sesuai</small></div></div>
        <div class="col-6 col-lg-2"><div class="report-stat attention"><span>Hasil NOT OK</span><strong>{{ $summary['equipment_not_ok'] }}</strong><small>perlu tindak lanjut</small></div></div>
    </div>
    <div class="card report-filter mb-3">
        <div class="card-header"><strong>Filter Periode</strong></div>
        <div class="card-body">
            <form method="GET" action="{{ route('reports.checklists') }}" class="row g-2 align-items-end">
                <div class="col-6 col-lg-3"><label class="form-label">Periode Dari</label><input type="date" name="from" value="{{ $filters['from'] }}" class="form-control"></div>
                <div class="col-6 col-lg-3"><label class="form-label">Sampai</label><input type="date" name="to" value="{{ $filters['to'] }}" class="form-control"></div>
                <div class="col-12 col-lg-3 d-flex gap-2"><button type="submit" class="btn btn-brand btn-sm">Terapkan</button><a href="{{ route('reports.checklists') }}" class="btn btn-outline-secondary btn-sm">Reset</a></div>
            </form>
        </div>
    </div>
    <div class="card report-card mb-3">
        <div class="card-header"><strong>Checklist Web Monitoring</strong></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Website</th><th>URL</th><th>Tipe</th><th>Jumlah Item</th><th>Diperiksa Oleh</th><th>Tanggal</th></tr></thead>
                    <tbody>
                        @forelse($webChecklists as $checklist)
                            <tr>
                                <td>{{ $checklist->site->name ?? '-' }}</td>
                                <td>{{ $checklist->site->url ?? '-' }}</td>
                                <td>{{ $checklist->checklist_type === 'security' ? 'Security' : 'Functional' }}</td>
                                <td>{{ $checklist->entries_count }}</td>
                                <td>{{ $checklist->checked_by ?: '-' }}</td>
                                <td>{{ $checklist->checked_at?->format('d M Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">Tidak ada checklist web pada periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="card report-card">
        <div class="card-header"><strong>Checklist Peralatan IT</strong></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Program Perawatan</th><th>Periode</th><th>Jumlah Item</th><th>OK</th><th>NOT OK</th><th>Pelapor</th><th>Tanggal</th></tr></thead>
                    <tbody>
                        @forelse($equipmentChecklists as $checklist)
                            <tr>
                                <td>{{ $checklist->checklistItem->title ?? '-' }}</td>
                                <td>{{ $checklist->month ? \Carbon\Carbon::create($checklist->year, $checklist->month, 1)->translatedFormat('F Y') : $checklist->year }}</td>
                                <td>{{ $checklist->entries->count() }}</td>
                                <td>{{ $checklist->entries->where('result', 'ok')->count() }}</td>
                                <td>{{ $checklist->entries->where('result', 'not_ok')->count() }}</td>
                                <td>{{ $checklist->reported_by ?: '-' }}</td>
                                <td>{{ $checklist->checked_at?->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada checklist peralatan pada periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@php
    $checklistTotal = $summary['equipment_ok'] + $summary['equipment_not_ok'];
@endphp
<div class="report-insight checklist-insight"><i class="bi bi-check2-circle"></i><div><strong>Insight kepatuhan</strong><span>{{ $checklistTotal > 0 ? round($summary['equipment_ok'] / $checklistTotal * 100) . '% hasil checklist peralatan dinyatakan OK.' : 'Belum ada hasil checklist peralatan pada periode ini.' }} {{ $summary['equipment_not_ok'] > 0 ? $summary['equipment_not_ok'] . ' item perlu ditindaklanjuti.' : '' }}</span></div></div>
@include('reports._styles')
@endsection
