@extends('layouts.app')

@section('content')
<div class="container mt-4 report-page">
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <div class="report-eyebrow">Laporan</div>
            <h2 class="mb-1">Laporan Perbaikan IT</h2>
            <p class="text-muted mb-0">Rekap tiket perbaikan hardware dan software beserta waktu penyelesaian.</p>
        </div>
    </div>
    <div class="row g-3 mb-3">
        <div class="col-6 col-lg-2"><div class="report-stat total"><span>Total Tiket</span><strong>{{ $summary['total'] }}</strong><small>periode terpilih</small></div></div>
        <div class="col-6 col-lg-2"><div class="report-stat accent"><span>Open</span><strong>{{ $summary['open'] }}</strong><small>menunggu</small></div></div>
        <div class="col-6 col-lg-2"><div class="report-stat info"><span>Proses</span><strong>{{ $summary['in_progress'] }}</strong><small>dikerjakan</small></div></div>
        <div class="col-6 col-lg-2"><div class="report-stat good"><span>Selesai</span><strong>{{ $summary['resolved'] }}</strong><small>tiket ditutup</small></div></div>
        <div class="col-6 col-lg-2"><div class="report-stat neutral"><span>Hardware / Software</span><strong>{{ $summary['hardware'] }} / {{ $summary['software'] }}</strong><small>komposisi tiket</small></div></div>
        <div class="col-6 col-lg-2"><div class="report-stat attention"><span>Rata-rata Selesai</span><strong>{{ $summary['avg_hours'] }} jam</strong><small>dari mulai perbaikan ke selesai</small></div></div>
    </div>
    <div class="card report-filter mb-3">
        <div class="card-header"><strong>Filter Laporan</strong></div>
        <div class="card-body">
            <form method="GET" action="{{ route('reports.repairs') }}" class="row g-2 align-items-end">
                <div class="col-6 col-lg-3"><label class="form-label">Periode Dari</label><input type="date" name="from" value="{{ $filters['from'] }}" class="form-control"></div>
                <div class="col-6 col-lg-3"><label class="form-label">Sampai</label><input type="date" name="to" value="{{ $filters['to'] }}" class="form-control"></div>
                <div class="col-6 col-lg-2"><label class="form-label">Status</label><select name="status" class="form-select"><option value="">Semua</option>@foreach(['open'=>'Open','in_progress'=>'Proses','resolved'=>'Selesai'] as $key => $label)<option value="{{ $key }}" @selected($filters['status'] === $key)>{{ $label }}</option>@endforeach</select></div>
                <div class="col-6 col-lg-2"><label class="form-label">Kategori</label><select name="repair_category" class="form-select"><option value="">Semua</option>@foreach(['hardware'=>'Hardware','software'=>'Software'] as $key => $label)<option value="{{ $key }}" @selected($filters['repair_category'] === $key)>{{ $label }}</option>@endforeach</select></div>
                <div class="col-12 col-lg-2 d-flex gap-2"><button type="submit" class="btn btn-brand btn-sm">Terapkan</button><a href="{{ route('reports.repairs') }}" class="btn btn-outline-secondary btn-sm">Reset</a></div>
            </form>
        </div>
    </div>
    <div class="card report-card mb-3">
        <div class="card-header"><strong>10 Jenis Gangguan Terbanyak</strong></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0 no-table-tools">
                    <thead><tr><th>Jenis Error</th><th class="text-end">Jumlah</th></tr></thead>
                    <tbody>
                        @forelse($byProblem as $row)
                            <tr><td>{{ $row['problem'] }}</td><td class="text-end">{{ $row['total'] }}</td></tr>
                        @empty
                            <tr><td colspan="2" class="text-center text-muted py-3">Tidak ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="card report-card">
        <div class="card-header"><strong>Rincian Tiket</strong></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>No. Tiket</th><th>Kategori</th><th>Peralatan / Aplikasi</th><th>Jenis Error</th><th>Lokasi</th><th>Pelapor</th><th>Teknisi</th><th>Status</th><th>Lapor</th><th>Selesai</th></tr></thead>
                    <tbody>
                        @forelse($tickets as $ticket)
                            <tr>
                                <td>{{ $ticket->ticket_number }}</td>
                                <td>{{ $ticket->repair_category === 'software' ? 'Software' : 'Hardware' }}</td>
                                <td>{{ $ticket->repair_category === 'software' ? ($ticket->software_name ?: '-') : ($ticket->equipment->name ?? $ticket->equipment_category ?: '-') }}</td>
                                <td>{{ $ticket->error_type ?: '-' }}</td>
                                <td>{{ $ticket->equipment?->assetLocation?->name ?: '-' }}</td>
                                <td>{{ $ticket->reported_by ?: '-' }}</td>
                                <td>{{ $ticket->assigned_to ?: '-' }}</td>
                                <td>{{ ['open'=>'Open','in_progress'=>'Proses','resolved'=>'Selesai'][$ticket->status] }}</td>
                                <td>{{ $ticket->reported_at?->format('d M Y H:i') }}</td>
                                <td>{{ $ticket->resolved_at?->format('d M Y H:i') ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="10" class="text-center text-muted py-4">Tidak ada tiket pada periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@include('reports._styles')
@endsection
