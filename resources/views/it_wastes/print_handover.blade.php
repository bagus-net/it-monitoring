<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Berita Acara Serah Terima Limbah IT - {{ $boxCode }}</title>
    <style>
        @page { size: A4 portrait; margin: 16mm; }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, Helvetica, sans-serif; color: #0f172a; font-size: 11px; }
        .actions { max-width: 760px; margin: 16px auto; text-align: right; }
        button { padding: 8px 14px; color: #fff; background: #1d4ed8; border: 0; border-radius: 4px; font: inherit; cursor: pointer; }
        .sheet { max-width: 760px; margin: 0 auto; }
        .header { text-align: center; padding-bottom: 12px; border-bottom: 2px solid #0f172a; }
        .header h1 { margin: 0; font-size: 16px; }
        .header h2 { margin: 5px 0 0; font-size: 13px; }
        .meta { margin: 18px 0; line-height: 1.7; }
        .meta strong { display: inline-block; min-width: 150px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 6px; border: 1px solid #334155; vertical-align: top; }
        th { background: #eaf1f8; text-align: left; }
        .signatures { display: grid; grid-template-columns: 1fr 1fr; gap: 80px; margin-top: 44px; text-align: center; }
        .signature-space { height: 70px; border-bottom: 1px solid #334155; }
        @media print { .actions { display: none; } }
    </style>
</head>
<body>
    <div class="actions"><button type="button" onclick="window.print()">Cetak</button></div>
    <main class="sheet">
        <header class="header"><h1>BERITA ACARA SERAH TERIMA LIMBAH IT</h1><h2>PT MULIA GRAND MANUFACTURE</h2></header>
        <section class="meta"><div><strong>Kode Box</strong>: {{ $boxCode }}</div><div><strong>Tanggal Serah Terima</strong>: {{ $handoverDate?->format('d F Y') ?? '-' }}</div><div><strong>Penerima</strong>: {{ $recipient }}</div></section>
        <p>Dengan ini limbah IT berikut telah diserahkan kepada Bagian Limbah B3 untuk ditangani sesuai prosedur yang berlaku.</p>
        <table><thead><tr><th>No.</th><th>Tanggal Limbah</th><th>Jenis Limbah</th><th>Deskripsi</th><th>Jumlah</th><th>Sumber Peralatan</th></tr></thead><tbody>@foreach ($wastes as $index => $waste)<tr><td>{{ $index + 1 }}</td><td>{{ $waste->waste_date->format('d M Y') }}</td><td>{{ $waste->waste_type }}</td><td>{{ $waste->description }}</td><td>{{ rtrim(rtrim(number_format($waste->quantity, 2, ',', '.'), '0'), ',') }} {{ $waste->unit }}</td><td>{{ $waste->equipment->name ?? '-' }}</td></tr>@endforeach</tbody></table>
        <div class="signatures"><div><strong>Diserahkan oleh,</strong><div class="signature-space"></div><span>Petugas IT</span></div><div><strong>Diterima oleh,</strong><div class="signature-space"></div><span>{{ $recipient }}</span></div></div>
    </main>
    <script>window.addEventListener('load', () => window.print());</script>
</body>
</html>
