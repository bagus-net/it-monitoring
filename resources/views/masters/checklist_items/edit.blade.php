@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Program Perawatan</h1>
    <form method="POST" action="{{ route('masters.checklist-items.update', $item) }}">
        @csrf @method('PUT')
        <div class="mb-3">
            <label class="form-label">Judul</label>
            <input name="title" class="form-control" required value="{{ old('title', $item->title) }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Kategori (Group)</label>
            <select name="category" class="form-control">
                <option value="">-- Pilih Kategori --</option>
                <option value="Perawatan Software" {{ (old('category', $item->category)=='Perawatan Software')?'selected':'' }}>Perawatan Software</option>
                <option value="Perawatan Hardware" {{ (old('category', $item->category)=='Perawatan Hardware')?'selected':'' }}>Perawatan Hardware</option>
                <option value="Perawatan Networking" {{ (old('category', $item->category)=='Perawatan Networking')?'selected':'' }}>Perawatan Networking</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Tipe Peralatan (opsional)</label>
            <select name="equipment_type_id" class="form-control">
                <option value="">-- Semua --</option>
                @foreach($types as $t)
                    <option value="{{ $t->id }}" {{ (old('equipment_type_id', $item->equipment_type_id) == $t->id)?'selected':'' }}>{{ $t->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Frekuensi</label>
            <select name="frequency" class="form-control">
                <option value="monthly" {{ (old('frequency', $item->frequency)=='monthly')?'selected':'' }}>Bulanan</option>
                <option value="annual" {{ (old('frequency', $item->frequency)=='annual')?'selected':'' }}>Tahunan</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Keterangan</label>
            <textarea name="description" class="form-control">{{ old('description', $item->description) }}</textarea>
        </div>
        <button class="btn btn-primary">Simpan</button>
    </form>
</div>
@endsection
