@extends('layouts.app')

@section('content')
@php $statusLabels=['open'=>'Open','in_progress'=>'Proses','resolved'=>'Selesai']; $priorityLabels=['low'=>'Rendah','normal'=>'Normal','high'=>'Tinggi','urgent'=>'Mendesak']; @endphp
<div class="container mt-4 ticket-detail"><div class="ticket-hero"><div><div class="ticket-kicker">IT Repair Ticket</div><h1>{{ $itRepairTicket->ticket_number }}</h1><p>{{ $itRepairTicket->equipment->name ?? 'Peralatan belum dipilih' }} | {{ $itRepairTicket->department ?? 'Bagian belum dicatat' }}</p></div><div><span class="ticket-status status-{{ $itRepairTicket->status }}">{{ $statusLabels[$itRepairTicket->status] }}</span><span class="priority priority-{{ $itRepairTicket->priority }}">{{ $priorityLabels[$itRepairTicket->priority] }}</span></div></div><div class="ticket-actions">@if(auth()->user()->isMaster() && $itRepairTicket->status === 'resolved' && !$itRepairTicket->approved_at)<form class="d-inline" method="POST" action="{{ route('it-repair-tickets.approve', $itRepairTicket) }}" onsubmit="return confirm('Setujui tiket ini? Tanda tangan Anda akan dipakai pada dokumen cetak.')">@csrf<button class="btn btn-success">Approve Tiket</button></form>@endif<button type="button" id="printTicketButton" class="btn btn-outline-primary">Cetak Tiket</button>@if(!auth()->user()->isEmployee())<a href="{{ route('it-repair-tickets.repair', $itRepairTicket) }}" class="btn btn-brand">Form Perbaikan IT</a>@endif<a href="{{ route('it-repair-tickets.index') }}" class="btn btn-outline-secondary">Kembali</a>@if(!auth()->user()->isEmployee())<form class="d-inline" method="POST" action="{{ route('it-repair-tickets.destroy', $itRepairTicket) }}" onsubmit="return confirm('Hapus tiket ini?')">@csrf @method('DELETE')<button class="btn btn-outline-danger">Hapus</button></form>@endif</div>
@if($itRepairTicket->approved_at)<div class="container"><div class="ticket-approved">Disetujui oleh <strong>{{ $itRepairTicket->approver->name ?? '-' }}</strong> pada {{ $itRepairTicket->approved_at->translatedFormat('d F Y H:i') }} WIB</div></div>@endif<div class="row g-3"><div class="col-lg-6"><section class="ticket-section h-100"><h2>Informasi Permintaan</h2><dl class="ticket-grid"><div><dt>Tanggal Lapor</dt><dd>{{ $itRepairTicket->reported_at?->format('d M Y H:i') }}</dd></div><div><dt>Dilaporkan Oleh</dt><dd>{{ $itRepairTicket->reported_by ?? '-' }}</dd></div><div><dt>Bagian</dt><dd>{{ $itRepairTicket->department ?? '-' }}</dd></div><div><dt>Peralatan IT</dt><dd>{{ $itRepairTicket->equipment->name ?? '-' }}</dd></div></dl><div class="ticket-block"><span>Keluhan / Kerusakan</span><p>{{ $itRepairTicket->problem_description }}</p></div></section></div><div class="col-lg-6"><section class="ticket-section h-100"><h2>Pelaksanaan Perbaikan</h2><dl class="ticket-grid"><div><dt>Diperbaiki Oleh</dt><dd>{{ $itRepairTicket->assigned_to ?? '-' }}</dd></div><div><dt>Status</dt><dd>{{ $statusLabels[$itRepairTicket->status] }}</dd></div><div><dt>Mulai Perbaikan</dt><dd>{{ $itRepairTicket->started_at?->format('d M Y H:i') ?? '-' }}</dd></div><div><dt>Selesai Perbaikan</dt><dd>{{ $itRepairTicket->resolved_at?->format('d M Y H:i') ?? '-' }}</dd></div></dl><div class="ticket-block"><span>Tindakan Perbaikan</span><p>{{ $itRepairTicket->repair_action ?? 'Belum ada tindakan yang dicatat.' }}</p></div></section></div></div>@if($itRepairTicket->notes)<section class="ticket-section mt-3"><h2>Catatan Tambahan</h2><p class="mb-0">{{ $itRepairTicket->notes }}</p></section>@endif</div>
<div class="container ticket-classification"><section class="ticket-section"><h2>Klasifikasi Gangguan</h2><div class="ticket-grid"><div><dt>Kategori Perbaikan</dt><dd>{{ $itRepairTicket->repair_category === 'software' ? 'Software / Aplikasi' : 'Hardware' }}</dd></div><div><dt>{{ $itRepairTicket->repair_category === 'software' ? 'Aplikasi / Software' : 'Jenis Peralatan' }}</dt><dd>{{ ($itRepairTicket->repair_category === 'software' ? $itRepairTicket->software_name : $itRepairTicket->equipment_category) ?? '-' }}</dd></div><div><dt>Jenis Error</dt><dd>{{ $itRepairTicket->error_type ?? '-' }}</dd></div></div></section></div>
@if ($itRepairTicket->error_photo_path)
<div class="container ticket-attachment"><section class="ticket-section"><h2>Lampiran Foto Error</h2><a href="{{ asset('storage/' . $itRepairTicket->error_photo_path) }}" target="_blank"><img src="{{ asset('storage/' . $itRepairTicket->error_photo_path) }}" alt="Lampiran error {{ $itRepairTicket->ticket_number }}" class="error-photo"></a></section></div>
@endif
@if ($itRepairTicket->repair_attachment_path)
<div class="container ticket-attachment"><section class="ticket-section"><h2>Lampiran Hasil Perbaikan</h2><a href="{{ asset('storage/' . $itRepairTicket->repair_attachment_path) }}" target="_blank"><img src="{{ asset('storage/' . $itRepairTicket->repair_attachment_path) }}" alt="Lampiran hasil perbaikan {{ $itRepairTicket->ticket_number }}" class="error-photo"></a></section></div>
@endif
<div id="ticketPrintSheet" hidden>
	<div class="sheet">
		<header class="sheet-head">
			<div class="sheet-brand">
				<img class="sheet-logo" src="{{ asset('images/logo-mgm.svg') }}" alt="Logo PT Mulia Grand Manufacture">
				<div>
					<strong>PT MULIA GRAND MANUFACTURE</strong>
					<small>Formulir Tiket Perbaikan IT</small>
					<span class="sheet-form-no">No. Form : FR-IT-04</span>
				</div>
			</div>
			<div class="sheet-id">
				<span class="sheet-number">{{ $itRepairTicket->ticket_number }}</span>
				<div class="sheet-chips">
					<span class="chip chip-status-{{ $itRepairTicket->status }}">{{ $statusLabels[$itRepairTicket->status] }}</span>
					<span class="chip chip-priority-{{ $itRepairTicket->priority }}">Prioritas {{ $priorityLabels[$itRepairTicket->priority] }}</span>
					<span class="chip chip-kind">{{ $itRepairTicket->repair_category === 'software' ? 'Software' : 'Hardware' }}</span>
					@if($itRepairTicket->approved_at)<span class="chip chip-approved">Approved</span>@endif
				</div>
			</div>
		</header>

		<section class="sheet-strip">
			<div><span>Tanggal Lapor</span><strong>{{ $itRepairTicket->reported_at?->format('d M Y H:i') ?: '-' }}</strong></div>
			<div><span>Pelapor</span><strong>{{ $itRepairTicket->reported_by ?: '-' }}</strong></div>
			<div><span>Bagian / Departemen</span><strong>{{ $itRepairTicket->department ?: '-' }}</strong></div>
			<div><span>Teknisi</span><strong>{{ $itRepairTicket->assigned_to ?: '-' }}</strong></div>
			<div><span>Selesai</span><strong>{{ $itRepairTicket->resolved_at?->format('d M Y H:i') ?: '-' }}</strong></div>
		</section>

		<div class="sheet-columns">
			<section class="panel panel-user">
				<h3>Informasi User / Pelapor</h3>
				<dl>
					<dt>Peralatan IT</dt><dd>{{ $itRepairTicket->equipment->name ?? '-' }}</dd>
					<dt>PIC Peralatan</dt><dd>{{ $itRepairTicket->equipment?->owner_name ?: '-' }}</dd>
					<dt>Lokasi</dt><dd>{{ $itRepairTicket->equipment?->assetLocation?->name ?: '-' }}</dd>
					<dt>Kategori Perbaikan</dt><dd>{{ $itRepairTicket->repair_category === 'software' ? 'Software / Aplikasi' : 'Hardware' }}</dd>
					<dt>{{ $itRepairTicket->repair_category === 'software' ? 'Aplikasi / Software' : 'Jenis Peralatan' }}</dt><dd>{{ ($itRepairTicket->repair_category === 'software' ? $itRepairTicket->software_name : $itRepairTicket->equipment_category) ?: '-' }}</dd>
					<dt>Jenis Error</dt><dd>{{ $itRepairTicket->error_type ?: '-' }}</dd>
				</dl>
				<div class="note note-problem">
					<span>Keluhan / Kerusakan</span>
					<p>{{ $itRepairTicket->problem_description }}</p>
				</div>
				@if ($itRepairTicket->error_photo_path)
					<div class="shot"><span>Foto Error</span><img src="{{ asset('storage/' . $itRepairTicket->error_photo_path) }}" alt="Foto error"></div>
				@endif
			</section>

			<section class="panel panel-repair">
				<h3>Informasi Perbaikan IT</h3>
				<dl>
					<dt>Status Penanganan</dt><dd>{{ $statusLabels[$itRepairTicket->status] }}</dd>
					<dt>Mulai Perbaikan</dt><dd>{{ $itRepairTicket->started_at?->format('d M Y H:i') ?: '-' }}</dd>
					<dt>Selesai Perbaikan</dt><dd>{{ $itRepairTicket->resolved_at?->format('d M Y H:i') ?: '-' }}</dd>
					<dt>Durasi</dt><dd>{{ $itRepairTicket->started_at && $itRepairTicket->resolved_at ? $itRepairTicket->started_at->diffForHumans($itRepairTicket->resolved_at, true) : '-' }}</dd>
				</dl>
				<div class="note note-action">
					<span>Tindakan Perbaikan</span>
					<p>{{ $itRepairTicket->repair_action ?: 'Belum ada tindakan yang dicatat.' }}</p>
				</div>
				<div class="note note-extra">
					<span>Catatan Tambahan</span>
					<p>{{ $itRepairTicket->notes ?: '-' }}</p>
				</div>
				@if ($itRepairTicket->repair_attachment_path)
					<div class="shot"><span>Foto Hasil Perbaikan</span><img src="{{ asset('storage/' . $itRepairTicket->repair_attachment_path) }}" alt="Foto hasil perbaikan"></div>
				@endif
			</section>
		</div>

		<footer class="sheet-sign">
			<div>
				<span>Pelapor</span>
				<i>@if($signatures['reporter'])<img src="{{ asset('storage/' . $signatures['reporter']->signature_path) }}" alt="Tanda tangan pelapor">@endif</i>
				<strong>{{ $itRepairTicket->reported_by ?: '(...........................)' }}</strong>
				<em>{{ $signatures['reporter']?->signature_title ?: $itRepairTicket->department }}</em>
			</div>
			<div>
				<span>Teknisi IT</span>
				<i>@if($signatures['technician'])<img src="{{ asset('storage/' . $signatures['technician']->signature_path) }}" alt="Tanda tangan teknisi">@endif</i>
				<strong>{{ $itRepairTicket->assigned_to ?: '(...........................)' }}</strong>
				<em>{{ $signatures['technician']?->signature_title ?: 'IT Support' }}</em>
			</div>
			<div>
				<span>Mengetahui / Approval</span>
				<i>@if($signatures['approver']?->signature_path)<img src="{{ asset('storage/' . $signatures['approver']->signature_path) }}" alt="Tanda tangan approval">@endif</i>
				<strong>{{ $signatures['approver']->name ?? '(...........................)' }}</strong>
				<em>{{ $signatures['approver']?->signature_title ?: ($itRepairTicket->approved_at ? 'Disetujui ' . $itRepairTicket->approved_at->format('d M Y H:i') : 'Menunggu persetujuan') }}</em>
			</div>
			<div class="sheet-print-time">No. Form : FR-IT-04 &nbsp;|&nbsp; Dicetak: {{ now()->translatedFormat('d F Y H:i') }} WIB</div>
		</footer>
	</div>
