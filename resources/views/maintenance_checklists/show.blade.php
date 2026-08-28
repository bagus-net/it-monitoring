@extends('layouts.app')

@section('content')
@php $monthName = $monthNames[$maintenanceChecklist->month]; $program = $maintenanceChecklist->checklistItem; @endphp
<div class="container mt-4 checklist-detail" style="--program-color:{{ $program->schedule_color }};--program-tint:{{ $program->schedule_tint }}">
    <div class="checklist-hero">
        <div><div class="checklist-eyebrow">Dokumen Pelaksanaan Perawatan</div><h1>Checklist Perawatan IT</h1><p>{{ $program->title }} | {{ $monthName }} {{ $maintenanceChecklist->year }}</p></div>
        <div class="checklist-hero-actions"><button type="button" id="printChecklistButton" class="btn btn-light"><i class="bi bi-printer"></i> Cetak</button><a href="{{ route('maintenance-checklists.edit', $maintenanceChecklist) }}" class="btn btn-light">Edit</a><a href="{{ route('maintenance-checklists.index') }}" class="btn btn-outline-light">Kembali</a></div>
    </div>

    @if (auth()->user()->isMaster() && !$maintenanceChecklist->acknowledged_at)
        <form method="POST" action="{{ route('maintenance-checklists.approve', $maintenanceChecklist) }}" class="mt-3 d-flex align-items-end gap-2">
            @csrf
            <div>
                <label class="form-label mb-1">Tanggal &amp; Jam Approval</label>
                <input type="datetime-local" name="acknowledged_at" class="form-control" value="{{ now()->format('Y-m-d\TH:i') }}">
            </div>
            <button type="submit" class="btn btn-success"><i class="bi bi-check-circle"></i> Setujui Checklist</button>
        </form>
    @endif

    <div class="row g-3 my-1"><div class="col-md-4"><div class="checklist-stat"><span>Total Peralatan</span><strong>{{ $summary['total'] }}</strong></div></div><div class="col-md-4"><div class="checklist-stat checklist-stat-ok"><span>Kondisi OK</span><strong>{{ $summary['ok'] }}</strong></div></div><div class="col-md-4"><div class="checklist-stat checklist-stat-not-ok"><span>Perlu Tindak Lanjut</span><strong>{{ $summary['not_ok'] }}</strong></div></div></div>

    <div class="card checklist-document mt-3">
        <div class="card-body">
			<div class="document-meta"><div><span>Program Perawatan</span><strong>{{ $program->title }}</strong></div><div><span>Periode</span><strong>{{ $monthName }} {{ $maintenanceChecklist->year }}</strong></div><div><span>Tanggal Input Checklist</span><strong>{{ $maintenanceChecklist->checked_at?->format('d M Y') }}</strong></div><div><span>Dilaporkan Oleh</span><strong>{{ $maintenanceChecklist->reported_by ?? '-' }}</strong></div><div><span>Mengetahui</span><strong>{{ $maintenanceChecklist->acknowledged_at ? $maintenanceChecklist->acknowledged_by : 'Menunggu persetujuan' }}</strong></div></div>
			<div class="table-responsive"><table class="table table-bordered checklist-table"><thead><tr><th>No.</th><th>Nama Peralatan</th><th>Tanggal Jadwal</th><th>Check Point</th><th class="text-center">Kondisi</th><th>Keterangan</th></tr></thead><tbody>@foreach ($maintenanceChecklist->entries->sortBy(fn ($entry) => $entry->equipment->name) as $index => $entry)<tr><td>{{ $index + 1 }}</td><td><strong>{{ $entry->equipment->name }}</strong><small>{{ $entry->equipment->asset_tag ?? $entry->equipment->serial_number }}</small></td><td>{{ count($scheduledDatesByEquipment->get($entry->equipment_id, [])) ? implode(', ', $scheduledDatesByEquipment->get($entry->equipment_id)) : '-' }}</td><td>{{ $program->title }}</td><td class="text-center"><span class="result-badge result-{{ $entry->result }}">{{ $entry->result === 'ok' ? 'OK' : 'NOT OK' }}</span></td><td>{{ $entry->remarks ?? '-' }}</td></tr>@endforeach</tbody></table></div>
            @if ($maintenanceChecklist->notes)<div class="document-notes"><span>Catatan Dokumen</span><p>{{ $maintenanceChecklist->notes }}</p></div>@endif
        </div>
    </div>
</div>

