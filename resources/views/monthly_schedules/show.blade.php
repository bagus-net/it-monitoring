@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4 monthly-schedule-page">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h2 style="color:{{ $checklistItem->schedule_color }}; margin-bottom:4px;">📋 {{ $checklistItem->title }}</h2>
                    <p class="text-muted mb-0">Detail Jadwal Bulanan - Tahun {{ $year }}</p>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <button type="button" id="printMonthlyScheduleButton" class="btn btn-outline-primary">
                        <i class="bi bi-printer"></i> Cetak
                    </button>
                    <a href="{{ route('monthly_schedules.select_months', [$checklistItem->id, $year]) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    <a href="{{ route('monthly_schedules.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>

            <div class="alert alert-light border mb-3">
                <strong>Jadwal ini berlaku di bulan:</strong>
                @if (count($scheduledMonthLabels))
                    {{ implode(', ', $scheduledMonthLabels) }}
                @else
                    <span class="text-muted">Belum ditentukan</span>
                @endif
            </div>

            @if ($byEquipment->count())
                <div class="table-responsive">
                    <table class="table table-bordered align-middle schedule-detail-table">
                        <thead class="table-warning">
                            <tr>
                                <th style="min-width: 180px;">Peralatan</th>
                                @foreach ($monthNames as $mNum => $mName)
                                    <th class="text-center">{{ $mName }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($byEquipment as $row)
                                <tr>
                                    <td>
                                        <i class="bi bi-hdd text-muted"></i>
                                        <strong>{{ $row['equipment']->name ?? 'N/A' }}</strong>
                                    </td>
                                    @foreach ($monthNames as $mNum => $mName)
                                        @php $monthSchedule = $row['months']->get($mNum); @endphp
                                        <td class="text-center">
                                            @if ($monthSchedule && count($monthSchedule->dates))
                                                <div class="date-badges">
                                                    @foreach ($monthSchedule->dates as $d)
                                                        <span class="date-badge" style="background-color:{{ $checklistItem->schedule_color }}">{{ $d }}</span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Belum ada tanggal yang dijadwalkan untuk program ini.
                </div>
            @endif
        </div>
    </div>
</div>

<div id="monthlyPrintSheet" hidden>
    <div class="monthly-sheet">
        <table class="monthly-head-table">
            <tr>
                <td class="monthly-brand">
                    <img src="{{ asset('images/logo-mgm.svg') }}" alt="Logo PT Mulia Grand Manufacture">
                    <div>
                        <strong>PT. MULIA GRAND MANUFACTURE</strong>
                        <small>Jadwal Perawatan IT</small>
                    </div>
                </td>
                <td class="monthly-title">
                    <div>JADWAL PERAWATAN IT</div>
                    <div>TAHUN : {{ $year }}</div>
                    <small>Waktu Perawatan</small>
                </td>
                <td class="monthly-formno">No. Form : FR-IT-02<br>Revisi : 01</td>
            </tr>
        </table>

        <div class="monthly-meta-row">
            <div><span>Program Perawatan</span><strong>{{ $checklistItem->title }}</strong></div>
            <div><span>Jadwal di bulan</span><strong>{{ count($scheduledMonthLabels) ? implode(', ', $scheduledMonthLabels) : 'Belum ditentukan' }}</strong></div>
        </div>

        <table class="monthly-grid-table">
            <thead>
                <tr>
                    <th class="monthly-program-col">Program Perawatan</th>
                    @foreach ($monthNames as $mNum => $mName)
                        <th>{{ $mName }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($byEquipment as $row)
                    <tr>
                        <td class="monthly-program-col">{{ $row['equipment']->name ?? 'N/A' }}</td>
                        @foreach ($monthNames as $mNum => $mName)
                            @php $monthSchedule = $row['months']->get($mNum); $days = $monthSchedule ? $monthSchedule->dates ?? [] : []; @endphp
                            <td class="monthly-cell">
                                @if ($days)
                                    <div class="monthly-date-stack">@foreach ($days as $day)<span>{{ $day }}</span>@endforeach</div>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="monthly-sign-row">
            <div>
                <span>Dibuat oleh,</span>
                <div class="monthly-sign-space">
                    @if($signatures['reporter']?->signature_path)
                        <img src="{{ asset('storage/' . $signatures['reporter']->signature_path) }}" alt="Tanda tangan dibuat oleh">
                    @endif
                </div>
                <strong class="monthly-sign-name">{{ $signatureNames['reporter'] }}</strong>
            </div>
            <div>
                <span>Mengetahui,</span>
                <div class="monthly-sign-space">
                    @if($signatures['acknowledger']?->signature_path)
                        <img src="{{ asset('storage/' . $signatures['acknowledger']->signature_path) }}" alt="Tanda tangan mengetahui">
                    @endif
                </div>
                <strong class="monthly-sign-name">{{ $signatureNames['acknowledger'] }}</strong>
            </div>
        </div>
    </div>
</div>

<script type="text/css" id="monthlyPrintCss">
    @page{size:A4 landscape;margin:10mm}
    *{box-sizing:border-box}
    body{margin:0;font-family:'Segoe UI',Arial,Helvetica,sans-serif;color:#17324d;background:#fff}
    .monthly-sheet{border:1px solid #94a3b8;background:#fff}
    .monthly-head-table{width:100%;border-collapse:collapse;border-bottom:2px solid #17324d}
    .monthly-head-table td{padding:7px 10px;vertical-align:middle}
    .monthly-brand{display:flex;align-items:center;gap:8px;font-size:11px;font-weight:700;width:1%;white-space:nowrap}
    .monthly-brand img{width:32px;height:32px;object-fit:contain}
    .monthly-title{text-align:center;font-size:12px;font-weight:800;letter-spacing:.03em}
    .monthly-title small{display:block;font-size:9px;font-weight:400;color:#64748b}
    .monthly-formno{text-align:right;font-size:8.5px;color:#475569;white-space:nowrap}
    .monthly-meta-row{display:grid;grid-template-columns:repeat(2,1fr);gap:12px;padding:10px 12px;border-bottom:1px solid #dbe5ef;background:#f8fafc}
    .monthly-meta-row div{padding:4px 0}
    .monthly-meta-row span{display:block;font-size:8.5px;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:#64748b}
    .monthly-meta-row strong{display:block;margin-top:4px;font-size:10.5px}
    .monthly-grid-table{width:100%;border-collapse:collapse}
    .monthly-grid-table th,.monthly-grid-table td{border:1px solid #94a3b8;text-align:center;padding:3px 2px;font-size:7px;vertical-align:top}
    .monthly-grid-table th{background:#f1f5f9;font-size:7.2px}
    .monthly-grid-table .monthly-program-col{text-align:left;white-space:nowrap;padding:2px 6px;font-size:7.5px}
    .monthly-cell{width:50px;height:22px}
    .monthly-date-stack{display:flex;flex-wrap:wrap;gap:2px;justify-content:center;align-items:center}
    .monthly-date-stack span{display:inline-flex;align-items:center;justify-content:center;min-width:10px;height:10px;padding:0 1px;border-radius:3px;background:#dbeafe;font-size:6px;font-weight:700}
    .monthly-sign-row{display:flex;justify-content:flex-end;gap:60px;margin-top:20px;padding:0 20px 8px}
    .monthly-sign-row div{text-align:center;font-size:10px}
    .monthly-sign-row span{display:block;font-weight:700;margin-bottom:6px}
    .monthly-sign-space{height:55px;width:130px;border-bottom:1px solid #17324d;margin-top:4px;display:flex;align-items:flex-end;justify-content:center}
    .monthly-sign-space img{max-height:50px;max-width:125px;object-fit:contain}
    .monthly-sign-name{display:block;margin-top:4px;font-size:10px;text-transform:capitalize}
</script>
<script>
    document.getElementById('printMonthlyScheduleButton')?.addEventListener('click', () => {
        const sheet = document.getElementById('monthlyPrintSheet');
        const css = document.getElementById('monthlyPrintCss').textContent;
        const frame = document.createElement('iframe');
        frame.style.cssText = 'position:fixed;right:0;bottom:0;width:0;height:0;border:0';
        document.body.appendChild(frame);
        const doc = frame.contentWindow.document;
        doc.open();
        doc.write('<html><head><meta charset="utf-8"><title>Jadwal Bulanan {{ $checklistItem->title }} {{ $year }}</title><style>' + css + '</style></head><body>' + sheet.innerHTML + '</body></html>');
        doc.close();

        // Tunggu logo & tanda tangan termuat, kalau tidak gambarnya hilang saat dicetak.
        const images = Array.from(doc.images);
        const ready = images.map(img => img.complete ? Promise.resolve() : new Promise(resolve => { img.onload = img.onerror = resolve; }));
        Promise.all(ready).then(() => {
            frame.contentWindow.focus();
            frame.contentWindow.print();
            setTimeout(() => frame.remove(), 1500);
        });
    });

    // Triggered from the "Cetak" link in the schedule list (?autoprint=1)
    if (new URLSearchParams(window.location.search).get('autoprint') === '1') {
        document.getElementById('printMonthlyScheduleButton')?.click();
    }
</script>

<style>
    .schedule-detail-table th, .schedule-detail-table td {
        vertical-align: middle;
    }

    .date-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 3px;
        justify-content: center;
    }

    .date-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 22px;
        height: 22px;
        padding: 0 4px;
        font-size: 0.75rem;
        border-radius: 4px;
        background-color: #ff9800;
        color: #fff;
        font-weight: 600;
    }
</style>
@endsection
