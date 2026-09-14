@extends('layouts.app')

@section('content')
<div class="container mt-4 additions-report">
    <div class="report-header d-flex justify-content-between align-items-start gap-3 mb-3">
        <div>
            <div class="report-eyebrow">Laporan Peralatan IT</div>
            <h2 class="mb-1">Riwayat Penambahan Peralatan</h2>
            <p class="text-muted mb-0">Peralatan yang ditambahkan pada periode {{ \Carbon\Carbon::parse($filters['from'])->format('d M Y') }} sampai {{ \Carbon\Carbon::parse($filters['to'])->format('d M Y') }}.</p>
        </div>
        <button type="button" class="btn btn-primary" onclick="window.print()"><i class="bi bi-printer me-1"></i>Cetak</button>
    </div>

    <form method="GET" action="{{ route('reports.equipment-additions') }}" class="card additions-filter mb-3">
        <div class="card-body row g-2 align-items-end">
            <div class="col-md-4"><label class="form-label">Dari Tanggal</label><input type="date" name="from" value="{{ $filters['from'] }}" class="form-control"></div>
            <div class="col-md-4"><label class="form-label">Sampai Tanggal</label><input type="date" name="to" value="{{ $filters['to'] }}" class="form-control"></div>
            <div class="col-md-4 d-flex gap-2"><button class="btn btn-brand">Terapkan</button><a href="{{ route('reports.equipment-additions') }}" class="btn btn-outline-secondary">Reset</a></div>
        </div>
    </form>

    <div class="card report-card">
        <div class="card-header d-flex justify-content-between"><strong>Daftar Peralatan Ditambahkan</strong><span>{{ $equipments->count() }} peralatan</span></div>
        <div class="card-body p-0"><div class="table-responsive"><table class="table align-middle mb-0">
            <thead><tr><th>No.</th><th>Tanggal Ditambahkan</th><th>Nama Peralatan</th><th>Kode Aset</th><th>Jenis</th><th>Merk / Model</th><th>Lokasi</th><th>PIC</th><th>Kondisi</th></tr></thead>
            <tbody>@forelse($equipments as $equipment)<tr><td>{{ $loop->iteration }}</td><td>{{ $equipment->created_at?->format('d M Y H:i') }}</td><td><strong>{{ $equipment->name }}</strong><small class="d-block text-muted">SN: {{ $equipment->serial_number ?: '-' }}</small></td><td>{{ $equipment->asset_tag ?: '-' }}</td><td>{{ $equipment->type?->name ?: '-' }}</td><td>{{ $equipment->manufacturer?->name ?: '-' }}{{ $equipment->model ? ' / ' . $equipment->model : '' }}</td><td>{{ $equipment->assetLocation?->name ?: '-' }}</td><td>{{ $equipment->owner?->name ?: ($equipment->owner_name ?: '-') }}</td><td>{{ ucfirst($equipment->condition ?: '-') }}</td></tr>@empty<tr><td colspan="9" class="text-center text-muted py-4">Tidak ada peralatan yang ditambahkan pada periode ini.</td></tr>@endforelse</tbody>
        </table></div></div>
    </div>
</div>

<style>
.additions-report{max-width:1480px}.additions-report .report-header{padding:4px 4px 18px;border-bottom:1px solid #e8edf4}.additions-report .report-eyebrow{color:#2161f5;font-size:.68rem;font-weight:800;letter-spacing:.13em;text-transform:uppercase}.additions-report .report-header h2{color:#18243d;font-weight:800}.additions-report .report-header p{font-size:.78rem}.additions-report .additions-filter,.additions-report .report-card{border:1px solid #e7ebf2;border-radius:16px;box-shadow:0 5px 18px rgba(35,52,85,.045);overflow:hidden}.additions-report .card-header{background:#fff}.additions-report .table{font-size:.75rem}.additions-report .table thead th{white-space:nowrap}.additions-report .table tbody td{padding:12px 14px}.additions-report .table small{font-size:.65rem}.additions-report .card-header span{color:#8792a7;font-size:.72rem}@media print{.app-sidebar,.app-topbar,.app-footer,.mobile-bottom-nav,.additions-filter,.btn{display:none!important}.app-main{margin:0!important;padding:0!important}.additions-report{width:100%;max-width:none;margin:0!important}.additions-report .report-header{border:1px solid #000;padding:10px}.additions-report .report-header p{font-size:9pt}.additions-report .report-card{border:1px solid #000;border-radius:0;box-shadow:none}.additions-report .table{font-size:8pt}.additions-report .table th,.additions-report .table td{padding:5px 6px!important}.additions-report .card-header{border-bottom:1px solid #000}}
</style>
@endsection
