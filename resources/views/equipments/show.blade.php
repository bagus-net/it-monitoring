@extends('layouts.app')

@section('content')
@php
    $criticalityLabels = ['low' => 'Rendah', 'medium' => 'Sedang', 'high' => 'Tinggi', 'critical' => 'Kritis'];
    $criticalityClasses = ['low' => 'asset-criticality-low', 'medium' => 'asset-criticality-medium', 'high' => 'asset-criticality-high', 'critical' => 'asset-criticality-critical'];
    $condition = $equipment->condition ?? $equipment->status ?? 'Tidak dicatat';
    $locationName = $equipment->assetLocation?->name ?: $equipment->getRawOriginal('location');
@endphp

<div class="container mt-4 asset-detail">
    <div class="asset-hero {{ $equipment->photo_path ? 'asset-hero-with-photo' : '' }}">
        <div class="asset-hero-main">
            @if ($equipment->photo_path)
                <img src="{{ asset('storage/' . $equipment->photo_path) }}" alt="Foto {{ $equipment->name }}" class="asset-photo">
            @else
                <div class="asset-icon">IT</div>
            @endif
            <div>
                <div class="asset-eyebrow">IT Asset Record</div>
                <h1>{{ $equipment->name }}</h1>
                <div class="asset-meta"><span><i class="bi bi-tag"></i>{{ $equipment->type->name ?? 'Tipe belum dipilih' }}</span><span><i class="bi bi-building"></i>{{ $equipment->manufacturer->name ?? 'Manufacturer belum dipilih' }}</span><span><i class="bi bi-cpu"></i>{{ $equipment->model ?? 'Model belum dicatat' }}</span></div>
            </div>
        </div>
        <div class="asset-hero-side">
            <span class="asset-tag">{{ $equipment->asset_tag ?? 'Belum ada kode aset' }}</span>
            <span class="asset-criticality {{ $criticalityClasses[$equipment->criticality] ?? 'asset-criticality-none' }}">{{ $criticalityLabels[$equipment->criticality] ?? 'Kritikalitas belum dinilai' }}</span>
        </div>
    </div>

    <div class="asset-actions">
        <a href="{{ route('equipments.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i>Kembali</a>
        <a href="{{ route('equipments.label', $equipment) }}" class="btn btn-outline-dark" target="_blank" rel="noopener"><i class="bi bi-qr-code"></i>Cetak Label QR</a>
        <a href="{{ route('equipments.label.download', $equipment) }}" class="btn btn-outline-success" target="_blank" rel="noopener"><i class="bi bi-download"></i>Unduh Label JPEG</a>
        <a href="{{ route('equipment-transfers.create', ['equipment_id' => $equipment->id]) }}" class="btn btn-outline-primary"><i class="bi bi-arrow-left-right"></i>Ajukan Mutasi</a>
        <a href="{{ route('equipments.edit', $equipment) }}" class="btn btn-brand"><i class="bi bi-pencil-square"></i>Edit Peralatan</a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-6"><section class="asset-section h-100"><h2><i class="bi bi-cpu"></i>Identitas Aset</h2><dl class="asset-data-grid"><div><dt>Kode Aset</dt><dd>{{ $equipment->asset_tag ?? '-' }}</dd></div><div><dt>No. Seri</dt><dd>{{ $equipment->serial_number ?? '-' }}</dd></div><div><dt>Tipe</dt><dd>{{ $equipment->type->name ?? '-' }}</dd></div><div><dt>Model</dt><dd>{{ $equipment->model ?? '-' }}</dd></div><div><dt>Sistem Operasi</dt><dd>{{ $equipment->operating_system ?? '-' }}</dd></div><div><dt>Manufacturer</dt><dd>{{ $equipment->manufacturer->name ?? '-' }}</dd></div><div><dt>Kapasitas</dt><dd>{{ $equipment->capacity ?? '-' }}</dd></div>@foreach(($equipment->technical_details ?? []) as $label => $value)<div><dt>{{ str_replace('_', ' ', $label) }}</dt><dd>{{ $value }}</dd></div>@endforeach</dl></section></div>
        <div class="col-lg-6"><section class="asset-section h-100"><h2><i class="bi bi-geo-alt"></i>Penempatan & Kepemilikan</h2><dl class="asset-data-grid"><div><dt>Lokasi</dt><dd>{{ $locationName ?: '-' }}</dd></div><div><dt>IP Address</dt><dd>{{ $equipment->ip_address ?? '-' }}</dd></div><div><dt>Akun User / Pemilik Peralatan</dt><dd>@if($equipment->owner)<span class="asset-owner"><img src="{{ $equipment->owner->profile_photo_path ? asset('storage/' . $equipment->owner->profile_photo_path) : asset('images/default-avatar.svg') }}" alt="Foto profil {{ $equipment->owner->name }}"><span><strong>{{ $equipment->owner->name }}</strong><small>{{ $equipment->owner->email }}</small><small>{{ $equipment->owner->department ?: 'Departemen belum dicatat' }}</small></span></span>@else<em class="text-muted">Belum ditugaskan ke user</em>@endif</dd></div><div><dt>Nama PIC (Kontak)</dt><dd>{{ $equipment->owner_name ?: '<em class="text-muted">Tidak ada informasi PIC</em>' }}</dd></div><div><dt>Unit / Departemen</dt><dd>{{ $equipment->owner?->department ?: $equipment->department ?: '-' }}</dd></div><div><dt>Vendor / Pemasok</dt><dd>{{ $equipment->vendor_name ?? '-' }}</dd></div><div><dt>Kondisi</dt><dd><span class="asset-condition">{{ ucfirst($condition) }}</span></dd></div></dl></section></div>
        <div class="col-lg-6"><section class="asset-section h-100"><h2><i class="bi bi-clock-history"></i>Siklus Hidup & Dukungan</h2><dl class="asset-data-grid"><div><dt>Tanggal Pembelian</dt><dd>{{ $equipment->purchase_date?->format('d M Y') ?? '-' }}</dd></div><div><dt>Tahun Pembuatan</dt><dd>{{ $equipment->manufacture_year ?? '-' }}</dd></div><div><dt>Akhir Garansi</dt><dd>{{ $equipment->warranty_expiry?->format('d M Y') ?? '-' }}</dd></div><div><dt>Akhir Kontrak Dukungan</dt><dd>{{ $equipment->support_contract_end?->format('d M Y') ?? '-' }}</dd></div><div><dt>Status Operasional</dt><dd>{{ ucfirst($equipment->status ?? '-') }}</dd></div><div><dt>Kritikalitas Layanan</dt><dd>{{ $criticalityLabels[$equipment->criticality] ?? '-' }}</dd></div></dl></section></div>
        <div class="col-lg-6"><section class="asset-section h-100"><h2><i class="bi bi-journal-text"></i>Spesifikasi & Catatan</h2><div class="asset-notes">{{ $equipment->specification ?: 'Spesifikasi detail belum dicatat.' }}</div>@if ($equipment->notes)<div class="asset-notes-label">Catatan Tambahan</div><div class="asset-notes">{{ $equipment->notes }}</div>@endif</section></div>
    </div>

    <section class="asset-section maintenance-section">
        <div class="section-heading"><div><h2><i class="bi bi-clipboard2-check"></i>Riwayat Perawatan</h2><p>Hasil pelaksanaan dari dokumen Checklist Perawatan.</p></div><a href="{{ route('maintenance-checklists.index') }}" class="btn btn-sm btn-outline-primary">Pelaksanaan Checklist</a></div>
        <div class="table-responsive"><table class="table asset-log-table align-middle mb-0"><thead><tr><th>Tanggal Jadwal</th><th>Program Perawatan</th><th>Periode</th><th>Hasil</th><th>Keterangan</th><th>Pelapor</th><th></th></tr></thead><tbody>@forelse ($equipment->maintenanceChecklistEntries->sortByDesc(fn ($entry) => $entry->maintenanceChecklist->checked_at) as $entry)@php $document = $entry->maintenanceChecklist; $schedulePeriodKey = $document->checklist_item_id . '|' . $document->year . '|' . $document->month; $scheduledDates = $scheduledDatesByPeriod->get($schedulePeriodKey, []); $monthLabel = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'][$document->month]; @endphp<tr><td>{{ count($scheduledDates) ? implode(', ', $scheduledDates) . ' ' . $monthLabel . ' ' . $document->year : '-' }}</td><td>{{ $document->checklistItem->title ?? '-' }}</td><td>{{ $monthLabel }} {{ $document->year }}</td><td><span class="log-result log-result-{{ $entry->result }}">{{ $entry->result === 'ok' ? 'OK' : 'NOT OK' }}</span></td><td>{{ $entry->remarks ?? '-' }}</td><td>{{ $document->reported_by ?? '-' }}</td><td><a href="{{ route('maintenance-checklists.show', $document) }}" class="btn btn-sm btn-outline-secondary">Dokumen</a></td></tr>@empty<tr><td colspan="7" class="asset-empty">Belum ada hasil Pelaksanaan Checklist untuk peralatan ini.</td></tr>@endforelse</tbody></table></div>
    </section>
    <section class="asset-section transfer-history-section">
        <div class="section-heading"><div><h2><i class="bi bi-arrow-left-right"></i>Riwayat Mutasi Peralatan</h2><p>Riwayat perpindahan PIC, departemen, dan lokasi aset.</p></div><a href="{{ route('equipment-transfers.index') }}" class="btn btn-sm btn-outline-primary">Kelola Mutasi</a></div>
        <div class="table-responsive"><table class="table asset-log-table align-middle mb-0"><thead><tr><th>Tanggal Efektif</th><th>Dari</th><th>Ke</th><th>Lokasi</th><th>Status</th><th></th></tr></thead><tbody>@forelse ($equipment->transfers->sortByDesc('effective_date') as $transfer)<tr><td>{{ $transfer->effective_date?->format('d M Y') ?? '-' }}</td><td>{{ $transfer->from_owner_name ?: '-' }}</td><td>{{ $transfer->to_owner_name ?: '-' }}</td><td>{{ $transfer->toLocation->name ?? 'Tidak diubah' }}</td><td><span class="transfer-history-status transfer-history-{{ $transfer->status }}">{{ ['pending_approval'=>'Menunggu Persetujuan','approved'=>'Disetujui','completed'=>'Selesai','rejected'=>'Ditolak','cancelled'=>'Dibatalkan'][$transfer->status] ?? $transfer->status }}</span></td><td><a href="{{ route('equipment-transfers.show', $transfer) }}" class="btn btn-sm btn-outline-secondary">Detail</a></td></tr>@empty<tr><td colspan="6" class="asset-empty">Belum ada riwayat mutasi untuk peralatan ini.</td></tr>@endforelse</tbody></table></div>
    </section>
    <section class="asset-section repair-history-section">
        <div class="section-heading"><div><h2><i class="bi bi-tools"></i>Kartu Riwayat Perbaikan</h2><p>Riwayat tiket Perbaikan IT untuk peralatan ini.</p></div><a href="{{ route('it-repair-tickets.index') }}" class="btn btn-sm btn-outline-primary">Perbaikan IT</a></div>
        <div class="repair-history-grid">@forelse($equipment->repairTickets->sortByDesc('reported_at') as $ticket)<a href="{{ route('it-repair-tickets.show', $ticket) }}" class="repair-history-card"><div class="repair-history-top"><strong>{{ $ticket->ticket_number }}</strong><span class="repair-ticket-status repair-status-{{ $ticket->status }}">{{ ['open'=>'Open','in_progress'=>'Proses','resolved'=>'Selesai'][$ticket->status] }}</span></div><span class="repair-history-time">{{ $ticket->reported_at?->format('d M Y H:i') }}</span><p>{{ $ticket->problem_description }}</p><div class="repair-history-meta"><span>{{ $ticket->equipment_category ?? 'Kategori belum dicatat' }}</span><span>{{ $ticket->error_type ?? '-' }}</span></div><div class="repair-history-footer"><span>{{ $ticket->assigned_to ? 'Teknisi: ' . $ticket->assigned_to : 'Belum ditugaskan' }}</span><span>Lihat tiket</span></div></a>@empty<div class="asset-empty">Belum ada riwayat tiket perbaikan untuk peralatan ini.</div>@endforelse</div>
    </section>
