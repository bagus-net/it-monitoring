<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Unduh Label {{ $equipment->name }}</title>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <style>
        body{margin:0;padding:24px;background:#eef3f4;font-family:Arial,Helvetica,sans-serif;color:#17324d}.download-status{max-width:360px;margin:0 auto 16px;text-align:center;font-size:14px}.label{position:relative;width:720px;height:720px;margin:auto;padding:49px 46px 37px;border:8px solid #125ea8;border-radius:38px;background:#fff;text-align:center;overflow:hidden}.label:before{content:'';position:absolute;top:0;left:0;right:0;height:31px;background:#125ea8}.company{white-space:nowrap;font-size:32px;font-weight:800;line-height:1;color:#125ea8}.divider{width:260px;height:5px;margin:22px auto 18px;background:#f59e0b}.equipment-name{min-height:84px;font-size:46px;font-weight:800;line-height:1.05;overflow-wrap:anywhere}.asset-label{margin-top:12px;color:#64748b;font-size:28px;font-weight:700;letter-spacing:.1em;text-transform:uppercase}#qrcode{display:flex;justify-content:center;margin:18px auto 0;padding:16px;border:5px solid #d7e4e5;border-radius:18px;background:#fff;width:360px;height:360px}#qrcode img,#qrcode canvas{width:320px!important;height:320px!important}.scan-note{margin-top:13px;color:#64748b;font-size:24px;font-weight:700;letter-spacing:.05em;text-transform:uppercase}@media(max-width:800px){body{padding:12px;overflow:auto}.label{transform-origin:top left;transform:scale(.45);margin-bottom:-396px}}
    </style>
</head>
<body>
    <div class="download-status" id="downloadStatus">Menyiapkan label JPEG...</div>
    <div class="label" id="assetLabel">
        <div class="company">PT MULIA GRAND MANUFACTURE</div>
        <div class="divider"></div>
        <div class="equipment-name">{{ $equipment->name }}</div>
        <div class="asset-label">IT Asset</div>
        <div id="qrcode"></div>
        <div class="scan-note">Scan untuk informasi aset</div>
    </div>
    <script>
        new QRCode(document.getElementById('qrcode'), { text: @json($scanUrl), width: 320, height: 320, correctLevel: QRCode.CorrectLevel.M });
        window.addEventListener('load', () => setTimeout(() => {
            html2canvas(document.getElementById('assetLabel'), { backgroundColor: '#ffffff', scale: 2 }).then(canvas => {
                const link = document.createElement('a');
                link.download = @json('label-' . ($equipment->asset_tag ?: $equipment->id) . '.jpeg');
                link.href = canvas.toDataURL('image/jpeg', 0.95);
                link.click();
                document.getElementById('downloadStatus').textContent = 'Label JPEG berhasil diunduh.';
            }).catch(() => { document.getElementById('downloadStatus').textContent = 'Label tidak dapat dibuat. Silakan coba lagi.'; });
        }, 300));
    </script>
</body>
</html>
