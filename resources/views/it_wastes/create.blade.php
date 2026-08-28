@extends('layouts.app')

@section('content')
<div class="container mt-4"><div class="d-flex justify-content-between align-items-center mb-3"><div><h2 class="mb-1">Tambah Limbah IT</h2><p class="text-muted mb-0">Catat limbah tinta, hasil cleaning printer, dan limbah IT lainnya.</p></div><a href="{{ route('it-wastes.index') }}" class="btn btn-outline-secondary">Kembali</a></div><form method="POST" action="{{ route('it-wastes.store') }}" class="card"><div class="card-body">@csrf @include('it_wastes.form')</div><div class="card-footer text-end"><button class="btn btn-primary">Simpan Data Limbah</button></div></form></div>
@endsection
