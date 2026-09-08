@extends('layouts.app')

@section('content')
<div class="container mt-4 user-detail-page">
    <div class="user-detail-hero"><div class="user-detail-profile"><img src="{{ $user->profile_photo_path ? asset('storage/' . $user->profile_photo_path) : asset('images/default-avatar.svg') }}" alt="Foto profil {{ $user->name }}"><div><div class="user-detail-kicker">PROFIL USER</div><h1>{{ $user->name }}</h1><p>{{ $user->email }}</p></div></div><div class="user-detail-summary"><div class="user-detail-stat"><span>Hak Akses</span><strong>{{ $user->roleLabel() }}</strong></div><div class="user-detail-stat"><span>Departemen</span><strong>{{ $user->department ?: '-' }}</strong></div><div class="user-detail-stat"><span>Peralatan Dipegang</span><strong>{{ $user->equipments->count() }} unit</strong></div></div><div class="d-flex flex-wrap gap-2 no-print"><button type="button" class="btn btn-light" id="downloadUserDetailJpg"><i class="bi bi-filetype-jpg"></i> Unduh Detail JPG</button><a href="{{ route('users.edit', $user) }}" class="btn btn-light">Edit User</a><a href="{{ route('users.index') }}" class="btn btn-outline-light">Kembali</a></div></div>
    <section class="card user-equipment-card mt-3"><div class="card-header d-flex justify-content-between align-items-center"><div><div class="user-detail-kicker">ASSET ASSIGNMENT</div><h2>Peralatan IT yang Dipegang</h2></div><span class="badge bg-primary">{{ $user->equipments->count() }} unit</span></div><div class="card-body p-0"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Peralatan</th><th>Kode Aset</th><th>Tipe</th><th>Manufacturer</th><th>Lokasi</th><th>Kondisi</th><th class="no-print"></th></tr></thead><tbody>@forelse($user->equipments->sortBy('name') as $equipment)<tr><td><strong>{{ $equipment->name }}</strong><small class="d-block text-muted">{{ $equipment->model ?: 'Model belum dicatat' }}</small></td><td>{{ $equipment->asset_tag ?: '-' }}</td><td>{{ $equipment->type->name ?? '-' }}</td><td>{{ $equipment->manufacturer->name ?? '-' }}</td><td>{{ $equipment->assetLocation->name ?? '-' }}</td><td>{{ ucfirst($equipment->condition ?: $equipment->status ?: '-') }}</td><td class="text-nowrap no-print"><a href="{{ route('equipments.show', $equipment) }}" class="btn btn-sm btn-outline-primary">Detail Aset</a><form method="POST" action="{{ route('users.equipments.detach', [$user, $equipment]) }}" class="d-inline" onsubmit="return confirm('Lepas peralatan {{ $equipment->name }} dari {{ $user->name }}?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Lepas Peralatan</button></form></td></tr>@empty<tr><td colspan="7" class="text-center text-muted py-5">Belum ada peralatan IT yang ditugaskan kepada user ini.</td></tr>@endforelse</tbody></table></div></div></section>
