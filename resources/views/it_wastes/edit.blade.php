@extends('layouts.app')

@section('content')
<div class="container mt-4"><div class="d-flex justify-content-between align-items-center mb-3"><div><h2 class="mb-1">Edit Limbah IT</h2><p class="text-muted mb-0">Perbarui data limbah IT yang telah dicatat.</p></div><a href="{{ route('it-wastes.index') }}" class="btn btn-outline-secondary">Kembali</a></div><form method="POST" action="{{ route('it-wastes.update', $itWaste) }}" class="card"><div class="card-body">@csrf @method('PUT') @include('it_wastes.form')</div><div class="card-footer text-end"><button class="btn btn-primary">Simpan Perubahan</button></div></form></div>
@endsection
