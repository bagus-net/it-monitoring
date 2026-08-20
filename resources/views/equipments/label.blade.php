<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Label QR Peralatan</title>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <style>
        @page { size: 100mm 100mm; margin: 0; }
        html, body { width: 100mm; height: 100mm; margin: 0; }
        * { box-sizing: border-box; }
        body { display: grid; place-items: center; background: #eef3f4; }
        .label { position: relative; width: 94mm; height: 94mm; padding: 6.4mm 6mm 4.8mm; border: 1.1mm solid #125ea8; border-radius: 5mm; background: #fff; text-align: center; font-family: Arial, Helvetica, sans-serif; color: #17324d; overflow: hidden; }
        .label::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4mm; background: #125ea8; }
        .company { white-space: nowrap; font-size: 12.4pt; font-weight: 800; letter-spacing: 0; line-height: 1; color: #125ea8; }
        .divider { width: 34mm; height: .6mm; margin: 2.4mm auto 2mm; background: #f59e0b; }
        .equipment-name { min-height: 11mm; font-size: 17pt; font-weight: 800; line-height: 1.05; overflow-wrap: anywhere; }
        .asset-label { margin-top: 1.6mm; color: #64748b; font-size: 11pt; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; }
        #qrcode { display: flex; justify-content: center; margin: 2.4mm auto 0; padding: 2mm; border: .6mm solid #d7e4e5; border-radius: 2.4mm; background: #fff; }
        #qrcode img, #qrcode canvas { display: block; width: 43mm; height: 43mm; }
        .scan-note { margin-top: 1.8mm; color: #64748b; font-size: 10.4pt; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; }
        @media screen { body { margin: 20px; } .label { box-shadow: 0 5px 20px rgba(18,94,168,.2); } }
    </style>
</head>
<body>
    <div class="label">
        <div class="company">PT MULIA GRAND MANUFACTURE</div>
        <div class="divider"></div>
        <div class="equipment-name">{{ $equipment->name }}</div>
        <div class="asset-label">IT Asset</div>
        <div id="qrcode" aria-label="QR code informasi peralatan"></div>
        <div class="scan-note">Scan untuk informasi aset</div>
    </div>
    <script>
        new QRCode(document.getElementById('qrcode'), {
            text: @json($scanUrl),
            width: 320,
            height: 320,
            correctLevel: QRCode.CorrectLevel.M
        });
        window.addEventListener('load', () => window.print());
    </script>
</body>
</html>
