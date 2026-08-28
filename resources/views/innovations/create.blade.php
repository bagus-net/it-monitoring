@extends('layouts.app')

@section('content')
<div class="container mt-4"><div class="d-flex justify-content-between align-items-center mb-3"><div><h2 class="mb-1">Tambah Inovasi IT</h2><p class="text-muted mb-0">Catat inovasi dan unggah paper pendukungnya.</p></div><a href="{{ route('innovations.index') }}" class="btn btn-outline-secondary">Kembali</a></div><form method="POST" action="{{ route('innovations.store') }}" enctype="multipart/form-data" class="card"><div class="card-body">@csrf @include('innovations.form')</div><div class="card-footer text-end"><button class="btn btn-primary">Simpan Inovasi</button></div></form></div>
@endsection
