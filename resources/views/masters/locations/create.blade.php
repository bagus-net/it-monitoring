@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">Tambah Location</div>
        <div class="card-body">
            <form method="POST" action="{{ route('masters.locations.store') }}">@csrf
                <div class="mb-3"><label class="form-label">Nama</label><input name="name" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Address</label><input name="address" class="form-control"></div>
                <div class="mb-3"><label class="form-label">Floor</label><input name="floor" class="form-control"></div>
                <button class="btn btn-brand">Simpan</button>
            </form>
        </div>
    </div>
</div>
@endsection
