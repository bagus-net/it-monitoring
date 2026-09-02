@extends('layouts.app')

@section('content')
<div class="container mt-4 report-page">
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <div class="report-eyebrow">Laporan</div>
            <h2 class="mb-1">Laporan Peralatan IT</h2>
            <p class="text-muted mb-0">Rekap inventaris aset IT berdasarkan jenis, kondisi, lokasi, dan kritikalitas.</p>
        </div>
    </div>
    <div class="row g-3 mb-3">
        <div class="col-6 col-lg-3"><div class="report-stat total"><span>Total Aset</span><strong>{{ $summary['total'] }}</strong><small>sesuai filter</small></div></div>
        <div class="col-6 col-lg-3"><div class="report-stat good"><span>Kondisi Baik</span><strong>{{ $summary['good'] }}</strong><small>siap digunakan</small></div></div>
        <div class="col-6 col-lg-3"><div class="report-stat attention"><span>Rusak / Perbaikan</span><strong>{{ $summary['attention'] }}</strong><small>perlu tindak lanjut</small></div></div>
        <div class="col-6 col-lg-3"><div class="report-stat neutral"><span>Tanpa PIC</span><strong>{{ $summary['unassigned'] }}</strong><small>belum ada penanggung jawab</small></div></div>
    </div>
    <div class="card report-filter mb-3">
        <div class="card-header"><strong>Filter Laporan</strong></div>
        <div class="card-body">
            <form method="GET" action="{{ route('reports.equipments') }}" class="row g-2 align-items-end">
                <div class="col-6 col-lg-3"><label class="form-label">Jenis</label><select name="equipment_type_id" class="form-select"><option value="">Semua</option>@foreach($types as $type)<option value="{{ $type->id }}" @selected((string) $filters['equipment_type_id'] === (string) $type->id)>{{ $type->name }}</option>@endforeach</select></div>
                <div class="col-6 col-lg-3"><label class="form-label">Lokasi</label><select name="location_id" class="form-select"><option value="">Semua</option>@foreach($locations as $location)<option value="{{ $location->id }}" @selected((string) $filters['location_id'] === (string) $location->id)>{{ $location->name }}</option>@endforeach</select></div>
                <div class="col-6 col-lg-2"><label class="form-label">Kondisi</label><select name="condition" class="form-select"><option value="">Semua</option>@foreach($conditions as $condition)<option value="{{ $condition }}" @selected($filters['condition'] === $condition)>{{ ucfirst($condition) }}</option>@endforeach</select></div>
                <div class="col-6 col-lg-2"><label class="form-label">Kritikalitas</label><select name="criticality" class="form-select"><option value="">Semua</option>@foreach(['critical'=>'Sangat Kritis','high'=>'Tinggi','medium'=>'Sedang','low'=>'Rendah'] as $key => $label)<option value="{{ $key }}" @selected($filters['criticality'] === $key)>{{ $label }}</option>@endforeach</select></div>
                <div class="col-12 col-lg-2 d-flex gap-2"><button type="submit" class="btn btn-brand btn-sm">Terapkan</button><a href="{{ route('reports.equipments') }}" class="btn btn-outline-secondary btn-sm">Reset</a></div>
            </form>
        </div>
    </div>
    <div class="card report-card mb-3">
        <div class="card-header"><strong>Rekap per Jenis</strong></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0 no-table-tools">
                    <thead><tr><th>Jenis</th><th class="text-end">Total</th><th class="text-end">Baik</th><th class="text-end">Rusak / Perbaikan</th></tr></thead>
                    <tbody>
                        @forelse($byType as $row)
                            <tr><td><strong>{{ $row['type'] }}</strong><div class="report-meter"><span style="width:{{ round($row['total'] / max(1, collect($byType)->max('total')) * 100) }}%"></span></div></td><td class="text-end">{{ $row['total'] }}</td><td class="text-end">{{ $row['good'] }}</td><td class="text-end">{{ $row['attention'] }}</td></tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">Tidak ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="card report-card">
        <div class="card-header"><strong>Rincian Peralatan</strong></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Nama</th><th>Kode Aset</th><th>Jenis</th><th>Merk / Model</th><th>Lokasi</th><th>PIC</th><th>Kondisi</th><th>Kritikalitas</th></tr></thead>
                    <tbody>
                        @forelse($equipments as $eq)
                            <tr>
                                <td>{{ $eq->name }}</td>
                                <td>{{ $eq->asset_tag ?: '-' }}</td>
                                <td>{{ $eq->type->name ?? '-' }}</td>
                                <td>{{ $eq->manufacturer->name ?? '-' }}{{ $eq->model ? ' / ' . $eq->model : '' }}</td>
                                <td>{{ $eq->assetLocation?->name ?: '-' }}</td>
                                <td>{{ $eq->owner_name ?: '-' }}</td>
                                <td>{{ ucfirst($eq->condition ?? '-') }}</td>
                                <td>{{ ['critical'=>'Sangat Kritis','high'=>'Tinggi','medium'=>'Sedang','low'=>'Rendah'][$eq->criticality] ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted py-4">Tidak ada peralatan sesuai filter.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="report-insight"><i class="bi bi-lightbulb"></i><div><strong>Insight aset</strong><span>{{ $summary['attention'] > 0 ? $summary['attention'] . ' aset membutuhkan tindak lanjut.' : 'Semua aset dalam kondisi baik.' }} {{ $summary['unassigned'] > 0 ? $summary['unassigned'] . ' aset belum memiliki PIC.' : 'Seluruh aset sudah memiliki PIC.' }}</span></div></div>
@include('reports._styles')
@endsection
