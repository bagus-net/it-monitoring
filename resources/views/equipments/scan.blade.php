<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $equipment->name }} | Informasi Peralatan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f7f9; color: #263238; }
        .scan-shell { max-width: 760px; margin: 32px auto; }
        .scan-header { padding: 24px; background: linear-gradient(120deg, #0f766e, #0e7490); color: #fff; border-radius: 8px 8px 0 0; }
        .scan-card { border: 1px solid #dbe3ea; border-radius: 0 0 8px 8px; background: #fff; }
        .scan-data dt { color: #64748b; font-size: .72rem; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; }
        .scan-data dd { margin-bottom: 16px; }
    </style>
</head>
<body>
    <main class="scan-shell px-3">
        <header class="scan-header">
            <div class="small text-uppercase opacity-75">Informasi Peralatan IT</div>
            <h1 class="h3 mb-1">{{ $equipment->name }}</h1>
            <div>{{ $equipment->asset_tag ?: 'Kode aset belum dicatat' }}</div>
        </header>
        <section class="scan-card p-4">
            <h2 class="h5 mb-3">Spesifikasi</h2>
            <dl class="row scan-data mb-0">
                <div class="col-sm-6"><dt>Tipe</dt><dd>{{ $equipment->type->name ?? '-' }}</dd></div>
                <div class="col-sm-6"><dt>Manufacturer</dt><dd>{{ $equipment->manufacturer->name ?? '-' }}</dd></div>
                <div class="col-sm-6"><dt>Model</dt><dd>{{ $equipment->model ?: '-' }}</dd></div>
                <div class="col-sm-6"><dt>No. Seri</dt><dd>{{ $equipment->serial_number ?: '-' }}</dd></div>
                <div class="col-sm-6"><dt>Sistem Operasi</dt><dd>{{ $equipment->operating_system ?: '-' }}</dd></div>
                <div class="col-sm-6"><dt>Kapasitas</dt><dd>{{ $equipment->capacity ?: '-' }}</dd></div>
                <div class="col-sm-6"><dt>Lokasi</dt><dd>{{ $equipment->assetLocation->name ?? $equipment->getRawOriginal('location') ?: '-' }}</dd></div>
                <div class="col-sm-6"><dt>PIC Peralatan</dt><dd>{{ $equipment->owner_name ?: ($equipment->owner->name ?? '-') }}</dd></div>
                <div class="col-sm-6"><dt>Departemen PIC</dt><dd>{{ $equipment->department ?: ($equipment->owner->department ?? '-') }}</dd></div>
                <div class="col-sm-6"><dt>Kondisi</dt><dd>{{ ucfirst($equipment->condition ?: $equipment->status ?: '-') }}</dd></div>
            </dl>
            @if ($equipment->technical_details)
                <h2 class="h6 border-top pt-3">Detail teknis</h2>
                <dl class="row scan-data mb-3">
                    @foreach ($equipment->technical_details as $label => $value)
                        <div class="col-sm-6"><dt>{{ str_replace('_', ' ', $label) }}</dt><dd>{{ $value }}</dd></div>
                    @endforeach
                </dl>
            @endif

            <a href="{{ route('maintenance-checklists.create', ['equipment_id' => $equipment->id]) }}" class="btn btn-primary w-100">Login untuk Checklist Peralatan</a>
        </section>
    </main>
</body>
</html>
