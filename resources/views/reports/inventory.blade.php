@extends('layouts.app')

@section('content')
<div class="container mt-4 report-page">
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <div class="report-eyebrow">Laporan</div>
            <h2 class="mb-1">Laporan Kelola &amp; Persediaan</h2>
            <p class="text-muted mb-0">Rekap tinta, limbah IT, sparepart, lisensi, dan CCTV.</p>
        </div>
    </div>

    <div class="card report-filter mb-3"><div class="card-body"><form method="GET" action="{{ route('reports.inventory') }}" class="row g-2 align-items-end">
        <div class="col-12 col-md-4"><label class="form-label">Jenis Kelola</label><select name="type" class="form-select"><option value="all" @selected($filters['type'] === 'all')>Semua Kelola</option><option value="ink" @selected($filters['type'] === 'ink')>Tinta</option><option value="sparepart" @selected($filters['type'] === 'sparepart')>Sparepart</option><option value="waste" @selected($filters['type'] === 'waste')>Limbah IT</option><option value="license" @selected($filters['type'] === 'license')>Lisensi</option><option value="cctv" @selected($filters['type'] === 'cctv')>CCTV</option></select></div>
        <div class="col-6 col-md-3"><label class="form-label">Dari Tanggal</label><input type="date" name="from" value="{{ $filters['from'] }}" class="form-control"></div>
        <div class="col-6 col-md-3"><label class="form-label">Sampai Tanggal</label><input type="date" name="to" value="{{ $filters['to'] }}" class="form-control"></div>
        <div class="col-12 col-md-2 d-flex gap-2"><button type="submit" class="btn btn-brand btn-sm">Filter</button><a href="{{ route('reports.inventory') }}" class="btn btn-outline-secondary btn-sm">Reset</a></div>
    </form></div></div>

    <div class="row g-3 mb-3">
        <div class="col-6 col-lg-3"><div class="report-stat total"><span>Jenis Tinta</span><strong>{{ $summary['ink_total'] }}</strong><small>{{ $summary['ink_low'] }} stok minimum</small></div></div>
        <div class="col-6 col-lg-3"><div class="report-stat attention"><span>Jenis Sparepart</span><strong>{{ $summary['sparepart_total'] }}</strong><small>{{ $summary['sparepart_low'] }} stok minimum</small></div></div>
        <div class="col-6 col-lg-3"><div class="report-stat neutral"><span>Limbah IT</span><strong>{{ $summary['waste_total'] }}</strong><small>total tercatat</small></div></div>
        <div class="col-6 col-lg-3"><div class="report-stat good"><span>CCTV Online</span><strong>{{ $summary['cctv_online'] }}/{{ $summary['cctv_total'] }}</strong><small>{{ $summary['license_expiring'] }} lisensi jatuh tempo &lt; 30 hari</small></div></div>
    </div>

    @if($filters['type'] === 'all' || $filters['type'] === 'ink')
    <div class="card report-card mb-3">
        <div class="card-header"><strong>Stok Tinta</strong></div>
        <div class="card-body p-0"><div class="table-responsive"><table class="table align-middle mb-0">
            <thead><tr><th>Nama</th><th>Merk</th><th>Warna</th><th>Satuan</th><th class="text-end">Stok</th><th class="text-end">Minimum</th><th>Status</th></tr></thead>
            <tbody>@forelse($inks as $ink)<tr><td>{{ $ink->name }}</td><td>{{ $ink->brand ?: '-' }}</td><td>{{ $ink->color ?: '-' }}</td><td>{{ $ink->unit }}</td><td class="text-end">{{ $ink->current_stock }}</td><td class="text-end">{{ $ink->minimum_stock }}</td><td><span class="badge {{ $ink->is_low_stock ? 'text-bg-warning' : 'text-bg-success' }}">{{ $ink->is_low_stock ? 'Stok minimum' : 'Aman' }}</span></td></tr>@empty<tr><td colspan="7" class="text-center text-muted py-3">Belum ada data tinta.</td></tr>@endforelse</tbody>
        </table></div></div>
    </div>
    @endif

    @if($filters['type'] === 'all' || $filters['type'] === 'sparepart')
    <div class="card report-card mb-3">
        <div class="card-header"><strong>Stok Sparepart</strong></div>
        <div class="card-body p-0"><div class="table-responsive"><table class="table align-middle mb-0">
            <thead><tr><th>Kode</th><th>Nama</th><th>Kategori</th><th>Merk</th><th class="text-end">Stok</th><th class="text-end">Minimum</th><th>Status</th></tr></thead>
            <tbody>@forelse($spareparts as $part)<tr><td>{{ $part->code }}</td><td>{{ $part->name }}</td><td>{{ $part->category ?: '-' }}</td><td>{{ $part->brand ?: '-' }}</td><td class="text-end">{{ $part->current_stock }}</td><td class="text-end">{{ $part->minimum_stock }}</td><td><span class="badge {{ $part->is_low_stock ? 'text-bg-warning' : 'text-bg-success' }}">{{ $part->is_low_stock ? 'Stok minimum' : 'Aman' }}</span></td></tr>@empty<tr><td colspan="7" class="text-center text-muted py-3">Belum ada data sparepart.</td></tr>@endforelse</tbody>
        </table></div></div>
    </div>
    @endif

    @if($filters['type'] === 'all' || $filters['type'] === 'waste')
    <div class="card report-card mb-3">
        <div class="card-header"><strong>Limbah IT</strong></div>
        <div class="card-body p-0"><div class="table-responsive"><table class="table align-middle mb-0">
            <thead><tr><th>Kode</th><th>Tanggal</th><th>Jenis</th><th>Jumlah</th><th>Status Pengumpulan</th><th>Lokasi Penyimpanan</th></tr></thead>
            <tbody>@forelse($wastes as $waste)<tr><td>{{ $waste->waste_code }}</td><td>{{ $waste->waste_date?->format('d M Y') ?: '-' }}</td><td>{{ $waste->waste_type }}</td><td>{{ $waste->quantity }} {{ $waste->unit }}</td><td>{{ $waste->collection_status ?: '-' }}</td><td>{{ $waste->storage_location ?: '-' }}</td></tr>@empty<tr><td colspan="6" class="text-center text-muted py-3">Belum ada data limbah IT.</td></tr>@endforelse</tbody>
        </table></div></div>
    </div>
    @endif

    @if($filters['type'] === 'all' || $filters['type'] === 'license' || $filters['type'] === 'cctv')
    <div class="row g-3">
        <div class="col-lg-6"><div class="card report-card h-100"><div class="card-header"><strong>Lisensi</strong></div><div class="card-body p-0"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Nama</th><th>Vendor</th><th>Seat</th><th>Berakhir</th></tr></thead><tbody>@forelse($licenses as $license)<tr><td>{{ $license->name }}</td><td>{{ $license->vendor ?: '-' }}</td><td>{{ $license->used_seats }}/{{ $license->total_seats }}</td><td>{{ $license->expiry_date?->format('d M Y') ?: '-' }}</td></tr>@empty<tr><td colspan="4" class="text-center text-muted py-3">Belum ada data lisensi.</td></tr>@endforelse</tbody></table></div></div></div></div>
        <div class="col-lg-6"><div class="card report-card h-100"><div class="card-header"><strong>Status CCTV</strong></div><div class="card-body p-0"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Kode</th><th>Nama</th><th>Lokasi</th><th>Status</th></tr></thead><tbody>@forelse($cctvs as $cctv)<tr><td>{{ $cctv->code }}</td><td>{{ $cctv->name }}</td><td>{{ $cctv->location_detail ?: '-' }}</td><td><span class="badge {{ $cctv->status === 'online' ? 'text-bg-success' : 'text-bg-secondary' }}">{{ ucfirst($cctv->status ?: 'unknown') }}</span></td></tr>@empty<tr><td colspan="4" class="text-center text-muted py-3">Belum ada data CCTV.</td></tr>@endforelse</tbody></table></div></div></div></div>
    </div>
    @endif

    <div class="card report-card mt-3"><div class="card-header d-flex justify-content-between"><strong>Riwayat Kelola</strong><span class="text-muted small">{{ $histories->count() }} transaksi</span></div><div class="card-body p-0"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Tanggal</th><th>Jenis</th><th>Item</th><th>Aksi</th><th>Jumlah</th><th>Referensi</th><th>Oleh</th></tr></thead><tbody>@forelse($histories as $history)<tr><td>{{ $history['date']?->format('d M Y') ?: '-' }}</td><td>{{ $history['module'] }}</td><td>{{ $history['item'] ?: '-' }}</td><td>{{ $history['action'] }}</td><td>{{ $history['quantity'] }}</td><td>{{ $history['reference'] ?: '-' }}</td><td>{{ $history['actor'] ?: '-' }}</td></tr>@empty<tr><td colspan="7" class="text-center text-muted py-3">Tidak ada riwayat pada periode/filter ini.</td></tr>@endforelse</tbody></table></div></div></div>
</div>
@include('reports._styles')
@endsection
