@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4" style="--program-color:{{ $checklistItem->schedule_color }}; --program-tint:{{ $checklistItem->schedule_tint }};">
    <div class="row">
        <div class="col-md-12">
            <h2 style="color:var(--program-color);">
                📅 Jadwal Bulanan - {{ $checklistItem->title }} ({{ $year }})
            </h2>

            @if ($message = Session::get('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ $message }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('monthly_schedules.store') }}">
                @csrf
                <input type="hidden" name="checklist_item_id" value="{{ $checklistItem->id }}">
                <input type="hidden" name="year" value="{{ $year }}">

                @forelse ($monthsData as $month => $data)
                    <div class="card shadow-sm mb-4">
                        <div class="card-header" style="background-color:var(--program-tint); color:var(--program-color);">
                            <h5 class="mb-0">{{ $data['name'] }}</h5>
                        </div>
                        <div class="card-body">
                            @forelse ($equipment as $item)
                                @php
                                    $existingSchedule = $data['schedules']->get($item->id);
                                    $selectedDates = $existingSchedule ? $existingSchedule->dates : [];
                                @endphp
                                <div class="mb-3 p-2 border rounded" style="background-color:var(--program-tint);">
                                    <h6 class="mb-2" style="color:var(--program-color);">
                                        <i class="bi bi-hdd"></i> {{ $item->name }}
                                    </h6>

                                    <div class="date-chip-group">
                                        @for ($day = 1; $day <= $data['days_in_month']; $day++)
                                            <input
                                                class="date-chip-input"
                                                type="checkbox"
                                                name="equipment_dates[{{ $month }}][{{ $item->id }}][]"
                                                value="{{ $day }}"
                                                id="date_{{ $month }}_{{ $item->id }}_{{ $day }}"
                                                {{ in_array($day, $selectedDates) ? 'checked' : '' }}
                                            >
                                            <label class="date-chip" for="date_{{ $month }}_{{ $item->id }}_{{ $day }}">{{ $day }}</label>
                                        @endfor
                                    </div>

                                    @if ($existingSchedule && $existingSchedule->notes)
                                        <small class="text-muted d-block mt-2">
                                            <strong>Catatan:</strong> {{ $existingSchedule->notes }}
                                        </small>
                                    @endif
                                </div>
                            @empty
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i>
                                    Tidak ada peralatan untuk Program Perawatan ini.
                                    Pastikan ada jadwal tahunan yang sudah dibuat terlebih dahulu.
                                </div>
                            @endforelse
                        </div>
                    </div>
                @empty
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Tidak ada bulan yang dipilih.
                    </div>
                @endforelse

                <div class="mt-3">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="bi bi-check-circle"></i> Simpan Jadwal Bulanan
                    </button>
                    <a href="{{ route('monthly_schedules.index') }}" class="btn btn-secondary btn-lg">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .date-chip-group {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .date-chip-input {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }

    .date-chip {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        font-size: 0.85rem;
        border: 1px solid #ddd;
        border-radius: 6px;
        background-color: white;
        color: #555;
        cursor: pointer;
        user-select: none;
        transition: all 0.15s;
    }

    .date-chip:hover {
        border-color: var(--program-color);
    }

    .date-chip-input:checked + .date-chip {
        background-color: var(--program-color);
        border-color: var(--program-color);
        color: #fff;
        font-weight: 600;
    }

    .date-chip-input:focus + .date-chip {
        box-shadow: 0 0 0 0.2rem var(--program-tint);
    }
</style>
@endsection

