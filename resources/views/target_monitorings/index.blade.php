@extends('layouts.app')

@php
    // Nilai default untuk kop dokumen. Ganti/lempar dari controller kalau
    // datanya memang tersimpan di database (lihat catatan di akhir jawaban).
    $noDokumen = $noDokumen ?? 'FR-MR-09';
    $noRevisi = $noRevisi ?? '00';
    $tglBerlaku = $tglBerlaku ?? '1 Agustus 2017';
    $bagian = $bagian ?? 'IT';

    // Default rencana tindakan (bisa diganti dengan data dari controller: $actionPlans)
    $actionPlans =
        $actionPlans ??
        [
            ['action' => 'Membuat Jadwal Perawatan Rutin per bulan', 'pic' => 'IT', 'schedule' => 'Setiap bulan'],
            ['action' => 'Membuat inovasi program IT', 'pic' => 'IT', 'schedule' => 'Setiap bulan'],
            [
                'action' => 'Memastikan tidak terjadi pemborosan energi & temuan inspeksi lingkungan',
                'pic' => 'IT',
                'schedule' => 'Setiap bulan',
            ],
        ];

    // Default penandatangan (bisa diganti dengan data dari controller: $preparedBy / $approvedBy)
    $preparedBy = $preparedBy ?? 'Bagus';
    $approvedBy = $approvedBy ?? 'Arifin S.';
@endphp

