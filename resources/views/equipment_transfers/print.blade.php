<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Berita Acara Mutasi - {{ $transfer->equipment->name }}</title>
    <style>
        @page { size: A4; margin: 16mm; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #17324d; font-family: Arial, Helvetica, sans-serif; font-size: 11pt; }
        .document { max-width: 180mm; margin: 0 auto; }
        .letterhead { display: flex; justify-content: space-between; align-items: flex-start; padding-bottom: 8mm; border-bottom: 2px solid #125ea8; }
        .brand { color: #125ea8; font-size: 15pt; font-weight: 800; letter-spacing: .04em; }
        .brand-subtitle { margin-top: 2mm; color: #64748b; font-size: 8.5pt; }
        .document-label { color: #125ea8; font-size: 8pt; font-weight: 700; letter-spacing: .1em; text-align: right; text-transform: uppercase; }
        h1 { margin: 13mm 0 3mm; color: #17324d; font-size: 19pt; text-align: center; letter-spacing: .03em; }
        .intro { margin: 0 auto 9mm; max-width: 145mm; color: #475569; line-height: 1.6; text-align: center; }
        .asset-banner { display: flex; justify-content: space-between; gap: 12mm; margin-bottom: 7mm; padding: 5mm; border: 1px solid #b9d4ed; border-left: 4px solid #125ea8; background: #f3f8fd; }
        .asset-banner strong { display: block; color: #125ea8; font-size: 14pt; }
        .asset-code { color: #64748b; font-size: 9pt; }
        .status { align-self: center; padding: 2mm 3mm; border-radius: 2mm; background: #dbeafe; color: #1d4ed8; font-size: 8pt; font-weight: 700; text-transform: uppercase; }
        .status-completed { background: #dcfce7; color: #166534; }
        .status-rejected { background: #fee2e2; color: #991b1b; }
        .status-pending_approval { background: #fef3c7; color: #92400e; }
        .party-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 5mm; }
        .party { min-height: 42mm; padding: 5mm; border: 1px solid #dbe5ef; border-radius: 2mm; }
        .party-title { margin-bottom: 5mm; padding-bottom: 2mm; border-bottom: 2px solid #f59e0b; color: #125ea8; font-size: 9pt; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; }
        dl { margin: 0; } dt { margin-top: 3mm; color: #64748b; font-size: 8pt; font-weight: 700; text-transform: uppercase; } dd { margin: 1mm 0 0; line-height: 1.4; }
        .detail-box { margin-top: 7mm; padding: 5mm; border: 1px solid #dbe5ef; background: #fbfdff; } .detail-row { display: grid; grid-template-columns: 40mm 1fr; gap: 5mm; padding: 2.5mm 0; border-bottom: 1px solid #e5edf4; } .detail-row:last-child { border-bottom: 0; } .detail-label { color: #64748b; font-size: 8.5pt; font-weight: 700; }
        .signature-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15mm; margin-top: 18mm; text-align: center; } .signature { min-height: 38mm; } .signature-image { display: block; width: auto; height: 20mm; max-width: 55mm; margin: 4mm auto 1mm; object-fit: contain; } .signature-missing { height: 20mm; margin: 4mm auto 1mm; color: #94a3b8; font-size: 8pt; font-style: italic; } .signature-line { padding-top: 2mm; border-top: 1px solid #17324d; font-weight: 700; }
        .print-actions { margin-bottom: 8mm; text-align: right; } button { padding: 8px 14px; border: 0; background: #125ea8; color: #fff; cursor: pointer; font-weight: 700; } @media print { .print-actions { display: none; } }
    </style>
</head>
<body>
    <div class="document">
        <div class="print-actions"><button type="button" onclick="window.print()">Cetak Dokumen</button></div>
        <header class="letterhead"><div><div class="brand">PT MULIA GRAND MANUFACTURE</div><div class="brand-subtitle">IT Monitoring &amp; Maintenance System</div></div><div class="document-label">Berita Acara<br>Mutasi Peralatan</div></header>
        <h1>BERITA ACARA MUTASI PERALATAN IT</h1>
        <p class="intro">Dokumen ini mencatat perpindahan penggunaan peralatan IT dari pemegang sebelumnya kepada pemegang baru.</p>
        <section class="asset-banner"><div><strong>{{ $transfer->equipment->name }}</strong><span class="asset-code">Kode aset: {{ $transfer->equipment->asset_tag ?: '-' }} | Serial: {{ $transfer->equipment->serial_number ?: '-' }}</span></div><span class="status status-{{ $transfer->status }}">{{ $statuses[$transfer->status] }}</span></section>
        <section class="party-grid"><div class="party"><div class="party-title">Pemegang Sebelumnya</div><dl><dt>Nama PIC</dt><dd>{{ $transfer->from_owner_name ?: '-' }}</dd><dt>Departemen</dt><dd>{{ $transfer->from_department ?: '-' }}</dd><dt>Lokasi</dt><dd>{{ $transfer->fromLocation->name ?? '-' }}</dd></dl></div><div class="party"><div class="party-title">Pemegang Baru</div><dl><dt>Nama PIC</dt><dd>{{ $transfer->to_owner_name ?: '-' }}</dd><dt>Departemen</dt><dd>{{ $transfer->to_department ?: '-' }}</dd><dt>Lokasi</dt><dd>{{ $transfer->toLocation->name ?? 'Tidak diubah' }}</dd></dl></div></section>
        <section class="detail-box"><div class="detail-row"><span class="detail-label">Tanggal Efektif</span><span>{{ $transfer->effective_date?->format('d F Y') ?? '-' }}</span></div><div class="detail-row"><span class="detail-label">Alasan Mutasi</span><span>{{ $transfer->reason ?: '-' }}</span></div><div class="detail-row"><span class="detail-label">Catatan</span><span>{{ $transfer->notes ?: '-' }}</span></div><div class="detail-row"><span class="detail-label">Dibuat Oleh</span><span>{{ $transfer->requester->name ?? '-' }}</span></div></section>
        <section class="signature-grid"><div class="signature"><div>Menyerahkan</div>@if($transfer->fromUser?->signature_path)<img class="signature-image" src="{{ asset('storage/' . $transfer->fromUser->signature_path) }}" alt="Tanda tangan {{ $transfer->from_owner_name ?: $transfer->fromUser->name }}">@else<div class="signature-missing">Tanda tangan belum tersedia</div>@endif<div class="signature-line">{{ $transfer->from_owner_name ?: ($transfer->fromUser->name ?? 'PIC Sebelumnya') }}</div></div><div class="signature"><div>Menerima</div>@if($transfer->toUser?->signature_path)<img class="signature-image" src="{{ asset('storage/' . $transfer->toUser->signature_path) }}" alt="Tanda tangan {{ $transfer->to_owner_name ?: $transfer->toUser->name }}">@else<div class="signature-missing">Tanda tangan belum tersedia</div>@endif<div class="signature-line">{{ $transfer->to_owner_name ?: ($transfer->toUser->name ?? 'PIC Baru') }}</div></div></section>
    </div>
    <script>window.addEventListener('load', () => window.print());</script>
</body>
</html>
