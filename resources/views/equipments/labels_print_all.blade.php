<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Cetak Semua Label Peralatan</title>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <style>
        @page{size:A4 portrait;margin:6mm}*{box-sizing:border-box}html,body{margin:0;background:#52545a;color:#17324d;font-family:Arial,Helvetica,sans-serif}.print-actions{position:fixed;z-index:5;top:12px;right:12px;display:flex;gap:6px}.print-actions button{padding:8px 12px;border:1px solid #cbd5e1;border-radius:5px;background:#fff;color:#17324d;font-weight:700}.label-page{width:198mm;height:285mm;margin:6mm auto;padding:1.6mm;display:grid;grid-template-columns:repeat(4,minmax(0,1fr));grid-template-rows:repeat(10,minmax(0,1fr));gap:.45mm;background:#fff;border:1px solid #1e293b;page-break-after:always}.label-page:last-child{page-break-after:auto}.label-cell{position:relative;display:flex;min-width:0;min-height:0;flex-direction:column;align-items:center;justify-content:flex-start;gap:.4mm;padding:2.5mm 1.2mm .8mm;border:.4mm solid #125ea8;border-radius:2mm;background:#fff;text-align:center;overflow:hidden}.label-cell:before{position:absolute;top:0;left:0;right:0;height:2.4mm;background:#125ea8;content:''}.label-cell .company{width:100%;margin-top:.4mm;overflow:hidden;color:#125ea8;font-size:5.3pt;font-weight:800;line-height:1;white-space:nowrap;text-overflow:clip}.label-cell .divider{width:18mm;height:.4mm;margin:.4mm auto .5mm;background:#f59e0b}.label-cell .name{width:100%;overflow:hidden;color:#17324d;font-size:7.2pt;font-weight:800;line-height:1.05;white-space:nowrap;text-overflow:ellipsis}.label-cell .asset-label{color:#64748b;font-size:5pt;font-weight:700;letter-spacing:.07em;line-height:1;text-transform:uppercase}.label-cell .qr{width:17.5mm;height:17.5mm;margin-top:.5mm;padding:.75mm;border:.3mm solid #d7e4e5;border-radius:1.1mm;background:#fff}.label-cell .qr img,.label-cell .qr canvas{display:block;width:100%!important;height:100%!important}.label-cell .scan-note{width:100%;margin-top:.35mm;overflow:hidden;color:#64748b;font-size:4pt;font-weight:700;line-height:1;text-overflow:clip;white-space:nowrap;text-transform:uppercase}.empty-cell{border-color:#fff;background:#fff}@media print{html,body{background:#fff}.print-actions{display:none!important}.label-page{width:198mm;height:285mm;margin:0;border:0}.label-cell{print-color-adjust:exact;-webkit-print-color-adjust:exact}}
    </style>
    <style>
        .label-page{grid-template-columns:repeat(4,minmax(0,1fr));grid-template-rows:repeat(6,minmax(0,1fr));gap:.8mm;padding:2mm}.label-cell{justify-content:center;gap:.7mm;padding:3.4mm 1.8mm 1.8mm;border:.45mm solid #125ea8;border-radius:2.8mm}.label-cell:before{height:3mm}.label-cell .company{margin-top:.5mm;font-size:5.9pt}.label-cell .divider{width:20mm;height:.45mm;margin:.45mm auto .65mm}.label-cell .name{font-size:8.4pt}.label-cell .asset-label{font-size:5.8pt}.label-cell .qr{width:22mm;height:22mm;margin-top:.7mm;padding:.9mm;border:.35mm solid #d7e4e5;border-radius:1.4mm}.label-cell .scan-note{margin-top:.5mm;font-size:4.7pt}@media print{.label-page{grid-template-columns:repeat(4,minmax(0,1fr));grid-template-rows:repeat(6,minmax(0,1fr));gap:.8mm;padding:2mm}}
    </style>
</head>
<body>
    <div class="print-actions"><button type="button" onclick="window.print()">Print</button><button type="button" onclick="window.close()">Kembali</button></div>
    <div id="pages"></div>
    <script>
        const labels = @json($labels);
        const pages = document.getElementById('pages');
        const perPage = 24;
        const chunks = Array.from({ length: Math.ceil(labels.length / perPage) }, (_, page) => labels.slice(page * perPage, (page + 1) * perPage));
        const wait = ms => new Promise(resolve => setTimeout(resolve, ms));
        chunks.forEach((chunk, pageIndex) => {
            const page = document.createElement('section');
            page.className = 'label-page';
            chunk.forEach((label, index) => {
                const cell = document.createElement('div');
                cell.className = 'label-cell';
                cell.innerHTML = `<div class="company">PT MULIA GRAND MANUFACTURE</div><div class="divider"></div><strong class="name">${label.name}</strong><span class="asset-label">IT Asset</span><div class="qr"></div><span class="scan-note">Scan untuk informasi aset</span>`;
                page.appendChild(cell);
                new QRCode(cell.querySelector('.qr'), { text: label.scanUrl, width: 160, height: 160, correctLevel: QRCode.CorrectLevel.M });
            });
            for (let index = chunk.length; index < perPage; index++) {
                const empty = document.createElement('div');
                empty.className = 'label-cell empty-cell';
                page.appendChild(empty);
            }
            pages.appendChild(page);
        });
        window.addEventListener('load', async () => { await wait(500); window.print(); });
    </script>
</body>
</html>
