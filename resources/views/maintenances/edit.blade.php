@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Jadwal Perawatan: {{ $item->title }}</h1>
    <form method="POST" action="{{ route('maintenances.schedules.update', $item->id) }}">
        @csrf
        @method('PUT')
        <input type="hidden" name="checklist_item_id" value="{{ $item->id }}">

        <div class="mb-3">
            <label class="form-label">Peralatan (centang untuk memilih; kosong = semua)</label>
            <div class="mb-2">
                <input type="checkbox" id="select_all_equipments"> <label for="select_all_equipments">Pilih Semua</label>
            </div>
            <div class="card p-2" style="max-height:220px;overflow:auto">
                @foreach($equipments as $e)
                    <div class="form-check">
                        <input class="form-check-input equipment-checkbox" type="checkbox" name="equipment_ids[]" value="{{ $e->id }}" id="eq{{ $e->id }}" {{ in_array($e->id, $selectedEquipments) ? 'checked' : '' }}>
                        <label class="form-check-label" for="eq{{ $e->id }}">{{ $e->name }}</label>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Frekuensi</label>
            <select name="frequency" id="frequency" class="form-control">
                <option value="monthly" {{ $frequency === 'monthly' ? 'selected' : '' }}>Bulanan</option>
                <option value="annual" {{ $frequency === 'annual' ? 'selected' : '' }}>Tahunan</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Tahun Jadwal</label>
            <input type="number" name="year" class="form-control" value="{{ $year ?? date('Y') }}" />
        </div>

        <div id="monthly-options">
            <div class="mb-3">
                <label class="form-label">Hari (1-31) - untuk perawatan bulanan</label>
                <input name="day_of_month" class="form-control" value="{{ $day ?? '' }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Minggu ke dalam bulan (opsional, pilih lebih dari satu)</label>
                <div class="d-flex flex-wrap">
                    @for($w=1;$w<=4;$w++)
                        <div class="form-check me-3">
                            <input class="form-check-input" type="checkbox" name="weeks[]" value="{{ $w }}" id="mweek{{ $w }}" {{ in_array($w, $weeks) ? 'checked' : '' }}>
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
                            <input class="form-check-input" type="checkbox" name="months[]" value="{{ $m }}" id="month{{ $m }}" {{ in_array($m, $months) ? 'checked' : '' }}>
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
                                <input class="form-check-input" type="checkbox" name="weeks[]" value="{{ $w }}" id="week{{ $w }}" {{ in_array($w, $weeks) ? 'checked' : '' }}>
                                <label class="form-check-label" for="week{{ $w }}">Minggu ke-{{ $w }}</label>
                            </div>
                        @endfor
                    </div>
                </div>
        </div>

        <button class="btn btn-primary">Simpan Perubahan</button>
    </form>

    <script>
        document.getElementById('select_all_equipments').addEventListener('change', function(e){
            document.querySelectorAll('.equipment-checkbox').forEach(cb => cb.checked = e.target.checked);
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
        // initialize visibility based on selected frequency
        (function(){
            var sel = document.getElementById('frequency');
            var evt = new Event('change'); sel.dispatchEvent(evt);
        })();
        // initialize select_all state
        (function(){
            var all = Array.from(document.querySelectorAll('.equipment-checkbox'));
            if(all.length === 0) return;
            var checked = all.filter(cb => cb.checked).length;
            if(checked === 0){
                // treat empty selection as 'all' for UX: check all boxes
                all.forEach(cb => cb.checked = true);
                document.getElementById('select_all_equipments').checked = true;
            } else if(checked === all.length){
                document.getElementById('select_all_equipments').checked = true;
            }
        })();
    </script>
</div>
@endsection
