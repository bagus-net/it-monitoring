@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card mb-3">
        <div class="card-header">Grid Checklist Perawatan - {{ $year }}</div>
        <div class="card-body">
    <form method="GET" class="row g-2 mb-3">
        <div class="col-auto">
            <label class="form-label">Peralatan</label>
            <select name="equipment_id" class="form-select">
                <option value="">-- Semua --</option>
                @foreach($equipments as $e)
                    <option value="{{ $e->id }}" {{ (string)$e->id === (string)$equipmentId ? 'selected' : '' }}>{{ $e->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <label class="form-label">Tahun</label>
            <input type="number" name="year" value="{{ $year }}" class="form-control" />
        </div>
        <div class="col-auto align-self-end">
            <button class="btn btn-brand">Tampilkan</button>
        </div>
    </form>
        <div class="table-responsive">
        <table class="table table-sm table-bordered">
        <thead>
            <tr>
                <th rowspan="2">Checklist</th>
                @for($m=1;$m<=12;$m++)
                    <th colspan="4" class="text-center">{{ DateTime::createFromFormat('!m', $m)->format('M') }}</th>
                @endfor
            </tr>
            <tr>
                @for($m=1;$m<=12;$m++)
                    @for($w=1;$w<=4;$w++)
                        <th class="text-center">W{{ $w }}</th>
                    @endfor
                @endfor
            </tr>
        </thead>
        <tbody>
        @foreach($items as $item)
            <tr>
                <td>{{ $item->title }}<br/><small class="text-muted">{{ $item->equipmentType->name ?? '-' }}</small></td>
                @for($m=1;$m<=12;$m++)
                    @for($w=1;$w<=4;$w++)
                        @php
                            if($equipmentId) {
                                $key = $equipmentId . '|' . $item->id . '|' . $m . '|' . $w;
                                $has = isset($logsIndex[$key]);
                            } else {
                                $anyKey = $item->id . '|' . $m . '|' . $w;
                                $has = isset($logsAny[$anyKey]);
                            }
                        @endphp
                        <td class="text-center month-cell">{!! $has ? '<span class="check-mark">&#10004;</span>' : '' !!}</td>
                    @endfor
                @endfor
            </tr>
        @endforeach
        </tbody>
    </table>
        </div>
        </div>
    </div>
</div>
@endsection
