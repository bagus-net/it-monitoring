@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
        <div><h2 class="mb-1">Karantina Aset Rusak</h2><p class="text-muted mb-0">Daftar peralatan yang dikeluarkan dari operasional dan checklist.</p></div>
        <a href="{{ route('equipment-quarantines.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Karantina Aset</a>
    </div>
    <div class="alert alert-info"><i class="bi bi-info-circle"></i> <strong>Alur Karantina:</strong> hanya peralatan dengan kondisi <strong>Rusak</strong> yang dapat dimasukkan. Setelah disimpan, status aset menjadi <strong>Nonaktif / Tidak dipakai</strong> dan aset tidak ditawarkan pada jadwal maupun checklist baru. Jika aset dicatat sebagai jenis limbah <strong>Peralatan IT</strong> dan batch sudah <strong>diserahterimakan ke Limbah B3</strong>, aset otomatis keluar dari daftar karantina aktif.</div>
    <div class="card"><div class="card-header"><strong>Daftar Aset Dalam Karantina</strong></div><div class="table-responsive"><table class="table align-middle mb-0"><thead class="table-light"><tr><th>Peralatan</th><th>Lokasi</th><th>Tanggal</th><th>Status Akhir</th><th>Alasan</th><th>Catatan</th><th>Dicatat Oleh</th></tr></thead><tbody>
    @forelse ($quarantines as $quarantine)
        <tr><td><strong>{{ $quarantine->equipment->name }}</strong><small class="d-block text-muted">{{ $quarantine->equipment->asset_tag ?: $quarantine->equipment->serial_number ?: '-' }}</small></td><td>{{ $quarantine->equipment->assetLocation?->name ?: '-' }}</td><td>{{ $quarantine->quarantined_at->format('d M Y') }}</td><td><span class="badge {{ $quarantine->outcome === 'nonaktif' ? 'bg-secondary' : 'bg-danger' }}">{{ ucfirst($quarantine->outcome) }}</span></td><td>{{ $quarantine->reason }}</td><td>{{ $quarantine->notes ?: '-' }}</td><td>{{ $quarantine->quarantinedBy?->name ?: '-' }}</td></tr>
    @empty
        <tr><td colspan="7" class="text-center text-muted py-4">Belum ada aset dalam karantina.</td></tr>
    @endforelse
    </tbody></table></div></div>
    <div class="mt-3">{{ $quarantines->links() }}</div>
</div>
@endsection