<div id="checklistPrintSheet" hidden>
	<div class="sheet">
		<header class="sheet-head">
			<div class="sheet-brand">
				<img class="sheet-logo" src="{{ asset('images/logo-mgm.svg') }}" alt="Logo PT Mulia Grand Manufacture">
				<div>
					<strong>PT MULIA GRAND MANUFACTURE</strong>
					<small>Checklist Perawatan IT</small>
					<span class="sheet-form-no">No. Form : FR-IT-03</span>
					<span class="sheet-form-no">Revisi : 00</span>
				</div>
			</div>
			<div class="sheet-id">
				<span class="sheet-number">{{ $program->title }}</span>
				<div class="sheet-chips">
					<span class="chip">{{ $monthName }} {{ $maintenanceChecklist->year }}</span>
					@if($maintenanceChecklist->acknowledged_at)<span class="chip chip-approved">Approved</span>@endif
				</div>
			</div>
		</header>

		<section class="sheet-strip">
			<div><span>Program Perawatan</span><strong>{{ $program->title }}</strong></div>
			<div><span>Periode</span><strong>{{ $monthName }} {{ $maintenanceChecklist->year }}</strong></div>
			<div><span>Tanggal Input Checklist</span><strong>{{ $maintenanceChecklist->checked_at?->format('d M Y') ?: '-' }}</strong></div>
			<div><span>Total Peralatan</span><strong>{{ $summary['total'] }}</strong></div>
			<div><span>Hasil</span><strong>OK {{ $summary['ok'] }} / NOT OK {{ $summary['not_ok'] }}</strong></div>
		</section>

		<table class="sheet-table">
			<thead><tr><th>No.</th><th>Nama Peralatan</th><th>Tanggal Jadwal</th><th>Check Point</th><th class="text-center">Kondisi</th><th>Keterangan</th></tr></thead>
			<tbody>
				@foreach ($maintenanceChecklist->entries->sortBy(fn ($entry) => $entry->equipment->name) as $index => $entry)
					<tr>
						<td>{{ $index + 1 }}</td>
						<td>{{ $entry->equipment->name }}</td>
						<td>{{ count($scheduledDatesByEquipment->get($entry->equipment_id, [])) ? implode(', ', $scheduledDatesByEquipment->get($entry->equipment_id)) : '-' }}</td>
						<td>{{ $program->title }}</td>
						<td class="text-center">{{ $entry->result === 'ok' ? 'OK' : 'NOT OK' }}</td>
						<td>{{ $entry->remarks ?: '-' }}</td>
					</tr>
				@endforeach
			</tbody>
		</table>

		@if ($maintenanceChecklist->notes)
			<div class="note">
				<span>Catatan Dokumen</span>
				<p>{{ $maintenanceChecklist->notes }}</p>
			</div>
		@endif

		<footer class="sheet-sign">
			<div>
				<span>Dibuat Oleh</span>
				<i>@if($signatures['reporter']?->signature_path)<img src="{{ asset('storage/' . $signatures['reporter']->signature_path) }}" alt="Tanda tangan pelapor">@endif</i>
				<strong>{{ $signatureNames['reporter'] ?: '(...........................)' }}</strong>
				<em>{{ $signatures['reporter']?->signature_title ?: 'Admin IT / Bagus' }}</em>
			</div>
			<div>
				<span>Mengetahui</span>
				<i>@if($signatures['acknowledger']?->signature_path)<img src="{{ asset('storage/' . $signatures['acknowledger']->signature_path) }}" alt="Tanda tangan approval">@endif</i>
				<strong>{{ $signatureNames['acknowledger'] ?: '(...........................)' }}</strong>
				<em>{{ $signatures['acknowledger']?->signature_title ?: 'Arifin' }}</em>
			</div>
			<div class="sheet-print-time">No. Form : FR-IT-03 &nbsp;|&nbsp; Revisi : 00 &nbsp;|&nbsp; Dicetak: {{ now()->translatedFormat('d F Y H:i') }} WIB</div>
		</footer>
	</div>
