@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4 monthly-schedule-page monthly-schedule-form-page" style="--program-color:{{ $checklistItem->schedule_color }}; --program-tint:{{ $checklistItem->schedule_tint }};">
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

                <div class="row">
                @forelse ($monthsData as $month => $data)
                    <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header py-2" style="background-color:var(--program-tint); color:var(--program-color);">
                            <h6 class="mb-0">{{ $data['name'] }}</h6>
                        </div>
                        <div class="card-body py-2">
                            @if ($equipment->isNotEmpty())
                                <div class="mb-2 p-2 border rounded template-row">
                                    <small class="text-muted d-block mb-1"><i class="bi bi-files"></i> Salin/kembarkan jadwal dari Program, Tahun &amp; Bulan lain</small>
                                    <div class="d-flex flex-wrap gap-2 align-items-end">
                                        <div>
                                            <label class="form-label mb-0 small">Program</label>
                                            <select class="form-select form-select-sm template-program">
                                                @foreach ($items as $programOption)
                                                    <option value="{{ $programOption->id }}" {{ $programOption->id === $checklistItem->id ? 'selected' : '' }}>{{ $programOption->title }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="form-label mb-0 small">Tahun</label>
                                            <input type="number" class="form-control form-control-sm template-year" style="width:85px;" value="{{ $year }}" min="2000" max="2100">
                                        </div>
                                        <div>
                                            <label class="form-label mb-0 small">Bulan</label>
                                            <select class="form-select form-select-sm template-month">
                                                @foreach ($monthNames as $monthNumber => $monthLabel)
                                                    <option value="{{ $monthNumber }}" {{ $monthNumber === $month ? 'selected' : '' }}>{{ $monthLabel }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-primary template-apply-btn" data-target-month="{{ $month }}">
                                            <i class="bi bi-clipboard-check"></i> Terapkan Template
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger reset-month-btn" data-target-month="{{ $month }}">
                                            <i class="bi bi-x-circle"></i> Reset Bulan Ini
                                        </button>
                                    </div>
                                </div>
                                <div class="mb-2 p-2 border rounded date-chip-master-row">
                                    <small class="text-muted d-block mb-1"><i class="bi bi-check2-square"></i> Centang untuk pilih tanggal ke semua peralatan</small>
                                    <div class="date-chip-group">
                                        @for ($day = 1; $day <= $data['days_in_month']; $day++)
                                            <input
                                                class="date-chip-input date-chip-master"
                                                type="checkbox"
                                                data-month="{{ $month }}"
                                                data-day="{{ $day }}"
                                                id="master_{{ $month }}_{{ $day }}"
                                            >
                                            <label class="date-chip" for="master_{{ $month }}_{{ $day }}">{{ $day }}</label>
                                        @endfor
                                    </div>
                                </div>
                            @endif
                            @forelse ($equipment as $item)
                                @php
                                    $existingSchedule = $data['schedules']->get($item->id);
                                    $selectedDates = $existingSchedule ? $existingSchedule->dates : [];
                                @endphp
                                <div class="mb-2 p-2 border rounded" style="background-color:var(--program-tint);">
                                    <h6 class="mb-1" style="color:var(--program-color);">
                                        <i class="bi bi-hdd"></i> {{ $item->name }}
                                    </h6>

                                    <div class="date-chip-group">
                                        @for ($day = 1; $day <= $data['days_in_month']; $day++)
                                            <input
                                                class="date-chip-input date-chip-row"
                                                type="checkbox"
                                                name="equipment_dates[{{ $month }}][{{ $item->id }}][]"
                                                value="{{ $day }}"
                                                data-month="{{ $month }}"
                                                data-day="{{ $day }}"
                                                data-equipment="{{ $item->id }}"
                                                id="date_{{ $month }}_{{ $item->id }}_{{ $day }}"
                                                {{ in_array($day, $selectedDates) ? 'checked' : '' }}
                                            >
                                            <label class="date-chip" for="date_{{ $month }}_{{ $item->id }}_{{ $day }}">{{ $day }}</label>
                                        @endfor
                                    </div>

                                    @if ($existingSchedule && $existingSchedule->notes)
                                        <small class="text-muted d-block mt-1">
                                            <strong>Catatan:</strong> {{ $existingSchedule->notes }}
                                        </small>
                                    @endif
                                </div>
                            @empty
                                <div class="alert alert-info py-2 mb-0">
                                    <i class="bi bi-info-circle"></i>
                                    Tidak ada peralatan untuk Program Perawatan ini.
                                    Pastikan ada jadwal tahunan yang sudah dibuat terlebih dahulu.
                                </div>
                            @endforelse
                        </div>
                    </div>
                    </div>
                @empty
                    <div class="col-12">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Tidak ada bulan yang dipilih.
                    </div>
                    </div>
                @endforelse
                </div>

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
        gap: 4px;
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
        width: 26px;
        height: 26px;
        font-size: 0.75rem;
        border: 1px solid #ddd;
        border-radius: 5px;
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

    .date-chip-master-row {
        background-color: #fff;
        border-style: dashed !important;
    }

    .date-chip-master:checked + .date-chip {
        background-color: #555;
        border-color: #555;
    }

    .template-row {
        background-color: #fff;
    }

    .template-row .form-select,
    .template-row .form-control {
        min-width: 110px;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const masters = document.querySelectorAll('.date-chip-master');

        function rowInputsFor(month, day) {
            return document.querySelectorAll('.date-chip-row[data-month="' + month + '"][data-day="' + day + '"]');
        }

        function syncMaster(master) {
            const rows = rowInputsFor(master.dataset.month, master.dataset.day);
            master.checked = rows.length > 0 && [...rows].every(function (input) { return input.checked; });
        }

        masters.forEach(function (master) {
            // Reflect current selection state when the page loads
            syncMaster(master);

            master.addEventListener('change', function () {
                rowInputsFor(master.dataset.month, master.dataset.day).forEach(function (input) {
                    input.checked = master.checked;
                });
            });
        });

        document.querySelectorAll('.date-chip-row').forEach(function (input) {
            input.addEventListener('change', function () {
                const master = document.getElementById('master_' + input.dataset.month + '_' + input.dataset.day);
                if (master) {
                    syncMaster(master);
                }
            });
        });

        document.querySelectorAll('.template-apply-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const card = btn.closest('.card-body');
                const targetMonth = btn.dataset.targetMonth;
                const params = new URLSearchParams({
                    checklist_item_id: card.querySelector('.template-program').value,
                    year: card.querySelector('.template-year').value,
                    month: card.querySelector('.template-month').value,
                });

                btn.disabled = true;
                const originalLabel = btn.innerHTML;
                btn.innerHTML = 'Memuat...';

                fetch('{{ route('monthly_schedules.template_dates') }}?' + params.toString())
                    .then(function (response) { return response.json(); })
                    .then(function (json) {
                        const templateDates = json.dates || {};

                        card.querySelectorAll('.date-chip-row[data-month="' + targetMonth + '"]').forEach(function (input) {
                            const days = templateDates[input.dataset.equipment] || [];
                            input.checked = days.includes(parseInt(input.dataset.day, 10));
                        });

                        card.querySelectorAll('.date-chip-master[data-month="' + targetMonth + '"]').forEach(syncMaster);
                    })
                    .catch(function () {
                        alert('Gagal mengambil data template. Silakan coba lagi.');
                    })
                    .finally(function () {
                        btn.disabled = false;
                        btn.innerHTML = originalLabel;
                    });
            });
        });

        document.querySelectorAll('.reset-month-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                if (!confirm('Kosongkan semua tanggal yang dicentang untuk bulan ini?')) {
                    return;
                }

                const card = btn.closest('.card-body');
                const targetMonth = btn.dataset.targetMonth;

                card.querySelectorAll('.date-chip-row[data-month="' + targetMonth + '"]').forEach(function (input) {
                    input.checked = false;
                });
                card.querySelectorAll('.date-chip-master[data-month="' + targetMonth + '"]').forEach(function (master) {
                    master.checked = false;
                });
            });
        });
    });
</script>
@endsection

