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
@endsection
