@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 style="color: #ff9800;">📋 Jadwal Bulanan</h2>
                <a href="{{ route('monthly_schedules.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Buat Jadwal Baru
                </a>
            </div>

            @if ($message = Session::get('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ $message }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form method="GET" action="{{ route('monthly_schedules.index') }}" class="row g-3 align-items-end mb-3 p-3 border rounded bg-light">
                <div class="col-md-3">
                    <label for="filter_year" class="form-label mb-1">Periode / Tahun</label>
                    <select id="filter_year" name="year" class="form-select">
                        <option value="">Semua Tahun</option>
                        @foreach ($availableYears as $year)
                            <option value="{{ $year }}" {{ $selectedYear === (int) $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filter_month" class="form-label mb-1">Filter Bulan</label>
                    <select id="filter_month" name="month" class="form-select">
                        <option value="">Semua Bulan</option>
                        @foreach ([1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'] as $monthNumber => $monthName)
                            <option value="{{ $monthNumber }}" {{ $selectedMonth === $monthNumber ? 'selected' : '' }}>{{ $monthName }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="filter_program" class="form-label mb-1">Filter Program Perawatan</label>
                    <select id="filter_program" name="checklist_item_id" class="form-select">
                        <option value="">Semua Program Perawatan</option>
                        @foreach ($programOptions as $program)
                            <option value="{{ $program->id }}" {{ $selectedProgram === $program->id ? 'selected' : '' }}>{{ $program->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-funnel"></i> Filter</button>
                    <a href="{{ route('monthly_schedules.index') }}" class="btn btn-outline-secondary" title="Reset Filter"><i class="bi bi-arrow-counterclockwise"></i></a>
                </div>
            </form>

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100 bg-white">
                        <div class="text-muted small">Program Terjadwal</div>
                        <strong class="fs-4">{{ $summary['program_count'] }}</strong>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100 bg-white">
                        <div class="text-muted small">Peralatan Terjadwal</div>
                        <strong class="fs-4">{{ $summary['equipment_count'] }}</strong>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100 bg-white">
                        <div class="text-muted small">Bulan Terjadwal</div>
                        <strong class="fs-4">{{ $summary['month_count'] }}</strong>
                    </div>
                </div>
            </div>

            @if ($groups->count())
                <form method="GET" action="{{ route('monthly_schedules.print_month') }}" target="_blank" class="d-flex flex-wrap align-items-end gap-2 mb-3 p-3 border rounded bg-light">
                    <div>
                        <label for="print_year" class="form-label mb-1">Tahun Cetak</label>
                        <select id="print_year" name="year" class="form-select" required>
                            @foreach ($availableYears as $printYear)
                                <option value="{{ $printYear }}" {{ $selectedYear === (int) $printYear ? 'selected' : '' }}>{{ $printYear }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="print_month" class="form-label mb-1">Bulan Cetak</label>
                        <select id="print_month" name="month" class="form-select" required>
                            @foreach ([1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'] as $monthNumber => $monthName)
                                <option value="{{ $monthNumber }}" {{ $selectedMonth === $monthNumber ? 'selected' : '' }}>{{ $monthName }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-printer"></i> Cetak Jadwal Bulanan
                    </button>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle">
                        <thead class="table-warning">
                            <tr>
                                <th>Program Perawatan</th>
                                <th>Tahun</th>
                                <th>Bulan Terjadwal</th>
                                <th>Jumlah Peralatan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($groups as $group)
                                <tr>
                                    <td><span class="program-dot" style="background-color:{{ $group['checklist_item']->schedule_color }}"></span><strong>{{ $group['checklist_item']->title ?? 'N/A' }}</strong></td>
                                    <td>{{ $group['year'] }}</td>
                                    <td>
                                        @foreach ($group['month_labels'] as $label)
                                            <span class="badge bg-info">{{ $label }}</span>
                                        @endforeach
                                    </td>
                                    <td>{{ $group['equipment_count'] }}</td>
                                    <td>
                                        <a href="{{ route('monthly_schedules.show', [$group['checklist_item_id'], $group['year']]) }}"
                                           class="btn btn-sm btn-info" title="Detail">
                                            <i class="bi bi-eye"></i> Detail
                                        </a>
                                        <a href="{{ route('monthly_schedules.select_months', [$group['checklist_item_id'], $group['year']]) }}"
                                           class="btn btn-sm btn-warning" title="Edit">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                        <form method="POST" action="{{ route('monthly_schedules.destroy', [$group['checklist_item_id'], $group['year']]) }}"
                                              style="display:inline;"
                                              onsubmit="return confirm('Yakin ingin menghapus seluruh jadwal bulanan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                <i class="bi bi-trash"></i> Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            @else
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Belum ada jadwal bulanan. <a href="{{ route('monthly_schedules.create') }}">Buat jadwal baru</a>
                </div>
            @endif
        </div>
    </div>
</div>
<style>.program-dot { display:inline-block; width:10px; height:10px; border-radius:50%; margin-right:7px; }</style>
@endsection
