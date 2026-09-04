<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan Inovasi PT MGM {{ $year }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            color: #000;
            font-family: Arial, Helvetica, sans-serif;
            background: #f1f5f9;
        }
        .print-actions {
            max-width: 1000px;
            margin: 16px auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            background: #fff;
            padding: 12px 16px;
            border-radius: 6px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .print-actions form {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .print-actions select {
            padding: 6px 10px;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            font: inherit;
        }
        .print-actions .btn-group {
            display: flex;
            gap: 8px;
        }
        .print-actions a, .print-actions button {
            border: 1px solid #2563eb;
            border-radius: 4px;
            padding: 7px 14px;
            color: #fff;
            background: #2563eb;
            font: inherit;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .print-actions a {
            border-color: #64748b;
            color: #334155;
            background: #fff;
        }

        .sheet {
            width: 100%;
            max-width: 1000px;
            margin: 0 auto 24px;
            background: #fff;
            padding: 20px;
            border: 1px solid #cbd5e1;
        }

        .report-title {
            text-align: center;
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 16px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table-innovation {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
        }
        .table-innovation th, .table-innovation td {
            border: 1px solid #000;
            padding: 5px 6px;
            vertical-align: middle;
        }
        .table-innovation thead th {
            font-weight: bold;
            text-align: center;
            background-color: #fff;
            height: 28px;
        }
        .col-no { width: 35px; text-align: center; }
        .col-tgl { width: 85px; text-align: center; }
        .col-inovasi { text-align: left; }
        .col-implementasi { text-align: left; }
        .col-tgl-imp { width: 105px; text-align: center; }
        .col-ket { width: 100px; text-align: left; }
        .col-ttd { width: 60px; text-align: center; }

        .month-header-row td {
            font-weight: bold;
            background-color: #fff;
            height: 24px;
        }

        .data-row td {
            height: 24px;
        }
        .data-row .text-center { text-align: center; }

        .signature-container {
            display: flex;
            justify-content: flex-end;
            margin-top: 35px;
            padding-right: 20px;
        }
        .signature-box {
            text-align: center;
            min-width: 180px;
            font-size: 10pt;
        }
        .signature-box .title {
            font-weight: bold;
            margin-bottom: 8px;
        }
        .signature-box .space {
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .signature-box img {
            max-height: 55px;
            max-width: 140px;
            object-fit: contain;
        }
        .signature-box .name {
            font-weight: bold;
            margin-top: 4px;
        }

        @media print {
            body {
                background: #fff;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .print-actions {
                display: none !important;
            }
            .sheet {
                max-width: none;
                margin: 0;
                padding: 0;
                border: none;
            }
        }
    </style>
</head>
<body>

    <div class="print-actions">
        <form method="GET" action="{{ route('innovations.print') }}">
            <label for="year">Tahun:</label>
            <select name="year" id="year" onchange="this.form.submit()">
                @foreach($availableYears as $y)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>

            <label for="month">Bulan:</label>
            <select name="month" id="month" onchange="this.form.submit()">
                <option value="">Semua Bulan (1 Tahun)</option>
                @foreach($monthsList as $mKey => $mVal)
                    <option value="{{ $mKey }}" {{ $month == $mKey ? 'selected' : '' }}>{{ $mVal }}</option>
                @endforeach
            </select>
        </form>

        <div class="btn-group">
            <a href="{{ route('innovations.index') }}">Kembali</a>
            <button type="button" onclick="window.print()">Cetak</button>
        </div>
    </div>

    <main class="sheet">
        <div class="report-title">
            INOVASI PT MGM {{ $year }}
        </div>

        <table class="table-innovation">
            <thead>
                <tr>
                    <th class="col-no">No</th>
                    <th class="col-tgl">Tanggal</th>
                    <th class="col-inovasi">Inovasi</th>
                    <th class="col-implementasi">Implementasi</th>
                    <th class="col-tgl-imp">Tgl Implementasi</th>
                    <th class="col-ket">Keterangan</th>
                    <th class="col-ttd">TTD</th>
                </tr>
            </thead>
            <tbody>
                @foreach($groupedInnovations as $mNum => $gData)
                    {{-- Subheader per Bulan --}}
                    <tr class="month-header-row">
                        <td></td>
                        <td class="font-weight-bold">{{ $gData['name'] }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

                    @php
                        $items = $gData['items'];
                        $itemCount = count($items);
                        $minRows = max(2, $itemCount);
                    @endphp

                    @for($i = 0; $i < $minRows; $i++)
                        @php
                            $item = $items[$i] ?? null;
                        @endphp
                        <tr class="data-row">
                            <td class="col-no">{{ $i + 1 }}</td>
                            <td class="col-tgl">{{ $item?->innovation_date?->format('d/m/Y') ?: '' }}</td>
                            <td class="col-inovasi">{{ $item?->title ?: '' }}</td>
                            <td class="col-implementasi">{{ $item?->implementation ?: '' }}</td>
                            <td class="col-tgl-imp">{{ $item?->implementation_date?->format('d/m/Y') ?: '' }}</td>
                            <td class="col-ket">{{ $item?->notes ?: '' }}</td>
                            <td class="col-ttd"></td>
                        </tr>
                    @endfor
                @endforeach
            </tbody>
        </table>

        <div class="signature-container">
            <div class="signature-box">
                <div class="title">Dibuat Oleh :</div>
                <div class="space">
                    @if($signatures['reporter']?->signature_path)
                        <img src="{{ asset('storage/' . $signatures['reporter']->signature_path) }}" alt="Tanda Tangan Bagus">
                    @endif
                </div>
                <div class="name">(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $signatureNames['reporter'] }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</div>
            </div>
        </div>
    </main>

</body>
</html>
