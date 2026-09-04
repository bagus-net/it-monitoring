<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $equipment->name }} | Label Informasi Peralatan IT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-bg: #0f172a;
            --card-bg: #ffffff;
            --accent-cyan: #06b6d4;
            --accent-blue: #3b82f6;
            --accent-teal: #10b981;
        }
        body {
            background-color: #f1f5f9;
            color: #1e293b;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            padding-bottom: 40px;
        }
        .scan-container {
            max-width: 680px;
            margin: 24px auto;
        }
        .scan-card-hero {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 60%, #0e7490 100%);
            color: #ffffff;
            border-radius: 20px 20px 0 0;
            padding: 28px 24px 24px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.15);
        }
        .scan-card-hero::after {
            content: '';
            position: absolute;
            top: -40px;
            right: -40px;
            width: 160px;
            height: 160px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            pointer-events: none;
        }
        .asset-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #67e8f9;
        }
        .scan-card-body {
            background: #ffffff;
            border-radius: 0 0 20px 20px;
            padding: 24px;
            border: 1px solid #e2e8f0;
            border-top: 0;
            box-shadow: 0 10px 30px rgba(0,0,0,0.04);
        }
        .spec-item {
            background: #f8fafc;
            border: 1px solid #edf2f7;
            border-radius: 12px;
            padding: 12px 14px;
            height: 100%;
            transition: all 0.2s ease;
        }
        .spec-item:hover {
            border-color: #cbd5e1;
            background: #ffffff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        }
        .spec-label {
            font-size: 0.7rem;
            font-weight: 800;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 3px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .spec-value {
            font-size: 0.92rem;
            font-weight: 700;
            color: #0f172a;
            word-break: break-word;
        }
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 0.78rem;
            font-weight: 700;
        }
        .status-pill.normal { background: #dcfce7; color: #166534; }
        .status-pill.warning { background: #fef3c7; color: #92400e; }
        .status-pill.danger { background: #fee2e2; color: #991b1b; }

        .btn-action-main {
            background: linear-gradient(135deg, #2563eb, #0d9488);
            border: 0;
            color: #fff;
            font-weight: 700;
            padding: 14px;
            border-radius: 14px;
            box-shadow: 0 6px 18px rgba(37, 99, 235, 0.25);
            transition: transform 0.15s, box-shadow 0.15s;
        }
        .btn-action-main:hover {
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 10px 22px rgba(37, 99, 235, 0.35);
        }
        .tech-detail-card {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 12px;
            padding: 14px;
        }
        .brand-logo-header {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: #38bdf8;
        }
    </style>
</head>
<body>
    <main class="scan-container px-3">
        <!-- Hero Header Card -->
        <header class="scan-card-hero">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="brand-logo-header">
                        <i class="bi bi-qr-code-scan"></i>
                    </div>
                    <div>
                        <div class="asset-badge">
                            <i class="bi bi-shield-check"></i> Verified IT Asset
                        </div>
                    </div>
                </div>
                <div>
                    @php
                        $cond = strtolower($equipment->condition ?: $equipment->status ?: 'normal');
                        $statusClass = str_contains($cond, 'rusak') || str_contains($cond, 'repair') ? 'danger' : (str_contains($cond, 'perhatian') || str_contains($cond, 'warning') ? 'warning' : 'normal');
                    @endphp
                    <span class="status-pill {{ $statusClass }}">
                        <i class="bi bi-circle-fill fs-8"></i> {{ ucfirst($equipment->condition ?: $equipment->status ?: 'Normal') }}
                    </span>
                </div>
            </div>

            <h1 class="h3 mb-1 fw-bold text-white">{{ $equipment->name }}</h1>
            <div class="text-cyan font-monospace opacity-90 fs-6">
                <i class="bi bi-tag-fill me-1"></i> {{ $equipment->asset_tag ?: 'ASET-NO-TAG' }}
            </div>
        </header>

        <!-- Main Body Specs -->
        <section class="scan-card-body">
            <h5 class="fw-bold mb-3 text-slate-800 d-flex align-items-center gap-2">
                <i class="bi bi-cpu text-primary"></i> Spesifikasi Peralatan
            </h5>

            <div class="row g-3 mb-4">
                <div class="col-6 col-md-4">
                    <div class="spec-item">
                        <div class="spec-label"><i class="bi bi-box"></i> Tipe</div>
                        <div class="spec-value">{{ $equipment->type->name ?? '-' }}</div>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="spec-item">
                        <div class="spec-label"><i class="bi bi-building"></i> Manufacturer</div>
                        <div class="spec-value">{{ $equipment->manufacturer->name ?? '-' }}</div>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="spec-item">
                        <div class="spec-label"><i class="bi bi-laptop"></i> Model</div>
                        <div class="spec-value">{{ $equipment->model ?: '-' }}</div>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="spec-item">
                        <div class="spec-label"><i class="bi bi-upc-scan"></i> No. Seri</div>
                        <div class="spec-value font-monospace">{{ $equipment->serial_number ?: '-' }}</div>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="spec-item">
                        <div class="spec-label"><i class="bi bi-window"></i> Sistem Operasi</div>
                        <div class="spec-value">{{ $equipment->operating_system ?: '-' }}</div>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="spec-item">
                        <div class="spec-label"><i class="bi bi-hdd-network"></i> Kapasitas</div>
                        <div class="spec-value">{{ $equipment->capacity ?: '-' }}</div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="spec-item">
                        <div class="spec-label"><i class="bi bi-geo-alt-fill text-danger"></i> Lokasi Aset</div>
                        <div class="spec-value">{{ $equipment->assetLocation->name ?? $equipment->getRawOriginal('location') ?: '-' }}</div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="spec-item">
                        <div class="spec-label"><i class="bi bi-person-badge-fill text-primary"></i> Pengguna / PIC</div>
                        <div class="spec-value">
                            {{ $equipment->owner_name ?: ($equipment->owner->name ?? '-') }}
                            @if($equipment->department ?: ($equipment->owner->department ?? null))
                                <small class="text-muted font-weight-normal">({{ $equipment->department ?: $equipment->owner->department }})</small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if ($equipment->technical_details && count($equipment->technical_details) > 0)
                <div class="tech-detail-card mb-4">
                    <h6 class="fw-bold mb-2 text-success d-flex align-items-center gap-1">
                        <i class="bi bi-sliders"></i> Detail Spesifikasi Teknis:
                    </h6>
                    <div class="row g-2">
                        @foreach ($equipment->technical_details as $label => $value)
                            <div class="col-6 col-md-4">
                                <small class="text-muted d-block text-uppercase" style="font-size: 0.68rem; font-weight: 700;">{{ str_replace('_', ' ', $label) }}</small>
                                <strong class="text-dark fs-7">{{ $value }}</strong>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Quick Action Button -->
            <div class="d-grid gap-2">
                <a href="{{ route('maintenance-checklists.create', ['equipment_id' => $equipment->id]) }}" class="btn btn-action-main btn-lg">
                    <i class="bi bi-clipboard2-check-fill me-2"></i> Buat / Isi Checklist Perawatan
                </a>
            </div>

            <div class="text-center mt-3">
                <small class="text-muted">
                    <i class="bi bi-info-circle me-1"></i> Sistem Monitoring & Maintenance IT PT Mulia Grand Manufacture
                </small>
            </div>
        </section>
    </main>
</body>
</html>
