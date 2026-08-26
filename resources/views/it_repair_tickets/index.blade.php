@extends('layouts.app')

@section('content')
    @php
        $canViewRepairDuration = !auth()->user()->isEmployee();
    @endphp
    <div class="container mt-4 repair-page">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
                <div class="repair-eyebrow">IT Service Desk</div>
                <h2 class="mb-1">Perbaikan IT</h2>
                <p class="text-muted mb-0">Tiket permintaan perbaikan dan tindak lanjut peralatan IT.</p>
            </div><a href="{{ route('it-repair-tickets.create') }}" class="btn btn-brand">Buat Tiket Perbaikan</a>
        </div>
        <div class="row g-3 mb-3">
            <div class="col-md-4"><a class="repair-stat open"
                    href="{{ route('it-repair-tickets.index', ['status' => 'open']) }}"><span>Open</span><strong>{{ $summary['open'] }}</strong><small>menunggu
                        penanganan</small></a></div>
            <div class="col-md-4"><a class="repair-stat repair-in-progress"
                    href="{{ route('it-repair-tickets.index', ['status' => 'in_progress']) }}"><span>Proses</span><strong>{{ $summary['in_progress'] }}</strong><small>sedang
                        dikerjakan</small></a></div>
            <div class="col-md-4"><a class="repair-stat resolved"
                    href="{{ route('it-repair-tickets.index', ['status' => 'resolved']) }}"><span>Selesai</span><strong>{{ $summary['resolved'] }}</strong><small>tiket
                        ditutup</small></a></div>
        </div>
        @if (auth()->user()->isEmployee())
            <div class="card my-equipment mb-3">
                <div class="card-header"><strong>Peralatan IT Saya</strong><span>{{ $myEquipments->count() }} unit terdaftar
                        atas nama Anda</span></div>
                <div class="card-body">
                    <div class="row g-3">
                        @forelse($myEquipments as $eq)
                            <div class="col-md-6 col-xl-4">
                                <div class="my-equipment-card">
                                    <div class="my-equipment-head">
                                        <strong>{{ $eq->name }}</strong>
                                        <span
                                            class="condition-chip {{ in_array($eq->condition, ['rusak', 'perbaikan']) ? 'chip-attention' : 'chip-good' }}">{{ ucfirst($eq->condition ?? ($eq->status ?? 'tidak dicatat')) }}</span>
                                    </div>
                                    <dl>
                                        <dt>Kode Aset</dt>
                                        <dd>{{ $eq->asset_tag ?: '-' }}</dd>
                                        <dt>Jenis</dt>
                                        <dd>{{ $eq->type->name ?? '-' }}</dd>
                                        <dt>Merk / Model</dt>
                                        <dd>{{ $eq->manufacturer->name ?? '-' }}{{ $eq->model ? ' / ' . $eq->model : '' }}
                                        </dd>
                                        <dt>Serial Number</dt>
                                        <dd>{{ $eq->serial_number ?: '-' }}</dd>
                                        <dt>Sistem Operasi</dt>
                                        <dd>{{ $eq->operating_system ?: '-' }}</dd>
                                        <dt>PIC</dt>
                                        <dd>{{ $eq->owner_name ?: $eq->owner->name ?? '-' }}</dd>
                                        <dt>Lokasi</dt>
                                        <dd>{{ $eq->assetLocation?->name ?: ($eq->getRawOriginal('location') ?: '-') }}
                                        </dd>
                                        <dt>Departemen</dt>
                                        <dd>{{ $eq->department ?: '-' }}</dd>
                                        @if (strtolower($eq->type->name ?? '') === 'monitor')
                                            <dt>Ukuran Layar | Resolusi</dt>
                                            <dd>{{ $eq->technical_details['screen_size'] ?? '-' }} |
                                                {{ $eq->technical_details['resolution'] ?? '-' }}</dd>
                                        @else
                                            <dt>IP Address</dt>
                                            <dd>{{ $eq->ip_address ?: '-' }}</dd>
                                        @endif
                                        <dt>Garansi</dt>
                                        <dd>{{ $eq->warranty_expiry?->format('d M Y') ?: '-' }}</dd>
                                    </dl>
                                    <a href="{{ route('it-repair-tickets.create') }}"
                                        class="btn btn-sm btn-outline-primary w-100">Laporkan Gangguan</a>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-muted">Belum ada peralatan IT yang terdaftar atas nama Anda. Hubungi tim
                                IT untuk pendataan.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        @endif
        <div class="card repair-filter mb-3">
            <div class="card-header"><strong>Filter Tiket</strong></div>
            <div class="card-body">
                <form method="GET" action="{{ route('it-repair-tickets.index') }}" class="row g-2 align-items-end">
                    @if ($search)
                        <input type="hidden" name="search" value="{{ $search }}">
                    @endif
                    <div class="col-6 col-lg-2"><label class="form-label">Periode Dari</label><input type="date"
                            name="from" value="{{ $filters['from'] }}" class="form-control"></div>
                    <div class="col-6 col-lg-2"><label class="form-label">Sampai</label><input type="date" name="to"
                            value="{{ $filters['to'] }}" class="form-control"></div>
                    <div class="col-6 col-lg-2"><label class="form-label">Status</label><select name="status"
                            class="form-select">
                            <option value="">Semua</option>
                            @foreach (['open' => 'Open', 'in_progress' => 'Proses', 'resolved' => 'Selesai'] as $key => $label)
                                <option value="{{ $key }}" @selected($status === $key)>{{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-lg-2"><label class="form-label">Prioritas</label><select name="priority"
                            class="form-select">
                            <option value="">Semua</option>
                            @foreach (['low' => 'Rendah', 'normal' => 'Normal', 'high' => 'Tinggi', 'urgent' => 'Mendesak'] as $key => $label)
                                <option value="{{ $key }}" @selected($filters['priority'] === $key)>{{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-lg-2"><label class="form-label">Kategori</label><select name="repair_category"
                            class="form-select">
                            <option value="">Semua</option>
                            @foreach (['hardware' => 'Hardware', 'software' => 'Software'] as $key => $label)
                                <option value="{{ $key }}" @selected($filters['repair_category'] === $key)>{{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-lg-2"><label class="form-label"></label>Jenis Peralatan</label><select
                            name="category" class="form-select">
                            <option value="">Semua</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category }}" @selected($filters['category'] === $category)>{{ $category }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-lg-2"><label class="form-label">Lokasi</label><select name="location_id"
                            class="form-select">
                            <option value="">Semua</option>
                            @foreach ($locations as $location)
                                <option value="{{ $location->id }}" @selected((string) $filters['location_id'] === (string) $location->id)>{{ $location->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 d-flex gap-2">
                        <button type="submit" class="btn btn-brand btn-sm">Terapkan Filter</button>
                        <a href="{{ route('it-repair-tickets.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                    </div>
                </form>
            </div>
        </div>
        <div class="card repair-list">
            <div class="card-header d-flex justify-content-between align-items-center"><strong>Daftar Tiket</strong><span
                    class="repair-kind-recap"><span class="repair-kind kind-hardware">Hardware
                        {{ $summary['hardware'] }}</span><span class="repair-kind kind-software">Software
                        {{ $summary['software'] }}</span>
                    @if ($status)
                        <a href="{{ route('it-repair-tickets.index') }}" class="btn btn-sm btn-outline-secondary">Semua
                            Tiket</a>
                    @endif
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>No. Tiket</th>
                                <th>Kategori</th>
                                <th>Peralatan</th>
                                <th>Lokasi</th>
                                <th>PIC</th>
                                <th>Pelapor</th>
                                <th>Keluhan</th>
                                <th>Waktu Lapor</th>
                                @if (!auth()->user()->isEmployee())
                                    <th>Durasi</th>
                                @endif
                                <th>
                                    <th>Prioritas</th>
                                <th>Status</th>
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tickets as $ticket)
                                <tr>
                                    <td><strong>{{ $ticket->ticket_number }}</strong><small>{{ $ticket->error_type ?? '-' }}</small>
                                    </td>
                                    <td><span
                                            class="repair-kind kind-{{ $ticket->repair_category }}">{{ $ticket->repair_category === 'software' ? 'Software' : 'Hardware' }}</span><small>{{ $ticket->repair_category === 'software' ? ($ticket->software_name ?: '-') : ($ticket->equipment_category ?: '-') }}</small>
                                    </td>
                                    <td>{{ $ticket->equipment->name ?? '-' }}</td>
                                    <td>{{ $ticket->equipment?->assetLocation?->name ?: ($ticket->equipment?->getRawOriginal('location') ?: '-') }}
                                    </td>
                                    <td>{{ $ticket->snapshotOwnerName() ?: '-' }}</td>
                                    <td>{{ $ticket->reported_by ?: '-' }}<small>{{ $ticket->department ?? '' }}</small>
                                    </td>
                                    <td class="problem-cell">{{ $ticket->problem_description }}</td>

                                    <td>{{ $ticket->reported_at?->format('d M Y H:i') }}</td>
                                    @if (!auth()->user()->isEmployee())
                                        <td>{{ $ticket->started_at ? $ticket->started_at->diffForHumans($ticket->resolved_at ?? now(), true) : '-' }}
                                        </td>
                                    @endif
                                    <td><span
                                            class="priority priority-{{ $ticket->priority }}">{{ ['low' => 'Rendah', 'normal' => 'Normal', 'high' => 'Tinggi', 'urgent' => 'Mendesak'][$ticket->priority] }}</span>
                                    </td>
                                    <td><span
                                            class="ticket-status status-{{ $ticket->status }}">{{ ['open' => 'Open', 'in_progress' => 'Proses', 'resolved' => 'Selesai'][$ticket->status] }}</span>
                                        @if ($ticket->approved_at)
                                            <small class="approved-note">Approved</small>
                                        @endif
                                    </td>
                                    <td class="text-nowrap">
                                        <a href="{{ route('it-repair-tickets.show', $ticket) }}"
                                            class="btn btn-sm btn-outline-secondary">Detail</a>
                                        @if (!auth()->user()->isEmployee())
                                            <a href="{{ route('it-repair-tickets.repair', $ticket) }}"
                                                class="btn btn-sm btn-outline-primary">Perbaiki</a>
                                        @endif
                                    </td>
                            </tr>@empty<tr>
                                    <td colspan="11" class="text-center text-muted py-4">Belum ada tiket perbaikan IT.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="table-pagination">{{ $tickets->links() }}</div>
            </div>
        </div>
    </div>
    <style>
        .repair-eyebrow {
            color: #0b5ea8;
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em
        }

        .repair-stat {
            display: block;
            padding: 15px 17px;
            background: #fff;
            border: 1px solid #dbe5ef;
            border-top: 4px solid #64748b;
            text-decoration: none;
            color: #17324d
        }

        .repair-stat span,
        .repair-stat small {
            display: block;
            color: #64748b;
            font-size: .76rem
        }

        .repair-stat strong {
            display: block;
            font-size: 1.65rem
        }

        .repair-stat.open {
            border-top-color: #f59e0b
        }

        .repair-stat.repair-in-progress {
            border-top-color: #0b5ea8
        }

        .repair-stat.resolved {
            border-top-color: #159957
        }

        .repair-list {
            border: 1px solid #dbe5ef
        }

        .repair-list .card-header {
            background: #f8fafc
        }

        .repair-filter {
            border: 1px solid #dbe5ef
        }

        .repair-filter .card-header {
            background: #f8fafc
        }

        .repair-filter .form-label {
            font-size: .76rem;
            font-weight: 700;
            color: #475569;
            margin-bottom: 3px
        }

        .my-equipment {
            border: 1px solid #dbe5ef
        }

        .my-equipment .card-header {
            background: #f8fafc;
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            justify-content: space-between;
            align-items: center
        }

        .my-equipment .card-header span {
            color: #64748b;
            font-size: .78rem
        }

        .my-equipment-card {
            height: 100%;
            padding: 13px 15px;
            background: #fff;
            border: 1px solid #dbe5ef;
            border-left: 4px solid #0b5ea8
        }

        .my-equipment-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 8px;
            margin-bottom: 8px
        }

        .my-equipment-head strong {
            font-size: .95rem;
            color: #17324d
        }

        .condition-chip {
            padding: 3px 7px;
            border-radius: 3px;
            font-size: .7rem;
            font-weight: 700;
            white-space: nowrap
        }

        .chip-good {
            background: #dcfce7;
            color: #166534
        }

        .chip-attention {
            background: #fee2e2;
            color: #991b1b
        }

        .my-equipment-card dl {
            display: grid;
            grid-template-columns: 112px 1fr;
            gap: 2px 8px;
            margin: 0 0 10px;
            font-size: .78rem
        }

        .my-equipment-card dt {
            color: #64748b;
            font-weight: 600
        }

        .my-equipment-card dd {
            margin: 0;
            color: #17324d
        }

        .repair-list small {
            display: block;
            color: #64748b
        }

        .problem-cell {
            max-width: 300px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis
        }

        .priority,
        .ticket-status {
            display: inline-block;
            padding: 4px 7px;
            border-radius: 3px;
            font-size: .74rem;
            font-weight: 700
        }

        .repair-kind {
            display: inline-block;
            padding: 4px 7px;
            border-radius: 3px;
            font-size: .72rem;
            font-weight: 700
        }

        .kind-hardware {
            background: #e0f2fe;
            color: #075985
        }

        .kind-software {
            background: #ede9fe;
            color: #5b21b6
        }

        .repair-kind-recap {
            display: flex;
            gap: 6px;
            align-items: center;
            flex-wrap: wrap
        }

        .priority-low {
            background: #e2e8f0;
            color: #475569
        }

        .priority-normal {
            background: #dbeafe;
            color: #1d4ed8
        }

        .priority-high {
            background: #ffedd5;
            color: #9a3412
        }

        .priority-urgent {
            background: #fee2e2;
            color: #991b1b
        }

        .status-open {
            background: #fef3c7;
            color: #92400e
        }

        .status-in_progress {
            background: #dbeafe;
            color: #1d4ed8
        }

        .status-resolved {
            background: #dcfce7;
            color: #166534
        }

        .approved-note {
            display: block;
            color: #166534;
            font-size: .7rem;
            font-weight: 700
        }
    </style>
@endsection