</div>
<script type="text/css" id="ticketPrintCss">
	@page{size:A4 landscape;margin:9mm}
	*{box-sizing:border-box}
	body{margin:0;font-family:'Segoe UI',Arial,Helvetica,sans-serif;color:#17324d;background:#fff;font-size:11px}
	.sheet{border:1px solid #cbd5e1;border-radius:6px;overflow:hidden}
	.sheet-head{display:flex;justify-content:space-between;align-items:center;gap:14px;padding:12px 16px;background:linear-gradient(120deg,#0b5ea8,#1f8fe0);color:#fff}
	.sheet-brand{display:flex;align-items:center;gap:11px}
	.sheet-mark{display:flex;align-items:center;justify-content:center;width:38px;height:38px;border-radius:8px;background:#f6b322;color:#17324d;font-weight:800;font-size:14px}
	.sheet-logo{width:44px;height:44px;object-fit:contain;background:#fff;border-radius:8px;padding:2px}
	.sheet-form-no{display:inline-block;margin-top:3px;padding:1px 7px;border-radius:9px;background:rgba(255,255,255,.22);font-size:9px;font-weight:700;letter-spacing:.04em}
	.sheet-brand strong{display:block;font-size:14px;letter-spacing:.04em}
	.sheet-brand small{display:block;font-size:10px;opacity:.9}
	.sheet-id{text-align:right}
	.sheet-number{display:block;font-size:19px;font-weight:800;letter-spacing:.02em}
	.sheet-chips{display:flex;gap:5px;justify-content:flex-end;margin-top:4px}
	.chip{padding:3px 8px;border-radius:11px;font-size:9.5px;font-weight:700;background:#fff;color:#0b5ea8}
	.chip-status-open{background:#fef3c7;color:#92400e}
	.chip-status-in_progress{background:#dbeafe;color:#1d4ed8}
	.chip-status-resolved{background:#dcfce7;color:#166534}
	.chip-priority-urgent{background:#fee2e2;color:#991b1b}
	.chip-priority-high{background:#ffedd5;color:#9a3412}
	.chip-priority-normal{background:#e0f2fe;color:#075985}
	.chip-priority-low{background:#e2e8f0;color:#475569}
	.chip-kind{background:#ede9fe;color:#5b21b6}
	.chip-approved{background:#dcfce7;color:#166534}
	.sheet-strip{display:grid;grid-template-columns:repeat(5,1fr);gap:1px;background:#dbe5ef;border-bottom:1px solid #dbe5ef}
	.sheet-strip div{padding:7px 12px;background:#f8fafc}
	.sheet-strip span{display:block;font-size:8.5px;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:#64748b}
	.sheet-strip strong{display:block;margin-top:2px;font-size:11px}
	.sheet-columns{display:grid;grid-template-columns:1fr 1fr;gap:0}
	.panel{padding:12px 16px}
	.panel-user{border-right:1px solid #dbe5ef}
	.panel h3{margin:0 0 9px;padding-bottom:5px;font-size:11.5px;color:#0b5ea8;border-bottom:2px solid #f6b322;text-transform:uppercase;letter-spacing:.05em}
	.panel dl{display:grid;grid-template-columns:118px 1fr;gap:3px 10px;margin:0 0 9px}
	.panel dt{color:#64748b;font-weight:700;font-size:9.5px;text-transform:uppercase}
	.panel dd{margin:0;font-size:11px}
	.note{margin-bottom:8px;padding:8px 10px;background:#f8fafc;border-left:3px solid #0b5ea8;border-radius:0 4px 4px 0}
	.note-problem{border-left-color:#dc2626}
	.note-action{border-left-color:#159957}
	.note-extra{border-left-color:#94a3b8}
	.note span{display:block;font-size:8.5px;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:#64748b}
	.note p{margin:3px 0 0;white-space:pre-line;font-size:10.5px}
	.shot span{display:block;font-size:8.5px;font-weight:700;text-transform:uppercase;color:#64748b;margin-bottom:3px}
	.shot img{width:100%;max-height:150px;object-fit:contain;border:1px solid #dbe5ef;border-radius:4px;background:#f8fafc}
	.sheet-sign{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;padding:12px 16px 10px;border-top:1px solid #dbe5ef;background:#fbfdff;position:relative}
	.sheet-sign span{display:block;font-size:9px;font-weight:700;text-transform:uppercase;color:#64748b}
	.sheet-sign i{display:flex;align-items:flex-end;justify-content:center;height:44px}
	.sheet-sign i img{max-height:44px;max-width:150px;object-fit:contain}
	.sheet-sign strong{display:block;padding-top:3px;border-top:1px solid #94a3b8;font-size:10.5px}
	.sheet-sign em{display:block;font-style:normal;font-size:9px;color:#64748b}
	.sheet-print-time{position:absolute;right:16px;bottom:4px;font-size:8.5px;color:#94a3b8}
</script>
<script>
	document.getElementById('printTicketButton')?.addEventListener('click', () => {
		const sheet = document.getElementById('ticketPrintSheet');
		const css = document.getElementById('ticketPrintCss').textContent;
		const frame = document.createElement('iframe');
		frame.style.cssText = 'position:fixed;right:0;bottom:0;width:0;height:0;border:0';
		document.body.appendChild(frame);
		const doc = frame.contentWindow.document;
		doc.open();
		doc.write('<html><head><meta charset="utf-8"><title>{{ $itRepairTicket->ticket_number }}</title><style>' + css + '</style></head><body>' + sheet.innerHTML + '</body></html>');
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
<script>
	const ticketSections = Array.from(document.querySelectorAll('.ticket-detail .ticket-section'));
	const userSection = ticketSections.find(section => section.querySelector('h2')?.textContent.includes('Informasi Permintaan'));
	const repairSection = ticketSections.find(section => section.querySelector('h2')?.textContent.includes('Pelaksanaan Perbaikan'));
	const userColumn = userSection?.closest('.col-lg-6');
	const repairColumn = repairSection?.closest('.col-lg-6');
	if (userSection) userSection.querySelector('h2').textContent = 'Informasi User / Pelapor';
	if (repairSection) repairSection.querySelector('h2').textContent = 'Informasi Perbaikan IT';
	const classification = document.querySelector('.ticket-classification');
	if (classification && userColumn) {
		classification.classList.remove('container');
		userColumn.appendChild(classification);
	}
	document.querySelectorAll('.ticket-attachment').forEach(attachment => {
		attachment.classList.remove('container');
		const heading = attachment.querySelector('h2')?.textContent || '';
		if (heading.includes('Foto Error') && userColumn) userColumn.appendChild(attachment);
		if (heading.includes('Hasil Perbaikan') && repairColumn) repairColumn.appendChild(attachment);
	});
</script>
<style>.ticket-hero{display:flex;justify-content:space-between;gap:20px;padding:22px;background:linear-gradient(120deg,#0b5ea8,#1675c1);color:#fff;border-radius:7px}.ticket-kicker{font-size:.7rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;opacity:.8}.ticket-hero h1{margin:3px 0;font-size:1.55rem}.ticket-hero p{margin:0;opacity:.9}.ticket-actions{display:flex;justify-content:flex-end;gap:8px;margin:14px 0}.ticket-approved{margin-bottom:12px;padding:9px 13px;background:#dcfce7;border-left:4px solid #159957;color:#166534;font-size:.85rem}.ticket-section{padding:16px;background:#fff;border:1px solid #dbe5ef;border-radius:6px}.ticket-detail .row > .col-lg-6 > .ticket-section.h-100{height:auto!important}.ticket-section h2{margin:0 0 12px;font-size:1rem;color:#0b5ea8}.ticket-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin:0}.ticket-grid dt,.ticket-block span{display:block;color:#64748b;font-size:.72rem;font-weight:700;text-transform:uppercase}.ticket-grid dd{margin:4px 0 0}.ticket-block{margin-top:14px;padding:10px;background:#f8fafc;border-left:3px solid #f6b322}.ticket-block p{margin:5px 0 0;white-space:pre-line}.ticket-classification,.ticket-attachment{width:auto;max-width:none;padding:0;margin-top:12px}.ticket-classification .ticket-section,.ticket-attachment .ticket-section{height:auto}.error-photo{display:block;width:100%;max-width:100%;max-height:220px;object-fit:contain;border:1px solid #dbe5ef;border-radius:5px;background:#f8fafc}.priority,.ticket-status{display:inline-block;padding:4px 7px;border-radius:3px;font-size:.74rem;font-weight:700}.priority-low{background:#e2e8f0;color:#475569}.priority-normal{background:#dbeafe;color:#1d4ed8}.priority-high{background:#ffedd5;color:#9a3412}.priority-urgent{background:#fee2e2;color:#991b1b}.status-open{background:#fef3c7;color:#92400e}.status-in_progress{background:#dbeafe;color:#1d4ed8}.status-resolved{background:#dcfce7;color:#166534}@media(max-width:767px){.ticket-hero{flex-direction:column}.ticket-grid{grid-template-columns:1fr}.ticket-actions{flex-wrap:wrap}}</style>
@endsection
