@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">Edit Manufacturer</div>
        <div class="card-body">
            <form method="POST" action="{{ route('masters.manufacturers.update', $manufacturer) }}">@csrf @method('PUT')
                <div class="mb-3"><label class="form-label">Nama</label><input name="name" value="{{ $manufacturer->name }}" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Country</label><input name="country" value="{{ $manufacturer->country }}" class="form-control"></div>
                <div class="mb-3"><label class="form-label">Notes</label><textarea name="notes" class="form-control">{{ $manufacturer->notes }}</textarea></div>
                <button class="btn btn-brand">Update</button>
            </form>
        </div>
    </div>
</div>
@endsection
