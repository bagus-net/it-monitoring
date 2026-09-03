@extends('layouts.app')

@section('content')
<div class="container mt-4"><div class="d-flex justify-content-between align-items-center mb-3"><div><h2 class="mb-1">Edit Folder Dokumen ISO</h2><p class="text-muted mb-0">Perbarui informasi folder atau daftar pengguna yang memiliki akses.</p></div><a href="{{ route('iso-documents.show', $isoDocument) }}" class="btn btn-outline-secondary">Kembali</a></div><form method="POST" action="{{ route('iso-documents.update', $isoDocument) }}" enctype="multipart/form-data" class="card"><div class="card-body">@csrf @method('PUT') @include('iso_documents.form')</div><div class="card-footer text-end"><button class="btn btn-primary">Simpan Perubahan</button></div></form></div>
@endsection
