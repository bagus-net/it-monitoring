@extends('layouts.app')

@section('content')
<div class="container mt-4 asset-page">
    <div class="d-flex justify-content-between align-items-start mb-3"><div class="d-flex align-items-start gap-3"><span class="asset-page-icon"><i class="bi bi-hdd-stack"></i></span><div><div class="asset-eyebrow">IT Asset Management</div><h2 class="mb-1">Peralatan IT</h2><p class="text-muted mb-0">Inventaris aset, kondisi, lokasi, dan informasi teknis peralatan IT.</p></div></div><a href="{{ route('equipments.create') }}" class="btn btn-brand"><i class="bi bi-plus-lg"></i>Tambah Peralatan</a></div>
    <div class="row g-3 mb-3">
      <div class="col-md-4"><div class="asset-widget total"><div class="widget-top"><span>Total Aset</span><i class="bi bi-hdd-stack widget-icon"></i></div><strong>{{ $summary['total'] }}</strong><div class="widget-spark"><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i></div><small>aset terdaftar</small></div></div>
      <div class="col-md-4"><div class="asset-widget active"><div class="widget-top"><span>Kondisi Normal</span><i class="bi bi-check-circle widget-icon"></i></div><strong>{{ $summary['active'] }}</strong><div class="widget-trend up">{{ $summary['total'] ? round($summary['active'] / $summary['total'] * 100) : 0 }}% dari total aset</div><small>siap digunakan</small></div></div>
      <div class="col-md-4"><div class="asset-widget attention"><div class="widget-top"><span>Perlu Perhatian</span><i class="bi bi-exclamation-triangle widget-icon"></i></div><strong>{{ $summary['attention'] }}</strong><div class="widget-trend down">{{ $summary['total'] ? round($summary['attention'] / $summary['total'] * 100) : 0 }}% dari total aset</div><small>rusak atau perbaikan</small></div></div>
    </div>
    <div class="card asset-recap mb-3">
        <div class="card-header"><strong><i class="bi bi-diagram-3"></i>Rekap Peralatan per Jenis</strong></div>
        <div class="card-body">
            <div class="row g-3">
                @forelse($typeRecap as $type)
                    <div class="col-6 col-lg-3">
                        <div class="type-card">
                            <span class="type-name">{{ $type->name }}</span>
                            <strong>{{ $type->equipments_count }}</strong>
                            <small>unit terdaftar</small>
                            <div class="type-bar"><span class="bar-good" style="width:{{ round($type->good_count / max(1, $type->equipments_count) * 100) }}%"></span><span class="bar-attention" style="width:{{ round($type->broken_count / max(1, $type->equipments_count) * 100) }}%"></span></div>
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
        <div class="card-header"><strong><i class="bi bi-shield-exclamation"></i>Tingkat Kritikalitas Layanan</strong></div>
        <div class="card-body">
            <div class="row g-3">
                @foreach($criticalityRecap as $level)
                    <div class="col-6 col-lg-3">
                        <div class="criticality-card level-{{ $level['key'] }}">
                            <span class="type-name">{{ $level['label'] }}</span>
                            <strong>{{ $level['total'] }}</strong>
                            <div class="level-bar"><span style="width:{{ $summary['total'] > 0 ? round($level['total'] / $summary['total'] * 100) : 0 }}%"></span></div>
                            <small>{{ $summary['total'] > 0 ? round($level['total'] / $summary['total'] * 100) : 0 }}% dari total aset</small>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="card asset-filter mb-3">
        <div class="card-header"><strong><i class="bi bi-sliders"></i>Filter Peralatan</strong></div>
        <div class="card-body">
            <form method="GET" action="{{ route('equipments.index') }}" class="row g-2 align-items-end">
                @if($search)<input type="hidden" name="search" value="{{ $search }}">@endif
                <div class="col-6 col-lg-2"><label class="form-label">Jenis</label><select name="equipment_type_id" class="form-select"><option value="">Semua</option>@foreach($filterOptions['types'] as $type)<option value="{{ $type->id }}" @selected((string) $filters['equipment_type_id'] === (string) $type->id)>{{ $type->name }}</option>@endforeach</select></div>
                <div class="col-6 col-lg-2"><label class="form-label">Merk</label><select name="manufacturer_id" class="form-select"><option value="">Semua</option>@foreach($filterOptions['manufacturers'] as $manufacturer)<option value="{{ $manufacturer->id }}" @selected((string) $filters['manufacturer_id'] === (string) $manufacturer->id)>{{ $manufacturer->name }}</option>@endforeach</select></div>
                <div class="col-6 col-lg-2"><label class="form-label">Lokasi</label><select name="location_id" class="form-select"><option value="">Semua</option>@foreach($filterOptions['locations'] as $location)<option value="{{ $location->id }}" @selected((string) $filters['location_id'] === (string) $location->id)>{{ $location->name }}</option>@endforeach</select></div>
                <div class="col-6 col-lg-2"><label class="form-label">Kondisi</label><select name="condition" class="form-select"><option value="">Semua</option>@foreach($filterOptions['conditions'] as $condition)<option value="{{ $condition }}" @selected($filters['condition'] === $condition)>{{ ucfirst($condition) }}</option>@endforeach</select></div>
                <div class="col-6 col-lg-2"><label class="form-label">Kritikalitas</label><select name="criticality" class="form-select"><option value="">Semua</option>@foreach($filterOptions['criticalities'] as $key => $label)<option value="{{ $key }}" @selected($filters['criticality'] === $key)>{{ $label }}</option>@endforeach</select></div>
                <div class="col-6 col-lg-2"><label class="form-label">Departemen</label><select name="department" class="form-select"><option value="">Semua</option>@foreach($filterOptions['departments'] as $department)<option value="{{ $department }}" @selected($filters['department'] === $department)>{{ $department }}</option>@endforeach</select></div>
                <div class="col-6 col-lg-2"><label class="form-label">Tahun Masuk</label><select name="purchase_year" class="form-select"><option value="">Semua</option>@foreach($filterOptions['purchase_years'] as $year)<option value="{{ $year }}" @selected((string) $filters['purchase_year'] === (string) $year)>{{ $year }}</option>@endforeach</select></div>
                <div class="col-6 col-lg-2"><label class="form-label">Tanggal Masuk Dari</label><input type="date" name="purchase_date_from" class="form-control" value="{{ $filters['purchase_date_from'] }}"></div>
                <div class="col-6 col-lg-2"><label class="form-label">Tanggal Masuk Sampai</label><input type="date" name="purchase_date_to" class="form-control" value="{{ $filters['purchase_date_to'] }}"></div>
                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-brand btn-sm">Terapkan Filter</button>
                    <a href="{{ route('equipments.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                </div>
            </form>
        </div>
    </div>
    <div class="card asset-list"><div class="card-header"><strong><i class="bi bi-list-ul"></i>Daftar Peralatan</strong></div><div class="card-body p-0"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Peralatan</th><th>Tipe / Merk</th><th>Lokasi</th><th>PIC</th><th>Kondisi</th><th>IP Address / Ukuran Layar | Resolusi / No. Seri</th><th>Aksi</th></tr></thead><tbody>@forelse($equipments as $eq)@php $condition = $eq->condition ?? $eq->status ?? 'tidak dicatat'; $conditionClass = in_array($condition, ['rusak','perbaikan']) ? 'condition-attention' : 'condition-good'; @endphp<tr><td><div class="asset-cell">@if($eq->photo_path)<img src="{{ asset('storage/' . $eq->photo_path) }}" alt="{{ $eq->name }}">@else<span class="asset-initial">IT</span>@endif<div><strong>{{ $eq->name }}</strong><small>{{ $eq->asset_tag ?? $eq->serial_number ?? '-' }}</small></div></div></td><td><strong>{{ $eq->type->name ?? '-' }}</strong><small>{{ $eq->manufacturer->name ?? $eq->model ?? '-' }}</small></td><td>{{ $eq->assetLocation?->name ?: $eq->getRawOriginal('location') ?: '-' }}</td><td>{{ $eq->owner_name ?? '-' }}<small>{{ $eq->department ?? '' }}</small></td><td><span class="condition-badge {{ $conditionClass }}">{{ ucfirst($condition) }}</span></td><td>@if(strtolower($eq->type->name ?? '') === 'monitor'){{ trim(($eq->technical_details['screen_size'] ?? '') . ' | ' . ($eq->technical_details['resolution'] ?? ''), ' |') ?: '-' }}@elseif(strtolower($eq->type->name ?? '') === 'printer'){{ $eq->serial_number ?? '-' }}@else{{ $eq->ip_address ?? '-' }}@endif</td><td class="text-nowrap"><a href="{{ route('equipments.show', $eq) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i>Detail</a><a href="{{ route('equipments.edit', $eq) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i>Edit</a><form action="{{ route('equipments.destroy', $eq) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus peralatan ini?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash3"></i>Hapus</button></form></td></tr>@empty<tr><td colspan="7" class="text-center text-muted py-4">Belum ada peralatan IT.</td></tr>@endforelse</tbody></table></div><div class="table-pagination">{{ $equipments->links() }}</div></div></div>
</div>
<style>.asset-eyebrow{color:#0b5ea8;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em}.asset-stat{padding:15px 17px;background:#fff;border:1px solid #dbe5ef;border-top:4px solid #64748b}.asset-stat span,.asset-stat small{display:block;color:#64748b;font-size:.76rem}.asset-stat strong{display:block;font-size:1.65rem}.asset-stat.total{border-top-color:#0b5ea8}.asset-stat.active{border-top-color:#159957}.asset-stat.attention{border-top-color:#f59e0b}.asset-list{border:1px solid #dbe5ef}.asset-list .card-header{background:#f8fafc}.asset-list small{display:block;color:#64748b}.asset-cell{display:flex;align-items:center;gap:10px}.asset-cell img,.asset-initial{width:38px;height:38px;flex:0 0 38px;object-fit:cover;border-radius:5px;border:1px solid #dbe5ef}.asset-initial{display:flex;align-items:center;justify-content:center;background:#edf5fc;color:#0b5ea8;font-size:.7rem;font-weight:700}.condition-badge{display:inline-block;padding:4px 7px;border-radius:3px;font-size:.74rem;font-weight:700}.condition-good{background:#dcfce7;color:#166534}.condition-attention{background:#fee2e2;color:#991b1b}.asset-recap{border:1px solid #dbe5ef}.asset-recap .card-header{background:#f8fafc}.asset-filter{border:1px solid #dbe5ef}.asset-filter .card-header{background:#f8fafc}.asset-filter .form-label{font-size:.76rem;font-weight:700;color:#475569;margin-bottom:3px}.type-card,.criticality-card{height:100%;padding:13px 15px;background:#fff;border:1px solid #dbe5ef;border-left:4px solid #0b5ea8}.type-name{display:block;color:#17324d;font-size:.82rem;font-weight:700}.type-card strong,.criticality-card strong{display:block;font-size:1.5rem;line-height:1.2}.type-card small,.criticality-card small{display:block;color:#64748b;font-size:.74rem}.type-condition{display:flex;gap:6px;flex-wrap:wrap;margin-top:8px}.criticality-card.level-critical{border-left-color:#b91c1c}.criticality-card.level-high{border-left-color:#f97316}.criticality-card.level-medium{border-left-color:#f6b322}.criticality-card.level-low{border-left-color:#159957}.criticality-card.level-unset{border-left-color:#94a3b8}</style>
<style>
.asset-page{max-width:1480px;margin-top:0!important;color:#18243d}
.asset-page>.d-flex{padding:4px 4px 18px;border-bottom:1px solid #e8edf4}
.asset-page h2{font-size:1.8rem;font-weight:800;letter-spacing:-.03em;color:#18243d}
.asset-page .asset-eyebrow{color:#2161f5;font-size:.68rem;font-weight:800;letter-spacing:.13em}
.asset-page .text-muted{color:#8792a7!important;font-size:.78rem}
.asset-page .btn-brand{border-radius:10px;padding:10px 16px;background:linear-gradient(135deg,#2161f5,#3b82f6);font-weight:700;box-shadow:0 8px 16px rgba(33,97,245,.18)}
.asset-page .card{border:1px solid #e7ebf2;border-radius:14px;background:#fff;box-shadow:0 5px 18px rgba(35,52,85,.045);overflow:hidden}
.asset-page .asset-stat{position:relative;min-height:112px;padding:18px 20px;border:0;border-left:4px solid #2161f5;background:#fff;overflow:hidden}
.asset-page .asset-stat:after{content:'';position:absolute;right:-22px;bottom:-35px;width:92px;height:92px;border-radius:50%;background:currentColor;opacity:.06}
.asset-page .asset-stat span{color:#78849a;font-size:.72rem;font-weight:700}.asset-page .asset-stat strong{margin:8px 0 3px;color:#18243d;font-size:1.8rem;font-weight:800}.asset-page .asset-stat small{color:#8792a7;font-size:.68rem}.asset-page .asset-stat.total{color:#2161f5;border-left-color:#2161f5}.asset-page .asset-stat.active{color:#27b47a;border-left-color:#27b47a}.asset-page .asset-stat.attention{color:#f3b54b;border-left-color:#f3b54b}
.asset-page .card-header{padding:16px 20px;border-bottom:1px solid #edf0f5;background:#fff;color:#18243d;font-size:.88rem;font-weight:800}.asset-page .card-body{padding:20px}
.asset-page .type-card,.asset-page .criticality-card{padding:15px;border:1px solid #e7ebf2;border-left:3px solid #2161f5;border-radius:11px;background:#f9fafc;transition:transform .15s,box-shadow .15s}.asset-page .type-card:hover,.asset-page .criticality-card:hover{transform:translateY(-2px);box-shadow:0 8px 18px rgba(35,52,85,.08)}.asset-page .type-name{color:#3f4a63;font-size:.76rem}.asset-page .type-card strong,.asset-page .criticality-card strong{margin:7px 0 2px;color:#18243d;font-size:1.45rem;font-weight:800}.asset-page .type-card small,.asset-page .criticality-card small{color:#8792a7;font-size:.68rem}.asset-page .condition-badge{padding:5px 8px;border-radius:999px;font-size:.66rem}
.asset-page .asset-filter .card-body{padding:18px 20px}.asset-page .form-label{color:#69758d!important;font-size:.68rem!important;font-weight:700!important;letter-spacing:.02em}.asset-page .form-control,.asset-page .form-select{min-height:39px;border:1px solid #dfe5ee;border-radius:9px;background:#f9fafc;color:#34415a;font-size:.76rem}.asset-page .form-control:focus,.asset-page .form-select:focus{border-color:#7aa3ff;box-shadow:0 0 0 3px rgba(33,97,245,.1);background:#fff}.asset-page .btn-sm{border-radius:8px;font-size:.7rem;font-weight:700}.asset-page .btn-outline-secondary{border-color:#dfe5ee;color:#68758d}.asset-page .btn-outline-primary{border-color:#b8ccff;color:#2161f5}.asset-page .btn-outline-danger{border-color:#f4c2c7;color:#dc5260}
.asset-page .asset-list .card-body{padding:0}.asset-page .table{font-size:.74rem}.asset-page .table thead th{padding:13px 16px;border-bottom:1px solid #e8edf4;background:#f8fafc;color:#7d899e;font-size:.65rem;font-weight:800;letter-spacing:.04em;text-transform:uppercase;white-space:nowrap}.asset-page .table tbody td{padding:14px 16px;border-color:#eef1f5;color:#536079;vertical-align:middle}.asset-page .table tbody tr{transition:background .15s}.asset-page .table tbody tr:hover{background:#f8faff}.asset-page .table td strong{color:#26324b;font-weight:700}.asset-page .asset-cell{gap:11px}.asset-page .asset-cell img,.asset-page .asset-initial{width:42px;height:42px;flex-basis:42px;border:0;border-radius:11px}.asset-page .asset-initial{background:#eef3ff;color:#2161f5;font-weight:800}.asset-page .asset-list small{margin-top:3px;color:#94a0b2;font-size:.66rem}.asset-page .table-pagination{padding:14px 20px;background:#fff}.asset-page .page-link{border-radius:7px;margin-left:4px!important;font-size:.72rem}
@media(max-width:767px){.asset-page>.d-flex{gap:14px;flex-direction:column!important}.asset-page>.d-flex .btn{align-self:stretch}.asset-page .card-body{padding:15px}.asset-page .card-header{padding:14px 15px}.asset-page .table-responsive{margin:0;padding:0}.asset-page .asset-stat{min-height:100px;padding:15px}.asset-page .asset-stat strong{font-size:1.5rem}}
</style>

<style>
.asset-page{--vio:#7c5cfc;--vio-dark:#5b3fd6;--teal:#14b8a6}
.asset-page-icon{display:flex;align-items:center;justify-content:center;width:46px;height:46px;flex:0 0 46px;border-radius:14px;background:linear-gradient(135deg,var(--vio),#a78bfa);color:#fff;font-size:1.15rem;box-shadow:0 8px 16px rgba(124,92,252,.28)}
.asset-page .asset-eyebrow{color:var(--vio)}
.asset-page .btn-brand{background:linear-gradient(135deg,var(--vio),#a78bfa);box-shadow:0 8px 16px rgba(124,92,252,.24)}
.asset-page .btn-brand i,.asset-page .card-header strong i{margin-right:7px}
.asset-page .card-header strong{display:flex;align-items:center;color:#18243d}
.asset-page .card-header strong i{color:var(--vio)}
.asset-page .btn-outline-primary{border-color:#d9d1ff;color:var(--vio)}
.asset-page .btn-outline-primary:hover{background:var(--vio);border-color:var(--vio)}
.asset-page .btn-sm i{margin-right:5px;font-size:.72rem}
.asset-page .asset-widget{position:relative;min-height:150px;padding:20px;border:1px solid #ece9fb;border-radius:18px;background:#fff;box-shadow:0 8px 22px rgba(92,71,207,.07);overflow:hidden}
.asset-page .widget-top{display:flex;align-items:center;justify-content:space-between;color:#8792a7;font-size:.74rem;font-weight:700}
.asset-page .widget-icon{display:flex;align-items:center;justify-content:center;width:34px;height:34px;border-radius:10px;font-size:.92rem}
.asset-page .asset-widget strong{display:block;margin:10px 0 4px;color:#18243d;font-size:1.9rem;font-weight:800;letter-spacing:-.02em}
.asset-page .asset-widget small{display:block;color:#8792a7;font-size:.68rem}
.asset-page .asset-widget.total .widget-icon{background:#efeafe;color:var(--vio)}
.asset-page .asset-widget.active .widget-icon{background:#e2f8f4;color:var(--teal)}
.asset-page .asset-widget.attention .widget-icon{background:#ffeef0;color:#f43f5e}
.asset-page .widget-spark{display:flex;align-items:flex-end;gap:5px;height:34px;margin:6px 0 8px}
.asset-page .widget-spark i{flex:1;border-radius:3px 3px 0 0;background:#ece9fb;font-style:normal}
.asset-page .widget-spark i:nth-child(1){height:40%}.asset-page .widget-spark i:nth-child(2){height:65%}.asset-page .widget-spark i:nth-child(3){height:48%}.asset-page .widget-spark i:nth-child(4){height:80%}.asset-page .widget-spark i:nth-child(5){height:58%}.asset-page .widget-spark i:nth-child(6){height:96%;background:var(--vio)}.asset-page .widget-spark i:nth-child(7){height:70%}.asset-page .widget-spark i:nth-child(8){height:52%}
.asset-page .widget-trend{display:inline-flex;align-items:center;margin:8px 0;padding:4px 9px;border-radius:999px;font-size:.7rem;font-weight:700}
.asset-page .widget-trend.up{background:#e2f8f4;color:#0f9c8a}
.asset-page .widget-trend.down{background:#ffeef0;color:#e11d48}
.asset-page .type-card,.asset-page .criticality-card{border-left:0;border-radius:16px;background:#faf9ff}
.asset-page .type-card:hover,.asset-page .criticality-card:hover{box-shadow:0 10px 22px rgba(92,71,207,.12)}
.asset-page .type-bar{display:flex;height:6px;margin:10px 0;border-radius:999px;overflow:hidden;background:#ece9fb}
.asset-page .type-bar .bar-good{background:var(--teal)}
.asset-page .type-bar .bar-attention{background:#f43f5e}
.asset-page .level-bar{height:6px;margin:8px 0 6px;border-radius:999px;background:#ece9fb;overflow:hidden}
.asset-page .level-bar span{display:block;height:100%;background:linear-gradient(90deg,var(--vio),#a78bfa);border-radius:999px}
.asset-page .criticality-card.level-critical .level-bar span{background:linear-gradient(90deg,#e11d48,#f43f5e)}
.asset-page .criticality-card.level-high .level-bar span{background:linear-gradient(90deg,#ea580c,#f97316)}
.asset-page .criticality-card.level-medium .level-bar span{background:linear-gradient(90deg,#c2870a,#f6b322)}
.asset-page .criticality-card.level-low .level-bar span{background:linear-gradient(90deg,#0f9c8a,var(--teal))}
.asset-page .condition-good{background:#e2f8f4;color:#0f9c8a}
.asset-page .condition-attention{background:#ffeef0;color:#e11d48}
.asset-page .form-control:focus,.asset-page .form-select:focus{border-color:#c4b5fd;box-shadow:0 0 0 3px rgba(124,92,252,.14)}
.asset-page .table tbody tr:hover{background:#faf9ff}
.asset-page .asset-initial{background:#efeafe;color:var(--vio)}
@media(max-width:767px){.asset-page .asset-widget{min-height:130px}}
</style>
@endsection
