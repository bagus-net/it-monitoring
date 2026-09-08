<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Unduh Semua Detail User</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jszip@3.10.1/dist/jszip.min.js"></script>
    <style>
        *{box-sizing:border-box}body{min-height:100vh;margin:0;padding:30px;background:#f4f7fb;font:14px Arial,sans-serif;color:#18243d;display:grid;place-items:center}.progress-card{width:min(560px,100%);padding:34px 36px;border:1px solid #e5eaf1;border-radius:22px;background:#fff;box-shadow:0 20px 55px rgba(35,52,85,.12);text-align:center}.progress-icon{width:62px;height:62px;margin:0 auto 18px;border-radius:18px;background:#eef3ff;color:#2161f5;display:grid;place-items:center;font-size:28px;box-shadow:0 10px 22px rgba(33,97,245,.16);animation:float 2.4s ease-in-out infinite}.progress-card h1{margin:0 0 8px;font-size:1.35rem}.status{min-height:21px;margin:0 0 24px;color:#758198}.progress-track{height:14px;padding:3px;border-radius:99px;background:#e7eaf4;overflow:hidden}.progress-bar{height:100%;width:0;border-radius:99px;background:#2161f5;box-shadow:0 0 16px rgba(33,97,245,.35);transition:width .25s ease;position:relative;overflow:hidden}.progress-bar:after{content:'';position:absolute;inset:0;background:linear-gradient(110deg,transparent 25%,rgba(255,255,255,.65) 50%,transparent 75%);animation:shine 1.3s linear infinite}.progress-meta{display:flex;justify-content:space-between;margin-top:12px;color:#8792a7;font-size:.78rem;font-weight:700}.percent{color:#2161f5}.user-sheet{position:fixed;left:-10000px;top:0;width:1100px;padding:28px;background:#f8fafc;color:#18243d}.sheet-grid{display:grid;grid-template-columns:340px minmax(0,1fr);gap:20px}.sheet-profile,.sheet-assets{border:1px solid #e5eaf1;border-radius:18px;background:#fff;box-shadow:0 8px 22px rgba(30,48,78,.06)}.sheet-profile{display:flex;min-height:520px;flex-direction:column;align-items:center;justify-content:space-between;padding:30px 22px;text-align:center}.sheet-profile img{width:150px;height:150px;border:6px solid #f1f5f9;border-radius:50%;object-fit:cover}.sheet-kicker{color:#8792a7;font-size:11px;font-weight:800;letter-spacing:.14em}.sheet-profile h2{margin:12px 0 5px;font-size:25px}.sheet-profile p{margin:0;color:#8792a7;font-size:13px}.sheet-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;width:100%;margin-top:26px}.sheet-stat{min-height:74px;padding:11px 9px;border-left:3px solid #2161f5;border-radius:9px;background:#f8fafc;text-align:left}.sheet-stat:nth-child(2){border-left-color:#e879b8}.sheet-stat:nth-child(3){border-left-color:#14b8a6}.sheet-stat span{display:block;color:#8792a7;font-size:9px;line-height:1.2}.sheet-stat strong{display:block;margin-top:8px;font-size:12px}.sheet-assets{overflow:hidden}.sheet-assets-head{display:flex;justify-content:space-between;align-items:center;padding:20px;border-bottom:1px solid #edf0f5}.sheet-assets-head h3{margin:5px 0 0;font-size:17px}.sheet-count{padding:5px 9px;border-radius:99px;background:#eef3ff;color:#2161f5;font-size:11px;font-weight:700}.sheet-assets table{width:100%;border-collapse:collapse;font-size:11px}.sheet-assets th{padding:12px 10px;background:#f8fafc;color:#8792a7;font-size:9px;text-align:left;text-transform:uppercase}.sheet-assets td{padding:12px 10px;border-top:1px solid #eef1f5}.sheet-assets td:first-child{font-weight:700}.sheet-empty{padding:35px!important;color:#8792a7;text-align:center!important}
        @keyframes shine{to{transform:translateX(220%)}}@keyframes float{50%{transform:translateY(-5px)}}@media(max-width:480px){body{padding:18px}.progress-card{padding:28px 22px}}
    </style>
</head>
<body>
    <div class="download-overlay" id="downloadOverlay">
        <main class="progress-card" id="progressCard" aria-live="polite">
            <div class="progress-icon"><i class="bi bi-people"></i></div>
            <h1>Menyiapkan Detail User</h1>
            <div class="status" id="status">Menyiapkan ZIP detail user...</div>
            <div class="progress-track"><div class="progress-bar" id="progressBar"></div></div>
            <div class="progress-meta"><span id="progressCount">0 dari {{ count($details) }} user</span><span class="percent" id="progressPercent">0%</span></div>
        </main>
        <section class="success-card" id="successCard" hidden>
            <div class="success-icon"><i class="bi bi-check2"></i></div>
            <h1>Download Berhasil</h1>
            <p>Semua detail user sudah berhasil dikemas dan diunduh dalam format ZIP.</p>
            <button type="button" class="success-button" id="returnUsersButton"><i class="bi bi-check-lg"></i> OK</button>
        </section>
    </div>
    <div class="user-sheet" id="userSheet"></div>
    <style>
        .download-overlay{position:fixed;z-index:10;inset:0;display:grid;place-items:center;padding:24px;background:rgba(15,23,42,.42);backdrop-filter:blur(5px)}.success-card{width:min(440px,100%);padding:34px 36px;border:1px solid #e5eaf1;border-radius:22px;background:#fff;box-shadow:0 24px 70px rgba(15,23,42,.25);text-align:center;animation:success-in .35s ease-out}.success-icon{width:66px;height:66px;margin:0 auto 16px;border-radius:20px;background:#dcfce7;color:#16a34a;display:grid;place-items:center;font-size:31px}.success-card h1{margin:0 0 8px;font-size:1.35rem;color:#18243d}.success-card p{margin:0 auto 23px;max-width:320px;color:#758198;font-size:.82rem;line-height:1.55}.success-button{border:0;border-radius:9px;padding:10px 30px;background:#2161f5;color:#fff;font-weight:800;box-shadow:0 8px 18px rgba(33,97,245,.22)}.success-button i{margin-right:5px}@keyframes success-in{from{opacity:0;transform:translateY(12px) scale(.96)}to{opacity:1;transform:translateY(0) scale(1)}}
    </style>
    <script>
        const details = @json($details);
        const status = document.getElementById('status');
        const progressBar = document.getElementById('progressBar');
        const progressCount = document.getElementById('progressCount');
        const progressPercent = document.getElementById('progressPercent');
        const userSheet = document.getElementById('userSheet');
        const progressCard = document.getElementById('progressCard');
        const successCard = document.getElementById('successCard');
        const wait = milliseconds => new Promise(resolve => setTimeout(resolve, milliseconds));
        const safeName = (value, fallback) => (value || fallback).toString().trim().replace(/[\\/:*?"<>|]+/g, '-').replace(/\s+/g, '-');
        const escapeHtml = value => String(value ?? '-').replace(/[&<>"']/g, character => ({ '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#039;' }[character]));

        function renderUser(user) {
            const rows = user.equipments.length
                ? user.equipments.map((equipment, index) => `<tr><td>${index + 1}</td><td>${escapeHtml(equipment.name)}</td><td>${escapeHtml(equipment.assetTag)}</td><td>${escapeHtml(equipment.type)}</td><td>${escapeHtml(equipment.manufacturer)}</td><td>${escapeHtml(equipment.location)}</td><td>${escapeHtml(equipment.condition)}</td></tr>`).join('')
                : '<tr><td colspan="7" class="sheet-empty">Belum ada peralatan IT yang ditugaskan.</td></tr>';
            userSheet.innerHTML = `<div class="sheet-grid"><section class="sheet-profile"><div><img src="${escapeHtml(user.photoUrl)}" alt="Foto profil"><div class="sheet-kicker">PROFIL USER</div><h2>${escapeHtml(user.name)}</h2><p>${escapeHtml(user.email)}</p><div class="sheet-stats"><div class="sheet-stat"><span>Hak Akses</span><strong>${escapeHtml(user.role)}</strong></div><div class="sheet-stat"><span>Departemen</span><strong>${escapeHtml(user.department)}</strong></div><div class="sheet-stat"><span>Status</span><strong>${escapeHtml(user.status)}</strong></div></div></div></section><section class="sheet-assets"><div class="sheet-assets-head"><div><div class="sheet-kicker">ASSET ASSIGNMENT</div><h3>Peralatan IT yang Dipegang</h3></div><span class="sheet-count">${user.equipments.length} unit</span></div><table><thead><tr><th>No.</th><th>Peralatan</th><th>Kode Aset</th><th>Tipe</th><th>Manufacturer</th><th>Lokasi</th><th>Kondisi</th></tr></thead><tbody>${rows}</tbody></table></section></div>`;
        }

        async function createZip() {
            const zip = new JSZip();
            const fileNames = new Set();
            for (let index = 0; index < details.length; index++) {
                const user = details[index];
                renderUser(user);
                await wait(100);
                const canvas = await html2canvas(userSheet, { backgroundColor: '#f8fafc', scale: 2, useCORS: true });
                const baseName = safeName(user.name, `user-${user.id}`);
                let fileName = baseName;
                let suffix = 2;
                while (fileNames.has(fileName)) fileName = `${baseName}-${suffix++}`;
                fileNames.add(fileName);
                zip.file(`detail-user-${fileName}.jpg`, canvas.toDataURL('image/jpeg', .95).split(',')[1], { base64: true });
                const progress = Math.round(((index + 1) / details.length) * 100);
                progressBar.style.width = `${progress}%`;
                progressCount.textContent = `${index + 1} dari ${details.length} user`;
                progressPercent.textContent = `${progress}%`;
                status.textContent = `Membuat detail ${index + 1} dari ${details.length}...`;
            }
            const blob = await zip.generateAsync({ type: 'blob' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'detail-semua-user.zip';
            link.click();
            status.textContent = 'Semua detail user berhasil diunduh.';
            progressCard.hidden = true;
            successCard.hidden = false;
        }

        document.getElementById('returnUsersButton').addEventListener('click', () => { window.location.href = @json(route('users.index')); });
        window.addEventListener('load', () => createZip().catch(() => { status.textContent = 'Detail user tidak dapat dibuat. Silakan coba lagi.'; }));
    </script>
</body>
</html>
