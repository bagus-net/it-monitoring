@extends('layouts.app')

@section('content')
<div class="container schedule-page">
    <div class="card card-colorful mb-3">
        <div class="card-header">Cetak Jadwal Perawatan IT Tahunan</div>
        <div class="card-body">
            <form method="GET" action="{{ route('maintenances.schedules') }}">
                <div class="row g-3 align-items-end mb-2">
                    <div class="col-md-3">
                        <label class="form-label">Pilih Periode (Tahun)</label>
                        <select name="print_year" class="form-select">
                            @if(!$availableYears->contains($printYear))
                                <option value="{{ $printYear }}" selected>{{ $printYear }}</option>
                            @endif
                            @foreach ($availableYears as $yearOption)
                                <option value="{{ $yearOption }}" {{ (int) $yearOption === $printYear ? 'selected' : '' }}>{{ $yearOption }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-9 d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-outline-secondary" id="toggleFeaturePicker"><i class="bi bi-sliders"></i> Opsi Fitur / Program</button>
                        <button type="submit" class="btn btn-brand">Tampilkan</button>
                        <button type="button" id="printScheduleButton" class="btn btn-outline-primary"><i class="bi bi-printer"></i> Cetak Jadwal</button>
                    </div>
                </div>
                <div id="featurePicker" class="border rounded p-3" hidden>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong>Fitur / Program yang diperlukan</strong>
                        <span>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="selectAllFeatures">Pilih Semua</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="clearAllFeatures">Kosongkan</button>
                        </span>
                    </div>
                    @foreach ($allItems->groupBy(fn ($i) => $i->category ?: 'Lainnya') as $category => $categoryItems)
                        <div class="mb-2">
                            <strong style="color:{{ $categoryItems->first()->schedule_color }}">{{ $category }}</strong>
                            <div class="d-flex flex-wrap gap-3 mt-1">
                                @foreach ($categoryItems as $featureItem)
                                    <label class="form-check">
                                        <input class="form-check-input feature-checkbox" type="checkbox" name="print_items[]" value="{{ $featureItem->id }}" {{ in_array($featureItem->id, $selectedItemIds) ? 'checked' : '' }}>
                                        <span class="form-check-label">{{ $featureItem->title }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </form>
        </div>
    </div>

    <div class="card card-colorful">
        <div class="card-header">Jadwal Perawatan</div>
        <div class="card-body">
            <a href="{{ route('maintenances.create') }}" class="btn btn-brand mb-3">Tambah Jadwal</a>
            <div class="table-responsive">
            <table class="table mt-3">
                <thead><tr><th>Program Perawatan</th><th>Frekuensi</th><th>Peralatan</th><th>Aksi</th></tr></thead>
                <tbody>
                @forelse($groups as $g)
                    <tr>
                        <td><span class="program-dot" style="background-color:{{ $g['item']->schedule_color }}"></span>{{ $g['item']->title }}</td>
                        <td>{{ implode(', ', $g['frequencies']) }}</td>
                        <td>
                            @if(in_array(null, $g['equipment_ids'])) Semua @else {{ count(array_filter($g['equipment_ids'])) }} items @endif
                        </td>
                        <td>
                            <a href="{{ route('maintenances.schedules.show', $g['item']->id) }}" class="btn btn-sm btn-outline-primary">View</a>
                            <a href="{{ route('maintenances.schedules.edit', $g['item']->id) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                            <form method="POST" action="{{ route('maintenances.schedules.destroy', $g['item']->id) }}" style="display:inline" onsubmit="return confirm('Hapus semua jadwal untuk program ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4">Belum ada jadwal.</td></tr>
                @endforelse
                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>

<div id="annualPrintSheet" hidden>
    <div class="sheet">
        <table class="sheet-head-table">
            <tr>
                <td class="brand">
                    <img src="{{ asset('images/logo-mgm.svg') }}" alt="Logo PT Mulia Grand Manufacture">
                    <strong>PT. MULIA GRAND MANUFACTURE</strong>
                </td>
                <td class="title">
                    <div>JADWAL PERAWATAN IT</div>
                    <div>TAHUN : {{ $printYear }}</div>
                    <small>Waktu Perawatan</small>
                </td>
                <td class="formno">No. Form : FR-IT-02<br>Revisi : 01</td>
            </tr>
        </table>
        <table class="sheet-grid-table">
            <thead>
                <tr>
                    <th rowspan="2" class="prog-col">Program Perawatan</th>
                    @foreach ($monthNamesShort as $monthLabel)
                        <th colspan="4">{{ $monthLabel }}</th>
                    @endforeach
                </tr>
                <tr>
                    @foreach ($monthNamesShort as $monthLabel)
                        @for ($w = 1; $w <= 4; $w++)<th>{{ $w }}</th>@endfor
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($printCategories as $category => $categoryItems)
                    <tr class="category-row"><td colspan="49">{{ $category }}</td></tr>
                    @foreach ($categoryItems as $printItem)
                        <tr>
                            <td class="prog-col">{{ $printItem->title }}</td>
                            @for ($m = 1; $m <= 12; $m++)
                                @for ($w = 1; $w <= 4; $w++)
                                    <td @if(!empty($weeksByItem[$printItem->id][$m][$w])) style="background-color:{{ $printItem->schedule_color }};" @endif></td>
                                @endfor
                            @endfor
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
        <div class="sheet-sign-row">
            <div>
                <span>Dibuat oleh</span>
                <div class="sign-space">
                    @if($signatures['reporter']?->signature_path)
                        <img class="signature-image" src="{{ asset('storage/' . $signatures['reporter']->signature_path) }}" alt="Tanda tangan dibuat oleh">
                    @endif
                </div>
                <strong>{{ $signatureNames['reporter'] ?? 'Admin IT / Bagus' }}</strong>
            </div>
            <div>
                <span>Mengetahui</span>
                <div class="sign-space">
                    @if($signatures['acknowledger']?->signature_path)
                        <img class="signature-image" src="{{ asset('storage/' . $signatures['acknowledger']->signature_path) }}" alt="Tanda tangan mengetahui">
                    @endif
                </div>
                <strong>{{ $signatureNames['acknowledger'] ?? 'Arifin' }}</strong>
            </div>
        </div>
    </div>
</div>
<script type="text/css" id="annualPrintCss">
    @page{size:A4 landscape;margin:8mm}
    *{box-sizing:border-box}
    body{margin:0;font-family:'Segoe UI',Arial,Helvetica,sans-serif;color:#17324d;background:#fff}
    .sheet{border:1px solid #94a3b8}
    .sheet-head-table{width:100%;border-collapse:collapse;border-bottom:2px solid #17324d}
    .sheet-head-table td{padding:6px 10px;vertical-align:middle}
    .sheet-head-table .brand{display:flex;align-items:center;gap:8px;font-size:11px;font-weight:700;width:1%;white-space:nowrap}
    .sheet-head-table .brand img{width:30px;height:30px;object-fit:contain}
    .sheet-head-table .title{text-align:center;font-size:12px;font-weight:800;letter-spacing:.03em}
    .sheet-head-table .title small{display:block;font-size:9px;font-weight:400;color:#64748b}
    .sheet-head-table .formno{text-align:right;font-size:8.5px;color:#475569;white-space:nowrap}
    .sheet-grid-table{width:100%;border-collapse:collapse}
    .sheet-grid-table th,.sheet-grid-table td{border:1px solid #94a3b8;text-align:center;padding:2px;font-size:7px}
    .sheet-grid-table th{background:#f1f5f9;font-size:7.2px}
    .sheet-grid-table .prog-col{text-align:left;white-space:nowrap;padding:2px 6px;font-size:7.5px}
    .sheet-grid-table td.prog-col{font-weight:600}
    .sheet-grid-table td:not(.prog-col){width:14px;height:14px}
    .category-row td{background:#e2e8f0;font-weight:800;text-align:left;padding:3px 6px;font-size:8px}
    .sheet-sign-row{display:flex;justify-content:flex-end;gap:60px;margin-top:26px;padding:0 20px}
    .sheet-sign-row div{text-align:center;font-size:10px}
    .sheet-sign-row span{display:block;font-weight:700;margin-bottom:6px}
    .sheet-sign-row .sign-space{position:relative;height:60px;width:130px;border-bottom:1px solid #17324d;margin-top:4px;display:flex;align-items:flex-end;justify-content:center}
    .sheet-sign-row .signature-image{display:block;max-height:46px;max-width:120px;object-fit:contain}
    .sheet-sign-row strong{display:block;margin-top:6px;font-size:9.5px}
</script>
<script>
    (function () {
        const toggleBtn = document.getElementById('toggleFeaturePicker');
        const picker = document.getElementById('featurePicker');
        toggleBtn?.addEventListener('click', () => { picker.hidden = !picker.hidden; });
        document.getElementById('selectAllFeatures')?.addEventListener('click', () => {
            document.querySelectorAll('.feature-checkbox').forEach(cb => cb.checked = true);
        });
        document.getElementById('clearAllFeatures')?.addEventListener('click', () => {
            document.querySelectorAll('.feature-checkbox').forEach(cb => cb.checked = false);
        });

        document.getElementById('printScheduleButton')?.addEventListener('click', () => {
            const sheet = document.getElementById('annualPrintSheet');
            const css = document.getElementById('annualPrintCss').textContent;
            const frame = document.createElement('iframe');
            frame.style.cssText = 'position:fixed;right:0;bottom:0;width:0;height:0;border:0';
            document.body.appendChild(frame);
            const doc = frame.contentWindow.document;
            doc.open();
            doc.write('<html><head><meta charset="utf-8"><title>Jadwal Perawatan IT {{ $printYear }}</title><style>' + css + '</style></head><body>' + sheet.innerHTML + '</body></html>');
            doc.close();
            const images = Array.from(doc.images);
            const start = () => { frame.contentWindow.focus(); frame.contentWindow.print(); setTimeout(() => frame.remove(), 1500); };
            if (!images.length) return start();
            let loaded = 0;
            images.forEach(image => {
                const done = () => { if (++loaded === images.length) start(); };
                image.complete ? done() : (image.onload = image.onerror = done);
            });
        });
    })();
</script>
<style>.program-dot { display:inline-block; width:10px; height:10px; border-radius:50%; margin-right:7px; }</style>
@endsection
