@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Tambah Jadwal Perawatan</h1>
    <form method="POST" action="{{ route('maintenances.store_schedule') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">Program Perawatan (Pilih 1)</label>
            <select name="checklist_item_id" class="form-control">
                <option value="">-- Pilih Program --</option>
                @foreach($items as $it)
                    <option value="{{ $it->id }}">{{ $it->title }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Peralatan (centang untuk memilih; kosong = semua)</label>
            <div class="mb-2">
                <input type="checkbox" id="select_all_equipments"> <label for="select_all_equipments">Pilih Semua Peralatan</label>
            </div>
            <div class="card p-2" style="max-height:320px;overflow:auto">
                @foreach($equipmentsByType as $typeName => $typeEquipments)
                    @php($typeSlug = \Illuminate\Support\Str::slug($typeName))
                    <div class="equipment-type-group mb-2">
                        <div class="form-check border-bottom pb-1 mb-1">
                            <input class="form-check-input equipment-type-toggle" type="checkbox" data-type-group="{{ $typeSlug }}" id="type_{{ $typeSlug }}">
                            <label class="form-check-label fw-bold" for="type_{{ $typeSlug }}">Pilih Semua {{ $typeName }} ({{ $typeEquipments->count() }})</label>
                        </div>
                        @foreach($typeEquipments as $e)
                            <div class="form-check ms-3">
                                <input class="form-check-input equipment-checkbox" data-type-group="{{ $typeSlug }}" type="checkbox" name="equipment_ids[]" value="{{ $e->id }}" id="eq{{ $e->id }}">
                                <label class="form-check-label" for="eq{{ $e->id }}">{{ $e->name }}</label>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Frekuensi</label>
            <select name="frequency" id="frequency" class="form-control">
                {{-- <option value="monthly">Bulanan</option> --}}
                <option value="annual">Tahunan</option>
            </select>
        </div>

        <div id="monthly-options">
            <div class="mb-3">
                <label class="form-label">Hari (1-31) - untuk perawatan bulanan</label>
                <input name="day_of_month" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Minggu ke dalam bulan (opsional, pilih lebih dari satu)</label>
                <div class="d-flex flex-wrap">
                    @for($w=1;$w<=4;$w++)
                        <div class="form-check me-3">
                            <input class="form-check-input" type="checkbox" name="weeks[]" value="{{ $w }}" id="mweek{{ $w }}">
                            <label class="form-check-label" for="mweek{{ $w }}">Minggu ke-{{ $w }}</label>
                        </div>
                    @endfor
                </div>
            </div>
        </div>

        <div id="annual-options" style="display:none">
            <div class="mb-3">
                <label class="form-label">Pilih Bulan (untuk jadwal tahunan)</label>
                <div class="d-flex flex-wrap">
                    @for($m=1;$m<=12;$m++)
                        <div class="form-check me-3">
                            <input class="form-check-input" type="checkbox" name="months[]" value="{{ $m }}" id="month{{ $m }}">
                            <label class="form-check-label" for="month{{ $m }}">{{ DateTime::createFromFormat('!m', $m)->format('M') }}</label>
                        </div>
                    @endfor
                </div>
            </div>
                <div class="mb-3">
                    <label class="form-label">Minggu ke dalam bulan (boleh pilih lebih dari satu)</label>
                    <div class="d-flex flex-wrap">
                        @for($w=1;$w<=4;$w++)
                            <div class="form-check me-3">
                                <input class="form-check-input" type="checkbox" name="weeks[]" value="{{ $w }}" id="week{{ $w }}">
                                <label class="form-check-label" for="week{{ $w }}">Minggu ke-{{ $w }}</label>
                            </div>
                        @endfor
                    </div>
                </div>
        </div>

        <button class="btn btn-primary">Simpan</button>
    </form>

    <script>
        document.getElementById('select_all_equipments').addEventListener('change', function(e){
            document.querySelectorAll('.equipment-checkbox').forEach(cb => cb.checked = e.target.checked);
            document.querySelectorAll('.equipment-type-toggle').forEach(cb => cb.checked = e.target.checked);
        });
        document.querySelectorAll('.equipment-type-toggle').forEach(function(typeToggle){
            typeToggle.addEventListener('change', function(e){
                const group = e.target.dataset.typeGroup;
                document.querySelectorAll('.equipment-checkbox[data-type-group="' + group + '"]').forEach(cb => cb.checked = e.target.checked);
            });
        });
        document.getElementById('frequency').addEventListener('change', function(e){
            if(e.target.value === 'annual'){
                document.getElementById('annual-options').style.display = '';
                document.getElementById('monthly-options').style.display = 'none';
            } else {
                document.getElementById('annual-options').style.display = 'none';
                document.getElementById('monthly-options').style.display = '';
            }
        });
    </script>
</div>
@endsection