</div>
<script type="text/css" id="checklistPrintCss">
	@page{size:A4 portrait;margin:9mm}
	*{box-sizing:border-box}
	body{margin:0;font-family:'Segoe UI',Arial,Helvetica,sans-serif;color:#17324d;background:#fff;font-size:11px}
	.sheet{border:1px solid #cbd5e1;border-radius:6px;overflow:hidden}
	.sheet-head{display:flex;justify-content:space-between;align-items:center;gap:14px;padding:12px 16px;background:linear-gradient(120deg,#0b5ea8,#1f8fe0);color:#fff}
	.sheet-brand{display:flex;align-items:center;gap:11px}
	.sheet-logo{width:44px;height:44px;object-fit:contain;background:#fff;border-radius:8px;padding:2px}
	.sheet-form-no{display:inline-block;margin-top:3px;padding:1px 7px;border-radius:9px;background:rgba(255,255,255,.22);font-size:9px;font-weight:700;letter-spacing:.04em}
	.sheet-form-no + .sheet-form-no{display:block;width:fit-content}
	.sheet-brand strong{display:block;font-size:14px;letter-spacing:.04em}
	.sheet-brand small{display:block;font-size:10px;opacity:.9}
	.sheet-id{text-align:right}
	.sheet-number{display:block;font-size:16px;font-weight:800;letter-spacing:.02em}
	.sheet-chips{display:flex;gap:5px;justify-content:flex-end;margin-top:4px}
	.chip{padding:3px 8px;border-radius:11px;font-size:9.5px;font-weight:700;background:#fff;color:#0b5ea8}
	.chip-approved{background:#dcfce7;color:#166534}
	.sheet-strip{display:grid;grid-template-columns:repeat(5,1fr);gap:1px;background:#dbe5ef;border-bottom:1px solid #dbe5ef}
	.sheet-strip div{padding:7px 10px;background:#f8fafc}
	.sheet-strip span{display:block;font-size:8.5px;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:#64748b}
	.sheet-strip strong{display:block;margin-top:2px;font-size:10.5px}
	.sheet-table{width:100%;border-collapse:collapse;margin:0}
	.sheet-table th,.sheet-table td{border:1px solid #dbe5ef;padding:6px 8px;font-size:10.5px}
	.sheet-table th{background:#fff3e6;text-align:left}
	.note{margin:10px 16px;padding:8px 10px;background:#f8fafc;border-left:3px solid #0b5ea8;border-radius:0 4px 4px 0}
	.note span{display:block;font-size:8.5px;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:#64748b}
	.note p{margin:3px 0 0;white-space:pre-line;font-size:10.5px}
	.sheet-sign{display:grid;grid-template-columns:repeat(2,1fr);gap:16px;padding:14px 16px 10px;border-top:1px solid #dbe5ef;background:#fbfdff;position:relative}
	.sheet-sign span{display:block;font-size:9px;font-weight:700;text-transform:uppercase;color:#64748b}
	.sheet-sign i{display:flex;align-items:flex-end;justify-content:center;height:44px}
	.sheet-sign i img{max-height:44px;max-width:150px;object-fit:contain}
	.sheet-sign strong{display:block;padding-top:3px;border-top:1px solid #94a3b8;font-size:10.5px}
	.sheet-sign em{display:block;font-style:normal;font-size:9px;color:#64748b}
	.sheet-print-time{position:absolute;right:16px;bottom:4px;font-size:8.5px;color:#94a3b8}
</script>
<script>
	document.getElementById('printChecklistButton')?.addEventListener('click', () => {
		const sheet = document.getElementById('checklistPrintSheet');
		const css = document.getElementById('checklistPrintCss').textContent;
		const frame = document.createElement('iframe');
		frame.style.cssText = 'position:fixed;right:0;bottom:0;width:0;height:0;border:0';
		document.body.appendChild(frame);
		const doc = frame.contentWindow.document;
		doc.open();
		doc.write('<html><head><meta charset="utf-8"><title>{{ $program->title }} {{ $monthName }} {{ $maintenanceChecklist->year }}</title><style>' + css + '</style></head><body>' + sheet.innerHTML + '</body></html>');
		doc.close();
		const images = Array.from(doc.images);
		const start = () => { frame.contentWindow.focus(); frame.contentWindow.print(); setTimeout(() => frame.remove(), 1500); };
		if (!images.length) return start();
		let loaded = 0;
		images.forEach(image => {
			const done = () => { if (++loaded === images.length) start(); };
			image.complete ? done() : (image.onload = image.onerror = done);
		});
	});
</script>
<style>.checklist-hero { display:flex; justify-content:space-between; align-items:flex-start; gap:20px; padding:24px; border-radius:8px; background:linear-gradient(120deg,var(--program-color),#0f766e); color:#fff; }.checklist-eyebrow { font-size:.72rem; text-transform:uppercase; letter-spacing:.08em; font-weight:700; opacity:.8; }.checklist-hero h1 { margin:4px 0; font-size:1.55rem; }.checklist-hero p { margin:0; opacity:.92; }.checklist-hero-actions { display:flex; gap:8px; }.checklist-stat { padding:14px 16px; background:#fff; border:1px solid #dbe3ea; border-top:4px solid var(--program-color); }.checklist-stat span { display:block; color:#64748b; font-size:.78rem; }.checklist-stat strong { font-size:1.6rem; }.checklist-stat-ok { border-top-color:#15803d; }.checklist-stat-not-ok { border-top-color:#dc2626; }.checklist-document { border:1px solid #dbe3ea; }.document-meta { display:grid; grid-template-columns:repeat(5,minmax(0,1fr)); gap:12px; padding-bottom:18px; }.document-meta div { min-width:0; }.document-meta span,.document-notes span { display:block; color:#64748b; font-size:.72rem; text-transform:uppercase; letter-spacing:.04em; font-weight:700; }.document-meta strong { display:block; margin-top:4px; font-size:.88rem; overflow-wrap:anywhere; }.checklist-table th { background:var(--program-tint); color:#334155; font-size:.8rem; }.checklist-table td { vertical-align:middle; }.checklist-table small { display:block; color:#64748b; font-weight:400; }.result-badge { display:inline-block; padding:4px 8px; border-radius:3px; font-size:.75rem; font-weight:700; }.result-ok { background:#dcfce7; color:#166534; }.result-not_ok { background:#fee2e2; color:#991b1b; }.document-notes { margin-top:18px; padding:12px; background:#f8fafc; border-left:3px solid var(--program-color); }.document-notes p { margin:5px 0 0; white-space:pre-line; }@media (max-width:767px) { .checklist-hero { flex-direction:column; }.document-meta { grid-template-columns:1fr 1fr; }.checklist-hero-actions { width:100%; }.checklist-hero-actions .btn { flex:1; } }</style>
@endsection
