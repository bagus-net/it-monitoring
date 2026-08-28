<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Realisasi Jadwal Perawatan IT - {{ $monthName }} {{ $year }}</title>
    <style>
        @page { size: A4 landscape; margin: 8mm; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #111827; font-family: Arial, Helvetica, sans-serif; background: #f1f5f9; }
        .print-actions { max-width: 1120px; margin: 18px auto; display: flex; justify-content: flex-end; gap: 8px; }
        .print-actions a, .print-actions button { border: 1px solid #1d4ed8; border-radius: 4px; padding: 8px 12px; color: #fff; background: #2563eb; font: inherit; cursor: pointer; text-decoration: none; }
        .print-actions a { border-color: #64748b; color: #334155; background: #fff; }
        .sheet { width: 100%; max-width: 1120px; margin: 0 auto 18px; background: #fff; border: 1px solid #0f172a; }
        .sheet-header { display: grid; grid-template-columns: 33% 52% 15%; border-bottom: 1px solid #0f172a; }
        .sheet-header > div { min-height: 55px; padding: 6px 10px; border-right: 1px solid #0f172a; display: flex; align-items: center; justify-content: center; text-align: center; font-weight: 700; }
        .sheet-header > div:last-child { border-right: 0; }
        .company { font-size: 13px; }
        .heading { flex-direction: column; font-size: 16px; line-height: 1.25; }
        .heading span { margin-top: 5px; }
        .form-info { flex-direction: column; align-items: flex-start !important; font-size: 8px; line-height: 1.8; }
        .schedule-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .schedule-table th, .schedule-table td { border: 1px solid #0f172a; padding: 0; text-align: center; font-size: 7px; height: 24px; }
        .schedule-table thead th { font-weight: 700; height: 28px; }
        .program-column { width: 218px; padding: 3px !important; text-align: left !important; font-size: 8px !important; }
        .schedule-table thead .program-column { text-align: center !important; }
        .category-row th { height: 20px; padding: 3px 6px; background: #f8fafc; text-align: left; font-size: 8px; }
        .date-cell { position: relative; }
        .date-cell.is-scheduled::after { content: ''; position: absolute; inset: 0; background: var(--program-color); }
        .signatures { display: grid; grid-template-columns: 1fr 1fr; min-height: 138px; padding: 28px 74px 10px; font-size: 10px; font-weight: 700; }
        .signature { width: 150px; text-align: center; }
        .signature:last-child { justify-self: end; }
        .signature-line { height: 78px; border-bottom: 1px solid #334155; }
        .empty-row td { height: 42px; color: #64748b; font-size: 9px; }
        @media print {
            body { background: #fff; }
            .print-actions { display: none; }
            .sheet { max-width: none; margin: 0; }
        }
    </style>
</head>
<body>
    <div class="print-actions">
        <a href="{{ route('monthly_schedules.index') }}">Kembali</a>
        <button type="button" id="printButton">Cetak</button>
    </div>

    <main class="sheet">
        <header class="sheet-header">
            <div class="company">PT. MULIA GRAND MANUFACTURE</div>
            <div class="heading">REALISASI JADWAL PERAWATAN IT<span>{{ strtoupper($monthName) }} TAHUN: {{ $year }}</span></div>
            <div class="form-info">No. Form&nbsp;&nbsp;&nbsp;: FR-IT-02<br>Revisi&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: 01</div>
        </header>

        <table class="schedule-table">
            <thead>
                <tr>
                    <th class="program-column">Program Perawatan</th>
                    @for ($day = 1; $day <= $daysInMonth; $day++)
                        <th>{{ $day }}</th>
                    @endfor
                </tr>
            </thead>
            <tbody>
                @forelse ($programCategories as $category => $programs)
                    <tr class="category-row">
                        <th colspan="{{ $daysInMonth + 1 }}">{{ $category }}</th>
                    </tr>
                    @foreach ($programs as $program)
                        <tr>
                            <td class="program-column">{{ $program['title'] }}</td>
                            @for ($day = 1; $day <= $daysInMonth; $day++)
                                <td class="date-cell {{ in_array($day, $program['dates']) ? 'is-scheduled' : '' }}" style="--program-color: {{ $program['color'] }};"></td>
                            @endfor
                        </tr>
                    @endforeach
                @empty
                    <tr class="empty-row">
                        <td colspan="{{ $daysInMonth + 1 }}">Belum ada program perawatan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="signatures">
            <div class="signature">Dibuat oleh,<div class="signature-line"></div></div>
            <div class="signature">Mengetahui,<div class="signature-line"></div></div>
        </div>
    </main>

    <script>
        document.getElementById('printButton').addEventListener('click', () => window.print());
        window.addEventListener('load', () => window.print());
    </script>
</body>
</html>
