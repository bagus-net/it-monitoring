@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <div class="text-uppercase fw-bold small text-primary">Pengaturan</div>
            <h2 class="mb-1">Edit User</h2>
            <p class="text-muted mb-0">{{ $user->name }} · {{ $user->email }}</p>
        </div>
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Kembali</a>
    </div>
    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0 ps-3">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('users.update', $user) }}" enctype="multipart/form-data">
            @method('PUT')
            @include('users._form')
        </form>
    </div></div>
</div>
@endsection
