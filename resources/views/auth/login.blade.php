@extends('layouts.auth')

@section('title', 'Masuk')
@section('heading', 'Masuk ke Akun')

@section('form')
<form method="POST" action="{{ route('login') }}">
    @csrf
    <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" value="{{ old('email') }}" class="form-control" required autofocus>
    </div>
    <div class="mb-3">
        <label class="form-label">Kata Sandi</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-brand w-100">Masuk</button>
</form>
@endsection

@section('footer')
Akun dibuat oleh administrator. Hubungi tim IT bila belum memiliki akses.
@endsection
