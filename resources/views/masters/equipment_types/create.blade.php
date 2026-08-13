@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">Tambah Tipe Peralatan</div>
        <div class="card-body">
            <form method="POST" action="{{ route('masters.equipment-types.store') }}">@csrf
                <div class="mb-3"><label class="form-label">Nama</label><input name="name" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Deskripsi</label><textarea name="description" class="form-control"></textarea></div>
                <button class="btn btn-brand">Simpan</button>
            </form>
        </div>
    </div>
</div>
@endsection
