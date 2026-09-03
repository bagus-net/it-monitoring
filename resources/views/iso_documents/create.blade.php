@extends('layouts.app')

@section('content')
<div class="container mt-4"><div class="d-flex justify-content-between align-items-center mb-3"><div><h2 class="mb-1">Buat Folder Dokumen ISO</h2><p class="text-muted mb-0">Folder hanya akan tersedia bagi pengguna yang Anda pilih. File bisa ditambahkan sekarang atau menyusul.</p></div><a href="{{ route('iso-documents.index') }}" class="btn btn-outline-secondary">Kembali</a></div><form method="POST" action="{{ route('iso-documents.store') }}" enctype="multipart/form-data" class="card"><div class="card-body">@csrf @include('iso_documents.form')</div><div class="card-footer text-end"><button class="btn btn-primary">Buat Folder</button></div></form></div>
@endsection
