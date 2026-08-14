@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h2 style="color:{{ $checklistItem->schedule_color }}; margin-bottom:4px;">📋 {{ $checklistItem->title }}</h2>
                    <p class="text-muted mb-0">Detail Jadwal Bulanan - Tahun {{ $year }}</p>
                </div>
                <div>
                    <a href="{{ route('monthly_schedules.select_months', [$checklistItem->id, $year]) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    <a href="{{ route('monthly_schedules.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
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
