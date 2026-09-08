@extends('layouts.app')

@section('content')
<div class="container mt-4 backup-page">
    <div class="backup-hero">
        <div>
            <div class="backup-eyebrow"><i class="bi bi-shield-lock"></i> Pengaturan Master</div>
            <h1>Backup Data</h1>
            <p>Unduh salinan seluruh data aplikasi dan file upload untuk kebutuhan pemulihan.</p>
        </div>
        <i class="bi bi-database-down backup-hero-icon"></i>
    </div>

    <div class="row g-3 mt-1">
        <div class="col-md-6"><div class="backup-stat"><i class="bi bi-table"></i><div><strong>{{ $tableCount }}</strong><span>Tabel database</span></div></div></div>
        <div class="col-md-6"><div class="backup-stat"><i class="bi bi-files"></i><div><strong>{{ $fileCount }}</strong><span>File upload</span></div></div></div>
    </div>

    <section class="backup-card mt-3">
        <div class="backup-card-icon"><i class="bi bi-file-earmark-zip"></i></div>
        <div class="backup-card-body">
            <h2>Backup lengkap</h2>
            <p>File ZIP berisi <code>database.sql</code> untuk pemulihan MySQL, <code>database.json</code> seluruh tabel, dan folder <code>storage</code> yang memuat foto aset, tanda tangan digital, lampiran tiket, serta dokumen upload.</p>
            <div class="backup-notice"><i class="bi bi-info-circle"></i><span>Backup dibuat saat tombol ditekan. File <code>database.sql</code> dapat diimpor melalui MySQL/phpMyAdmin. File <code>.env</code> dan kredensial aplikasi tidak ikut disertakan.</span></div>
            <form method="POST" action="{{ route('settings.backup.download') }}" class="mt-3" id="backupDownloadForm">
                @csrf
                <button type="submit" class="btn btn-brand" id="backupDownloadButton"><i class="bi bi-download"></i><span>Unduh Backup Semua Data</span></button>
            </form>
            <div class="backup-progress" id="backupProgress" aria-live="polite" hidden>
                <div class="backup-progress-heading"><span><i class="bi bi-cloud-arrow-down"></i> Backup sedang disiapkan</span><strong><i class="bi bi-hourglass-split"></i> Mohon tunggu</strong></div>
                <div class="backup-progress-track"><div class="backup-progress-bar"></div></div>
                <div class="backup-progress-note">Database dan file upload sedang dikemas ke dalam ZIP.</div>
            </div>
        </div>
    </section>
</div>
<style>
.backup-page{max-width:980px;color:#18243d}.backup-hero{display:flex;align-items:center;justify-content:space-between;padding:25px 28px;border-radius:16px;background:linear-gradient(135deg,#172554,#1d4ed8);color:#fff;box-shadow:0 12px 28px rgba(29,78,216,.18)}.backup-eyebrow{font-size:.68rem;font-weight:800;letter-spacing:.12em;text-transform:uppercase;color:#bfdbfe}.backup-hero h1{margin:7px 0 4px;font-size:1.65rem;font-weight:800}.backup-hero p{margin:0;color:#dbeafe;font-size:.8rem}.backup-hero-icon{font-size:3.1rem;color:#93c5fd;opacity:.85}.backup-stat{display:flex;align-items:center;gap:14px;padding:17px 20px;border:1px solid #e7ebf2;border-radius:14px;background:#fff;box-shadow:0 5px 18px rgba(35,52,85,.045)}.backup-stat>i{display:flex;align-items:center;justify-content:center;width:38px;height:38px;border-radius:11px;background:#eef3ff;color:#2161f5;font-size:1.1rem}.backup-stat strong,.backup-stat span{display:block}.backup-stat strong{font-size:1.35rem}.backup-stat span{color:#8792a7;font-size:.72rem}.backup-card{display:flex;gap:18px;padding:24px;border:1px solid #e7ebf2;border-radius:16px;background:#fff;box-shadow:0 5px 18px rgba(35,52,85,.045)}.backup-card-icon{display:flex;align-items:center;justify-content:center;width:48px;height:48px;flex:0 0 48px;border-radius:13px;background:#e0f2fe;color:#0369a1;font-size:1.35rem}.backup-card-body{min-width:0}.backup-card h2{margin:1px 0 6px;font-size:1rem;font-weight:800}.backup-card p{margin:0;color:#64748b;font-size:.8rem;line-height:1.6}.backup-notice{display:flex;gap:8px;align-items:flex-start;margin-top:14px;padding:10px 12px;border-radius:9px;background:#f8fafc;color:#64748b;font-size:.72rem}.backup-notice i{color:#2161f5;margin-top:2px}.backup-page code{color:#1d4ed8}.backup-page .btn-brand{border:0;border-radius:9px;background:linear-gradient(135deg,#2161f5,#3b82f6);box-shadow:0 8px 16px rgba(33,97,245,.18);font-weight:700}.backup-page .btn-brand i{margin-right:7px}@media(max-width:600px){.backup-hero{padding:20px}.backup-card{padding:18px}.backup-hero-icon{font-size:2.2rem}}
</style>
<style>
.backup-progress{margin-top:16px;padding:14px 16px;border:1px solid #dbeafe;border-radius:12px;background:linear-gradient(135deg,#eff6ff,#f0fdfa);animation:backup-reveal .25s ease-out}.backup-progress-heading{display:flex;justify-content:space-between;gap:12px;color:#1d4ed8;font-size:.76rem;font-weight:800}.backup-progress-heading strong{color:#64748b;font-weight:700}.backup-progress-heading i{margin-right:5px}.backup-progress-track{height:8px;margin-top:12px;border-radius:99px;background:#dbeafe;overflow:hidden}.backup-progress-bar{width:42%;height:100%;border-radius:99px;background:linear-gradient(90deg,#2563eb,#14b8a6);box-shadow:0 0 14px rgba(37,99,235,.3);animation:backup-slide 1.25s ease-in-out infinite}.backup-progress-note{margin-top:9px;color:#64748b;font-size:.7rem}@keyframes backup-slide{0%{transform:translateX(-115%)}50%{transform:translateX(125%)}100%{transform:translateX(260%)}}@keyframes backup-reveal{from{opacity:0;transform:translateY(-5px)}to{opacity:1;transform:translateY(0)}}.backup-page .btn-brand:disabled{cursor:wait;opacity:.8}.backup-page .btn-brand .spin{animation:backup-spin .9s linear infinite}@keyframes backup-spin{to{transform:rotate(360deg)}}@media(max-width:600px){.backup-progress-heading{align-items:flex-start;flex-direction:column;gap:5px}}
</style>
<script>
    document.getElementById('backupDownloadForm').addEventListener('submit', function () {
        const button = document.getElementById('backupDownloadButton');
        const progress = document.getElementById('backupProgress');

        button.disabled = true;
        button.querySelector('i').className = 'bi bi-arrow-repeat spin';
        button.querySelector('span').textContent = 'Backup sedang diproses...';
        progress.hidden = false;
    });
</script>
@endsection
