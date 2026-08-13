@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">Edit Location</div>
        <div class="card-body">
            <form method="POST" action="{{ route('masters.locations.update', $location) }}">@csrf @method('PUT')
                <div class="mb-3"><label class="form-label">Nama</label><input name="name" value="{{ $location->name }}" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Address</label><input name="address" value="{{ $location->address }}" class="form-control"></div>
                <div class="mb-3"><label class="form-label">Floor</label><input name="floor" value="{{ $location->floor }}" class="form-control"></div>
                <button class="btn btn-brand">Update</button>
            </form>
        </div>
    </div>
</div>
@endsection