</div>

<style>
.asset-detail { color:#263238; }.asset-hero { display:flex; justify-content:space-between; gap:20px; padding:24px; background:linear-gradient(120deg,#0f766e,#0e7490); color:#fff; border-radius:8px; }.asset-hero-main { display:flex; align-items:center; gap:16px; min-width:0; }.asset-icon { display:flex; align-items:center; justify-content:center; flex:0 0 52px; width:52px; height:52px; border:1px solid rgba(255,255,255,.35); border-radius:8px; font-size:.85rem; font-weight:700; }.asset-photo { width:120px; height:90px; flex:0 0 120px; object-fit:cover; border:1px solid rgba(255,255,255,.5); border-radius:6px; background:#fff; }.asset-eyebrow { font-size:.72rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; opacity:.8; }.asset-hero h1 { margin:3px 0 6px; font-size:1.55rem; }.asset-meta { font-size:.84rem; opacity:.9; }.asset-hero-side { display:flex; flex-direction:column; align-items:flex-end; gap:8px; }.asset-tag,.asset-criticality { display:inline-flex; align-items:center; padding:5px 9px; border-radius:4px; font-size:.78rem; font-weight:700; }.asset-tag { border:1px solid rgba(255,255,255,.4); }.asset-criticality-low { background:#dcfce7; color:#166534; }.asset-criticality-medium { background:#fef3c7; color:#92400e; }.asset-criticality-high { background:#ffedd5; color:#9a3412; }.asset-criticality-critical { background:#fee2e2; color:#991b1b; }.asset-criticality-none { background:rgba(255,255,255,.16); color:#fff; }.asset-actions { display:flex; justify-content:flex-end; gap:8px; margin:14px 0; }.asset-section { background:#fff; border:1px solid #dbe3ea; border-radius:6px; padding:18px; }.asset-section h2 { margin:0 0 14px; font-size:1rem; color:#0f766e; }.asset-data-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:14px 20px; margin:0; }.asset-data-grid div { min-width:0; }.asset-data-grid dt { margin-bottom:3px; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.04em; color:#64748b; }.asset-data-grid dd { margin:0; overflow-wrap:anywhere; font-size:.9rem; }.asset-condition { display:inline-block; padding:3px 7px; border-radius:3px; background:#e0f2fe; color:#075985; font-weight:700; font-size:.78rem; }.asset-notes { white-space:pre-line; color:#334155; line-height:1.55; }.asset-notes-label { margin:16px 0 5px; color:#64748b; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.04em; }.maintenance-section { padding:0; overflow:hidden; }.section-heading { display:flex; align-items:center; justify-content:space-between; gap:14px; padding:18px; border-bottom:1px solid #dbe3ea; }.section-heading h2 { margin:0 0 3px; }.section-heading p { margin:0; color:#64748b; font-size:.85rem; }.asset-log-table { font-size:.86rem; }.asset-log-table thead th { background:#f1f5f9; color:#334155; }.log-result { display:inline-block; padding:3px 7px; border-radius:3px; font-size:.75rem; font-weight:700; }.log-result-ok { background:#dcfce7; color:#166534; }.log-result-not_ok { background:#fee2e2; color:#991b1b; }.repair-history-section { margin-top:16px; padding:0; overflow:hidden; }.repair-history-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(260px,1fr)); gap:12px; padding:16px; }.repair-history-card { display:block;padding:14px;border:1px solid #dbe5ef;border-radius:5px;background:#fbfdff;color:#17324d;text-decoration:none;transition:border .15s,box-shadow .15s; }.repair-history-card:hover { color:#17324d;border-color:#0b5ea8;box-shadow:0 5px 14px rgba(11,94,168,.12); }.repair-history-top,.repair-history-footer { display:flex;justify-content:space-between;gap:8px;align-items:center; }.repair-history-time { display:block;margin-top:4px;color:#64748b;font-size:.75rem; }.repair-history-card p { display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;margin:10px 0;font-size:.84rem; }.repair-history-meta { display:flex;flex-wrap:wrap;gap:5px;margin-bottom:10px; }.repair-history-meta span { padding:3px 5px;background:#eef6ff;color:#0b5ea8;font-size:. সাতrem; }.repair-history-footer { color:#64748b;font-size:.72rem; }.repair-ticket-status { padding:3px 6px;border-radius:3px;font-size:. सातrem;font-weight:700; }.repair-status-open { background:#fef3c7;color:#92400e; }.repair-status-in_progress { background:#dbeafe;color:#1d4ed8; }.repair-status-resolved { background:#dcfce7;color:#166534; }.asset-empty { padding:25px !important; text-align:center; color:#64748b; }@media (max-width:767px) { .asset-hero { flex-direction:column; }.asset-hero-side { align-items:flex-start; }.asset-data-grid { grid-template-columns:1fr; }.section-heading { align-items:flex-start; flex-direction:column; }.asset-actions .btn { flex:1; }.repair-history-grid { grid-template-columns:1fr; } }
.repair-history-meta span { font-size: .7rem; }
.repair-ticket-status { font-size: .7rem; }
.transfer-history-section { margin-top:16px; padding:0; overflow:hidden; }.transfer-history-status { display:inline-block; padding:3px 7px; border-radius:3px; font-size:.72rem; font-weight:700; }.transfer-history-pending_approval { background:#fef3c7; color:#92400e; }.transfer-history-approved { background:#dbeafe; color:#1d4ed8; }.transfer-history-completed { background:#dcfce7; color:#166534; }.transfer-history-rejected { background:#fee2e2; color:#991b1b; }
.asset-owner{display:flex;align-items:center;gap:8px}.asset-owner img{width:38px;height:38px;flex:0 0 38px;border:1px solid #cbd5e1;border-radius:50%;object-fit:cover;background:#e0f2fe}.asset-owner strong,.asset-owner small{display:block}.asset-owner small{color:#64748b;font-size:.72rem;font-weight:400}
</style>

<style>
.asset-detail{max-width:1480px;font-size:.875rem;color:#18243d}
.asset-hero{position:relative;align-items:center;padding:26px 28px;background:linear-gradient(135deg,#2161f5,#3b82f6 60%,#60a5fa);border-radius:18px;box-shadow:0 14px 34px rgba(33,97,245,.22);overflow:hidden}
.asset-hero:before{content:'';position:absolute;right:-60px;top:-60px;width:220px;height:220px;border-radius:50%;background:rgba(255,255,255,.12)}
.asset-hero:after{content:'';position:absolute;left:-40px;bottom:-70px;width:180px;height:180px;border-radius:50%;background:rgba(255,255,255,.08)}
.asset-hero-main{position:relative;z-index:1;gap:18px}
.asset-icon{width:60px;height:60px;flex:0 0 60px;border-radius:16px;background:rgba(255,255,255,.16);border:1px solid rgba(255,255,255,.3);font-size:1rem}
.asset-photo{width:132px;height:96px;flex:0 0 132px;border-radius:14px;border:2px solid rgba(255,255,255,.55)}
.asset-eyebrow{opacity:.85;letter-spacing:.14em}
.asset-hero h1{font-size:1.65rem;font-weight:800;letter-spacing:-.02em}
.asset-meta{display:flex;flex-wrap:wrap;gap:8px;margin-top:6px}
.asset-meta span{display:inline-flex;align-items:center;gap:6px;padding:5px 11px;border-radius:999px;background:rgba(255,255,255,.16);font-size:.74rem;font-weight:600}
.asset-meta i{font-size:.78rem}
.asset-hero-side{position:relative;z-index:1;gap:10px}
.asset-tag{border:1px solid rgba(255,255,255,.45);border-radius:999px;background:rgba(255,255,255,.14);font-weight:700}
.asset-criticality{border-radius:999px;font-weight:700}
.asset-criticality-none{background:rgba(255,255,255,.18)}
.asset-actions{gap:9px;margin:18px 0 20px;flex-wrap:wrap}
.asset-actions .btn{display:inline-flex;align-items:center;gap:7px;border-radius:10px;padding:9px 15px;font-weight:700;font-size:.8rem;transition:transform .15s,box-shadow .15s}
.asset-actions .btn:hover{transform:translateY(-1px)}
.asset-actions .btn-brand{background:linear-gradient(135deg,#2161f5,#3b82f6);border:0;box-shadow:0 8px 16px rgba(33,97,245,.2)}
.asset-actions .btn-outline-secondary{border-color:#dfe5ee;color:#68758d}
.asset-actions .btn-outline-primary{border-color:#b8ccff;color:#2161f5}
.asset-actions .btn-outline-dark{border-color:#dfe5ee;color:#34415a}
.asset-actions .btn-outline-success{border-color:#bfe8d4;color:#1c9c68}
.asset-section{border:1px solid #e7ebf2;border-radius:16px;padding:22px;box-shadow:0 5px 18px rgba(35,52,85,.045)}
.asset-section h2{display:flex;align-items:center;gap:9px;font-size:.95rem;font-weight:800;color:#18243d}
.asset-section h2 i{display:flex;align-items:center;justify-content:center;width:30px;height:30px;flex:0 0 30px;border-radius:9px;background:#eef3ff;color:#2161f5;font-size:.86rem}
.asset-data-grid{gap:16px 22px}
.asset-data-grid dt{color:#94a0b2;font-weight:700;letter-spacing:.03em}
.asset-data-grid dd{color:#34415a;font-weight:600}
.asset-condition{border-radius:999px;background:#eef3ff;color:#2161f5}
.asset-notes{padding:14px 16px;border-radius:12px;background:#f9fafc;border:1px solid #eef1f5}
.asset-owner{gap:10px}
.asset-owner img{width:42px;height:42px;flex:0 0 42px;border:0;box-shadow:0 0 0 3px #eef3ff}
.maintenance-section,.transfer-history-section,.repair-history-section{margin-top:18px}
.section-heading{padding:20px 22px;border-bottom:1px solid #edf0f5}
.section-heading h2{display:flex;align-items:center;gap:9px;font-size:.95rem;font-weight:800;color:#18243d}
.section-heading h2 i{display:flex;align-items:center;justify-content:center;width:30px;height:30px;flex:0 0 30px;border-radius:9px;background:#eef3ff;color:#2161f5;font-size:.86rem}
.section-heading .btn{border-radius:9px;font-weight:700}
.asset-log-table{font-size:.78rem}
.asset-log-table thead th{padding:12px 16px;background:#f8fafc;color:#7d899e;font-size:.65rem;font-weight:800;letter-spacing:.04em;text-transform:uppercase;border-bottom:1px solid #e8edf4}
.asset-log-table tbody td{padding:13px 16px;border-color:#eef1f5;color:#536079}
.asset-log-table tbody tr:hover{background:#f8faff}
.log-result{border-radius:999px}
.transfer-history-status{border-radius:999px}
.repair-history-grid{padding:20px;gap:14px}
.repair-history-card{border:1px solid #e7ebf2;border-radius:14px;background:#fff;box-shadow:0 3px 12px rgba(35,52,85,.05)}
.repair-history-card:hover{border-color:#b8ccff;box-shadow:0 10px 22px rgba(33,97,245,.12);transform:translateY(-2px)}
.repair-ticket-status{border-radius:999px}
.repair-history-meta span{border-radius:999px}
.asset-empty{border-radius:12px}
@media(max-width:767px){.asset-hero{padding:20px}.asset-section{padding:16px}.section-heading{padding:16px}}
</style>
@endsection