</div>
<style>.user-detail-page{position:relative;display:grid;grid-template-columns:minmax(280px,.9fr) minmax(0,1.45fr);gap:18px;max-width:1120px;padding:26px 0 42px;color:#17324d;isolation:isolate}.user-detail-page:before,.user-detail-page:after{position:absolute;z-index:-1;content:'';pointer-events:none}.user-detail-page:before{top:18px;left:-8vw;width:52%;height:96%;background:linear-gradient(135deg,rgba(255,54,152,.14),rgba(255,132,30,.08) 55%,transparent 56%);clip-path:polygon(0 10%,78% 0,100% 35%,63% 100%,0 82%)}.user-detail-page:after{right:-8vw;bottom:0;width:48%;height:62%;background:linear-gradient(135deg,rgba(20,184,166,.15),rgba(124,92,252,.1));clip-path:polygon(30% 0,100% 18%,78% 100%,0 75%)}.user-detail-hero{display:flex;position:relative;grid-column:1;grid-row:1 / span 2;min-height:480px;flex-direction:column;justify-content:space-between;align-items:flex-start;gap:28px;padding:30px 26px;border:1px solid rgba(255,255,255,.85);border-radius:22px;background:linear-gradient(145deg,#ffffff 0%,#ffffff 72%,#fff3fb 100%);color:#17324d;box-shadow:0 18px 45px rgba(35,52,85,.12);overflow:hidden}.user-detail-hero:after{position:absolute;right:-45px;bottom:-68px;width:190px;height:190px;border-radius:50%;background:linear-gradient(135deg,#ff3e9e,#ff8b21);content:'';opacity:.86}.user-detail-profile{position:relative;z-index:1;display:flex;align-items:center;gap:16px;min-width:0;width:100%;flex-direction:column;text-align:center}.user-detail-profile img{width:152px;height:152px;flex:0 0 152px;border:8px solid #fff;border-radius:50%;object-fit:cover;background:#e0f2fe;box-shadow:0 12px 30px rgba(28,44,80,.18)}.user-detail-kicker{color:#8792a7;font-size:.68rem;font-weight:800;letter-spacing:.14em}.user-detail-hero h1{margin:10px 0 5px;color:#18243d;font-size:1.6rem;font-weight:800}.user-detail-hero p{margin:0;color:#8792a7;font-size:.77rem}.user-detail-hero>.d-flex{position:relative;z-index:1;width:100%;justify-content:center}.user-detail-hero .btn{border-radius:9px;font-size:.72rem;font-weight:700}.user-detail-hero .btn-light{border:1px solid #e7ebf2;color:#2161f5;background:#fff}.user-detail-hero .btn-outline-light{border-color:#dfe5ee;color:#68758d}.user-detail-hero .btn i{margin-right:5px}.user-detail-page>.row{grid-column:2;grid-row:1;display:grid;grid-template-columns:repeat(3,1fr);margin:0!important;gap:12px}.user-detail-stat{height:100%;min-height:116px;padding:18px;border:1px solid #e7ebf2;border-top:0;border-left:4px solid #2161f5;border-radius:15px;background:#fff;box-shadow:0 10px 24px rgba(35,52,85,.07)}.user-detail-page>.row .col-md-4{padding:0}.user-detail-page>.row .col-md-4:nth-child(2) .user-detail-stat{border-left-color:#ff3e9e}.user-detail-page>.row .col-md-4:nth-child(3) .user-detail-stat{border-left-color:#14b8a6}.user-detail-stat span{display:block;color:#8792a7;font-size:.69rem;font-weight:700}.user-detail-stat strong{display:block;margin-top:12px;color:#18243d;font-size:1.05rem}.user-equipment-card{grid-column:2;grid-row:2;margin-top:0!important;border:1px solid #e7ebf2;border-radius:18px;background:#fff;box-shadow:0 12px 28px rgba(35,52,85,.08);overflow:hidden}.user-equipment-card .card-header{padding:19px 22px;background:#fff;border-bottom:1px solid #edf0f5}.user-equipment-card h2{margin:5px 0 0;color:#18243d;font-size:1.02rem}.user-equipment-card thead th{background:#f8fafc;font-size:.67rem;color:#8792a7}.user-equipment-card .badge{border-radius:999px;background:#efeafe!important;color:#7c5cfc!important;font-size:.68rem}@media(max-width:850px){.user-detail-page{display:block;padding-top:15px}.user-detail-hero{min-height:0;margin-bottom:15px}.user-detail-page>.row{display:grid;grid-template-columns:repeat(3,1fr);margin-bottom:15px!important}.user-equipment-card{margin-top:0!important}}@media(max-width:650px){.user-detail-page>.row{grid-template-columns:1fr}.user-detail-hero{padding:24px 18px}.user-detail-profile img{width:120px;height:120px;flex-basis:120px}.user-detail-hero>.d-flex{flex-direction:column}.user-detail-hero>.d-flex .btn{width:100%}}@media print{@page{size:A4;margin:14mm}.no-print,.app-sidebar,.app-topbar,.app-footer,.mobile-bottom-nav,.print-letterhead,#ticketToast,#transferToast{display:none!important}body,html{background:#fff!important;color:#17324d!important}.app-main{padding:0!important;margin:0!important}.user-detail-page{display:block;max-width:none!important;margin:0!important;padding:0!important}.user-detail-page:before,.user-detail-page:after{display:none}.user-detail-hero{display:flex;min-height:0;margin-bottom:8mm;padding:19mm 15mm;border-radius:0;background:linear-gradient(120deg,#075985,#0f766e)!important;-webkit-print-color-adjust:exact;print-color-adjust:exact}.user-detail-profile{flex-direction:row;text-align:left}.user-detail-profile img{width:31mm;height:31mm;flex-basis:31mm}.user-detail-hero h1{font-size:22pt;color:#fff}.user-detail-hero p,.user-detail-kicker{color:#dbeafe}.user-detail-page>.row{display:grid;grid-template-columns:repeat(3,1fr);margin-bottom:8mm!important}.user-detail-stat{padding:10px 13px;border-top-width:3px;box-shadow:none}.user-detail-stat strong{font-size:11pt}.user-equipment-card{margin-top:0!important;box-shadow:none}.user-equipment-card .card-header{padding:12px 15px}.user-equipment-card h2{font-size:13pt}.user-equipment-card .table{font-size:9pt}.user-equipment-card .table thead th{padding:8px 10px}.user-equipment-card .table tbody td{padding:8px 10px}.user-equipment-card tr{break-inside:avoid}.user-detail-page:after{display:block;margin-top:10mm;padding-top:4mm;border-top:1px solid #dbe5ef;color:#64748b;content:'Dicetak dari IT Monitoring & Maintenance System';font-size:8pt;text-align:right}}
</style>
<style>
    .user-detail-page{grid-template-columns:minmax(320px,380px) minmax(0,1fr);max-width:1180px;gap:20px;padding:24px 0 40px;background:#f8fafc}
    .user-detail-page:before,.user-detail-page:after,.user-detail-hero:after{display:none}
    .user-detail-hero{grid-column:1;grid-row:1;min-height:460px;padding:32px 24px;border:1px solid #e5eaf1;border-radius:18px;background:#fff;color:#17324d;box-shadow:0 8px 24px rgba(30,48,78,.07)}
    .user-detail-summary{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:8px;width:100%;position:relative;z-index:1}
    .user-detail-profile img{width:136px;height:136px;flex-basis:136px;border:5px solid #f1f5f9;box-shadow:0 8px 20px rgba(30,48,78,.12)}
    .user-detail-hero h1{font-size:1.45rem}
    .user-detail-hero>.d-flex{flex-wrap:wrap}
    .user-detail-hero .btn-light,.user-detail-hero .btn-outline-light{border:1px solid #dfe5ee;background:#fff;color:#2161f5}
    .user-detail-page>.row{grid-column:1;grid-row:2;display:grid;grid-template-columns:1fr;gap:10px;margin:0!important}
    .user-detail-stat{min-height:120px;padding:18px 16px;border:1px solid #e5eaf1;border-left:3px solid #2161f5;border-radius:12px;box-shadow:0 6px 18px rgba(30,48,78,.05)}
    .user-detail-summary .user-detail-stat{min-height:86px;padding:12px 10px;border-left-width:3px;border-radius:10px;box-shadow:none;background:#f8fafc}
    .user-detail-summary .user-detail-stat span{font-size:.61rem;line-height:1.25}
    .user-detail-summary .user-detail-stat strong{margin-top:8px;font-size:.84rem;line-height:1.2}
    .user-detail-page>.row .col-md-4:nth-child(2) .user-detail-stat{border-left-color:#e879b8}
    .user-detail-page>.row .col-md-4:nth-child(3) .user-detail-stat{border-left-color:#14b8a6}
    .user-equipment-card{grid-column:2;grid-row:1 / span 2;margin-top:0!important;border:1px solid #e5eaf1;border-radius:16px;box-shadow:0 8px 22px rgba(30,48,78,.06)}
    .user-equipment-card .card-header{padding:18px 20px;background:#fff}
    .user-equipment-card thead th{background:#f8fafc}
    @media(max-width:850px){.user-detail-page{background:transparent}.user-detail-hero{min-height:0}.user-detail-summary{grid-template-columns:repeat(3,1fr)}.user-detail-page>.row{display:grid;grid-template-columns:repeat(3,1fr);gap:12px}.user-equipment-card{margin-top:0!important}}
    @media(max-width:650px){.user-detail-summary{grid-template-columns:1fr}.user-detail-page>.row{grid-template-columns:1fr}}
    @media print{.user-detail-page{background:#fff}.user-detail-hero{background:#fff!important}.user-detail-page>.row{gap:0}}
    @media print{
        @page{size:A4 landscape;margin:10mm}
        body,html{background:#fff!important;color:#17324d!important}
        .app-main{padding:0!important;margin:0!important}
        .user-detail-page{display:grid!important;grid-template-columns:34% 66%;gap:12px;max-width:none!important;margin:0!important;padding:0!important;background:#fff!important}
        .user-detail-page:before,.user-detail-page:after{display:none!important}
        .user-detail-hero{grid-column:1;grid-row:1;display:flex;min-height:0;height:100%;padding:22px 16px;border:1px solid #e5eaf1;border-radius:14px;background:#fff!important;box-shadow:none;justify-content:space-between}
        .user-detail-profile{display:flex;flex-direction:column;text-align:center;gap:10px}
        .user-detail-profile img{width:34mm;height:34mm;flex-basis:34mm;border:4px solid #f1f5f9;box-shadow:none}
        .user-detail-hero h1{margin:5px 0 3px;color:#18243d;font-size:18pt}
        .user-detail-hero p,.user-detail-kicker{color:#64748b;font-size:8pt}
        .user-detail-summary{grid-template-columns:1fr;gap:7px}
        .user-detail-summary .user-detail-stat{min-height:0;padding:9px 10px;border-left-width:3px;border-radius:7px;background:#f8fafc;box-shadow:none}
        .user-detail-summary .user-detail-stat span{font-size:7pt}
        .user-detail-summary .user-detail-stat strong{margin-top:4px;font-size:9pt}
        .user-detail-hero>.d-flex{justify-content:center;gap:5px!important}
        .user-equipment-card{grid-column:2;grid-row:1;margin:0!important;border:1px solid #e5eaf1;border-radius:14px;box-shadow:none;overflow:hidden}
        .user-equipment-card .card-header{padding:12px 14px;background:#fff}
        .user-equipment-card h2{font-size:12pt}
        .user-equipment-card .table{font-size:8pt}
        .user-equipment-card .table thead th{padding:7px 8px;background:#f8fafc;font-size:7pt}
        .user-equipment-card .table tbody td{padding:7px 8px}
        .user-equipment-card tr{break-inside:avoid}
        .no-print,.app-sidebar,.app-topbar,.app-footer,.mobile-bottom-nav,.print-letterhead,#ticketToast,#transferToast{display:none!important}
    }
    @media print{
        @page{size:A5 landscape;margin:7mm}
        .user-detail-page{height:134mm}
        .user-detail-hero,.user-equipment-card{height:134mm;max-height:134mm}
        .user-detail-hero{padding:13px 11px}
        .user-detail-profile img{width:24mm;height:24mm;flex-basis:24mm}
        .user-detail-hero h1{font-size:15pt}
        .user-detail-summary .user-detail-stat{padding:5px 7px}
        .user-detail-summary .user-detail-stat span{font-size:6pt}
        .user-detail-summary .user-detail-stat strong{margin-top:3px;font-size:7.5pt}
        .user-equipment-card .card-header{padding:7px 9px}
        .user-equipment-card h2{font-size:10pt}
        .user-equipment-card .table{font-size:6pt}
        .user-equipment-card .table thead th{padding:4px 5px;font-size:5.5pt}
        .user-equipment-card .table tbody td{padding:4px 5px}
        .user-equipment-card small{font-size:5.5pt}
    }
    @media print{
        .user-detail-page{height:134mm;overflow:hidden;align-items:stretch}
        .user-detail-hero,.user-equipment-card{height:134mm;max-height:134mm}
        .user-detail-hero{padding:17px 13px}
        .user-detail-profile img{width:29mm;height:29mm;flex-basis:29mm}
        .user-detail-summary{gap:5px}
        .user-detail-summary .user-detail-stat{padding:7px 8px}
        .user-detail-summary .user-detail-stat strong{font-size:8.5pt}
        .user-equipment-card .card-header{padding:9px 11px}
        .user-equipment-card .table{font-size:7pt}
        .user-equipment-card .table thead th{padding:5px 6px;font-size:6.5pt}
        .user-equipment-card .table tbody td{padding:5px 6px}
        .user-equipment-card small{font-size:6.5pt}
    }
    @media print{
        @page{size:A5 portrait;margin:5mm}
        .user-detail-page{display:block!important;width:138mm;max-width:138mm!important;height:200mm;max-height:200mm;overflow:hidden;margin:0 auto!important;padding:5mm!important;border:0.45mm solid #334155;background:#fff!important;box-sizing:border-box}
        .user-detail-hero{display:flex;min-height:0;height:auto;max-height:none;margin:0 0 4mm;padding:7mm 6mm;border:0.3mm solid #dbe5ef;border-radius:3mm;box-shadow:none;align-items:stretch}
        .user-detail-profile{gap:2mm}
        .user-detail-profile img{width:25mm;height:25mm;flex-basis:25mm}
        .user-detail-hero h1{font-size:16pt}
        .user-detail-summary{grid-template-columns:repeat(3,1fr);gap:2mm;margin-top:3mm}
        .user-detail-summary .user-detail-stat{min-height:15mm;padding:2.5mm 2mm;border-radius:1.5mm}
        .user-detail-summary .user-detail-stat span{font-size:6.5pt}
        .user-detail-summary .user-detail-stat strong{font-size:8pt}
        .user-detail-hero>.d-flex{margin-top:4mm}
        .user-equipment-card{display:block;height:auto;max-height:none;margin:0!important;border:0.3mm solid #dbe5ef;border-radius:3mm;box-shadow:none}
        .user-equipment-card .card-header{padding:4mm 4mm 3mm}
        .user-equipment-card h2{font-size:11pt}
        .user-equipment-card .table{font-size:6.5pt}
        .user-equipment-card .table thead th{padding:2mm 1.5mm;font-size:5.5pt}
        .user-equipment-card .table tbody td{padding:2mm 1.5mm}
        .user-equipment-card small{font-size:5.5pt}
    }
    @media print{
        @page{size:A6 portrait;margin:4mm}
        .user-detail-page{width:97mm;max-width:97mm!important;height:140mm;max-height:140mm;padding:3.5mm!important;border-width:.35mm}
        .user-detail-hero{margin-bottom:2.5mm;padding:4mm 3mm;border-radius:2mm}
        .user-detail-profile{gap:1mm}
        .user-detail-profile img{width:18mm;height:18mm;flex-basis:18mm;border-width:2px}
        .user-detail-kicker{font-size:5pt}
        .user-detail-hero h1{margin:2px 0;font-size:11pt}
        .user-detail-hero p{font-size:5.5pt}
        .user-detail-summary{gap:1mm;margin-top:2mm}
        .user-detail-summary .user-detail-stat{min-height:11mm;padding:1.5mm 1mm;border-left-width:1.5mm;border-radius:1mm}
        .user-detail-summary .user-detail-stat span{font-size:4.5pt}
        .user-detail-summary .user-detail-stat strong{margin-top:2px;font-size:6pt}
        .user-detail-hero>.d-flex{margin-top:2.5mm}
        .user-equipment-card{border-radius:2mm}
        .user-equipment-card .card-header{padding:2mm 2.5mm}
        .user-equipment-card h2{font-size:7.5pt}
        .user-equipment-card .badge{font-size:4.5pt}
        .user-equipment-card .table{font-size:4.3pt}
        .user-equipment-card .table thead th{padding:1mm .7mm;font-size:3.7pt}
        .user-equipment-card .table tbody td{padding:1mm .7mm}
        .user-equipment-card small{font-size:3.7pt}
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
<script>
    document.getElementById('downloadUserDetailJpg')?.addEventListener('click', async () => {
        const source = document.querySelector('.user-detail-page');
        const button = document.getElementById('downloadUserDetailJpg');
        if (!source || typeof html2canvas === 'undefined') return;

        button.disabled = true;
        button.innerHTML = '<i class="bi bi-hourglass-split"></i> Menyiapkan JPG...';
        const canvasHost = document.createElement('div');
        const clone = source.cloneNode(true);
        clone.querySelectorAll('.no-print').forEach(element => element.remove());
        canvasHost.style.cssText = 'position:fixed;left:-10000px;top:0;width:1100px;background:#f8fafc;z-index:-1;padding:24px;';
        clone.style.cssText = 'display:grid!important;width:1100px!important;max-width:none!important;height:auto!important;min-height:0!important;padding:24px!important;box-sizing:border-box!important;';
        document.body.appendChild(canvasHost);
        canvasHost.appendChild(clone);
        try {
            const canvas = await html2canvas(clone, { backgroundColor: '#f8fafc', scale: 2, useCORS: true });
            const link = document.createElement('a');
            link.download = 'detail-user-{{ str($user->name)->slug('-') }}.jpg';
            link.href = canvas.toDataURL('image/jpeg', 0.95);
            link.click();
        } finally {
            canvasHost.remove();
            button.disabled = false;
            button.innerHTML = '<i class="bi bi-filetype-jpg"></i> Unduh Detail JPG';
        }
    });
</script>
@endsection
