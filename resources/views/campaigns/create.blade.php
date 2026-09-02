@extends('layouts.app')
@section('content')
<div class="container campaign-page campaign-form-page"><div class="campaign-form-heading"><div><div class="campaign-eyebrow">IT CAMPAIGN STUDIO</div><h1>Create Campaign</h1><p>Rancang campaign, tetapkan target, dan pantau progres eksekusi.</p></div><a href="{{ route('campaigns.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i>Kembali</a></div>
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form method="POST" action="{{ route('campaigns.store') }}" class="card campaign-form-card">@csrf<div class="card-header"><i class="bi bi-megaphone"></i><strong>Campaign Brief</strong><span>Define the mission</span></div><div class="card-body">@include('campaigns.form')</div><div class="card-footer text-end"><button class="btn btn-brand"><i class="bi bi-check2-circle"></i>Simpan Campaign</button></div></form></div>
@endsection
