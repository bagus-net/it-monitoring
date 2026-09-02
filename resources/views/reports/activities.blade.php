@extends('layouts.app')

@section('content')
<div class="container mt-4 report-page">
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <div class="report-eyebrow">Laporan</div>
            <h2 class="mb-1">Laporan Log Aktivitas</h2>
            <p class="text-muted mb-0">Rekap aktivitas pengguna pada sistem berdasarkan periode, modul, dan jenis tindakan.</p>
        </div>
    </div>
    <div class="row g-3 mb-3">
        <div class="col-6 col-lg-3"><div class="report-stat total"><span>Total Aktivitas</span><strong>{{ $summary['total'] }}</strong><small>periode terpilih</small></div></div>
        <div class="col-6 col-lg-3"><div class="report-stat good"><span>Membuat Data</span><strong>{{ $summary['created'] }}</strong><small>input baru</small></div></div>
        <div class="col-6 col-lg-3"><div class="report-stat info"><span>Memperbarui Data</span><strong>{{ $summary['updated'] }}</strong><small>perubahan</small></div></div>
        <div class="col-6 col-lg-3"><div class="report-stat attention"><span>Menghapus Data</span><strong>{{ $summary['deleted'] }}</strong><small>penghapusan</small></div></div>
    </div>
    <div class="card report-filter mb-3">
        <div class="card-header"><strong>Filter Laporan</strong></div>
        <div class="card-body">
            <form method="GET" action="{{ route('reports.activities') }}" class="row g-2 align-items-end">
                <div class="col-6 col-lg-3"><label class="form-label">Periode Dari</label><input type="date" name="from" value="{{ $filters['from'] }}" class="form-control"></div>
                <div class="col-6 col-lg-3"><label class="form-label">Sampai</label><input type="date" name="to" value="{{ $filters['to'] }}" class="form-control"></div>
                <div class="col-6 col-lg-2"><label class="form-label">Modul</label><select name="module" class="form-select"><option value="">Semua</option>@foreach($modules as $module)<option value="{{ $module }}" @selected($filters['module'] === $module)>{{ $module }}</option>@endforeach</select></div>
                <div class="col-6 col-lg-2"><label class="form-label">Tindakan</label><select name="action" class="form-select"><option value="">Semua</option>@foreach(['Membuat data','Memperbarui data','Menghapus data'] as $action)<option value="{{ $action }}" @selected($filters['action'] === $action)>{{ $action }}</option>@endforeach</select></div>
                <div class="col-12 col-lg-2 d-flex gap-2"><button type="submit" class="btn btn-brand btn-sm">Terapkan</button><a href="{{ route('reports.activities') }}" class="btn btn-outline-secondary btn-sm">Reset</a></div>
            </form>
        </div>
    </div>
    <div class="card report-card mb-3">
        <div class="card-header"><strong>10 User Paling Aktif</strong></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0 no-table-tools">
                    <thead><tr><th>Nama User</th><th class="text-end">Jumlah Aktivitas</th></tr></thead>
                    <tbody>
                        @forelse($byUser as $row)
                            <tr><td><strong>{{ $row['actor'] }}</strong><div class="report-meter teal"><span style="width:{{ round($row['total'] / max(1, collect($byUser)->max('total')) * 100) }}%"></span></div></td><td class="text-end">{{ $row['total'] }}</td></tr>
                        @empty
                            <tr><td colspan="2" class="text-center text-muted py-3">Tidak ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="card report-card">
        <div class="card-header"><strong>Rincian Aktivitas</strong></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Waktu</th><th>Nama User</th><th>Tindakan</th><th>Modul</th><th>Halaman</th><th>IP</th></tr></thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td>{{ $log->created_at?->format('d M Y H:i') }}</td>
                                <td>{{ $log->actor_name }}</td>
                                <td>{{ $log->action }}</td>
                                <td>{{ $log->module }}</td>
                                <td>{{ $log->url }}</td>
                                <td>{{ $log->ip_address }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">Tidak ada aktivitas pada periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@php
    $dominantAction = collect($summary)->except('total')->sortDesc()->keys()->first();
@endphp
<div class="report-insight activity-insight"><i class="bi bi-shield-check"></i><div><strong>Insight aktivitas</strong><span>{{ $summary['total'] > 0 ? $summary['total'] . ' aktivitas tercatat pada periode ini.' : 'Belum ada aktivitas pada periode ini.' }} {{ $dominantAction ? ucfirst($dominantAction) . ' menjadi jenis aktivitas terbanyak.' : '' }}</span></div></div>
@include('reports._styles')
@endsection
