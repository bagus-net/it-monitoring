@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">Edit Tipe Peralatan</div>
        <div class="card-body">
            <form method="POST" action="{{ route('masters.equipment-types.update', $item) }}">@csrf @method('PUT')
                <div class="mb-3"><label class="form-label">Nama</label><input name="name" value="{{ $item->name }}" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Deskripsi</label><textarea name="description" class="form-control">{{ $item->description }}</textarea></div>
                <button class="btn btn-brand">Update</button>
            </form>
        </div>
    </div>
</div>
@endsection
