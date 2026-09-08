<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Download Semua Label</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jszip@3.10.1/dist/jszip.min.js"></script>
    <style>
        *{box-sizing:border-box}body{min-height:100vh;margin:0;padding:30px;background:radial-gradient(circle at 15% 10%,#e7ddff 0,transparent 32%),linear-gradient(135deg,#f5f7ff,#eefaf8);font:14px Arial,sans-serif;color:#17324d;display:grid;place-items:center}.progress-card{width:min(560px,100%);padding:34px 36px;border:1px solid rgba(255,255,255,.9);border-radius:24px;background:rgba(255,255,255,.86);box-shadow:0 24px 70px rgba(48,53,112,.16);text-align:center}.progress-icon{width:62px;height:62px;margin:0 auto 18px;border-radius:20px;background:linear-gradient(135deg,#7c5cfc,#25c7b5);color:#fff;display:grid;place-items:center;font-size:28px;box-shadow:0 12px 24px rgba(124,92,252,.25);animation:float 2.4s ease-in-out infinite}.progress-card h1{margin:0 0 8px;font-size:1.35rem;color:#18243d}.status{min-height:21px;margin:0 0 24px;color:#758198}.progress-track{height:14px;padding:3px;border-radius:99px;background:#e7eaf4;overflow:hidden}.progress-bar{height:100%;width:0;border-radius:99px;background:linear-gradient(90deg,#7c5cfc,#25c7b5);box-shadow:0 0 16px rgba(37,199,181,.42);transition:width .25s ease;position:relative;overflow:hidden}.progress-bar:after{content:'';position:absolute;inset:0;background:linear-gradient(110deg,transparent 25%,rgba(255,255,255,.65) 50%,transparent 75%);animation:shine 1.3s linear infinite}.progress-meta{display:flex;justify-content:space-between;margin-top:12px;color:#8792a7;font-size:.78rem;font-weight:700}.percent{color:#7c5cfc}.label{position:fixed;left:-10000px;top:0;width:720px;height:720px;padding:49px 46px 37px;border:8px solid #125ea8;border-radius:38px;background:#fff;text-align:center;overflow:hidden}.label:before{content:'';position:absolute;top:0;left:0;right:0;height:31px;background:#125ea8}.company{white-space:nowrap;font-size:32px;font-weight:800;line-height:1;color:#125ea8}.divider{width:260px;height:5px;margin:22px auto 18px;background:#f59e0b}.equipment-name{min-height:84px;font-size:46px;font-weight:800;line-height:1.05;overflow-wrap:anywhere}.asset-label{margin-top:12px;color:#64748b;font-size:28px;font-weight:700;letter-spacing:.1em;text-transform:uppercase}#qrcode{display:flex;justify-content:center;margin:18px auto 0;padding:16px;border:5px solid #d7e4e5;border-radius:18px;background:#fff;width:360px;height:360px;box-sizing:border-box}#qrcode img,#qrcode canvas{width:320px!important;height:320px!important}.scan-note{margin-top:13px;color:#64748b;font-size:24px;font-weight:700;letter-spacing:.05em;text-transform:uppercase}.error{color:#b42318}@keyframes shine{to{transform:translateX(220%)}}@keyframes float{50%{transform:translateY(-5px)}}@media(max-width:480px){body{padding:18px}.progress-card{padding:28px 22px}}
    </style>
</head>
<body>
    <main class="progress-card" aria-live="polite">
        <div class="progress-icon"><i class="bi bi-stars"></i></div>
        <h1>Menyiapkan Label Peralatan</h1>
        <div class="status" id="status">Menyiapkan ZIP label peralatan...</div>
        <div class="progress-track"><div class="progress-bar" id="progressBar"></div></div>
        <div class="progress-meta"><span id="progressCount">0 dari {{ count($labels) }} label</span><span class="percent" id="progressPercent">0%</span></div>
    </main>
    <div class="label" id="assetLabel"><div class="company">PT MULIA GRAND MANUFACTURE</div><div class="divider"></div><div class="equipment-name" id="equipmentName"></div><div class="asset-label">IT Asset</div><div id="qrcode"></div><div class="scan-note">Scan untuk informasi aset</div></div>
    <script>
        const labels = @json($labels);
        const status = document.getElementById('status');
        const progressBar = document.getElementById('progressBar');
        const progressCount = document.getElementById('progressCount');
        const progressPercent = document.getElementById('progressPercent');
        const labelElement = document.getElementById('assetLabel');
        const equipmentName = document.getElementById('equipmentName');
        const qrElement = document.getElementById('qrcode');

        const safeName = (value, fallback) => (value || fallback).toString().trim().replace(/[\\/:*?"<>|]+/g, '-').replace(/\s+/g, '-');
        const wait = (milliseconds) => new Promise(resolve => setTimeout(resolve, milliseconds));

        async function createZip() {
            const zip = new JSZip();
            const fileNames = new Set();
            for (let index = 0; index < labels.length; index++) {
                const item = labels[index];
                equipmentName.textContent = item.name;
                qrElement.replaceChildren();
                new QRCode(qrElement, { text: item.scanUrl, width: 320, height: 320, correctLevel: QRCode.CorrectLevel.M });
                await wait(150);
                const canvas = await html2canvas(labelElement, { backgroundColor: '#ffffff', scale: 2 });
                const baseName = safeName(item.assetTag || item.name, `peralatan-${item.id}`);
                let fileName = baseName;
                let suffix = 2;
                while (fileNames.has(fileName)) fileName = `${baseName}-${suffix++}`;
                fileNames.add(fileName);
                const imageData = canvas.toDataURL('image/jpeg', 0.95).split(',')[1];
                zip.file(`label-${fileName}.jpeg`, imageData, { base64: true });
                const progress = Math.round(((index + 1) / labels.length) * 100);
                progressBar.style.width = `${progress}%`;
                progressCount.textContent = `${index + 1} dari ${labels.length} label`;
                progressPercent.textContent = `${progress}%`;
                status.textContent = `Membuat label ${index + 1} dari ${labels.length}...`;
            }
            const blob = await zip.generateAsync({ type: 'blob' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'label-peralatan.zip';
            link.click();
            progressBar.style.width = '100%';
            progressPercent.textContent = '100%';
            status.textContent = 'Semua label berhasil diunduh.';
        }

        window.addEventListener('load', () => createZip().catch(() => { status.className = 'status error'; status.textContent = 'Label tidak dapat dibuat. Pastikan koneksi internet tersedia lalu coba lagi.'; }));
    </script>
</body>
</html>
