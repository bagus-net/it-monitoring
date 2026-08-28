@extends('layouts.app')

@section('content')
<div class="container mt-4"><div class="d-flex justify-content-between align-items-center mb-3"><div><h2 class="mb-1">Edit Inovasi IT</h2><p class="text-muted mb-0">Perbarui informasi inovasi dan paper pendukungnya.</p></div><a href="{{ route('innovations.show', $innovation) }}" class="btn btn-outline-secondary">Kembali</a></div><form method="POST" action="{{ route('innovations.update', $innovation) }}" enctype="multipart/form-data" class="card"><div class="card-body">@csrf @method('PUT') @include('innovations.form')</div><div class="card-footer text-end"><button class="btn btn-primary">Simpan Perubahan</button></div></form></div>
@endsection
