@extends('layouts.app')

@section('content')
<div class="container mt-4" style="--program-color:{{ $checklistItem->schedule_color }}; --program-tint:{{ $checklistItem->schedule_tint }};">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h2 style="color:var(--program-color);">📅 Pilih Bulan</h2>
            <p class="text-muted">
                <strong>{{ $checklistItem->title }}</strong> - Tahun {{ $year }}
            </p>

            <form method="GET" action="{{ route('monthly_schedules.edit', [$checklistItem->id, $year]) }}">
                <div class="card shadow-sm">
                    <div class="card-header" style="background-color:var(--program-tint); color:var(--program-color);">
                        <h5 class="mb-0">Centang bulan yang ingin ditentukan tanggal pelaksanaannya</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach ($months as $m)
                                <div class="col-md-3 col-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                               name="months[]" value="{{ $m }}"
                                               id="month_{{ $m }}" checked>
                                        <label class="form-check-label" for="month_{{ $m }}">
                                            {{ $monthNames[$m] ?? $m }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-arrow-right"></i> Lanjut ke Pemilihan Tanggal
                    </button>
                    <a href="{{ route('monthly_schedules.create') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .form-check-input:checked {
        background-color: var(--program-color);
        border-color: var(--program-color);
    }
</style>
@endsection
