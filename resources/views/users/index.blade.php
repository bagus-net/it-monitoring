@extends('layouts.app')

@section('content')
<div class="container mt-4 user-page">
    <div class="d-flex justify-content-between align-items-start mb-3"><div class="d-flex align-items-start gap-3"><span class="user-page-icon"><i class="bi bi-people"></i></span><div><div class="user-eyebrow">Pengaturan</div><h2 class="mb-1">Pengaturan User</h2><p class="text-muted mb-0">Kelola akun, hak akses, dan peralatan IT yang dipegang setiap user.</p></div></div><div class="d-flex gap-2"><button type="button" class="btn btn-outline-success" id="openUserBulkDownload"><i class="bi bi-filetype-jpg"></i>Unduh Semua Detail</button><a href="{{ route('users.create') }}" class="btn btn-brand"><i class="bi bi-plus-lg"></i>Tambah User</a></div></div>
    <div class="row g-3 mb-3">
      <div class="col-6 col-lg-3"><div class="user-widget total"><div class="widget-top"><span>Total User</span><i class="bi bi-people-fill widget-icon"></i></div><strong>{{ $summary['total'] }}</strong><small>akun terdaftar</small></div></div>
      <div class="col-6 col-lg-3"><div class="user-widget master"><div class="widget-top"><span>Master</span><i class="bi bi-shield-lock-fill widget-icon"></i></div><strong>{{ $summary['master'] }}</strong><small>akses penuh</small></div></div>
      <div class="col-6 col-lg-3"><div class="user-widget admin"><div class="widget-top"><span>Admin IT</span><i class="bi bi-person-gear widget-icon"></i></div><strong>{{ $summary['admin_it'] }}</strong><small>tanpa jadwal & log</small></div></div>
      <div class="col-6 col-lg-3"><div class="user-widget employee"><div class="widget-top"><span>User</span><i class="bi bi-person-badge widget-icon"></i></div><strong>{{ $summary['user'] }}</strong><small>karyawan</small></div></div>
    </div>
    <div class="card user-filter mb-3">
        <div class="card-header"><strong><i class="bi bi-sliders"></i>Filter User</strong></div>
        <div class="card-body">
            <form method="GET" action="{{ route('users.index') }}" class="row g-2 align-items-end">
                @if($search)<input type="hidden" name="search" value="{{ $search }}">@endif
                <div class="col-6 col-lg-3"><label class="form-label">Hak Akses</label><select name="role" class="form-select"><option value="">Semua</option>@foreach(\App\Models\User::ROLE_LABELS as $key => $label)<option value="{{ $key }}" @selected($role === $key)>{{ $label }}</option>@endforeach</select></div>
                <div class="col-12 col-lg-3 d-flex gap-2">
                    <button type="submit" class="btn btn-brand btn-sm"><i class="bi bi-check2"></i>Terapkan</button>
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                </div>
            </form>
        </div>
    </div>
    <div class="card user-list">
        <div class="card-header"><strong><i class="bi bi-table"></i>Daftar User</strong></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Nama</th><th>Email</th><th>Departemen</th><th>Hak Akses</th><th>Peralatan</th><th>Status</th><th>Aksi</th></tr></thead>
                    <tbody>
                        @forelse($users as $item)
                            <tr>
                                <td><div class="user-name-cell"><img src="{{ $item->profile_photo_path ? asset('storage/' . $item->profile_photo_path) : asset('images/default-avatar.svg') }}" alt="Foto profil {{ $item->name }}"><strong>{{ $item->name }}</strong></div></td>
                                <td>{{ $item->email }}</td>
                                <td>{{ $item->department ?: '-' }}</td>
                                <td><span class="role-badge role-{{ $item->role }}">{{ $item->roleLabel() }}</span></td>
                                <td>{{ $item->equipments_count }} unit</td>
                                <td>{!! $item->is_active ? '<span class="role-badge role-active">Aktif</span>' : '<span class="role-badge role-inactive">Nonaktif</span>' !!}</td>
                                <td class="text-nowrap">
                                    <a href="{{ route('users.show', $item) }}" class="btn btn-sm btn-outline-secondary">Detail</a>
                                    <a href="{{ route('users.edit', $item) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form action="{{ route('users.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus user ini?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Hapus</button></form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-4">Belum ada user.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="table-pagination">{{ $users->links() }}</div>
        </div>
    </div>
</div>
<style>
.user-page{--ublue:#2161f5;--uteal:#14b8a6}
.user-page-icon{display:flex;align-items:center;justify-content:center;width:46px;height:46px;flex:0 0 46px;border-radius:14px;background:linear-gradient(135deg,var(--ublue),#6ea3ff);color:#fff;font-size:1.15rem;box-shadow:0 8px 16px rgba(33,97,245,.28)}
.user-page .user-eyebrow{color:var(--ublue);font-size:.72rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em}
.user-page .btn-brand{background:linear-gradient(135deg,var(--ublue),#6ea3ff);box-shadow:0 8px 16px rgba(33,97,245,.24)}
.user-page .btn-brand i,.user-page .card-header strong i{margin-right:7px}
.user-page .card-header strong{display:flex;align-items:center;color:#18243d}
.user-page .card-header strong i{color:var(--ublue)}
.user-page .card{border:1px solid #e7ebf2;border-radius:16px;box-shadow:0 8px 22px rgba(35,52,85,.05)}
.user-page .card-header{padding:16px 20px;border-bottom:1px solid #edf0f5;background:#fff;color:#18243d;font-size:.88rem;font-weight:800;border-radius:16px 16px 0 0}
.user-page .card-body{padding:20px}
.user-page .user-widget{position:relative;min-height:126px;padding:18px;border:1px solid #e7ebf2;border-radius:16px;background:#fff;box-shadow:0 8px 20px rgba(35,52,85,.05)}
.user-page .widget-top{display:flex;align-items:center;justify-content:space-between;color:#8792a7;font-size:.72rem;font-weight:700}
.user-page .widget-icon{display:flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:10px;font-size:.86rem}
.user-page .user-widget strong{display:block;margin:9px 0 3px;color:#18243d;font-size:1.75rem;font-weight:800;letter-spacing:-.02em}
.user-page .user-widget small{display:block;color:#8792a7;font-size:.68rem}
.user-page .user-widget.total .widget-icon{background:#e8f0ff;color:var(--ublue)}
.user-page .user-widget.master .widget-icon{background:#fee2e2;color:#dc2626}
.user-page .user-widget.admin .widget-icon{background:#fef3c7;color:#c2870a}
.user-page .user-widget.employee .widget-icon{background:#e2f8f4;color:var(--uteal)}
.user-page .user-filter .form-label{color:#69758d;font-size:.68rem;font-weight:700;letter-spacing:.02em}
.user-page .form-control,.user-page .form-select{min-height:39px;border:1px solid #dfe5ee;border-radius:9px;background:#f9fafc;color:#34415a;font-size:.76rem}
.user-page .form-control:focus,.user-page .form-select:focus{border-color:#7aa3ff;box-shadow:0 0 0 3px rgba(33,97,245,.1);background:#fff}
.user-page .btn-sm{border-radius:8px;font-size:.7rem;font-weight:700}
.user-page .btn-sm i{margin-right:5px;font-size:.72rem}
.user-page .btn-outline-secondary{border-color:#dfe5ee;color:#68758d}
.user-page .btn-outline-primary{border-color:#b8ccff;color:var(--ublue)}
.user-page .btn-outline-primary:hover{background:var(--ublue);border-color:var(--ublue)}
.user-page .btn-outline-danger{border-color:#f4c2c7;color:#dc5260}
.user-page .user-list .card-body{padding:0}
.user-page .table{font-size:.74rem}
.user-page .table thead th{padding:13px 16px;border-bottom:1px solid #e8edf4;background:#f8fafc;color:#7d899e;font-size:.65rem;font-weight:800;letter-spacing:.04em;text-transform:uppercase;white-space:nowrap}
.user-page .table tbody td{padding:14px 16px;border-color:#eef1f5;color:#536079;vertical-align:middle}
.user-page .table tbody tr{transition:background .15s}
.user-page .table tbody tr:hover{background:#f8faff}
.user-page .table td strong{color:#26324b;font-weight:700}
.user-page .user-name-cell{display:flex;align-items:center;gap:10px;min-width:170px}
.user-page .user-name-cell img{width:38px;height:38px;flex:0 0 38px;border:0;border-radius:11px;object-fit:cover;background:#e8f0ff}
.user-page .table-pagination{padding:14px 20px;background:#fff}
.user-page .page-link{border-radius:7px;margin-left:4px!important;font-size:.72rem}
.user-page .role-badge{display:inline-block;padding:5px 9px;border-radius:999px;font-size:.68rem;font-weight:700}
.user-page .role-master{background:#fee2e2;color:#991b1b}
.user-page .role-admin_it{background:#fef3c7;color:#92400e}
.user-page .role-user{background:#dcfce7;color:#166534}
.user-page .role-active{background:#dbeafe;color:#1d4ed8}
.user-page .role-inactive{background:#e2e8f0;color:#475569}
@media(max-width:767px){.user-page>.d-flex{gap:14px;flex-direction:column!important}.user-page>.d-flex .btn{align-self:stretch}.user-page .card-body{padding:15px}.user-page .card-header{padding:14px 15px}.user-page .user-widget{min-height:108px;padding:15px}.user-page .user-widget strong{font-size:1.45rem}}
</style>
<div class="user-bulk-modal" id="userBulkModal" hidden>
    <div class="user-bulk-dialog" role="dialog" aria-modal="true" aria-labelledby="userBulkTitle">
        <div class="user-bulk-progress" id="userBulkProgress">
            <div class="user-bulk-icon"><i class="bi bi-people"></i></div>
            <h2 id="userBulkTitle">Menyiapkan Detail User</h2>
            <p id="userBulkStatus">Mengambil data user...</p>
            <div class="user-bulk-track"><div id="userBulkBar"></div></div>
            <div class="user-bulk-meta"><span id="userBulkCount">0 user</span><strong id="userBulkPercent">0%</strong></div>
        </div>
        <div class="user-bulk-success" id="userBulkSuccess" hidden>
            <div class="user-bulk-success-icon"><i class="bi bi-check2"></i></div>
            <h2>Download Berhasil</h2>
            <p>Semua detail user sudah berhasil diunduh dalam format ZIP.</p>
            <button type="button" class="btn btn-brand" id="closeUserBulkModal"><i class="bi bi-check-lg"></i>OK</button>
        </div>
    </div>
</div>
<style>
    .user-bulk-modal{position:fixed;z-index:1090;inset:0;display:grid;place-items:center;padding:20px;background:rgba(15,23,42,.4);backdrop-filter:blur(5px)}.user-bulk-modal[hidden],.user-bulk-progress[hidden],.user-bulk-success[hidden]{display:none!important}.user-bulk-dialog{width:min(460px,calc(100vw - 32px));padding:34px 36px;border:1px solid #e5eaf1;border-radius:22px;background:#fff;box-shadow:0 24px 70px rgba(15,23,42,.25);text-align:center}.user-bulk-icon,.user-bulk-success-icon{width:64px;height:64px;margin:0 auto 17px;border-radius:19px;background:#eef3ff;color:#2161f5;display:grid;place-items:center;font-size:28px;box-shadow:0 10px 22px rgba(33,97,245,.16);animation:userBulkFloat 2.4s ease-in-out infinite}.user-bulk-dialog h2{margin:0 0 8px;color:#18243d;font-size:1.25rem}.user-bulk-dialog p{min-height:20px;margin:0 0 23px;color:#758198;font-size:.8rem}.user-bulk-track{height:12px;padding:3px;border-radius:99px;background:#e7eaf4;overflow:hidden}.user-bulk-track>div{width:0;height:100%;border-radius:99px;background:#2161f5;transition:width .25s ease;position:relative;overflow:hidden}.user-bulk-track>div:after{position:absolute;inset:0;background:linear-gradient(110deg,transparent 25%,rgba(255,255,255,.65) 50%,transparent 75%);content:'';animation:userBulkShine 1.3s linear infinite}.user-bulk-meta{display:flex;justify-content:space-between;margin-top:11px;color:#8792a7;font-size:.75rem;font-weight:700}.user-bulk-meta strong{color:#2161f5}.user-bulk-success-icon{background:#dcfce7;color:#16a34a;animation:none}.user-bulk-success p{margin-bottom:22px}.user-bulk-success .btn{min-width:110px}.user-bulk-sheet-grid{display:grid;grid-template-columns:340px minmax(0,1fr);gap:20px;color:#18243d}.user-bulk-sheet-profile,.user-bulk-sheet-assets{border:1px solid #e5eaf1;border-radius:18px;background:#fff;box-shadow:0 8px 22px rgba(30,48,78,.06)}.user-bulk-sheet-profile{display:flex;min-height:520px;flex-direction:column;align-items:center;justify-content:center;padding:30px 22px;text-align:center}.user-bulk-sheet-profile img{width:150px;height:150px;border:6px solid #f1f5f9;border-radius:50%;object-fit:cover}.user-bulk-sheet-profile>div>span,.user-bulk-sheet-assets header span{display:block;margin-top:16px;color:#8792a7;font-size:11px;font-weight:800;letter-spacing:.14em}.user-bulk-sheet-profile h2{margin:12px 0 5px;font-size:25px}.user-bulk-sheet-profile p{margin:0;color:#8792a7;font-size:13px}.user-bulk-sheet-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-top:26px;text-align:left}.user-bulk-sheet-stats b{padding:11px 9px;border-left:3px solid #2161f5;border-radius:9px;background:#f8fafc;color:#8792a7;font-size:9px}.user-bulk-sheet-stats b:nth-child(2){border-left-color:#e879b8}.user-bulk-sheet-stats b:nth-child(3){border-left-color:#14b8a6}.user-bulk-sheet-stats strong{display:block;margin-top:8px;color:#18243d;font-size:12px}.user-bulk-sheet-assets{overflow:hidden}.user-bulk-sheet-assets header{display:flex;justify-content:space-between;align-items:center;padding:20px;border-bottom:1px solid #edf0f5}.user-bulk-sheet-assets header span{margin:0}.user-bulk-sheet-assets h3{margin:5px 0 0;font-size:17px}.user-bulk-sheet-assets em{padding:5px 9px;border-radius:99px;background:#eef3ff;color:#2161f5;font-size:11px;font-style:normal;font-weight:700}.user-bulk-sheet-assets table{width:100%;border-collapse:collapse;font-size:11px}.user-bulk-sheet-assets th{padding:12px 10px;background:#f8fafc;color:#8792a7;font-size:9px;text-align:left;text-transform:uppercase}.user-bulk-sheet-assets td{padding:12px 10px;border-top:1px solid #eef1f5}.user-bulk-sheet-assets td:first-child{font-weight:700}.user-bulk-empty{padding:35px!important;color:#8792a7;text-align:center!important}@keyframes userBulkShine{to{transform:translateX(220%)}}@keyframes userBulkFloat{50%{transform:translateY(-5px)}}
</style>
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jszip@3.10.1/dist/jszip.min.js"></script>
<script>
(() => {
    const openButton = document.getElementById('openUserBulkDownload');
    const modal = document.getElementById('userBulkModal');
    const progress = document.getElementById('userBulkProgress');
    const success = document.getElementById('userBulkSuccess');
    const status = document.getElementById('userBulkStatus');
    const bar = document.getElementById('userBulkBar');
    const count = document.getElementById('userBulkCount');
    const percent = document.getElementById('userBulkPercent');
    const wait = milliseconds => new Promise(resolve => setTimeout(resolve, milliseconds));
    const safeName = (value, fallback) => (value || fallback).toString().trim().replace(/[\\/:*?"<>|]+/g, '-').replace(/\s+/g, '-');
    const escapeHtml = value => String(value ?? '-').replace(/[&<>"']/g, character => ({ '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#039;' }[character]));
    const closeModal = () => { modal.hidden = true; progress.hidden = false; success.hidden = true; };

    function renderSheet(user, host) {
        const rows = user.equipments.length ? user.equipments.map((equipment, index) => `<tr><td>${index + 1}</td><td>${escapeHtml(equipment.name)}</td><td>${escapeHtml(equipment.assetTag)}</td><td>${escapeHtml(equipment.type)}</td><td>${escapeHtml(equipment.manufacturer)}</td><td>${escapeHtml(equipment.location)}</td><td>${escapeHtml(equipment.condition)}</td></tr>`).join('') : '<tr><td colspan="7" class="user-bulk-empty">Belum ada peralatan IT.</td></tr>';
        host.innerHTML = `<div class="user-bulk-sheet-grid"><section class="user-bulk-sheet-profile"><div><img src="${escapeHtml(user.photoUrl)}" alt="Foto profil"><span>PROFIL USER</span><h2>${escapeHtml(user.name)}</h2><p>${escapeHtml(user.email)}</p><div class="user-bulk-sheet-stats"><b>Hak Akses<strong>${escapeHtml(user.role)}</strong></b><b>Departemen<strong>${escapeHtml(user.department)}</strong></b><b>Status<strong>${escapeHtml(user.status)}</strong></b></div></div></section><section class="user-bulk-sheet-assets"><header><div><span>ASSET ASSIGNMENT</span><h3>Peralatan IT yang Dipegang</h3></div><em>${user.equipments.length} unit</em></header><table><thead><tr><th>No.</th><th>Peralatan</th><th>Serial</th><th>Tipe</th><th>Manufacturer</th><th>PIC</th><th>Kondisi</th></tr></thead><tbody>${rows}</tbody></table></section></div>`;
    }

    async function startDownload() {
        modal.hidden = false;
        openButton.disabled = true;
        try {
            const response = await fetch('{{ route('users.details.download-all') }}', { headers: { Accept: 'application/json' } });
            if (!response.ok) throw new Error('Data user tidak dapat dimuat.');
            const payload = await response.json();
            const details = payload.details || [];
            const zip = new JSZip();
            const host = document.createElement('div');
            host.style.cssText = 'position:fixed;left:-10000px;top:0;width:1100px;padding:28px;background:#f8fafc;';
            document.body.appendChild(host);
            const names = new Set();
            for (let index = 0; index < details.length; index++) {
                const user = details[index];
                renderSheet(user, host);
                const equipmentStat = host.querySelector('.user-bulk-sheet-stats b:nth-child(3)');
                if (equipmentStat) { equipmentStat.firstChild.textContent = 'Peralatan Dipegang'; equipmentStat.querySelector('strong').textContent = `${user.equipments.length} unit`; }
                await wait(80);
                const canvas = await html2canvas(host, { backgroundColor: '#f8fafc', scale: 2, useCORS: true });
                const baseName = safeName(user.name, `user-${user.id}`);
                let fileName = baseName; let suffix = 2;
                while (names.has(fileName)) fileName = `${baseName}-${suffix++}`;
                names.add(fileName);
                zip.file(`detail-user-${fileName}.jpg`, canvas.toDataURL('image/jpeg', .95).split(',')[1], { base64: true });
                const value = Math.round(((index + 1) / details.length) * 100);
                bar.style.width = `${value}%`; percent.textContent = `${value}%`; count.textContent = `${index + 1} dari ${details.length} user`; status.textContent = `Membuat detail ${index + 1} dari ${details.length}...`;
            }
            host.remove();
            const blob = await zip.generateAsync({ type: 'blob' });
            const link = document.createElement('a'); link.href = URL.createObjectURL(blob); link.download = 'detail-semua-user.zip'; link.click();
            progress.hidden = true; success.hidden = false;
        } catch (error) {
            status.textContent = error.message || 'Detail user tidak dapat dibuat.';
            openButton.disabled = false;
        }
    }
    openButton?.addEventListener('click', startDownload);
    document.getElementById('closeUserBulkModal')?.addEventListener('click', () => { closeModal(); openButton.disabled = false; });
    modal?.addEventListener('click', event => { if (event.target === modal && !success.hidden) { closeModal(); openButton.disabled = false; } });
})();
</script>
@endsection
