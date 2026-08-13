@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Detail Jadwal: {{ $item->title }} - Tahun {{ $year ?? date('Y') }}</h3>
    <form method="GET" class="mb-3 row g-2">
        <div class="col-auto">
            <label class="form-label">Tahun</label>
            <input type="number" name="year" class="form-control" value="{{ $year ?? date('Y') }}" />
        </div>
        <div class="col-auto align-self-end">
            <button class="btn btn-secondary">Tampilkan</button>
        </div>
    </form>
    <div class="mb-3">
        <a href="{{ route('maintenances.schedules') }}" class="btn btn-secondary">Kembali</a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th rowspan="2">Peralatan</th>
                    @for($m=1;$m<=12;$m++)
                        <th colspan="4">{{ DateTime::createFromFormat('!m', $m)->format('M') }}</th>
                    @endfor
                </tr>
                <tr>
                        @for($m=1;$m<=12;$m++)
                            @for($w=1;$w<=4;$w++)
                                <th style="width:40px">W{{ $w }}</th>
                            @endfor
                        @endfor
                </tr>
            </thead>
            <tbody>
                @foreach($equipmentList as $eq)
                    <tr>
                        <td>{{ $eq->name }}</td>
                        @for($m=1;$m<=12;$m++)
                            @for($w=1;$w<=4;$w++)
                                @php
                                    $keyExact = $eq->id . '|' . $m . '|' . $w;
                                    $keyMonthAll = $eq->id . '|all|' . $w;
                                    $keyWeekAll = $eq->id . '|' . $m . '|all';
                                    $keyAllAll = $eq->id . '|all|all';
                                    $present = isset($grid[$keyExact]) || isset($grid[$keyMonthAll]) || isset($grid[$keyWeekAll]) || isset($grid[$keyAllAll]);
                                @endphp
                                <td class="text-center">@if($present) <span class="badge bg-success">✓</span> @else &nbsp; @endif</td>
                            @endfor
                        @endfor
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
