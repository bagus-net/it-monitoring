@extends('layouts.app')

@section('content')
<div class="container mt-4 asset-page">
    <div class="d-flex justify-content-between align-items-start mb-3"><div><div class="asset-eyebrow">IT Asset Management</div><h2 class="mb-1">Peralatan IT</h2><p class="text-muted mb-0">Inventaris aset, kondisi, lokasi, dan informasi teknis peralatan IT.</p></div><a href="{{ route('equipments.create') }}" class="btn btn-brand">Tambah Peralatan</a></div>
    <div class="row g-3 mb-3"><div class="col-md-4"><div class="asset-stat total"><span>Total Aset</span><strong>{{ $summary['total'] }}</strong><small>aset terdaftar</small></div></div><div class="col-md-4"><div class="asset-stat active"><span>Kondisi Normal</span><strong>{{ $summary['active'] }}</strong><small>siap digunakan</small></div></div><div class="col-md-4"><div class="asset-stat attention"><span>Perlu Perhatian</span><strong>{{ $summary['attention'] }}</strong><small>rusak atau perbaikan</small></div></div></div>
    <div class="card asset-recap mb-3">
        <div class="card-header"><strong>Rekap Peralatan per Jenis</strong></div>
        <div class="card-body">
            <div class="row g-3">
                @forelse($typeRecap as $type)
                    <div class="col-6 col-lg-3">
                        <div class="type-card">
                            <span class="type-name">{{ $type->name }}</span>
                            <strong>{{ $type->equipments_count }}</strong>
                            <small>unit terdaftar</small>
                            <div class="type-condition">
                                <span class="condition-badge condition-good">Baik {{ $type->good_count }}</span>
                                <span class="condition-badge condition-attention">Rusak {{ $type->broken_count }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-muted">Belum ada jenis peralatan yang terdaftar.</div>
                @endforelse
            </div>
        </div>
    </div>
    <div class="card asset-recap mb-3">
        <div class="card-header"><strong>Tingkat Kritikalitas Layanan</strong></div>
        <div class="card-body">
            <div class="row g-3">
                @foreach($criticalityRecap as $level)
                    <div class="col-6 col-lg-3">
                        <div class="criticality-card level-{{ $level['key'] }}">
                            <span class="type-name">{{ $level['label'] }}</span>
                            <strong>{{ $level['total'] }}</strong>
                            <small>{{ $summary['total'] > 0 ? round($level['total'] / $summary['total'] * 100) : 0 }}% dari total aset</small>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="card asset-filter mb-3">
        <div class="card-header"><strong>Filter Peralatan</strong></div>
        <div class="card-body">
            <form method="GET" action="{{ route('equipments.index') }}" class="row g-2 align-items-end">
                @if($search)<input type="hidden" name="search" value="{{ $search }}">@endif
                <div class="col-6 col-lg-2"><label class="form-label">Jenis</label><select name="equipment_type_id" class="form-select"><option value="">Semua</option>@foreach($filterOptions['types'] as $type)<option value="{{ $type->id }}" @selected((string) $filters['equipment_type_id'] === (string) $type->id)>{{ $type->name }}</option>@endforeach</select></div>
                <div class="col-6 col-lg-2"><label class="form-label">Merk</label><select name="manufacturer_id" class="form-select"><option value="">Semua</option>@foreach($filterOptions['manufacturers'] as $manufacturer)<option value="{{ $manufacturer->id }}" @selected((string) $filters['manufacturer_id'] === (string) $manufacturer->id)>{{ $manufacturer->name }}</option>@endforeach</select></div>
                <div class="col-6 col-lg-2"><label class="form-label">Lokasi</label><select name="location_id" class="form-select"><option value="">Semua</option>@foreach($filterOptions['locations'] as $location)<option value="{{ $location->id }}" @selected((string) $filters['location_id'] === (string) $location->id)>{{ $location->name }}</option>@endforeach</select></div>
                <div class="col-6 col-lg-2"><label class="form-label">Kondisi</label><select name="condition" class="form-select"><option value="">Semua</option>@foreach($filterOptions['conditions'] as $condition)<option value="{{ $condition }}" @selected($filters['condition'] === $condition)>{{ ucfirst($condition) }}</option>@endforeach</select></div>
                <div class="col-6 col-lg-2"><label class="form-label">Kritikalitas</label><select name="criticality" class="form-select"><option value="">Semua</option>@foreach($filterOptions['criticalities'] as $key => $label)<option value="{{ $key }}" @selected($filters['criticality'] === $key)>{{ $label }}</option>@endforeach</select></div>
                <div class="col-6 col-lg-2"><label class="form-label">Departemen</label><select name="department" class="form-select"><option value="">Semua</option>@foreach($filterOptions['departments'] as $department)<option value="{{ $department }}" @selected($filters['department'] === $department)>{{ $department }}</option>@endforeach</select></div>
                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-brand btn-sm">Terapkan Filter</button>
                    <a href="{{ route('equipments.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                </div>
            </form>
        </div>
    </div>
    <div class="card asset-list"><div class="card-header"><strong>Daftar Peralatan</strong></div><div class="card-body p-0"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Peralatan</th><th>Tipe / Merk</th><th>Lokasi</th><th>PIC</th><th>Kondisi</th><th>IP Address / Ukuran Layar | Resolusi / No. Seri</th><th>Aksi</th></tr></thead><tbody>@forelse($equipments as $eq)@php $condition = $eq->condition ?? $eq->status ?? 'tidak dicatat'; $conditionClass = in_array($condition, ['rusak','perbaikan']) ? 'condition-attention' : 'condition-good'; @endphp<tr><td><div class="asset-cell">@if($eq->photo_path)<img src="{{ asset('storage/' . $eq->photo_path) }}" alt="{{ $eq->name }}">@else<span class="asset-initial">IT</span>@endif<div><strong>{{ $eq->name }}</strong><small>{{ $eq->asset_tag ?? $eq->serial_number ?? '-' }}</small></div></div></td><td><strong>{{ $eq->type->name ?? '-' }}</strong><small>{{ $eq->manufacturer->name ?? $eq->model ?? '-' }}</small></td><td>{{ $eq->assetLocation?->name ?: $eq->getRawOriginal('location') ?: '-' }}</td><td>{{ $eq->owner_name ?? '-' }}<small>{{ $eq->department ?? '' }}</small></td><td><span class="condition-badge {{ $conditionClass }}">{{ ucfirst($condition) }}</span></td><td>@if(strtolower($eq->type->name ?? '') === 'monitor'){{ trim(($eq->technical_details['screen_size'] ?? '') . ' | ' . ($eq->technical_details['resolution'] ?? ''), ' |') ?: '-' }}@elseif(strtolower($eq->type->name ?? '') === 'printer'){{ $eq->serial_number ?? '-' }}@else{{ $eq->ip_address ?? '-' }}@endif</td><td class="text-nowrap"><a href="{{ route('equipments.show', $eq) }}" class="btn btn-sm btn-outline-secondary">Detail</a><a href="{{ route('equipments.edit', $eq) }}" class="btn btn-sm btn-outline-primary">Edit</a><form action="{{ route('equipments.destroy', $eq) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus peralatan ini?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Hapus</button></form></td></tr>@empty<tr><td colspan="7" class="text-center text-muted py-4">Belum ada peralatan IT.</td></tr>@endforelse</tbody></table></div><div class="table-pagination">{{ $equipments->links() }}</div></div></div>
</div>
<style>.asset-eyebrow{color:#0b5ea8;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em}.asset-stat{padding:15px 17px;background:#fff;border:1px solid #dbe5ef;border-top:4px solid #64748b}.asset-stat span,.asset-stat small{display:block;color:#64748b;font-size:.76rem}.asset-stat strong{display:block;font-size:1.65rem}.asset-stat.total{border-top-color:#0b5ea8}.asset-stat.active{border-top-color:#159957}.asset-stat.attention{border-top-color:#f59e0b}.asset-list{border:1px solid #dbe5ef}.asset-list .card-header{background:#f8fafc}.asset-list small{display:block;color:#64748b}.asset-cell{display:flex;align-items:center;gap:10px}.asset-cell img,.asset-initial{width:38px;height:38px;flex:0 0 38px;object-fit:cover;border-radius:5px;border:1px solid #dbe5ef}.asset-initial{display:flex;align-items:center;justify-content:center;background:#edf5fc;color:#0b5ea8;font-size:.7rem;font-weight:700}.condition-badge{display:inline-block;padding:4px 7px;border-radius:3px;font-size:.74rem;font-weight:700}.condition-good{background:#dcfce7;color:#166534}.condition-attention{background:#fee2e2;color:#991b1b}.asset-recap{border:1px solid #dbe5ef}.asset-recap .card-header{background:#f8fafc}.asset-filter{border:1px solid #dbe5ef}.asset-filter .card-header{background:#f8fafc}.asset-filter .form-label{font-size:.76rem;font-weight:700;color:#475569;margin-bottom:3px}.type-card,.criticality-card{height:100%;padding:13px 15px;background:#fff;border:1px solid #dbe5ef;border-left:4px solid #0b5ea8}.type-name{display:block;color:#17324d;font-size:.82rem;font-weight:700}.type-card strong,.criticality-card strong{display:block;font-size:1.5rem;line-height:1.2}.type-card small,.criticality-card small{display:block;color:#64748b;font-size:.74rem}.type-condition{display:flex;gap:6px;flex-wrap:wrap;margin-top:8px}.criticality-card.level-critical{border-left-color:#b91c1c}.criticality-card.level-high{border-left-color:#f97316}.criticality-card.level-medium{border-left-color:#f6b322}.criticality-card.level-low{border-left-color:#159957}.criticality-card.level-unset{border-left-color:#94a3b8}</style>
@endsection
