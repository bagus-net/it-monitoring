@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <h2 style="color: #ff9800;">📋 Buat Jadwal Bulanan</h2>
            <p class="text-muted">Pilih jadwal tahunan yang akan dijadwalkan tanggal pelaksanaannya per bulan.</p>

            @if ($message = Session::get('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ $message }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (count($annualSchedules))
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
                            @php
                                $monthShort = [
                                    1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
                                    5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
                                    9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
                                ];
                            @endphp
                            @foreach ($annualSchedules as $sched)
                                @php $ci = $items->get($sched['checklist_item_id']); @endphp
                                <tr>
                                    <td><strong>{{ $ci->title ?? 'N/A' }}</strong></td>
                                    <td>{{ $sched['year'] }}</td>
                                    <td>
                                        @if (empty($sched['months']))
                                            <span class="badge bg-secondary">Semua Bulan</span>
                                        @else
                                            @foreach ($sched['months'] as $m)
                                                <span class="badge bg-info">{{ $monthShort[$m] ?? $m }}</span>
                                            @endforeach
                                        @endif
                                    </td>
                                    <td>{{ $sched['equipment_count'] }}</td>
                                    <td>
                                        <a href="{{ route('monthly_schedules.select_months', [$sched['checklist_item_id'], $sched['year']]) }}"
                                           class="btn btn-sm btn-primary">
                                            <i class="bi bi-arrow-right-circle"></i> Pilih
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i>
                    Belum ada jadwal tahunan. Buat jadwal tahunan terlebih dahulu di menu
                    <a href="{{ route('maintenances.create') }}">Jadwal Tahunan</a>.
                </div>
            @endif

            <div class="mt-3">
                <a href="{{ route('monthly_schedules.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
