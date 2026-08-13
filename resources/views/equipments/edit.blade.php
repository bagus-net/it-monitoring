
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card card-colorful">
        <div class="card-header">Edit Peralatan IT</div>
        <div class="card-body">
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form method="POST" action="{{ route('equipments.update', $equipment) }}">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">Nama</label>
            <input name="name" class="form-control" required value="{{ old('name', $equipment->name) }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Tipe</label>
            <select name="equipment_type_id" class="form-control">
                <option value="">-- Pilih --</option>
                @foreach($types as $t)
                    <option value="{{ $t->id }}" {{ (old('equipment_type_id', $equipment->equipment_type_id) == $t->id) ? 'selected' : '' }}>{{ $t->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Serial</label>
            <input name="serial_number" class="form-control" value="{{ old('serial_number', $equipment->serial_number) }}">
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Manufacturer</label>
                <select name="manufacturer_id" class="form-control">
                    <option value="">-- Pilih --</option>
                    @foreach($manufacturers as $m)
                        <option value="{{ $m->id }}" {{ (old('manufacturer_id', $equipment->manufacturer_id) == $m->id) ? 'selected' : '' }}>{{ $m->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Lokasi</label>
                <select name="location_id" class="form-control">
                    <option value="">-- Pilih --</option>
                    @foreach($locations as $l)
                        <option value="{{ $l->id }}" {{ (old('location_id', $equipment->location_id) == $l->id) ? 'selected' : '' }}>{{ $l->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Kapasitas / Spesifikasi singkat</label>
                <input name="capacity" class="form-control" value="{{ old('capacity', $equipment->capacity) }}">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Tahun Pembuatan / Pembelian</label>
                <input name="manufacture_year" type="number" class="form-control" placeholder="YYYY" value="{{ old('manufacture_year', $equipment->manufacture_year) }}">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Spesifikasi (detail)</label>
            <textarea name="specification" class="form-control">{{ old('specification', $equipment->specification) }}</textarea>
        </div>
        <div class="row">
                <div class="col-md-6">
                <label class="form-label">IP Address</label>
                <input name="ip_address" class="form-control" placeholder="192.168.x.x" value="{{ old('ip_address', $equipment->ip_address) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Kondisi</label>
                <select name="condition" class="form-control">
                    <option value="">-- Pilih --</option>
                    <option value="baik" {{ (old('condition', $equipment->condition) == 'baik')? 'selected':'' }}>Baik</option>
                    <option value="rusak" {{ (old('condition', $equipment->condition) == 'rusak')? 'selected':'' }}>Rusak</option>
                    <option value="perbaikan" {{ (old('condition', $equipment->condition) == 'perbaikan')? 'selected':'' }}>Perbaikan</option>
                </select>
            </div>
            <div class="col-md-3 align-self-end">
                <button class="btn btn-brand">Simpan</button>
            </div>
        </div>
    </form>
        </div>
    </div>
</div>
@endsection
