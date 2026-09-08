@extends('layouts.app')

@section('content')
<div class="container mt-4 maintenance-check-page">
    <div class="d-flex justify-content-between align-items-start mb-3"><div><div class="maintenance-eyebrow">IT Maintenance Operations</div><h2 class="mb-1">Pelaksanaan Checklist IT</h2><p class="text-muted mb-0">Dokumen hasil perawatan per Program, Bulan, dan Tahun.</p></div><a href="{{ route('maintenance-checklists.create') }}" class="btn btn-brand">Buat Checklist</a></div>
    <form method="GET" action="{{ route('maintenance-checklists.index') }}" class="row g-3 align-items-end mb-3 p-3 border rounded bg-light">
        <div class="col-lg-2 col-md-4"><label for="filter_year" class="form-label mb-1">Periode / Tahun</label><select id="filter_year" name="year" class="form-select"><option value="">Semua Tahun</option>@foreach ($availableYears as $year)<option value="{{ $year }}" {{ $selectedYear === (int) $year ? 'selected' : '' }}>{{ $year }}</option>@endforeach</select></div>
        <div class="col-lg-2 col-md-4"><label for="filter_month" class="form-label mb-1">Filter Bulan</label><select id="filter_month" name="month" class="form-select"><option value="">Semua Bulan</option>@foreach (['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $monthNumber => $monthName)<option value="{{ $monthNumber + 1 }}" {{ $selectedMonth === $monthNumber + 1 ? 'selected' : '' }}>{{ $monthName }}</option>@endforeach</select></div>
        <div class="col-lg-3 col-md-4"><label for="filter_program" class="form-label mb-1">Program Perawatan</label><select id="filter_program" name="checklist_item_id" class="form-select"><option value="">Semua Program</option>@foreach ($programOptions as $program)<option value="{{ $program->id }}" {{ $selectedProgram === $program->id ? 'selected' : '' }}>{{ $program->title }}</option>@endforeach</select></div>
        <div class="col-lg-3 col-md-4"><label for="filter_approval" class="form-label mb-1">Status Persetujuan</label><select id="filter_approval" name="approval" class="form-select"><option value="">Semua Status</option><option value="approved" {{ $selectedApproval === 'approved' ? 'selected' : '' }}>Disetujui</option><option value="pending" {{ $selectedApproval === 'pending' ? 'selected' : '' }}>Belum Disetujui</option></select></div>
        <div class="col-lg-2 col-md-4 d-flex gap-2"><button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-funnel"></i> Filter</button><a href="{{ route('maintenance-checklists.index') }}" class="btn btn-outline-secondary" title="Reset Filter"><i class="bi bi-arrow-counterclockwise"></i></a></div>
    </form>
    <div class="row g-3 mb-3"><div class="col-lg col-md-4"><div class="maintenance-stat total"><span>Total Dokumen</span><strong>{{ $summary['documents'] }}</strong><small>checklist tersimpan</small></div></div><div class="col-lg col-md-4"><div class="maintenance-stat ok"><span>Kondisi OK</span><strong>{{ $summary['ok'] }}</strong><small>hasil pemeriksaan sesuai</small></div></div><div class="col-lg col-md-4"><div class="maintenance-stat issue"><span>Perlu Tindak Lanjut</span><strong>{{ $summary['not_ok'] }}</strong><small>hasil NOT OK</small></div></div><div class="col-lg col-md-6"><div class="maintenance-stat scheduled"><span>Jadwal Bulanan Sudah Dicek</span><strong>{{ $scheduleProgress['completed'] }}</strong><small>dari {{ $scheduleProgress['scheduled'] }} program dan periode terjadwal</small></div></div><div class="col-lg col-md-6"><div class="maintenance-stat pending"><span>Jadwal Bulanan Belum Dicek</span><strong>{{ $scheduleProgress['pending'] }}</strong><small>program dan periode yang belum memiliki checklist</small></div></div></div>
    <div class="card maintenance-list">
        <div class="card-header d-flex flex-wrap gap-2 justify-content-between align-items-center">
            <span>Daftar Hasil Checklist</span>
            <div class="data-table-tools p-0">
                <div class="data-table-export">
                    <button type="button" class="btn btn-sm btn-outline-success" data-checklist-export="excel">Excel</button>
                    <button type="button" class="btn btn-sm btn-outline-danger" data-checklist-export="pdf">PDF</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-checklist-export="print">Print</button>
                    <a href="{{ route('maintenance-checklists.bulk-print', request()->query()) }}" class="btn btn-sm btn-brand"><i class="bi bi-printer"></i> Print Semua</a>
                </div>
                <form method="GET" action="{{ route('maintenance-checklists.index') }}" class="data-table-search-form" id="checklistSearchForm">
                    @if ($selectedYear)<input type="hidden" name="year" value="{{ $selectedYear }}">@endif
                    @if ($selectedMonth)<input type="hidden" name="month" value="{{ $selectedMonth }}">@endif
                    @if ($selectedProgram)<input type="hidden" name="checklist_item_id" value="{{ $selectedProgram }}">@endif
                    @if ($selectedApproval)<input type="hidden" name="approval" value="{{ $selectedApproval }}">@endif
                    <input type="search" name="search" value="{{ $search ?? '' }}" autocomplete="off" placeholder="Cari di semua data..." aria-label="Cari di semua data">
                </form>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0 no-table-tools">
                    <thead><tr><th>No.</th><th>Program Perawatan</th><th>Bulan - Tahun</th><th>Tanggal Jadwal</th><th>Kelengkapan Peralatan</th><th>Kondisi</th><th>Pelapor</th><th>Keterangan</th><th>Aksi</th></tr></thead>
                    <tbody>
                        @php $rowNumber = ($checklists->currentPage() - 1) * 50; $monthNamesShort = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']; @endphp
                        @forelse ($checklists as $checklist)
                            <tr>
                                <td>{{ ++$rowNumber }}</td>
                                <td><span class="program-dot" style="--program-color:{{ $checklist->checklistItem->schedule_color }}"></span><strong>{{ $checklist->checklistItem->title ?? '-' }}</strong></td>
                                <td>{{ $monthNamesShort[$checklist->month] }} {{ $checklist->year }}</td>
                                <td>{{ count($checklist->scheduled_dates) ? implode(', ', $checklist->scheduled_dates) : '-' }}</td>
                                <td>
                                    @if ($checklist->scheduled_equipment_count === 0)
                                        <span class="badge bg-secondary">Tidak ada jadwal bulanan</span>
                                    @elseif ($checklist->is_complete)
                                        <span class="badge bg-success">Lengkap</span> {{ $checklist->checked_scheduled_equipment_count }}/{{ $checklist->scheduled_equipment_count }} peralatan
                                    @else
                                        <span class="badge bg-warning text-dark">Belum Lengkap</span> {{ $checklist->checked_scheduled_equipment_count }}/{{ $checklist->scheduled_equipment_count }} peralatan
                                    @endif
                                </td>
                                <td>
                                    @if ($checklist->overall_result === 'ok')
                                        <span class="result-badge result-ok">OK</span>
                                    @elseif ($checklist->overall_result === 'not_ok')
                                        <span class="result-badge result-not_ok">NOT OK</span>
                                    @else
                                        <span class="text-muted">Menunggu lengkap</span>
                                    @endif
                                </td>
                                <td>{{ $checklist->reported_by ?? '-' }}</td>
                                <td>{{ $checklist->notes ?? '-' }} @if ($checklist->acknowledged_at)<small class="d-block text-success">Disetujui</small>@else<small class="d-block text-warning">Belum disetujui</small>@endif</td>
                                <td class="text-nowrap"><a class="btn btn-sm btn-outline-secondary" href="{{ route('maintenance-checklists.show', $checklist) }}">Detail</a><a class="btn btn-sm btn-outline-primary" href="{{ route('maintenance-checklists.edit', $checklist) }}">Edit</a>@if (!$checklist->acknowledged_at && auth()->user()->isMaster())<form method="POST" action="{{ route('maintenance-checklists.approve', $checklist) }}" class="d-inline">@csrf<button type="submit" class="btn btn-sm btn-success">Setujui</button></form>@endif<form method="POST" action="{{ route('maintenance-checklists.destroy', [$checklist, 'year' => $selectedYear, 'month' => $selectedMonth, 'checklist_item_id' => $selectedProgram, 'approval' => $selectedApproval, 'search' => $search]) }}" class="d-inline" onsubmit="return confirm('Hapus checklist ini? Jadwal pada periode ini akan ditandai perlu checklist ulang.')">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button></form></td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="text-center text-muted py-4">Belum ada dokumen checklist perawatan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="table-pagination">{{ $checklists->links() }}</div>
</div>
<style>.maintenance-eyebrow{color:#0b5ea8;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em}.maintenance-stat{padding:15px 17px;background:#fff;border:1px solid #dbe5ef;border-top:4px solid #64748b}.maintenance-stat span,.maintenance-stat small{display:block;color:#64748b;font-size:.76rem}.maintenance-stat strong{display:block;font-size:1.65rem}.maintenance-stat.total{border-top-color:#0b5ea8}.maintenance-stat.ok{border-top-color:#159957}.maintenance-stat.issue{border-top-color:#dc2626}.maintenance-stat.scheduled{border-top-color:#0891b2}.maintenance-stat.pending{border-top-color:#d97706}.maintenance-list{border:1px solid #dbe5ef}.maintenance-list .card-header{background:#f8fafc;font-weight:700}.program-dot { display:inline-block; width:9px; height:9px; border-radius:50%; margin-right:6px; background:var(--program-color); }.checklist-program-row td { padding:9px 12px !important; background:var(--program-tint); color:var(--program-color); }.checklist-period-row td { padding:6px 12px !important; background:#f8fafc; border-top:1px dashed #dbe5ef; border-bottom:1px dashed #dbe5ef; font-size:.82rem; }.checklist-period-row form { margin:0; }.equipment-cell { padding-left:24px !important; }.equipment-cell small { display:block; color:#64748b; }.result-badge { display:inline-block; padding:4px 8px; border-radius:3px; font-size:.75rem; font-weight:700; }.result-ok { background:#dcfce7; color:#166534; }.result-not_ok { background:#fee2e2; color:#991b1b; }</style>
<script>
    (function () {
        const form = document.getElementById('checklistSearchForm');
        const input = form?.querySelector('input[name="search"]');
        const table = document.querySelector('.maintenance-list table');
        if (input) {
            let timer;
            input.addEventListener('input', () => {
                clearTimeout(timer);
                timer = setTimeout(() => form.submit(), 450);
            });
            if (input.value) {
                input.focus();
                try { input.setSelectionRange(input.value.length, input.value.length); } catch (error) { /* caret tidak didukung */ }
            }
        }
        document.querySelectorAll('[data-checklist-export]').forEach(button => {
            button.addEventListener('click', () => {
                const params = new URLSearchParams(window.location.search);
                if (document.querySelector('.table-pagination .pagination') && params.get('per_page') !== 'all') {
                    params.set('per_page', 'all');
                    params.delete('page');
                    params.set('export', button.dataset.checklistExport);
                    window.location.href = window.location.pathname + '?' + params.toString();
                    return;
                }
                window.ItTableExport?.(button.dataset.checklistExport, table);
            });
        });
        const pendingExport = new URLSearchParams(window.location.search).get('export');
        if (pendingExport && table) {
            const params = new URLSearchParams(window.location.search);
            params.delete('export');
            const query = params.toString();
            window.history.replaceState({}, '', window.location.pathname + (query ? '?' + query : ''));
            setTimeout(() => window.ItTableExport?.(pendingExport, table), 300);
        }
    })();
</script>
<div class="checklist-bulk-modal" id="checklistBulkModal" hidden>
    <div class="checklist-bulk-dialog" role="dialog" aria-modal="true" aria-labelledby="checklistBulkTitle">
        <div id="checklistBulkProgress"><div class="checklist-bulk-icon"><i class="bi bi-file-earmark-zip"></i></div><h2 id="checklistBulkTitle">Menyiapkan PDF Checklist</h2><p id="checklistBulkStatus">Mengambil checklist sesuai filter...</p><div class="checklist-bulk-track"><div id="checklistBulkBar"></div></div><div class="checklist-bulk-meta"><span id="checklistBulkCount">0 dokumen</span><strong id="checklistBulkPercent">0%</strong></div></div>
        <div id="checklistBulkSuccess" hidden><div class="checklist-bulk-success-icon"><i class="bi bi-check2"></i></div><h2>Download Berhasil</h2><p>Semua PDF checklist sudah dikemas dalam satu ZIP.</p><button type="button" class="btn btn-brand" id="closeChecklistBulkModal"><i class="bi bi-check-lg"></i>OK</button></div>
    </div>
</div>
<style>
    .checklist-bulk-modal{position:fixed;z-index:1090;inset:0;display:grid;place-items:center;padding:20px;background:rgba(15,23,42,.42);backdrop-filter:blur(5px)}.checklist-bulk-modal[hidden],#checklistBulkProgress[hidden],#checklistBulkSuccess[hidden]{display:none!important}.checklist-bulk-dialog{width:min(460px,calc(100vw - 32px));padding:34px 36px;border:1px solid #dbe5ef;border-radius:22px;background:#fff;box-shadow:0 24px 70px rgba(15,23,42,.25);text-align:center}.checklist-bulk-icon,.checklist-bulk-success-icon{width:64px;height:64px;margin:0 auto 17px;border-radius:19px;background:#eef3ff;color:#2161f5;display:grid;place-items:center;font-size:28px;box-shadow:0 10px 22px rgba(33,97,245,.16);animation:checklistBulkFloat 2.4s ease-in-out infinite}.checklist-bulk-dialog h2{margin:0 0 8px;color:#18243d;font-size:1.25rem}.checklist-bulk-dialog p{min-height:20px;margin:0 0 23px;color:#758198;font-size:.8rem}.checklist-bulk-track{height:12px;padding:3px;border-radius:99px;background:#e7eaf4;overflow:hidden}.checklist-bulk-track>div{width:0;height:100%;border-radius:99px;background:#2161f5;transition:width .25s ease;position:relative;overflow:hidden}.checklist-bulk-track>div:after{position:absolute;inset:0;background:linear-gradient(110deg,transparent 25%,rgba(255,255,255,.65) 50%,transparent 75%);content:'';animation:checklistBulkShine 1.3s linear infinite}.checklist-bulk-meta{display:flex;justify-content:space-between;margin-top:11px;color:#8792a7;font-size:.75rem;font-weight:700}.checklist-bulk-meta strong{color:#2161f5}.checklist-bulk-success-icon{background:#dcfce7;color:#16a34a;animation:none}.checklist-bulk-dialog .btn{min-width:110px}.bulk-pdf-sheet{position:fixed;left:-10000px;top:0;width:1120px;padding:34px;background:#fff;font-family:'Segoe UI',Arial,sans-serif;color:#17324d}.bulk-pdf-paper{border:1px solid #cbd5e1;border-radius:6px;overflow:hidden}.bulk-pdf-head{display:flex;justify-content:space-between;align-items:center;padding:16px 20px;background:linear-gradient(120deg,#0b5ea8,#1f8fe0);color:#fff}.bulk-pdf-brand{display:flex;align-items:center;gap:12px}.bulk-pdf-logo{width:52px;height:52px;object-fit:contain;padding:2px;border-radius:8px;background:#fff}.bulk-pdf-brand strong,.bulk-pdf-brand small,.bulk-pdf-form{display:block}.bulk-pdf-brand strong{font-size:16px;letter-spacing:.04em}.bulk-pdf-brand small,.bulk-pdf-form{font-size:11px}.bulk-pdf-form{margin-top:4px}.bulk-pdf-id{text-align:right}.bulk-pdf-id strong{display:block;font-size:18px}.bulk-pdf-id span{display:inline-block;margin-top:5px;padding:4px 9px;border-radius:12px;background:#dcfce7;color:#166534;font-size:10px;font-weight:700}.bulk-pdf-meta{display:grid;grid-template-columns:repeat(5,1fr);gap:1px;background:#dbe5ef}.bulk-pdf-meta div{min-height:46px;padding:9px 12px;background:#f8fafc}.bulk-pdf-meta span{display:block;color:#64748b;font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.05em}.bulk-pdf-meta strong{display:block;margin-top:3px;font-size:11px}.bulk-pdf-table{width:100%;border-collapse:collapse}.bulk-pdf-table th,.bulk-pdf-table td{padding:8px 10px;border:1px solid #dbe5ef;font-size:11px;text-align:left}.bulk-pdf-table th{background:#fff3e6}.bulk-pdf-table td:first-child{width:38px}.bulk-pdf-table td small{display:block;color:#64748b;font-size:9px}.bulk-pdf-note{margin:14px 18px;padding:10px 12px;border-left:4px solid #0b5ea8;background:#f8fafc;font-size:10px}.bulk-pdf-note strong{display:block;margin-bottom:4px;color:#64748b;font-size:9px;text-transform:uppercase}.bulk-pdf-sign{display:grid;grid-template-columns:1fr 1fr;gap:35px;padding:16px 24px 12px;border-top:1px solid #dbe5ef;background:#fbfdff}.bulk-pdf-sign div{font-size:10px}.bulk-pdf-sign span{display:block;color:#64748b;font-weight:700;text-transform:uppercase}.bulk-pdf-sign b{display:block;margin-top:32px;padding-top:4px;border-top:1px solid #94a3b8;font-size:11px}.bulk-pdf-footer{grid-column:1 / -1;color:#94a3b8;font-size:9px;text-align:right}@keyframes checklistBulkShine{to{transform:translateX(220%)}}@keyframes checklistBulkFloat{50%{transform:translateY(-5px)}}
</style>
<script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jspdf-autotable@3.8.2/dist/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jszip@3.10.1/dist/jszip.min.js"></script>
<script>
(() => {
    const button = document.getElementById('bulkChecklistPdfButton'); const modal = document.getElementById('checklistBulkModal'); const progress = document.getElementById('checklistBulkProgress'); const success = document.getElementById('checklistBulkSuccess'); const status = document.getElementById('checklistBulkStatus'); const bar = document.getElementById('checklistBulkBar'); const count = document.getElementById('checklistBulkCount'); const percent = document.getElementById('checklistBulkPercent'); const wait = milliseconds => new Promise(resolve => setTimeout(resolve, milliseconds)); const safeName = (value, fallback) => (value || fallback).toString().trim().replace(/[\\/:*?"<>|]+/g, '-').replace(/\s+/g, '-'); const escapeHtml = value => String(value ?? '-').replace(/[&<>"']/g, character => ({ '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#039;' }[character]));
    const closeModal = () => { modal.hidden = true; progress.hidden = false; success.hidden = true; };
    async function startBulkPdf() {
        modal.hidden = false; button.disabled = true;
        try {
            const params = new URLSearchParams(window.location.search); params.delete('page'); params.delete('per_page');
            const response = await fetch('{{ route('maintenance-checklists.bulk-pdf') }}?' + params.toString(), { headers: { Accept: 'application/json' } }); if (!response.ok) throw new Error('Data checklist tidak dapat dimuat.');
            const documents = (await response.json()).documents || []; if (!documents.length) throw new Error('Tidak ada checklist sesuai filter bulan dan tahun.');
            const zip = new JSZip(); const names = new Set(); const { jsPDF } = window.jspdf;
            for (let index = 0; index < documents.length; index++) {
                const item = documents[index]; const okCount = item.entries.filter(entry => entry.result === 'OK').length; const notOkCount = item.entries.filter(entry => entry.result === 'NOT OK').length; const entriesPerPage = 35; const entryPages = item.entries.length ? Array.from({ length: Math.ceil(item.entries.length / entriesPerPage) }, (_, pageIndex) => item.entries.slice(pageIndex * entriesPerPage, (pageIndex + 1) * entriesPerPage)) : [[]]; const pdf = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'a4' }); const originalAddImage = pdf.addImage.bind(pdf); pdf.addImage = (image, format, x, y, width, height) => { const pageWidth = pdf.internal.pageSize.getWidth(); const maxPageWidth = pageWidth - 20; if (width > maxPageWidth) { const ratio = height / width; width = maxPageWidth; height = width * ratio; } return originalAddImage(image, format, (pageWidth - width) / 2, y, width, height); };
                for (let pageIndex = 0; pageIndex < entryPages.length; pageIndex++) {
                    const pageEntries = entryPages[pageIndex]; const sheet = document.createElement('div'); sheet.className = 'bulk-pdf-sheet'; sheet.innerHTML = `<div class="bulk-pdf-paper"><header class="bulk-pdf-head"><div class="bulk-pdf-brand"><img class="bulk-pdf-logo" src="{{ asset('images/logo-mgm.svg') }}"><div><strong>PT MULIA GRAND MANUFACTURE</strong><small>Checklist Perawatan IT</small><span class="bulk-pdf-form">No. Form : FR-IT-03</span><span class="bulk-pdf-form">Revisi : 00</span></div></div><div class="bulk-pdf-id"><strong>${escapeHtml(item.program)}</strong><span>${escapeHtml(item.month)} ${item.year} &nbsp; ${item.approved ? 'Approved' : 'Pending'}</span></div></header><section class="bulk-pdf-meta"><div><span>Program Perawatan</span><strong>${escapeHtml(item.program)}</strong></div><div><span>Periode</span><strong>${escapeHtml(item.month)} ${item.year}</strong></div><div><span>Tanggal Input Checklist</span><strong>${escapeHtml(item.checkedAt)}</strong></div><div><span>Total Peralatan</span><strong>${item.entries.length}</strong></div><div><span>Hasil</span><strong>OK ${okCount} / NOT OK ${notOkCount}</strong></div></section><table class="bulk-pdf-table"><thead><tr><th>No.</th><th>Nama Peralatan</th><th>Tanggal Jadwal</th><th>Check Point</th><th>Kondisi</th><th>Keterangan</th></tr></thead><tbody>${pageEntries.map((entry, row) => { const number = pageIndex * entriesPerPage + row + 1; return `<tr><td>${number}</td><td>${escapeHtml(entry.name)}<small>${escapeHtml(entry.assetTag)}</small></td><td>${escapeHtml(entry.dates)}</td><td>${escapeHtml(item.program)}</td><td>${escapeHtml(entry.result)}</td><td>${escapeHtml(entry.remarks)}</td></tr>`; }).join('')}</tbody></table>${pageIndex === entryPages.length - 1 ? `<div class="bulk-pdf-note"><strong>Catatan Dokumen</strong>${escapeHtml(item.notes)}</div><footer class="bulk-pdf-sign"><div><span>Dibuat Oleh</span><b>${escapeHtml(item.reportedBy)}</b></div><div><span>Mengetahui</span><b>${escapeHtml(item.approvedBy)}</b></div><small class="bulk-pdf-footer">Halaman ${pageIndex + 1} dari ${entryPages.length} | No. Form : FR-IT-03 | Revisi : 00</small></footer>` : `<div class="bulk-pdf-page-number">Halaman ${pageIndex + 1} dari ${entryPages.length}</div>`}</div>`; document.body.appendChild(sheet); await wait(80); const canvas = await html2canvas(sheet, { backgroundColor: '#fff', scale: 2, useCORS: true }); const maxWidth = 277; const maxHeight = 190; const imageRatio = canvas.width / canvas.height; let imageWidth = maxWidth; let imageHeight = imageWidth / imageRatio; if (imageHeight > maxHeight) { imageHeight = maxHeight; imageWidth = imageHeight * imageRatio; } if (pageIndex > 0) pdf.addPage(); pdf.addImage(canvas.toDataURL('image/jpeg', .95), 'JPEG', (297 - imageWidth) / 2, 10, imageWidth, imageHeight); sheet.remove();
                }
                pdf.setProperties({ title: `${item.program} ${item.month} ${item.year}` });
                const baseName = safeName(`${item.program}-${item.month}-${item.year}`, `checklist-${item.id}`); let fileName = baseName; let suffix = 2; while (names.has(fileName)) fileName = `${baseName}-${suffix++}`; names.add(fileName); zip.file(`${fileName}.pdf`, pdf.output('arraybuffer'));
                const value = Math.round(((index + 1) / documents.length) * 100); bar.style.width = `${value}%`; count.textContent = `${index + 1} dari ${documents.length} dokumen`; percent.textContent = `${value}%`; status.textContent = `Membuat PDF ${index + 1} dari ${documents.length}...`; await wait(20);
            }
            const blob = await zip.generateAsync({ type: 'blob' }); const link = document.createElement('a'); link.href = URL.createObjectURL(blob); link.download = 'semua-checklist-peralatan.zip'; link.click(); progress.hidden = true; success.hidden = false;
        } catch (error) { status.textContent = error.message || 'PDF checklist tidak dapat dibuat.'; button.disabled = false; }
    }
    button?.addEventListener('click', startBulkPdf); document.getElementById('closeChecklistBulkModal')?.addEventListener('click', () => { closeModal(); button.disabled = false; }); modal?.addEventListener('click', event => { if (event.target === modal && !success.hidden) { closeModal(); button.disabled = false; } });
})();
</script>
@endsection
