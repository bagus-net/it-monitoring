@extends('layouts.app')
@section('content')
<div class="container campaign-page campaign-form-page"><div class="campaign-form-heading"><div><div class="campaign-eyebrow">IT CAMPAIGN STUDIO</div><h1>Edit Campaign</h1><p>Perbarui target, PIC, status, dan informasi eksekusi campaign.</p></div><a href="{{ route('campaigns.show', $campaign) }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i>Kembali</a></div>
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form method="POST" action="{{ route('campaigns.update', $campaign) }}" class="card campaign-form-card">@csrf @method('PUT')<div class="card-header"><i class="bi bi-pencil-square"></i><strong>Campaign Brief</strong><span>Refine the mission</span></div><div class="card-body">@include('campaigns.form', ['campaign' => $campaign])</div><div class="card-footer text-end"><button class="btn btn-brand"><i class="bi bi-check2-circle"></i>Simpan Perubahan</button></div></form></div>
@endsection