@section('content')
    <div class="container-fluid mt-4 target-monitoring-page target-modern-page">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
            <div>
                <div class="target-kicker">IT PERFORMANCE CONTROL</div>
                <h2 class="mb-1">Pemantauan Sasaran</h2>
                <p class="text-muted mb-0">Rekap capaian sasaran IT berdasarkan perbaikan, inovasi, dan temuan operasional.
                </p>
            </div>
            <div class="no-print">
                <button type="button" class="btn-print" onclick="window.print()">
                    <span class="btn-print-icon">
                        <i class="bi bi-printer-fill"></i>
                    </span>
                    <span class="btn-print-label">
                        <strong>Cetak Dokumen</strong>
                        <small>Simpan / Print Pemantauan Sasaran</small>
                    </span>
                </button>
            </div>
        </div>
        <form method="GET" class="row g-3 align-items-end p-3 mb-3 border rounded bg-light no-print">
            <div class="col-md-3"><label class="form-label">Tahun</label><input type="number" name="year"
                    class="form-control" value="{{ $year }}" min="2000" max="2100"></div>
            <div class="col-md-3"><label class="form-label">Bulan Awal</label><select name="start_month"
                    class="form-select">
                    @foreach ($monthOptions as $number => $name)
                        <option value="{{ $number }}" {{ $startMonth === $number ? 'selected' : '' }}>
                            {{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3"><label class="form-label">Bulan Akhir</label><select name="end_month" class="form-select">
                    @foreach ($monthOptions as $number => $name)
                        <option value="{{ $number }}" {{ $endMonth === $number ? 'selected' : '' }}>
                            {{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3"><button class="btn btn-primary">Tampilkan Pemantauan</button></div>
        </form>
        <form method="POST" action="{{ route('target-monitorings.manual.update') }}">@csrf<input type="hidden"
                name="year" value="{{ $year }}"><input type="hidden" name="start_month"
                value="{{ $startMonth }}"><input type="hidden" name="end_month" value="{{ $endMonth }}">
            <section class="target-sheet">
                {{-- ===== KOP DOKUMEN ===== --}}
                <table class="doc-header-table">
                    <tr>
                        <td rowspan="3" class="logo-cell"><img src="{{ asset('images/logo-mgm.svg') }}"
                                alt="Logo MGM"></td>
                        <td rowspan="3" class="company-cell">
                            <strong>PT. MULIA GRAND MANUFACTURE</strong>
                            <h3>PEMANTAUAN SASARAN</h3>
                        </td>
                        <td class="info-label">No. Dokumen</td>
                        <td class="info-value">: {{ $noDokumen }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">No. Revisi</td>
                        <td class="info-value">: {{ $noRevisi }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Tgl Berlaku</td>
                        <td class="info-value">: {{ $tglBerlaku }}</td>
                    </tr>
                </table>

                {{-- ===== BAGIAN & PERIODE ===== --}}
                <div class="doc-meta">
                    <span>Bagian : {{ $bagian }}</span>
                    <span>Periode : {{ $months->first() }} - {{ $months->last() }} {{ $year }}</span>
                </div>

                {{-- ===== TABEL SASARAN ===== --}}
                <div class="table-responsive">
                    <table class="table table-bordered target-table mb-0">
                        <thead>
                            <tr>
                                <th rowspan="2">No.</th>
                                <th rowspan="2">Sasaran</th>
                                <th rowspan="2">Target</th>
                                <th colspan="{{ $months->count() }}">Pencapaian Bulan</th>
                                <th rowspan="2">Keterangan</th>
                            </tr>
                            <tr>
                                @foreach ($months as $name)
                                    <th>{{ $name }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($metrics as $index => $metric)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="target-name">{{ $metric['label'] }}</td>
                                    <td>{{ $metric['target'] }}</td>
                                    @foreach ($months as $monthNumber => $name)
                                        @php($value = $metric['manual'] ? $manualValues->get($metric['key'] . '|' . $monthNumber)?->value ?? 0 : $metric['values']->get($monthNumber, 0))
                                        <td class="text-center">
                                            @if ($metric['manual'])
                                                <input type="number" min="0"
                                                    name="manual[{{ $metric['key'] }}][{{ $monthNumber }}][value]"
                                                    value="{{ $value }}"
                                                class="form-control form-control-sm target-input">@else<span
                                                    class="target-value">{{ $value }}</span>
                                            @endif
                                        </td>
                                    @endforeach
                                    <td>
                                        <span
                                            class="target-result {{ collect($months->keys())->every(fn($month) => $metric['achieved']($metric['manual'] ? $manualValues->get($metric['key'] . '|' . $month)?->value ?? 0 : $metric['values']->get($month, 0))) ? 'target-result-ok' : 'target-result-alert' }}">{{ collect($months->keys())->every(fn($month) => $metric['achieved']($metric['manual'] ? $manualValues->get($metric['key'] . '|' . $month)?->value ?? 0 : $metric['values']->get($month, 0))) ? 'Tercapai' : 'Perlu Tindak Lanjut' }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="target-actions no-print"><span>Baris 1 dan 2 dihitung otomatis. Baris 3 dan 4 dapat diisi
                        manual.</span><button class="btn btn-primary">Simpan Data Manual</button></div>

                {{-- ===== RENCANA TINDAKAN ===== --}}
                <table class="action-table">
                    <thead>
                        <tr>
                            <th style="width:40px">No.</th>
                            <th>Rencana Tindakan</th>
                            <th style="width:110px">Pelaksana</th>
                            <th style="width:110px">Waktu</th>
                            <th style="width:150px">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($actionPlans as $i => $plan)
                            <tr>
                                <td class="text-center">{{ $i + 1 }}</td>
                                <td>{{ $plan['action'] }}</td>
                                <td class="text-center">{{ $plan['pic'] }}</td>
                                <td class="text-center">{{ $plan['schedule'] }}</td>
                                <td></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- ===== TANDA TANGAN ===== --}}
                <div class="sign-section">
                    <div class="sign-box">
                        <div class="sign-title">Dibuat Oleh</div>
                        <div class="sign-space"></div>
                        <div class="sign-name">{{ $preparedBy }}</div>
                    </div>
                    <div class="sign-box">
                        <div class="sign-title">Disetujui Oleh</div>
                        <div class="sign-space"></div>
                        <div class="sign-name">{{ $approvedBy }}</div>
                    </div>
                </div>
            </section>
        </form>
    </div>
    <style>
        .target-monitoring-page {
            color: #17324d
        }

        .target-kicker {
            font-size: .72rem;
            font-weight: 800;
            color: #0b5ea8;
            letter-spacing: .11em
        }

        /* ===== Tombol Cetak ===== */
        .btn-print {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            padding: 10px 22px 10px 14px;
            border: none;
            border-radius: 999px;
            background: linear-gradient(135deg, #0b5ea8 0%, #17324d 100%);
            color: #fff;
            cursor: pointer;
            box-shadow: 0 6px 16px rgba(11, 94, 168, .35);
            transition: transform .15s ease, box-shadow .15s ease, filter .15s ease;
        }

        .btn-print:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 22px rgba(11, 94, 168, .45);
            filter: brightness(1.08);
        }

        .btn-print:active {
            transform: translateY(0);
            box-shadow: 0 4px 10px rgba(11, 94, 168, .35);
        }

        .btn-print-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .18);
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .btn-print-label {
            display: flex;
            flex-direction: column;
            text-align: left;
            line-height: 1.2;
        }

        .btn-print-label strong {
            font-size: .92rem;
            font-weight: 800;
        }

        .btn-print-label small {
            font-size: .68rem;
            color: rgba(255, 255, 255, .8);
        }

        .target-sheet {
            border: 1px solid #94a3b8;
            background: #fff
        }

        /* ===== Kop dokumen (tabel) ===== */
        .doc-header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2px solid #17324d;
        }

        .doc-header-table td {
            border: 1px solid #17324d;
            padding: 6px 12px;
            font-size: .8rem;
            vertical-align: middle;
        }

        .logo-cell {
            width: 90px;
            text-align: center;
            border-left: none !important;
        }

        .logo-cell img {
            width: 58px;
            height: 58px;
            object-fit: contain;
        }

        .company-cell {
            text-align: center;
        }

        .company-cell strong {
            display: block;
            font-size: .95rem;
        }

        .company-cell h3 {
            margin: 4px 0 0;
            font-size: 1.2rem;
            font-weight: 800;
        }

        .info-label {
            font-weight: 600;
            width: 110px;
            white-space: nowrap;
        }

        .info-value {
            width: 140px;
            border-right: none !important;
        }

        /* ===== Bagian & Periode ===== */
        .doc-meta {
            display: flex;
            justify-content: space-between;
            padding: 8px 16px;
            font-size: .82rem;
            font-weight: 600;
            border-bottom: 2px solid #17324d;
        }

        .target-table th,
        .target-table td {
            border-color: #334155;
            vertical-align: middle;
            font-size: .82rem
        }

        .target-table thead th {
            text-align: center;
            background: #f8fafc
        }

        .target-name {
            min-width: 290px
        }

        .target-input {
            min-width: 58px;
            text-align: center
        }

        .target-value {
            font-size: 1rem;
            font-weight: 700
        }

        .target-result {
            display: inline-block;
            padding: 4px 7px;
            border-radius: 3px;
            font-size: .74rem;
            font-weight: 700
        }

        .target-result-ok {
            background: #dcfce7;
            color: #166534
        }

        .target-result-alert {
            background: #fee2e2;
            color: #991b1b
        }

        .target-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 14px 16px;
            background: #f8fafc;
            font-size: .8rem;
            color: #64748b
        }

        /* ===== Rencana Tindakan ===== */
        .action-table {
            width: 100%;
            border-collapse: collapse;
            border-top: 2px solid #17324d;
        }

        .action-table th,
        .action-table td {
            border: 1px solid #334155;
            padding: 6px 10px;
            font-size: .8rem;
        }

        .action-table th {
            background: #f8fafc;
            text-align: center;
        }

        /* ===== Tanda tangan ===== */
        .sign-section {
            display: flex;
            justify-content: flex-end;
            gap: 0;
        }

        .sign-box {
            border: 1px solid #334155;
            border-top: none;
            width: 220px;
            text-align: center;
        }

        .sign-box + .sign-box {
            border-left: none;
        }

        .sign-title {
            font-weight: 700;
            padding: 6px;
            border-bottom: 1px solid #334155;
            background: #f8fafc;
            font-size: .8rem;
        }

        .sign-space {
            height: 70px;
        }

        .sign-name {
            padding: 6px;
            border-top: 1px solid #334155;
            font-size: .8rem;
            text-decoration: underline;
        }

        @media(max-width:700px) {
            .doc-header-table {
                display: block;
            }

            .doc-header-table tr {
                display: flex;
                flex-wrap: wrap;
            }

            .target-actions {
                align-items: flex-start;
                flex-direction: column
            }

            .sign-section {
                flex-direction: column;
            }

            .sign-box + .sign-box {
                border-left: 1px solid #334155;
                border-top: none;
            }
        }

        /* ==== ATURAN CETAK ==== */
        @media print {

            html, body {
                margin: 0;
                padding: 0;
            }

            /* Sembunyikan seluruh isi halaman terlebih dahulu */
            body * {
                visibility: hidden;
            }

            /* Tampilkan hanya lembar target-sheet dan isinya */
            .target-sheet,
            .target-sheet * {
                visibility: visible;
            }

            .target-sheet {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                border: none;
                box-shadow: none;
                page-break-inside: avoid;
                break-inside: avoid;
            }

            /* Elemen bertanda no-print tidak pernah ikut tercetak */
            .no-print {
                display: none !important;
            }

            input.target-input {
                border: none;
                background: transparent;
                -webkit-appearance: none;
                appearance: none;
            }

            /* Perkecil semua elemen supaya muat 1 halaman */
            .doc-header-table td {
                padding: 3px 8px;
                font-size: .7rem;
            }

            .logo-cell img {
                width: 42px;
                height: 42px;
            }

            .company-cell strong {
                font-size: .82rem;
            }

            .company-cell h3 {
                margin: 2px 0 0;
                font-size: 1rem;
            }

            .doc-meta {
                padding: 4px 12px;
                font-size: .72rem;
            }

            .target-table th,
            .target-table td {
                padding: 3px 6px;
                font-size: .7rem;
                line-height: 1.2;
            }

            .target-name {
                min-width: 0;
            }

            .target-value {
                font-size: .78rem;
            }

            .target-result {
                padding: 2px 5px;
                font-size: .64rem;
            }

            .action-table th,
            .action-table td {
                padding: 3px 8px;
                font-size: .7rem;
            }

            .sign-box {
                width: 180px;
            }

            .sign-title {
                padding: 3px;
                font-size: .7rem;
            }

            .sign-space {
                height: 30px;
            }

            .sign-name {
                padding: 3px;
                font-size: .7rem;
            }

            table, tr, td, th {
                page-break-inside: avoid;
                break-inside: avoid;
            }

            @page {
                size: landscape;
                margin: 6mm;
            }
        }
        @media print {
            .target-modern-page .target-sheet { border: none; background: #fff; box-shadow: none; }
            .target-modern-page .target-table thead th { background: #f8fafc; color: #17324d; }
            .target-modern-page .target-table th,
            .target-modern-page .target-table td { border-color: #334155; }
            .target-modern-page .target-result { border-radius: 3px; }
            .target-modern-page .target-actions { background: #f8fafc; }
        }
    </style>
@endsection
