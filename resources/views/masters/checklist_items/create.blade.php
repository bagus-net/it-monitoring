@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Tambah Program Perawatan</h1>
    <form method="POST" action="{{ route('masters.checklist-items.store') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">Judul</label>
            <input name="title" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Kategori (Group)</label>
            <select name="category" class="form-control">
                <option value="">-- Pilih Kategori --</option>
                <option value="Perawatan Software">Perawatan Software</option>
                <option value="Perawatan Hardware">Perawatan Hardware</option>
                <option value="Perawatan Networking">Perawatan Networking</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Tipe Peralatan (opsional)</label>
            <select name="equipment_type_id" class="form-control">
                <option value="">-- Semua --</option>
                @foreach($types as $t)
                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Frekuensi</label>
            <select name="frequency" class="form-control">
                <option value="monthly">Bulanan</option>
                <option value="annual">Tahunan</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Keterangan</label>
            <textarea name="description" class="form-control"></textarea>
        </div>
        <button class="btn btn-primary">Simpan</button>
    </form>
</div>
@endsection
